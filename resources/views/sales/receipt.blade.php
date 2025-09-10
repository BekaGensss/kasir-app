<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian #{{ $sale->id }}</title>
    @vite(['resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        @media print {
            body {
                width: 58mm;
                font-size: 10px;
                color: #000;
                background-color: #fff;
            }
            .no-print {
                display: none;
            }
            .receipt-container {
                box-shadow: none;
                border: 1px dashed #ccc;
                padding: 10px;
            }
            .dashed-line {
                border-style: dashed;
            }
        }
    </style>
</head>
<body class="bg-gray-100 p-8 antialiased">

    <div class="receipt-container max-w-sm mx-auto bg-white p-8 rounded-2xl shadow-2xl">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-blue-600 tracking-wide mb-1">Kasir<span class="text-gray-800">App</span></h1>
            <h2 class="text-sm font-semibold text-gray-700 mt-2 mb-1">Struk Pembelian</h2>
            <p class="text-xs text-gray-500">{{ $sale->created_at->format('d M Y, H:i') }}</p>
        </div>
        <hr class="my-6 border-dashed border-gray-300">
        
        <div class="text-center text-xs text-gray-600 mb-6">
            <p class="font-bold">Kasir App Store</p>
            <p>Jalan Mawar No. 123, Kota ABC</p>
            <p>Telp: 0812-3456-7890</p>
        </div>
        
        <hr class="my-6 border-dashed border-gray-300">

        <div class="space-y-4">
            @foreach($sale->items as $item)
            <div class="flex justify-between items-start text-sm">
                <div class="flex-1 pr-2">
                    <p class="font-medium text-gray-800">{{ $item->product->name }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $item->quantity }} x Rp. {{ number_format($item->price_per_item) }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-900">Rp. {{ number_format($item->price_per_item * $item->quantity) }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <hr class="my-6 border-dashed border-gray-300">

        <div class="space-y-2 text-sm">
            <div class="flex justify-between items-center font-bold text-lg text-gray-900">
                <span>Total:</span>
                <span class="text-green-600">Rp. {{ number_format($sale->total_price) }}</span>
            </div>
            <div class="flex justify-between items-center text-gray-700">
                <span>Metode Pembayaran:</span>
                <span class="font-semibold">{{ ucfirst($sale->payment_method) }}</span>
            </div>
            @if(strtolower($sale->payment_method) === 'cash')
            <div class="flex justify-between items-center text-gray-700">
                <span>Tunai:</span>
                <span class="font-semibold">Rp. {{ number_format($sale->cash_paid) }}</span>
            </div>
            <div class="flex justify-between items-center text-gray-700">
                <span>Kembalian:</span>
                <span class="font-bold text-blue-600">Rp. {{ number_format($sale->cash_paid - $sale->total_price) }}</span>
            </div>
            @endif
        </div>
        
        <div class="text-center mt-8">
            <p class="text-sm font-semibold text-gray-700 mb-2">Sampai jumpa lagi!</p>
            <p class="text-sm text-gray-500 mb-4">
                <i class="fas fa-heart text-red-500 mr-1"></i>
                Terima kasih telah berbelanja!
            </p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('sales.receipt', ['sale' => $sale->id])) }}" alt="QR Code" class="mx-auto my-4 border border-gray-200">
            <p class="text-xs text-gray-400 mt-2">Struk #{{ $sale->id }}</p>
        </div>
    </div>

    <div class="no-print mt-8 flex justify-center space-x-4">
        <a href="{{ route('sales.index') }}" class="flex items-center bg-gray-500 text-white px-6 py-3 rounded-xl shadow-lg hover:bg-gray-600 transition-all duration-300 transform hover:scale-105">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
        <button onclick="window.print()" class="flex items-center bg-blue-600 text-white px-6 py-3 rounded-xl shadow-lg hover:bg-blue-700 transition-all duration-300 transform hover:scale-105">
            <i class="fas fa-print mr-2"></i>
            Cetak Struk
        </button>
    </div>

</body>
</html>