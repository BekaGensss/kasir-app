<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Menampilkan halaman kasir.
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $products = Product::all(); // Tambahkan ini
        return view('sales.index', compact('products')); // Kirimkan variabel 'products' ke view
    }

    /**
     * Mencari produk berdasarkan nama atau SKU.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        $products = Product::where('sku', 'like', "%{$query}%")
                            ->orWhere('name', 'like', "%{$query}%")
                            ->get();

        return response()->json($products);
    }

    /**
     * Memproses transaksi penjualan dan mengurangi stok.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'cash_paid' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $total_price = 0;
                $items = collect($request->items);

                foreach ($items as $item) {
                    $product = Product::find($item['id']);
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stok {$product->name} tidak mencukupi.");
                    }
                    $total_price += $product->price * $item['quantity'];
                }

                if ($request->payment_method === 'cash' && $request->cash_paid < $total_price) {
                    throw new \Exception("Jumlah pembayaran kurang dari total harga.");
                }

                $sale = Sale::create([
                    'total_price' => $total_price,
                    'cash_paid' => $request->cash_paid,
                    'payment_method' => $request->payment_method,
                ]);

                foreach ($items as $item) {
                    $product = Product::find($item['id']);
                    $sale->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price_per_item' => $product->price,
                    ]);

                    $product->stock -= $item['quantity'];
                    $product->save();
                }
            });

            $saleId = Sale::latest('id')->first()->id;
            return response()->json([
                'message' => 'Transaksi berhasil!', 
                'redirect' => route('sales.receipt', ['sale' => $saleId])
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Menampilkan riwayat transaksi penjualan.
     * @return \Illuminate\View\View
     */
    public function history()
    {
        $sales = Sale::with('items.product')->orderBy('created_at', 'desc')->get();
        return view('sales.history', compact('sales'));
    }

    /**
     * Menampilkan laporan penjualan dengan filter tanggal.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function report(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $query = Sale::query();
        if ($start_date && $end_date) {
            $query->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59']);
        }

        $total_sales = $query->sum('total_price');
        $total_transactions = $query->count();
        $top_products = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
                                 ->whereHas('sale', function ($q) use ($start_date, $end_date) {
                                     if ($start_date && $end_date) {
                                         $q->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59']);
                                     }
                                 })
                                 ->groupBy('product_id')
                                 ->orderBy('total_quantity', 'desc')
                                 ->with('product')
                                 ->limit(5)
                                 ->get();

        $daily_sales_query = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_price) as total')
        );

        if ($start_date && $end_date) {
            $daily_sales_query->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59']);
        }

        $daily_sales = $daily_sales_query->groupBy('date')
                                         ->orderBy('date', 'asc')
                                         ->get();

        $chart_labels = $daily_sales->pluck('date');
        $chart_data = $daily_sales->pluck('total');

        return view('sales.report', compact('total_sales', 'total_transactions', 'top_products', 'chart_labels', 'chart_data', 'start_date', 'end_date'));
    }

    /**
     * Menampilkan struk berdasarkan ID penjualan.
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\View\View
     */
    public function receipt(Sale $sale)
    {
        $sale->load('items.product');
        return view('sales.receipt', compact('sale'));
    }

    /**
     * Menghapus transaksi dari riwayat (satu per satu atau semua).
     * @param  string|int  $sale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($sale)
    {
        try {
            if ($sale === 'all') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                SaleItem::truncate();
                Sale::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } else {
                DB::transaction(function () use ($sale) {
                    $saleRecord = Sale::findOrFail($sale);
                    $saleRecord->items()->delete();
                    $saleRecord->delete();
                });
            }
            return redirect()->route('sales.history')->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('sales.history')->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}