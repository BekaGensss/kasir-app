import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const productsTableBody = document.querySelector('#productsTable tbody');
    const cartTableBody = document.querySelector('#cartTable tbody');

    // Menambahkan kelas untuk fade-in card
    const mainContentCards = document.querySelectorAll('.bg-white.rounded-2xl.shadow-xl');
    mainContentCards.forEach(card => {
        card.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
    });

    // Cek jika berada di halaman kasir
    if (searchInput && productsTableBody && cartTableBody) {
        const totalPriceElement = document.getElementById('totalPrice');
        const cashPaidInput = document.getElementById('cashPaid');
        const changeElement = document.getElementById('change');
        const checkoutButton = document.getElementById('checkoutButton');
        const paymentMethodBtns = document.querySelectorAll('.payment-method-btn');
        const paymentFields = document.getElementById('payment-fields');

        // Elemen Modal QRIS
        const qrisModal = document.getElementById('qris-modal');
        const qrisConfirmBtn = document.getElementById('qris-confirm-btn');
        const qrisTotalPriceElement = document.getElementById('qris-total-price');

        let cart = [];
        let totalPrice = 0;
        let selectedPaymentMethod = '';

        // Tampilkan elemen utama dengan fade-in setelah DOM siap
        setTimeout(() => {
            mainContentCards.forEach(card => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            });
        }, 100);

        const fetchProducts = async (query) => {
            if (query.length < 2) {
                productsTableBody.innerHTML = '';
                return;
            }

            try {
                const response = await fetch(`/kasir/search?query=${query}`);
                if (!response.ok) throw new Error('Network response was not ok');
                const products = await response.json();
                
                productsTableBody.innerHTML = '';
                if (products.length === 0) {
                    productsTableBody.innerHTML = `<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Produk tidak ditemukan.</td></tr>`;
                    return;
                }

                products.forEach(product => {
                    const row = document.createElement('tr');
                    const stockClass = product.stock < 5 ? 'text-red-500 font-bold' : 'text-green-600';
                    row.classList.add('hover:bg-gray-50', 'transition-colors', 'duration-200');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">${product.name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${product.sku}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">Rp. ${product.price.toLocaleString('id-ID')}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold ${stockClass}">${product.stock}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 transition-all duration-300 transform hover:scale-110 add-to-cart-btn" 
                                data-id="${product.id}" 
                                data-price="${product.price}" 
                                data-name="${product.name}" 
                                data-stock="${product.stock}">
                                <i class="fas fa-plus-circle text-lg"></i>
                            </button>
                        </td>
                    `;
                    productsTableBody.appendChild(row);
                });
            } catch (error) {
                console.error('Fetching products failed:', error);
                Toastify({
                    text: "Gagal memuat produk. Coba lagi.",
                    duration: 3000,
                    backgroundColor: "#F44336",
                }).showToast();
            }
        };

        const updateCartDisplay = () => {
            cartTableBody.innerHTML = '';
            totalPrice = 0;
            
            if (cart.length > 0) {
                checkoutButton.disabled = false;
            } else {
                checkoutButton.disabled = true;
            }

            cart.forEach(item => {
                const row = document.createElement('tr');
                const subtotal = item.price * item.quantity;
                totalPrice += subtotal;
                
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">${item.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="number" value="${item.quantity}" min="1" max="${item.stock}" class="w-16 quantity-input border border-gray-300 rounded-lg p-1 text-center focus:ring-blue-500 focus:border-blue-500" data-id="${item.id}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">Rp. ${subtotal.toLocaleString('id-ID')}</td>
                `;
                cartTableBody.appendChild(row);
            });

            totalPriceElement.textContent = `Rp. ${totalPrice.toLocaleString('id-ID')}`;
            calculateChange();
        };

        const calculateChange = () => {
            if (selectedPaymentMethod === 'cash') {
                const cashPaid = parseFloat(cashPaidInput.value) || 0;
                const change = cashPaid - totalPrice;
                changeElement.textContent = `Rp. ${Math.max(0, change).toLocaleString('id-ID')}`;
            } else {
                changeElement.textContent = `Rp. 0`;
            }
            updateCheckoutButtonState();
        };

        const updateCheckoutButtonState = () => {
            checkoutButton.disabled = true;
            if (cart.length === 0) return;

            if (selectedPaymentMethod === 'cash') {
                const cashPaid = parseFloat(cashPaidInput.value) || 0;
                if (cashPaid >= totalPrice && totalPrice > 0) {
                    checkoutButton.disabled = false;
                }
            } else if (selectedPaymentMethod) {
                checkoutButton.disabled = false;
            }
        };

        paymentMethodBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Hapus kelas aktif dari semua tombol
                paymentMethodBtns.forEach(b => {
                    b.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
                    b.classList.add('bg-gray-100', 'text-gray-800');
                });
                
                // Tambahkan kelas aktif ke tombol yang diklik
                btn.classList.add('bg-blue-600', 'text-white', 'shadow-md');
                btn.classList.remove('bg-gray-100', 'text-gray-800');
                
                selectedPaymentMethod = btn.dataset.method;
                
                if (selectedPaymentMethod === 'cash') {
                    paymentFields.classList.remove('hidden');
                    cashPaidInput.value = totalPrice > 0 ? totalPrice : '';
                } else {
                    paymentFields.classList.add('hidden');
                    cashPaidInput.value = '';
                }
                calculateChange();
            });
        });

        searchInput.addEventListener('input', (e) => {
            fetchProducts(e.target.value);
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstProductBtn = productsTableBody.querySelector('.add-to-cart-btn');
                if (firstProductBtn) {
                    firstProductBtn.click();
                    searchInput.value = '';
                    fetchProducts('');
                }
            }
        });

        productsTableBody.addEventListener('click', (e) => {
            const btn = e.target.closest('.add-to-cart-btn');
            if (btn) {
                const productId = parseInt(btn.dataset.id);
                const productPrice = parseInt(btn.dataset.price);
                const productName = btn.dataset.name;
                const productStock = parseInt(btn.dataset.stock);

                const existingItem = cart.find(item => item.id === productId);

                if (existingItem) {
                    if (existingItem.quantity < productStock) {
                        existingItem.quantity += 1;
                        showFeedback(btn, true);
                    } else {
                        Toastify({
                            text: 'Stok tidak mencukupi!',
                            duration: 3000,
                            backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
                        }).showToast();
                    }
                } else {
                    if (productStock > 0) {
                        cart.push({
                            id: productId,
                            name: productName,
                            price: productPrice,
                            quantity: 1,
                            stock: productStock
                        });
                        showFeedback(btn, true);
                    } else {
                        Toastify({
                            text: 'Produk ini kehabisan stok!',
                            duration: 3000,
                            backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
                        }).showToast();
                    }
                }
                updateCartDisplay();
            }
        });

        // Fungsi untuk memberikan feedback visual pada tombol Tambah
        function showFeedback(btn, success) {
            if (success) {
                btn.innerHTML = `<i class="fas fa-check text-green-500 animate-pulse"></i>`;
                setTimeout(() => {
                    btn.innerHTML = `<i class="fas fa-plus-circle text-lg text-blue-600"></i>`;
                }, 800);
            }
        }

        cartTableBody.addEventListener('change', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                const productId = parseInt(e.target.dataset.id);
                let newQuantity = parseInt(e.target.value);
                
                const item = cart.find(item => item.id === productId);
                if (item) {
                    if (isNaN(newQuantity) || newQuantity < 1) {
                         newQuantity = 1;
                         e.target.value = 1;
                    }
                    
                    if (newQuantity <= item.stock) {
                        item.quantity = newQuantity;
                    } else {
                        Toastify({
                            text: `Stok hanya tersedia ${item.stock} unit.`,
                            duration: 3000,
                            backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
                        }).showToast();
                        e.target.value = item.stock;
                        item.quantity = item.stock;
                    }
                }
                updateCartDisplay();
            }
        });

        cashPaidInput.addEventListener('input', calculateChange);

        checkoutButton.addEventListener('click', async () => {
            if (cart.length === 0) return;

            if (selectedPaymentMethod === 'qris') {
                qrisTotalPriceElement.textContent = `Rp. ${totalPrice.toLocaleString('id-ID')}`;
                
                // Tambahkan animasi untuk modal
                qrisModal.classList.remove('hidden');
                setTimeout(() => {
                    qrisModal.classList.add('opacity-100');
                    qrisModal.querySelector('.rounded-2xl').classList.add('scale-100');
                }, 10);
                
                return;
            }

            // Jika metode pembayaran lain, langsung proses checkout
            await processCheckout();
        });

        qrisConfirmBtn.addEventListener('click', async () => {
            // Hilangkan animasi modal
            qrisModal.classList.remove('opacity-100');
            qrisModal.querySelector('.rounded-2xl').classList.remove('scale-100');
            setTimeout(() => {
                qrisModal.classList.add('hidden');
            }, 300);
            
            await processCheckout();
        });

        // Menutup modal QRIS saat klik di luar
        qrisModal.addEventListener('click', (e) => {
            if (e.target === qrisModal) {
                qrisModal.classList.remove('opacity-100');
                qrisModal.querySelector('.rounded-2xl').classList.remove('scale-100');
                setTimeout(() => {
                    qrisModal.classList.add('hidden');
                }, 300);
            }
        });

        async function processCheckout() {
            let payload = {
                items: cart.map(item => ({ id: item.id, quantity: item.quantity })),
                payment_method: selectedPaymentMethod,
            };

            if (selectedPaymentMethod === 'cash') {
                payload.cash_paid = parseFloat(cashPaidInput.value);
            }

            try {
                const response = await fetch('/kasir/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('Server error:', errorData);
                    Toastify({
                        text: 'Transaksi gagal: ' + (errorData.message || 'Terjadi kesalahan.'),
                        duration: 3000,
                        backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
                    }).showToast();
                    return;
                }

                const data = await response.json();
                window.location.href = data.redirect;
                
            } catch (error) {
                console.error('Error:', error);
                Toastify({
                    text: 'Terjadi kesalahan saat memproses transaksi.',
                    duration: 3000,
                    backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
                }).showToast();
            }
        }
        
        const defaultMethodBtn = document.querySelector('[data-method="cash"]');
        if (defaultMethodBtn) {
            defaultMethodBtn.click();
        }
    }
});