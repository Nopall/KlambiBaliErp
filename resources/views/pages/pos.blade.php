@extends('adminlte::page')

@section('title', 'Point of Sale (POS) Kasir')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-calculator-fill text-success me-2"></i>Point of Sale (POS) Kasir
            </h1>
            <p class="text-muted small mb-0">Kasir Toko Fisik KlambiBali - Transaksi Langsung & Scan Barcode</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-light text-dark border p-2">
                <i class="bi bi-clock me-1"></i> {{ date('d M Y') }}
            </span>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
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
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" id="posSearch" class="form-control border-start-0" placeholder="Scan Barcode / Ketik SKU atau Nama Produk (misal: Kebaya, Endek, Safari)..." autofocus>
                    <button class="btn btn-primary" type="button"><i class="bi bi-upc-scan me-1"></i> Scan</button>
                </div>
            </div>
        </div>

        <div class="row g-3" id="productGrid">
            @php
                $catalog = [
                    ['id' => 1, 'name' => 'Kebaya Bordir Bali Premium Maroon', 'sku' => 'KB-BRD-MRN', 'price' => 385000, 'stock' => 24, 'category' => 'Kebaya'],
                    ['id' => 2, 'name' => 'Kamen Endek Sutra Motif Wajik Emas', 'sku' => 'KM-EDK-GLD', 'price' => 450000, 'stock' => 18, 'category' => 'Kamen'],
                    ['id' => 3, 'name' => 'Kemeja Safari Pria Katun Putih Bersih', 'sku' => 'KMJ-SFR-WHT', 'price' => 275000, 'stock' => 32, 'category' => 'Kemeja'],
                    ['id' => 4, 'name' => 'Selendang Obi Songket Tradisional', 'sku' => 'SLD-OBI-TRD', 'price' => 85000, 'stock' => 50, 'category' => 'Aksesoris'],
                    ['id' => 5, 'name' => 'Udeng Bali Pria Setengah Jadi Hitam', 'sku' => 'UDG-BLI-BLK', 'price' => 65000, 'stock' => 40, 'category' => 'Aksesoris'],
                    ['id' => 6, 'name' => 'Kebaya Katun Brokat Halus Sage Green', 'sku' => 'KB-BRK-SGE', 'price' => 320000, 'stock' => 15, 'category' => 'Kebaya'],
                ];
            @endphp

            @foreach($catalog as $item)
                <div class="col-6 col-md-4 product-card" data-name="{{ strtolower($item['name']) }}" data-sku="{{ strtolower($item['sku']) }}">
                    <div class="card border-0 shadow-sm rounded-3 h-100 hover-shadow transition cursor-pointer" onclick="addToCart('{{ $item['name'] }}', {{ $item['price'] }}, '{{ $item['sku'] }}')">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-light text-secondary border small mb-1">{{ $item['category'] }}</span>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">{{ $item['name'] }}</h6>
                                <div class="text-muted small mb-2" style="font-size: 11px;">SKU: {{ $item['sku'] }}</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                <span class="fw-bold text-primary">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                <span class="badge bg-success-subtle text-success small">Stok {{ $item['stock'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- CART & PAYMENT COLUMN --}}
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cart3 text-primary me-2"></i>Keranjang Kasir</h5>
                <button class="btn btn-sm btn-outline-danger" onclick="clearCart()"><i class="bi bi-trash me-1"></i> Reset</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" id="cartTable">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th width="100">Qty</th>
                                <th>Subtotal</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <tr>
                                <td>
                                    <div class="fw-bold small">Kebaya Bordir Bali Premium Maroon</div>
                                    <div class="text-muted" style="font-size: 11px;">@ Rp 385.000</div>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm qty-input" value="1" min="1" onchange="updateTotal()">
                                </td>
                                <td class="fw-semibold small">Rp 385.000</td>
                                <td><button class="btn btn-sm text-danger p-0" onclick="this.closest('tr').remove(); updateTotal();"><i class="bi bi-x-circle"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- PAYMENT SUMMARY --}}
                <div class="p-3 bg-light border-top">
                    {{-- CUSTOMER / MEMBER SELECTOR (SESUAI BAB 11) --}}
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark mb-1"><i class="bi bi-person-badge me-1"></i> Customer / Member:</label>
                        <select class="form-select form-select-sm" id="customerSelect">
                            <option value="1">MBR-001 - Ni Wayan Sukmawati (Member Platinum - 1.250 Poin)</option>
                            <option value="2">MBR-002 - I Made Suardana (Member Gold - 480 Poin)</option>
                            <option value="0">Pelanggan Umum / Non-Member</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between mb-1 small">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-semibold" id="subtotalVal">Rp 385.000</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 small">
                        <span class="text-muted">Diskon Member (5%):</span>
                        <span class="text-success fw-semibold" id="discountVal">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-top border-bottom mb-3">
                        <span class="fw-bold fs-5 text-dark">Total Tagihan:</span>
                        <span class="fw-bold fs-5 text-primary" id="totalVal">Rp 385.000</span>
                    </div>

                    {{-- PAYMENT METHOD INCLUDING SPLIT PAYMENT (BAB 11) --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Metode Pembayaran:</label>
                        <div class="row g-1">
                            <div class="col-3">
                                <input type="radio" class="btn-check" name="paymentMethod" id="payCash" checked>
                                <label class="btn btn-outline-primary btn-sm w-100" for="payCash"><i class="bi bi-cash"></i> Cash</label>
                            </div>
                            <div class="col-3">
                                <input type="radio" class="btn-check" name="paymentMethod" id="payDebit">
                                <label class="btn btn-outline-primary btn-sm w-100" for="payDebit"><i class="bi bi-credit-card"></i> Debit</label>
                            </div>
                            <div class="col-3">
                                <input type="radio" class="btn-check" name="paymentMethod" id="payQris">
                                <label class="btn btn-outline-primary btn-sm w-100" for="payQris"><i class="bi bi-qr-code"></i> QRIS</label>
                            </div>
                            <div class="col-3">
                                <input type="radio" class="btn-check" name="paymentMethod" id="paySplit">
                                <label class="btn btn-outline-warning btn-sm w-100" for="paySplit" title="Contoh: Cash + QRIS / Debit + QRIS"><i class="bi bi-pie-chart"></i> Split</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-lg fw-bold shadow-sm" onclick="processPayment()">
                            <i class="bi bi-check2-circle me-1"></i> Bayar & Cetak Struk (F9)
                        </button>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-danger btn-sm w-50" onclick="voidTransaction()">
                                <i class="bi bi-shield-lock me-1"></i> Void / Batal (OTP)
                            </button>
                            <button class="btn btn-outline-secondary btn-sm w-50" onclick="closeCashier()">
                                <i class="bi bi-receipt me-1"></i> Tutup Kasir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    function addToCart(name, price, sku) {
        const tbody = document.getElementById('cartItems');
        const formattedPrice = new Intl.NumberFormat('id-ID').format(price);
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div class="fw-bold small">${name}</div>
                <div class="text-muted" style="font-size: 11px;">@ Rp ${formattedPrice}</div>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm qty-input" value="1" min="1" onchange="updateTotal()">
            </td>
            <td class="fw-semibold small">Rp ${formattedPrice}</td>
            <td><button class="btn btn-sm text-danger p-0" onclick="this.closest('tr').remove(); updateTotal();"><i class="bi bi-x-circle"></i></button></td>
        `;
        tbody.appendChild(row);
        updateTotal();
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Ditambahkan ke keranjang',
            showConfirmButton: false,
            timer: 1200
        });
    }

    function updateTotal() {
        const rows = document.querySelectorAll('#cartItems tr');
        let total = 0;
        rows.forEach(r => {
            const qtyInput = r.querySelector('.qty-input');
            const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
            // parse price
            const priceText = r.querySelector('.text-muted').innerText;
            const price = parseInt(priceText.replace(/[^0-9]/g, '')) || 0;
            const subtotal = qty * price;
            const subCell = r.querySelector('.fw-semibold');
            if (subCell) {
                subCell.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
            }
            total += subtotal;
        });

        const formatted = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        document.getElementById('subtotalVal').innerText = formatted;
        document.getElementById('totalVal').innerText = formatted;
    }

    function clearCart() {
        document.getElementById('cartItems').innerHTML = '';
        updateTotal();
    }

    function processPayment() {
        const isSplit = document.getElementById('paySplit').checked;
        const note = isSplit ? 'Metode Pembayaran: Split Payment (Cash + QRIS/Debit) telah berhasil diverifikasi.' : 'Pembayaran telah tercatat dan stok barang otomatis dikurangi.';
        Swal.fire({
            title: 'Transaksi Berhasil!',
            text: note,
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-printer me-1"></i> Cetak Struk',
            cancelButtonText: 'Transaksi Baru'
        }).then((result) => {
            clearCart();
        });
    }

    function voidTransaction() {
        Swal.fire({
            title: 'Otorisasi Void / Pembatalan',
            text: 'Masukkan PIN / OTP Supervisor untuk membatalkan transaksi (tidak menghapus histori):',
            input: 'password',
            inputPlaceholder: 'Masukkan 6 digit PIN Supervisor...',
            showCancelButton: true,
            confirmButtonText: 'Verifikasi Void',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                clearCart();
                Swal.fire('Void Berhasil', 'Transaksi telah dibatalkan dengan audit trail tercatat.', 'info');
            }
        });
    }

    function closeCashier() {
        Swal.fire({
            title: 'Tutup Kasir & Rekonsiliasi Kas',
            html: '<div class="text-start small mb-2">Total Penjualan Tunai Sistem: <b>Rp 4.520.000</b><br>Total QRIS / EDC: <b>Rp 3.830.000</b></div><input id="swalPhysicalCash" class="swal2-input" placeholder="Masukkan Uang Fisik Kasir (Rp)...">',
            showCancelButton: true,
            confirmButtonText: 'Konfirmasi Tutup Kasir',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Kasir Ditutup', 'Laporan rekonsiliasi kasir telah dicetak dan dikirim ke Accounting.', 'success');
            }
        });
    }

    // Filter search
    document.getElementById('posSearch').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.getAttribute('data-name');
            const sku = card.getAttribute('data-sku');
            if (name.includes(query) || sku.includes(query)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@stop
