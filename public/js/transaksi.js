document.addEventListener('DOMContentLoaded', function () {
    const config = window.posConfig;
    
    // Elements
    const searchInput = document.getElementById('search-menu');
    const tabBtns = document.querySelectorAll('.tab-btn');
    const menuGrid = document.getElementById('menu-grid');
    const menuCards = document.querySelectorAll('.menu-card');
    const cartContainer = document.getElementById('cart-items-container');
    const subtotalText = document.getElementById('summary-subtotal');
    const pajakText = document.getElementById('summary-pajak');
    const totalText = document.getElementById('summary-total');
    const cashInput = document.getElementById('cash-received');
    const changeText = document.getElementById('change-display');
    const submitBtn = document.getElementById('btn-submit-order');
    const trxIdDisplay = document.getElementById('trx-id-display');

    // State
    let currentCategory = 'all';
    let searchQuery = '';

    // Initialize TRX ID format placeholder
    const currentYear = new Date().getFullYear();
    trxIdDisplay.textContent = `TRX-${currentYear}XXXXX`;

    // 1. FILTER CATEGORY & SEARCH
    function filterMenu() {
        menuCards.forEach(card => {
            const name = card.getAttribute('data-name');
            const category = card.getAttribute('data-category');
            
            const matchCategory = currentCategory === 'all' || category === currentCategory;
            const matchSearch = name.includes(searchQuery);

            if (matchCategory && matchSearch) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', function (e) {
        searchQuery = e.target.value.toLowerCase().trim();
        filterMenu();
    });

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentCategory = btn.getAttribute('data-category');
            filterMenu();
        });
    });

    // 2. AJAX CART OPERATIONS
    async function updateCartDOM(data) {
        if (!data.success) return;

        // Reset all selected classes on cards
        menuCards.forEach(card => card.classList.remove('selected'));

        if (data.items.length === 0) {
            cartContainer.innerHTML = `
                <div class="order-empty">
                    <i class="ti ti-shopping-cart-off"></i>
                    <p>Belum ada pesanan</p>
                </div>
            `;
            submitBtn.disabled = true;
        } else {
            let html = '';
            data.items.forEach(item => {
                // Highlight the menu card in the grid
                const card = document.querySelector(`.menu-card[data-id="${item.menu_id}"]`);
                if (card) card.classList.add('selected');

                html += `
                    <div class="order-item" data-id="${item.menu_id}">
                        <div class="order-item-info">
                            <div class="order-item-name">${item.nama_menu}</div>
                            <div class="order-item-price">${item.harga_formatted}</div>
                        </div>
                        <div class="order-item-actions">
                            <button class="qty-btn btn-minus" data-id="${item.menu_id}">−</button>
                            <span class="qty-num">${item.jumlah}</span>
                            <button class="qty-btn btn-plus" data-id="${item.menu_id}">+</button>
                            <button class="remove-item-btn" data-id="${item.menu_id}"><i class="ti ti-trash"></i></button>
                        </div>
                    </div>
                `;
            });
            cartContainer.innerHTML = html;
            submitBtn.disabled = false;
        }

        // Update totals
        subtotalText.textContent = data.subtotal_formatted;
        pajakText.textContent = data.pajak_formatted;
        totalText.textContent = data.total_formatted;
        totalText.setAttribute('data-value', data.total);

        // Recalculate change
        calculateChange();
    }

    async function sendCartRequest(url, payload) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.token
                },
                body: JSON.stringify(payload)
            });
            const data = await response.json();
            updateCartDOM(data);
        } catch (error) {
            console.error('Cart operation failed:', error);
            alert('Gagal memperbarui keranjang.');
        }
    }

    // Grid Card Click
    menuGrid.addEventListener('click', function (e) {
        const card = e.target.closest('.menu-card');
        if (!card) return;

        if (card.classList.contains('out-of-stock')) {
            alert('Stok menu ini sudah habis!');
            return;
        }

        const menuId = parseInt(card.getAttribute('data-id'));
        sendCartRequest(config.cartAddUrl, { menu_id: menuId });
    });

    // Cart Action Clicks (+ / - / delete)
    cartContainer.addEventListener('click', function (e) {
        const btnMinus = e.target.closest('.btn-minus');
        const btnPlus = e.target.closest('.btn-plus');
        const btnRemove = e.target.closest('.remove-item-btn');

        if (btnMinus) {
            const id = parseInt(btnMinus.getAttribute('data-id'));
            sendCartRequest(config.cartUpdateUrl, { menu_id: id, delta: -1 });
        } else if (btnPlus) {
            const id = parseInt(btnPlus.getAttribute('data-id'));
            // Check stock limit before updating qty
            const card = document.querySelector(`.menu-card[data-id="${id}"]`);
            const stock = card ? parseInt(card.getAttribute('data-stock')) : 0;
            const currentQty = parseInt(btnPlus.previousElementSibling.textContent);

            if (currentQty >= stock) {
                alert('Tidak bisa menambah jumlah. Stok habis/terbatas!');
                return;
            }

            sendCartRequest(config.cartUpdateUrl, { menu_id: id, delta: 1 });
        } else if (btnRemove) {
            const id = parseInt(btnRemove.getAttribute('data-id'));
            sendCartRequest(config.cartRemoveUrl, { menu_id: id });
        }
    });

    // 3. PAYMENT & CHANGE CALCULATION
    function calculateChange() {
        const total = parseFloat(totalText.getAttribute('data-value') || 0);
        const cash = parseFloat(cashInput.value || 0);
        const change = Math.max(0, cash - total);

        if (cash > 0 && cash < total) {
            changeText.textContent = 'Uang kurang';
            changeText.style.color = 'var(--danger)';
        } else {
            changeText.textContent = formatRupiah(change);
            changeText.style.color = 'var(--coffee)';
        }
    }

    cashInput.addEventListener('input', calculateChange);

    function formatRupiah(amount) {
        return 'Rp ' + amount.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // 4. SUBMIT ORDER
    submitBtn.addEventListener('click', async function () {
        const total = parseFloat(totalText.getAttribute('data-value') || 0);
        const cash = parseFloat(cashInput.value || 0);

        if (total <= 0) {
            alert('Keranjang masih kosong!');
            return;
        }

        if (isNaN(cash) || cash < total) {
            alert('Nominal uang bayar kurang atau tidak valid!');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ti ti-loader-2 animate-spin"></i> Memproses...';

        try {
            const response = await fetch(config.storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.token
                },
                body: JSON.stringify({ uang_bayar: cash })
            });

            const data = await response.json();

            if (data.success) {
                // Clear inputs
                cashInput.value = '';
                changeText.textContent = 'Rp 0';
                
                // Clear cart locally
                updateCartDOM({
                    success: true,
                    items: [],
                    subtotal: 0,
                    subtotal_formatted: 'Rp 0',
                    pajak: 0,
                    pajak_formatted: 'Rp 0',
                    total: 0,
                    total_formatted: 'Rp 0'
                });

                // Inform and redirect/print
                alert(data.message);
                
                // Open printing window in a new tab
                window.open(data.print_url, '_blank', 'width=600,height=800');
                
                // Reload window to update stocks in grid
                window.location.reload();
            } else {
                alert(data.message || 'Transaksi gagal.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ti ti-circle-check"></i> Simpan & Cetak Struk';
            }
        } catch (error) {
            console.error('Checkout failed:', error);
            alert('Terjadi kesalahan jaringan.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ti ti-circle-check"></i> Simpan & Cetak Struk';
        }
    });
});
