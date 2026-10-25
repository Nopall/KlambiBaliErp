@extends('adminlte::page')

@section('title', 'Point of Sale (POS) Kasir - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-display text-success me-2"></i>Point of Sale (POS) Kasir Toko
                <span class="text-secondary fw-normal fs-5">/ Outlet Sanur (Gudang Store POS)</span>
            </h1>
            <p class="text-muted small mb-0">Kasir Toko Fisik KlambiBali - Transaksi Langsung, Scan Barcode & Split Payment</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-warning btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#closeShiftModal">
                <i class="bi bi-door-closed me-1"></i> Tutup Kasir (Shift)
            </button>
            <a href="{{ route('pos.history') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-clock-history me-1"></i> Riwayat Transaksi
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-speedometer2"></i>
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row g-3">
    {{-- PRODUCT SELECTION COLUMN --}}
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-3 mb-3">
            <div class="card-body p-3">
                <div class="row g-2">
                    <div class="col-12 col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="posSearch" class="form-control border-start-0" placeholder="Scan Barcode / Ketik SKU atau Nama Produk (misal: Kebaya, Endek)..." autofocus onkeyup="filterProducts()">
                            <button class="btn btn-primary" type="button" onclick="focusSearch()"><i class="bi bi-upc-scan me-1"></i> Scan</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <select class="form-select" id="posCategoryFilter" onchange="filterProducts()">
                            <option value="">Semua Kategori</option>
                            <option value="kebaya">Kebaya Bali</option>
                            <option value="kamen">Kamen & Bawahan</option>
                            <option value="safari">Kemeja Safari</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3" id="productGrid" style="max-height: 680px; overflow-y: auto;">
            @forelse($catalog as $item)
                <div class="col-6 col-md-4 product-card" 
                     data-name="{{ strtolower($item['name']) }}" 
                     data-sku="{{ strtolower($item['sku']) }}"
                     data-barcode="{{ strtolower($item['barcode'] ?? '') }}"
                     data-category="{{ strtolower($item['category']) }}">
                    <div class="card border-0 shadow-sm rounded-3 h-100 hover-shadow transition cursor-pointer bg-white" 
                         onclick="addToCart({{ json_encode($item) }})" style="cursor: pointer;">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-secondary-subtle text-secondary small">{{ $item['category'] }}</span>
                                    <span class="badge {{ $item['stock'] > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} small">
                                        Stok {{ $item['stock'] }} {{ $item['unit'] }}
                                    </span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 13px;">{{ $item['name'] }}</h6>
                                <div class="text-muted small mb-2 font-monospace" style="font-size: 11px;">
                                    SKU: {{ $item['sku'] }}
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                <span class="fw-bold text-primary">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                <button class="btn btn-sm btn-primary rounded-circle" style="width: 28px; height: 28px; padding: 0;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Belum ada produk barang jadi yang tersedia di Gudang Store POS.
                </div>
            @endforelse
        </div>
    </div>

    {{-- CART & PAYMENT COLUMN --}}
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cart3 text-primary me-2"></i>Keranjang Kasir</h5>
                <button class="btn btn-sm btn-outline-danger" onclick="clearCart()"><i class="bi bi-trash me-1"></i> Kosongkan</button>
            </div>
            
            {{-- MEMBER SELECTION --}}
            <div class="px-3 pt-2 pb-0">
                <label class="form-label small fw-semibold text-muted mb-1">Customer / Member KlambiBali:</label>
                <select class="form-select form-select-sm" id="customerSelect" onchange="calculateCart()">
                    <option value="" data-discount="0">Pelanggan Umum (Non-Member)</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" data-discount="{{ str_contains(strtolower($c->tier), 'platinum') ? 5 : (str_contains(strtolower($c->tier), 'gold') ? 3 : 0) }}">
                            {{ $c->name }} ({{ $c->member_code }} - {{ $c->tier }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="card-body p-3">
                <div class="table-responsive" style="max-height: 260px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" id="cartTable">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th width="85">Qty</th>
                                <th class="text-end">Subtotal</th>
                                <th width="35"></th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            {{-- Generated via JS --}}
                        </tbody>
                    </table>
                </div>

                {{-- SUMMARY TOTALS --}}
                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between mb-1 small text-muted">
                        <span>Subtotal Belanja:</span>
                        <span id="subtotalText" class="fw-semibold">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 small text-success">
                        <span>Diskon Member:</span>
                        <span id="discountText" class="fw-semibold">-Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-top pt-2">
                        <span class="fw-bold fs-5 text-dark">Total Tagihan:</span>
                        <span class="fw-bold fs-4 text-primary" id="grandTotalText">Rp 0</span>
                    </div>

                    {{-- METODE PEMBAYARAN & SPLIT PAYMENT --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Pilih Metode Pembayaran:</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="payment_method" id="payCash" value="cash" checked onchange="toggleSplitPayment()">
                            <label class="btn btn-outline-primary btn-sm" for="payCash"><i class="bi bi-cash me-1"></i> Tunai (Cash)</label>

                            <input type="radio" class="btn-check" name="payment_method" id="payQris" value="qris" onchange="toggleSplitPayment()">
                            <label class="btn btn-outline-primary btn-sm" for="payQris"><i class="bi bi-qr-code me-1"></i> QRIS</label>

                            <input type="radio" class="btn-check" name="payment_method" id="payDebit" value="debit" onchange="toggleSplitPayment()">
                            <label class="btn btn-outline-primary btn-sm" for="payDebit"><i class="bi bi-credit-card me-1"></i> Debit BCA</label>

                            <input type="radio" class="btn-check" name="payment_method" id="paySplit" value="split" onchange="toggleSplitPayment()">
                            <label class="btn btn-outline-warning btn-sm" for="paySplit"><i class="bi bi-pie-chart me-1"></i> Split Pay</label>
                        </div>
                    </div>

                    {{-- SPLIT PAYMENT INPUTS --}}
                    <div id="splitPaymentBox" class="p-3 bg-light rounded-3 border mb-3 d-none">
                        <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-pie-chart text-warning me-1"></i>Kombinasi Split Payment:</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="small text-muted">Nominal Tunai (Rp):</label>
                                <input type="number" id="splitCashInput" class="form-control form-control-sm" placeholder="0" onkeyup="calculateChange()">
                            </div>
                            <div class="col-6">
                                <label class="small text-muted">Nominal Non-Tunai (QRIS/Debit):</label>
                                <input type="number" id="splitNonCashInput" class="form-control form-control-sm" placeholder="0" onkeyup="calculateChange()">
                            </div>
                        </div>
                    </div>

                    {{-- NORMAL CASH INPUT --}}
                    <div id="cashPaymentBox" class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Uang Diterima dari Pelanggan (Rp):</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" id="paidAmountInput" class="form-control fw-bold" placeholder="0" onkeyup="calculateChange()">
                        </div>
                        <div class="d-flex justify-content-between mt-2 small">
                            <span class="text-muted">Kembalian:</span>
                            <span class="fw-bold text-success fs-6" id="changeText">Rp 0</span>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success btn-lg fw-bold shadow-sm" id="btnSubmitPayment" onclick="submitPosPayment()">
                            <i class="bi bi-check2-circle me-1"></i> PROSES PEMBAYARAN & CETAK STRUK
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL STRUK SUKSES --}}
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-printer me-1"></i> Struk Transaksi Kasir</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 font-monospace small" id="receiptContent">
                {{-- Injected dynamically --}}
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i> Cetak Struk</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TUTUP KASIR (SHIFT CLOSING) --}}
<div class="modal fade" id="closeShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-door-closed me-1"></i> Form Tutup Kasir & Rekonsiliasi Kas Fisik</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted mb-3">
                    Hitung uang tunai fisik yang ada di laci kasir untuk dibandingkan dengan total penjualan kasir tercatat di sistem hari ini.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">Jumlah Uang Kas Fisik di Laci (Rp):</label>
                    <input type="number" id="actualCashInput" class="form-control form-control-lg fw-bold" placeholder="Contoh: 1500000" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted">Catatan Tutup Shift / Shift Kasir:</label>
                    <textarea id="shiftNotes" class="form-control" rows="2" placeholder="Catatan selisih atau serah terima shift..."></textarea>
                </div>
                <div id="closeShiftResult" class="p-3 bg-light rounded-3 border d-none">
                    {{-- Result message --}}
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning btn-sm fw-bold" onclick="submitCloseShift()"><i class="bi bi-check-circle me-1"></i> Konfirmasi Tutup Kasir</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    let cart = [];

    function focusSearch() {
        document.getElementById('posSearch').focus();
    }

    function filterProducts() {
        const query = document.getElementById('posSearch').value.toLowerCase();
        const cat = document.getElementById('posCategoryFilter').value.toLowerCase();
        const cards = document.querySelectorAll('.product-card');

        cards.forEach(c => {
            const name = c.getAttribute('data-name');
            const sku = c.getAttribute('data-sku');
            const barcode = c.getAttribute('data-barcode');
            const itemCat = c.getAttribute('data-category');

            const matchQuery = !query || name.includes(query) || sku.includes(query) || barcode.includes(query);
            const matchCat = !cat || itemCat.includes(cat);

            if (matchQuery && matchCat) {
                c.style.display = 'block';
            } else {
                c.style.display = 'none';
            }
        });
    }

    function addToCart(item) {
        const existing = cart.find(x => x.product_id === item.product_id && x.variant_id === item.variant_id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({
                product_id: item.product_id,
                variant_id: item.variant_id,
                name: item.name,
                sku: item.sku,
                price: item.price,
                hpp: item.hpp,
                qty: 1
            });
        }
        renderCart();
    }

    function updateQty(index, qty) {
        if (qty <= 0) {
            cart.splice(index, 1);
        } else {
            cart[index].qty = parseFloat(qty);
        }
        renderCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function renderCart() {
        const tbody = document.getElementById('cartItems');
        tbody.innerHTML = '';

        if (cart.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4 small"><i class="bi bi-cart-x fs-2 d-block mb-1"></i>Keranjang kosong. Klik produk di sebelah kiri atau scan barcode.</td></tr>`;
            calculateCart();
            return;
        }

        cart.forEach((item, index) => {
            const subtotal = item.qty * item.price;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="fw-bold small text-dark">${item.name}</div>
                    <div class="text-muted font-monospace" style="font-size: 11px;">${item.sku} @ Rp ${item.price.toLocaleString('id-ID')}</div>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm text-center" value="${item.qty}" min="1" onchange="updateQty(${index}, this.value)">
                </td>
                <td class="text-end fw-semibold small">Rp ${subtotal.toLocaleString('id-ID')}</td>
                <td class="text-center">
                    <button class="btn btn-sm text-danger p-0" onclick="removeItem(${index})"><i class="bi bi-x-circle"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        calculateCart();
    }

    function calculateCart() {
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += (item.qty * item.price);
        });

        const custSelect = document.getElementById('customerSelect');
        const discountPercent = parseFloat(custSelect.options[custSelect.selectedIndex]?.getAttribute('data-discount') || 0);
        const discountAmount = subtotal * (discountPercent / 100);
        const grandTotal = subtotal - discountAmount;

        document.getElementById('subtotalText').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('discountText').innerText = '-Rp ' + discountAmount.toLocaleString('id-ID') + (discountPercent > 0 ? ` (${discountPercent}%)` : '');
        document.getElementById('grandTotalText').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');

        // Set default paid amount if cash
        const activeMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
        if (activeMethod !== 'cash') {
            document.getElementById('paidAmountInput').value = grandTotal;
        }
        calculateChange();
    }

    function toggleSplitPayment() {
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        const splitBox = document.getElementById('splitPaymentBox');
        const cashBox = document.getElementById('cashPaymentBox');

        if (method === 'split') {
            splitBox.classList.remove('d-none');
            cashBox.classList.add('d-none');
        } else {
            splitBox.classList.add('d-none');
            cashBox.classList.remove('d-none');
        }
        calculateChange();
    }

    function calculateChange() {
        let grandTotal = 0;
        cart.forEach(item => grandTotal += (item.qty * item.price));
        const custSelect = document.getElementById('customerSelect');
        const discountPercent = parseFloat(custSelect.options[custSelect.selectedIndex]?.getAttribute('data-discount') || 0);
        grandTotal -= (grandTotal * (discountPercent / 100));

        const method = document.querySelector('input[name="payment_method"]:checked').value;
        let paid = 0;

        if (method === 'split') {
            const cash = parseFloat(document.getElementById('splitCashInput').value || 0);
            const nonCash = parseFloat(document.getElementById('splitNonCashInput').value || 0);
            paid = cash + nonCash;
        } else {
            paid = parseFloat(document.getElementById('paidAmountInput').value || 0);
        }

        const change = Math.max(0, paid - grandTotal);
        document.getElementById('changeText').innerText = 'Rp ' + change.toLocaleString('id-ID');
    }

    async function submitPosPayment() {
        if (cart.length === 0) {
            alert('Keranjang belanja masih kosong!');
            return;
        }

        let subtotal = 0;
        cart.forEach(item => subtotal += (item.qty * item.price));
        const custSelect = document.getElementById('customerSelect');
        const discountPercent = parseFloat(custSelect.options[custSelect.selectedIndex]?.getAttribute('data-discount') || 0);
        const discountAmount = subtotal * (discountPercent / 100);
        const grandTotal = subtotal - discountAmount;

        const method = document.querySelector('input[name="payment_method"]:checked').value;
        let paid = 0;
        let splitInfo = '';

        if (method === 'split') {
            const cash = parseFloat(document.getElementById('splitCashInput').value || 0);
            const nonCash = parseFloat(document.getElementById('splitNonCashInput').value || 0);
            paid = cash + nonCash;
            splitInfo = `Tunai: Rp ${cash.toLocaleString('id-ID')} | Non-Tunai: Rp ${nonCash.toLocaleString('id-ID')}`;
        } else {
            paid = parseFloat(document.getElementById('paidAmountInput').value || 0);
        }

        if (paid < grandTotal) {
            alert('Jumlah uang pembayaran kurang dari total tagihan!');
            return;
        }

        const btn = document.getElementById('btnSubmitPayment');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses Pembayaran...';

        try {
            const response = await fetch("{{ route('pos.checkout') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    customer_id: custSelect.value,
                    payment_method: method,
                    subtotal: subtotal,
                    discount_amount: discountAmount,
                    grand_total: grandTotal,
                    paid_amount: paid,
                    split_info: splitInfo,
                    items: cart
                })
            });

            const res = await response.json();
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> PROSES PEMBAYARAN & CETAK STRUK';

            if (res.success) {
                // Tampilkan Modal Struk
                showReceipt(res.order, paid, paid - grandTotal, method, splitInfo);
                clearCart();
            } else {
                alert('Gagal: ' + res.message);
            }
        } catch (err) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> PROSES PEMBAYARAN & CETAK STRUK';
            alert('Terjadi kesalahan jaringan atau server: ' + err.message);
        }
    }

    function showReceipt(order, paid, change, method, splitInfo) {
        const rc = document.getElementById('receiptContent');
        let itemsHtml = '';
        order.items.forEach(it => {
            itemsHtml += `
                <div class="d-flex justify-content-between">
                    <span>${it.item_name} x ${it.qty}</span>
                    <span>Rp ${parseFloat(it.subtotal).toLocaleString('id-ID')}</span>
                </div>
            `;
        });

        rc.innerHTML = `
            <div class="text-center mb-2">
                <h6 class="fw-bold mb-0">KLAMBIBALI INDONESIA</h6>
                <small class="text-muted d-block">Butik & Outlet Sanur, Bali</small>
                <small class="text-muted">Telp: 0812-3456-7890</small>
            </div>
            <div class="border-top border-bottom py-1 my-2">
                <div>No: <strong>${order.order_number}</strong></div>
                <div>Tgl: ${order.order_date}</div>
                <div>Metode: ${method.toUpperCase()} ${splitInfo ? '(' + splitInfo + ')' : ''}</div>
            </div>
            <div class="mb-2">
                ${itemsHtml}
            </div>
            <div class="border-top pt-2">
                <div class="d-flex justify-content-between">
                    <span>Subtotal:</span>
                    <span>Rp ${parseFloat(order.subtotal).toLocaleString('id-ID')}</span>
                </div>
                ${parseFloat(order.discount_amount) > 0 ? `
                <div class="d-flex justify-content-between text-success">
                    <span>Diskon:</span>
                    <span>-Rp ${parseFloat(order.discount_amount).toLocaleString('id-ID')}</span>
                </div>` : ''}
                <div class="d-flex justify-content-between fw-bold fs-6">
                    <span>TOTAL:</span>
                    <span>Rp ${parseFloat(order.grand_total).toLocaleString('id-ID')}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Bayar:</span>
                    <span>Rp ${paid.toLocaleString('id-ID')}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Kembali:</span>
                    <span>Rp ${change.toLocaleString('id-ID')}</span>
                </div>
            </div>
            <div class="text-center mt-3 pt-2 border-top">
                <small>Terima Kasih Atas Kunjungan Anda</small>
                <div class="text-muted" style="font-size: 10px;">Barang yang sudah dibeli dapat ditukar max 3 hari dg struk</div>
            </div>
        `;

        const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
        modal.show();
    }

    async function submitCloseShift() {
        const cash = document.getElementById('actualCashInput').value;
        const notes = document.getElementById('shiftNotes').value;
        if (!cash) {
            alert('Masukkan jumlah uang fisik di laci kasir!');
            return;
        }

        const resBox = document.getElementById('closeShiftResult');
        resBox.classList.remove('d-none');
        resBox.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menghitung rekonsiliasi shift kasir...';

        try {
            const resp = await fetch("{{ route('pos.close-shift') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ actual_cash: cash, notes: notes })
            });
            const data = await resp.json();
            if (data.success) {
                resBox.className = 'p-3 rounded-3 border bg-success-subtle text-success';
                resBox.innerHTML = `
                    <h6 class="fw-bold mb-1"><i class="bi bi-check-circle me-1"></i> Shift Kasir Berhasil Ditutup!</h6>
                    <div>Total Kas Penjualan Sistem: <strong>Rp ${data.system_cash.toLocaleString('id-ID')}</strong></div>
                    <div>Uang Fisik Kasir: <strong>Rp ${data.actual_cash.toLocaleString('id-ID')}</strong></div>
                    <div>Status Rekonsiliasi: <strong>${data.status}</strong></div>
                `;
            } else {
                resBox.className = 'p-3 rounded-3 border bg-danger-subtle text-danger';
                resBox.innerText = 'Gagal: ' + data.message;
            }
        } catch (err) {
            resBox.className = 'p-3 rounded-3 border bg-danger-subtle text-danger';
            resBox.innerText = 'Error: ' + err.message;
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        renderCart();
    });
</script>
@stop
