@extends('app')

@section('title', 'Aplikasi Kasir')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="col-span-1 lg:col-span-2">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mb-4 md:mb-0">Aplikasi Kasir</h1>
            <div class="relative w-full md:w-auto md:max-w-xs">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" placeholder="Cari produk (Nama / SKU)" class="w-full p-3 pl-10 border border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all duration-300">
            </div>
        </div>
        
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Daftar Produk</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="productsTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                            <th class="relative px-6 py-3 text-center"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($products as $product)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">Rp. {{ number_format($product->price) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold @if($product->stock < 5) text-red-500 @else text-green-600 @endif">{{ $product->stock }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 transition-all duration-300 transform hover:scale-110 add-to-cart-btn" 
                                            data-id="{{ $product->id }}" 
                                            data-price="{{ $product->price }}" 
                                            data-name="{{ $product->name }}" 
                                            data-stock="{{ $product->stock }}"
                                            @if($product->stock === 0) disabled @endif>
                                        <i class="fas fa-plus-circle text-lg"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-span-1">
        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-6">Keranjang Belanja</h2>
        <div class="bg-white shadow-xl rounded-2xl p-6">
            <div class="overflow-x-auto mb-4">
                <table class="min-w-full divide-y divide-gray-200" id="cartTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">PRODUK</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">QTY</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">HARGA</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        </tbody>
                </table>
            </div>
            
            <div class="flex justify-between items-center mb-6 border-t border-gray-200 pt-4">
                <p class="text-xl font-extrabold text-gray-900">Total:</p>
                <p class="text-2xl font-extrabold text-blue-600" id="totalPrice">Rp. 0</p>
            </div>

            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Pilih Pembayaran:</h3>
                <div class="grid grid-cols-2 gap-4" id="payment-methods-buttons">
                    <button data-method="cash" class="payment-method-btn flex items-center justify-center p-4 bg-gray-100 rounded-xl shadow-sm text-gray-800 font-bold transition-all duration-300 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-money-bill-wave text-green-500 w-6 h-6 mr-2"></i>
                        <span>Tunai</span>
                    </button>
                    
                    <button data-method="qris" class="payment-method-btn flex items-center justify-center p-4 bg-gray-100 rounded-xl shadow-sm text-gray-800 font-bold transition-all duration-300 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-qrcode text-blue-500 w-6 h-6 mr-2"></i>
                        <span>QRIS</span>
                    </button>

                    <div class="relative group">
                        <button data-method="bca" class="payment-method-btn peer w-full flex items-center justify-center p-4 bg-gray-100 rounded-xl shadow-sm text-gray-800 font-bold transition-all duration-300 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-building-columns text-blue-600 w-6 h-6 mr-2"></i>
                            <span>BCA</span>
                        </button>
                        <div class="absolute z-10 top-full mt-2 w-full p-3 bg-white border border-gray-200 rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform scale-95 group-hover:scale-100">
                            <p class="text-xs font-semibold text-gray-500 mb-1">Nomor Rekening</p>
                            <p class="text-sm font-bold text-gray-900">1234567890</p>
                        </div>
                    </div>

                    <div class="relative group">
                        <button data-method="dana" class="payment-method-btn peer w-full flex items-center justify-center p-4 bg-gray-100 rounded-xl shadow-sm text-gray-800 font-bold transition-all duration-300 hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <i class="fas fa-wallet text-purple-600 w-6 h-6 mr-2"></i>
                            <span>DANA</span>
                        </button>
                        <div class="absolute z-10 top-full mt-2 w-full p-3 bg-white border border-gray-200 rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform scale-95 group-hover:scale-100">
                            <p class="text-xs font-semibold text-gray-500 mb-1">Nomor DANA</p>
                            <p class="text-sm font-bold text-gray-900">081234567890</p>
                        </div>
                    </div>

                    <div class="relative group">
                        <button data-method="bri" class="payment-method-btn peer w-full flex items-center justify-center p-4 bg-gray-100 rounded-xl shadow-sm text-gray-800 font-bold transition-all duration-300 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <i class="fas fa-landmark text-red-600 w-6 h-6 mr-2"></i>
                            <span>BRI</span>
                        </button>
                        <div class="absolute z-10 top-full mt-2 w-full p-3 bg-white border border-gray-200 rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform scale-95 group-hover:scale-100">
                            <p class="text-xs font-semibold text-gray-500 mb-1">Nomor Rekening</p>
                            <p class="text-sm font-bold text-gray-900">9876543210</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="payment-fields" class="mb-6">
                <div class="mb-4">
                    <label for="cashPaid" class="block text-sm font-semibold text-gray-700 uppercase mb-2">Uang Tunai</label>
                    <input type="number" id="cashPaid" placeholder="Masukkan jumlah uang" class="w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all duration-300">
                </div>
                <div class="flex justify-between items-center border-t border-gray-200 pt-4">
                    <p class="text-md font-bold text-gray-900">Kembalian:</p>
                    <p class="text-xl font-extrabold text-green-600" id="change">Rp. 0</p>
                </div>
            </div>
            
            <button id="checkoutButton" class="mt-4 w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-blue-700 transition-all duration-300 transform hover:scale-105 disabled:bg-gray-400 disabled:shadow-none disabled:transform-none">
                <i class="fas fa-dollar-sign mr-2"></i>
                Bayar Sekarang
            </button>
        </div>
    </div>
</div>

<div id="qris-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 transition-opacity duration-300 hidden opacity-0">
    <div class="bg-white p-8 rounded-2xl shadow-2xl text-center max-w-sm w-full transform scale-95 transition-transform duration-300">
        <h3 class="text-2xl font-bold mb-4 text-gray-900">Pembayaran QRIS</h3>
        <p class="text-gray-600 mb-6">Total Tagihan:</p>
        <p class="text-3xl font-extrabold text-blue-600 mb-6" id="qris-total-price"></p>
        <img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/QRIS_-_Quick_Response_Code_Indonesian_Standard.png" alt="QRIS Code" class="mx-auto my-6 w-56 h-56 border-4 border-gray-100 rounded-lg shadow-md">
        <p class="text-sm text-gray-500 mb-6">Silakan pindai kode QR ini untuk menyelesaikan pembayaran.</p>
        <button id="qris-confirm-btn" class="w-full bg-green-600 text-white font-bold py-3 rounded-xl shadow-lg hover:bg-green-700 transition-all duration-300">
            <i class="fas fa-check mr-2"></i>
            Konfirmasi Pembayaran
        </button>
    </div>
</div>

<div id="bank-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 transition-opacity duration-300 hidden opacity-0">
    <div class="bg-white p-8 rounded-2xl shadow-2xl text-center max-w-sm w-full transform scale-95 transition-transform duration-300">
        <h3 class="text-2xl font-bold mb-4 text-gray-900" id="bank-modal-title"></h3>
        <p class="text-gray-600 mb-6">Total Tagihan:</p>
        <p class="text-3xl font-extrabold text-blue-600 mb-6" id="bank-total-price"></p>
        
        <div class="mb-6 space-y-4">
            <div>
                <p class="text-sm font-semibold text-gray-700 uppercase mb-1">Nomor Rekening / E-Wallet</p>
                <p class="text-xl font-bold text-gray-900" id="bank-account-number"></p>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-700 uppercase mb-1">Nama Pemilik Rekening</p>
                <p class="text-lg font-bold text-gray-900" id="bank-account-name">Nama Pemilik Anda</p>
            </div>
        </div>

        <p class="text-sm text-gray-500 mb-6">Lakukan pembayaran ke nomor di atas dan konfirmasi setelahnya.</p>
        
        <button id="bank-confirm-btn" class="w-full bg-green-600 text-white font-bold py-3 rounded-xl shadow-lg hover:bg-green-700 transition-all duration-300">
            <i class="fas fa-check mr-2"></i>
            Konfirmasi Pembayaran
        </button>
    </div>
</div>
@endsection