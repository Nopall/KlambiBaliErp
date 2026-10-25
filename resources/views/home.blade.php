@extends('adminlte::page')

@section('title', 'Dashboard ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard KlambiBali ERP
            </h1>
            <p class="text-muted small mb-0">Sistem Terintegrasi Produksi, Multi-Gudang, POS, Marketplace & Akuntansi</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pos.index') }}" class="btn btn-success shadow-sm">
                <i class="bi bi-calculator me-1"></i> Buka Kasir POS
            </a>
            <a href="{{ url('produksi/work-orders') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Order Produksi (WO)
            </a>
            <a href="{{ url('purchasing/po') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-file-earmark-plus me-1"></i> Buat PO
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- DEMO ACCOUNT WALKTHROUGH ACCORDION / CARD (SESUAI HALAMAN 9 PDF) --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white" style="border-left: 5px solid #0d6efd !important;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
                <div>
                    <span class="badge bg-primary px-3 py-1 mb-2 text-uppercase fw-semibold shadow-sm">
                        <i class="bi bi-patch-check-fill me-1"></i> Demo Account Scenario (Halaman 9 PDF)
                    </span>
                    <h4 class="fw-bold mb-1 text-dark" style="color: #0f172a !important;">Simulasi 12 Langkah Alur Bisnis KlambiBali</h4>
                    <p class="text-secondary small mb-0" style="color: #475569 !important;">Semua dummy data di sistem ini telah disinkronkan secara presisi dengan alur produksi kebaya, 7 gudang, dan pembukuan akuntansi.</p>
                </div>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#demoStepsCollapse" aria-expanded="true">
                    <i class="bi bi-chevron-down me-1"></i> Sembunyikan / Tampilkan
                </button>
            </div>

            <div class="collapse show" id="demoStepsCollapse">
                <div class="row g-2 mt-2">
                    @foreach($demoSteps as $step)
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="p-3 rounded-3 h-100" style="background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;">
                                <div class="d-flex align-items-start gap-2">
                                    <span class="badge bg-primary rounded-circle p-2 fs-6 fw-bold text-white shadow-sm" style="width: 30px; height: 30px; min-width: 30px; display: inline-flex; align-items: center; justify-content: center;">{{ $step['step'] }}</span>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="fw-bold mb-0 small" style="color: #0f172a !important; font-size: 13px;">{{ $step['title'] }}</h6>
                                            <a href="{{ url($step['url']) }}" class="btn btn-xs btn-outline-primary py-0 px-2 text-decoration-none fw-semibold shadow-sm" style="font-size: 11px; white-space: nowrap;">
                                                Buka <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                        <p class="mb-0" style="font-size: 12px; line-height: 1.45; color: #475569 !important;">{{ $step['desc'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- METRIC CARDS ROW 1: CORE KPIS --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 text-primary">
                        <i class="bi bi-currency-dollar fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Total Penjualan</span>
                        <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($metrics['penjualan_bulan_ini'], 0, ',', '.') }}</h4>
                        <span class="badge bg-success-subtle text-success mt-1">
                            <i class="bi bi-arrow-up-right"></i> 4 Channel Aktif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3 text-info">
                        <i class="bi bi-cart-check fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Pembelian & PO</span>
                        <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($metrics['pembelian_bulan_ini'], 0, ',', '.') }}</h4>
                        <span class="badge bg-info-subtle text-info mt-1">
                            <i class="bi bi-truck"></i> 100 Yard Bahan Baku
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 text-success">
                        <i class="bi bi-boxes fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Total Stok Siap Jual</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($metrics['total_stok_jadi']) }} <small class="text-muted fs-6">Pcs</small></h4>
                        <span class="badge bg-success-subtle text-success mt-1">
                            <i class="bi bi-wallet2"></i> Nilai Rp {{ number_format($metrics['nilai_persediaan'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 text-warning">
                        <i class="bi bi-gear-wide-connected fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Produksi & WIP</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $metrics['produksi_aktif'] }} <small class="text-muted fs-6">WO Aktif</small></h4>
                        <span class="badge bg-warning-subtle text-warning mt-1">
                            <i class="bi bi-scissors"></i> Standar 1,56 Yard
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- METRIC CARDS ROW 2: OMNICHANNEL BREAKDOWN (SESUAI BAB 1 PDF) --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-header bg-white py-2 border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark m-0 small text-uppercase">
                <i class="bi bi-diagram-3 text-primary me-2"></i>Laporan Penjualan per Channel (Omnichannel)
            </h6>
            <span class="badge bg-light text-secondary border">Real-time Sync</span>
        </div>
        <div class="card-body pt-1 pb-3">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 bg-light border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-dark"><i class="bi bi-bag text-warning me-1"></i> Shopee</span>
                            <span class="badge bg-warning text-dark">{{ $channelSales['shopee']['share'] }}</span>
                        </div>
                        <h5 class="fw-bold text-dark mb-0">Rp {{ number_format($channelSales['shopee']['total'], 0, ',', '.') }}</h5>
                        <small class="text-muted">{{ $channelSales['shopee']['orders'] }} Transaksi</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 bg-light border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-dark"><i class="bi bi-tiktok text-dark me-1"></i> TikTok Shop</span>
                            <span class="badge bg-dark">{{ $channelSales['tiktok']['share'] }}</span>
                        </div>
                        <h5 class="fw-bold text-dark mb-0">Rp {{ number_format($channelSales['tiktok']['total'], 0, ',', '.') }}</h5>
                        <small class="text-muted">{{ $channelSales['tiktok']['orders'] }} Transaksi</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 bg-light border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-dark"><i class="bi bi-card-checklist text-primary me-1"></i> Reguler / Reseller</span>
                            <span class="badge bg-primary">{{ $channelSales['reguler']['share'] }}</span>
                        </div>
                        <h5 class="fw-bold text-dark mb-0">Rp {{ number_format($channelSales['reguler']['total'], 0, ',', '.') }}</h5>
                        <small class="text-muted">{{ $channelSales['reguler']['orders'] }} Transaksi (Resi 4 Digit)</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 bg-light border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-dark"><i class="bi bi-calculator text-success me-1"></i> Toko Fisik (POS)</span>
                            <span class="badge bg-success">{{ $channelSales['store_pos']['share'] }}</span>
                        </div>
                        <h5 class="fw-bold text-dark mb-0">Rp {{ number_format($channelSales['store_pos']['total'], 0, ',', '.') }}</h5>
                        <small class="text-muted">{{ $channelSales['store_pos']['orders'] }} Transaksi Kasir</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- METRIC CARDS ROW 3: QC TRANSIT, REJECT, SALE & HUTANG/PIUTANG --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Gudang Transit (QC)</div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $metrics['qc_pending'] }} Pcs</h5>
                        <small class="text-warning"><i class="bi bi-clock"></i> Antre Inspeksi</small>
                    </div>
                    <span class="badge bg-warning p-2"><i class="bi bi-hourglass-split fs-5"></i></span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Gudang Reject / Service</div>
                        <h5 class="fw-bold mb-0 text-danger">{{ $metrics['stok_reject_service'] }} Pcs</h5>
                        <small class="text-danger"><i class="bi bi-tools"></i> Rework Jahitan</small>
                    </div>
                    <span class="badge bg-danger p-2"><i class="bi bi-exclamation-octagon fs-5"></i></span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Gudang Sale (Barang Cacat)</div>
                        <h5 class="fw-bold mb-0 text-secondary">{{ $metrics['stok_sale_cacat'] }} Pcs</h5>
                        <small class="text-muted"><i class="bi bi-tag"></i> Clearance Cacat</small>
                    </div>
                    <span class="badge bg-secondary p-2"><i class="bi bi-trash3 fs-5"></i></span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Hutang Supplier (Kain)</div>
                        <h5 class="fw-bold mb-0 text-danger">Rp {{ number_format($metrics['utang_supplier'], 0, ',', '.') }}</h5>
                        <small class="text-danger"><i class="bi bi-calendar-event"></i> CV Tekstil Bali</small>
                    </div>
                    <span class="badge bg-danger-subtle text-danger p-2"><i class="bi bi-receipt fs-5"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS & WAREHOUSE ROW --}}
    <div class="row g-4 mb-4">
        {{-- Chart Section --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold m-0 text-dark">
                        <i class="bi bi-graph-up text-primary me-2"></i>Tren Penjualan vs Pembelian (Juta Rupiah)
                    </h5>
                    <span class="badge bg-light text-secondary border">Mei - Oktober 2026</span>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="salesPurchaseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Warehouse Capacity Section --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title fw-bold m-0 text-dark">
                        <i class="bi bi-building text-success me-2"></i>Kapasitas Multi-Gudang
                    </h5>
                </div>
                <div class="card-body pt-1">
                    @foreach($warehouseStock as $wh)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark small">{{ $wh['name'] }}</span>
                                <span class="badge bg-{{ $wh['color'] }}-subtle text-{{ $wh['color'] }} small">{{ $wh['items'] }} item</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $wh['color'] }}" role="progressbar" style="width: {{ $wh['capacity'] }};" aria-valuenow="{{ rtrim($wh['capacity'], '%') }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 11px;">
                                <span>Status: {{ $wh['status'] }}</span>
                                <span>Kapasitas: {{ $wh['capacity'] }}</span>
                            </div>
                        </div>
                    @endforeach
                    <div class="text-center mt-3 pt-2 border-top">
                        <a href="{{ url('inventory/stok') }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> Kelola Seluruh Inventori
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RECENT ORDERS & WORK ORDERS TABLES --}}
    <div class="row g-4 mb-4">
        {{-- Work Orders Table --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold m-0 text-dark">
                        <i class="bi bi-gear text-warning me-2"></i>Perintah Produksi (Work Order)
                    </h5>
                    <a href="{{ url('produksi/work-orders') }}" class="text-primary small text-decoration-none">Lihat Semua &rarr;</a>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. WO</th>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Target</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentWorkOrders as $wo)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $wo['code'] }}</td>
                                    <td>{{ $wo['product'] }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $wo['qty'] }} pcs</span></td>
                                    <td class="small text-muted">{{ $wo['target'] }}</td>
                                    <td><span class="badge bg-{{ $wo['badge'] }}">{{ $wo['status'] }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Omnichannel Transactions Table --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold m-0 text-dark">
                        <i class="bi bi-receipt text-success me-2"></i>Transaksi Penjualan Terkini
                    </h5>
                    <a href="{{ url('penjualan/transaksi') }}" class="text-primary small text-decoration-none">Lihat Semua &rarr;</a>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice</th>
                                <th>Channel</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $tx)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $tx['invoice'] }}</td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary small">{{ $tx['channel'] }}</span>
                                    </td>
                                    <td>{{ $tx['customer'] }}</td>
                                    <td class="fw-semibold">Rp {{ number_format($tx['total'], 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $tx['status'] == 'Lunas' ? 'success' : ($tx['status'] == 'Diproses' ? 'warning' : 'primary') }}">
                                            {{ $tx['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODUL ERP NAVIGATOR GRID --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="card-title fw-bold m-0 text-dark">
                <i class="bi bi-grid-3x3-gap text-primary me-2"></i>10 Modul Terintegrasi KlambiBali ERP
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @php
                    $modules = [
                        ['id' => 1, 'name' => 'Dashboard & Master Data', 'desc' => 'Produk, Varian, Bahan Baku, Supplier & Hak Akses', 'icon' => 'bi-folder2-open', 'url' => 'master/produk', 'badge' => 'Master'],
                        ['id' => 2, 'name' => 'Purchasing & Supplier', 'desc' => 'PO, Penerimaan Barang, Riwayat & Utang Supplier', 'icon' => 'bi-cart3', 'url' => 'purchasing/po', 'badge' => 'Purchasing'],
                        ['id' => 3, 'name' => 'Multi-Warehouse & Inventory', 'desc' => 'Stok Multi-Gudang, Transfer, Opname & Alert Min Stok', 'icon' => 'bi-boxes', 'url' => 'inventory/stok', 'badge' => 'Warehouse'],
                        ['id' => 4, 'name' => 'Production, BOM & HPP', 'desc' => 'Work Order, Resep BOM, Pemakaian Bahan & Kalkulasi HPP', 'icon' => 'bi-gear-wide-connected', 'url' => 'produksi/work-orders', 'badge' => 'Produksi'],
                        ['id' => 5, 'name' => 'Quality Control & Barcode', 'desc' => 'Inspeksi QC, Produk Reject & Cetak Barcode Produk', 'icon' => 'bi-upc-scan', 'url' => 'qc/inspeksi', 'badge' => 'QC & Barcode'],
                        ['id' => 6, 'name' => 'Penjualan & Retur', 'desc' => 'Pesanan Pelanggan, Invoice, Pembayaran & Retur', 'icon' => 'bi-bag-check', 'url' => 'penjualan/transaksi', 'badge' => 'Penjualan'],
                        ['id' => 7, 'name' => 'Integrasi Marketplace', 'desc' => 'Sinkronisasi Pesanan & Stok Shopee & TikTok Shop', 'icon' => 'bi-shop', 'url' => 'marketplace/pesanan', 'badge' => 'Marketplace'],
                        ['id' => 8, 'name' => 'Point of Sale (POS)', 'desc' => 'Kasir Toko Fisik, Barcode Scanner & Struk Transaksi', 'icon' => 'bi-display', 'url' => 'pos', 'badge' => 'POS Kasir'],
                        ['id' => 9, 'name' => 'Finance & Accounting', 'desc' => 'Arus Kas, Laba Rugi, Utang-Piutang & Biaya Operasional', 'icon' => 'bi-cash-coin', 'url' => 'finance/arus-kas', 'badge' => 'Finance'],
                        ['id' => 10, 'name' => 'Reports & Export', 'desc' => 'Laporan Lengkap Seluruh Modul & Ekspor Excel', 'icon' => 'bi-file-earmark-ruled', 'url' => 'laporan/transaksi', 'badge' => 'Laporan'],
                    ];
                @endphp

                @foreach($modules as $m)
                    <div class="col-12 col-md-6 col-xl-4">
                        <a href="{{ url($m['url']) }}" class="text-decoration-none">
                            <div class="card h-100 border border-light-subtle shadow-sm hover-shadow transition p-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 me-3 fs-4">
                                        <i class="bi {{ $m['icon'] }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="fw-bold mb-1 text-dark">{{ $m['id'] }}. {{ $m['name'] }}</h6>
                                        </div>
                                        <p class="text-muted small mb-0">{{ $m['desc'] }}</p>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted ms-2"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('salesPurchaseChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartTrend['labels']) !!},
                    datasets: [
                        {
                            label: 'Penjualan (Juta Rp)',
                            data: {!! json_encode($chartTrend['sales']) !!},
                            backgroundColor: 'rgba(13, 110, 253, 0.85)',
                            borderColor: 'rgba(13, 110, 253, 1)',
                            borderRadius: 6,
                        },
                        {
                            label: 'Pembelian Bahan (Juta Rp)',
                            data: {!! json_encode($chartTrend['purchases']) !!},
                            backgroundColor: 'rgba(13, 202, 240, 0.85)',
                            borderColor: 'rgba(13, 202, 240, 1)',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + context.parsed.y + '.000.000';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value + 'jt';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@stop

@section('css')
<style>
    .hover-shadow:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    }
    .transition {
        transition: all 0.2s ease-in-out;
    }
</style>
@stop
