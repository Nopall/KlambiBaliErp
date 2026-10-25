@extends('adminlte::page')

@section('title', 'Kartu Stok Produk & Varian - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-journal-arrow-down text-primary me-2"></i>Kartu Stok (Stock Card Ledger)
                <span class="text-secondary fw-normal fs-5">/ Histori Mutasi per Produk & Varian</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Gudang & Inventori</li>
                    <li class="breadcrumb-item active" aria-current="page">Kartu Stok</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Kartu Stok
            </button>
            <a href="{{ route('inventory.stock-overview') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-grid-3x3 me-1"></i> Matriks Multi-Gudang
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-speedometer2"></i>
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- FILTER BOX UTAMA --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="bi bi-funnel text-primary me-2"></i>Filter Kartu Stok per Produk, Varian & Gudang
            </h6>
        </div>
        <div class="card-body pt-0">
            <form method="GET" action="{{ route('inventory.stock-card') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    {{-- 1. Pilihan Produk --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold text-muted">Pilih Produk</label>
                        <select name="product_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}" {{ $selectedProductId == $prod->id ? 'selected' : '' }}>
                                    [{{ $prod->sku }}] {{ $prod->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Pilihan Varian --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold text-muted">Pilihan Varian Ukuran / Warna</label>
                        <select name="variant_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Varian Terpilih --</option>
                            @foreach($variants as $var)
                                <option value="{{ $var->id }}" {{ $selectedVariantId == $var->id ? 'selected' : '' }}>
                                    {{ $var->variant_name }} ({{ $var->sku }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. Pilihan 7 Gudang KlambiBali --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold text-muted">Lokasi Gudang</label>
                        <select name="warehouse_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">-- Semua 7 Gudang KlambiBali --</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ $selectedWarehouseId == $wh->id ? 'selected' : '' }}>
                                    {{ $wh->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 4. Rentang Tanggal --}}
                    <div class="col-12 col-md-2">
                        <label class="form-label small fw-semibold text-muted">Tanggal Dari</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm" onchange="this.form.submit()">
                    </div>

                    <div class="col-12 col-md-2">
                        <label class="form-label small fw-semibold text-muted">Tanggal Sampai</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm" onchange="this.form.submit()">
                    </div>

                    <div class="col-12 col-md-2">
                        <a href="{{ route('inventory.stock-card') }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- HEADER INFORMASI PRODUK & VARIAN AKTIF --}}
    @if($selectedProduct)
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-auto">
                        <div class="rounded-3 bg-primary bg-opacity-10 p-3 text-primary text-center" style="width: 70px; height: 70px;">
                            <i class="bi bi-box-seam fs-1"></i>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-secondary text-uppercase">{{ $selectedProduct->category?->name ?? 'Produk' }}</span>
                            <span class="badge bg-info text-dark">{{ $selectedProduct->type === 'finished_goods' ? 'Barang Jadi (FG)' : 'Bahan Baku' }}</span>
                            @if($selectedVariant)
                                <span class="badge bg-success">Varian: {{ $selectedVariant->variant_name }}</span>
                            @endif
                            @if($selectedWarehouse)
                                <span class="badge bg-warning text-dark"><i class="bi bi-geo-alt me-1"></i>{{ $selectedWarehouse->name }}</span>
                            @endif
                        </div>
                        <h4 class="fw-bold mb-1 text-dark">{{ $selectedProduct->name }}</h4>
                        <div class="small text-muted d-flex flex-wrap gap-3">
                            <span><strong class="text-dark">SKU Utama:</strong> <code>{{ $selectedProduct->sku }}</code></span>
                            @if($selectedVariant)
                                <span><strong class="text-dark">SKU Varian:</strong> <code>{{ $selectedVariant->sku }}</code></span>
                                <span><strong class="text-dark">Barcode:</strong> {{ $selectedVariant->barcode ?? '-' }}</span>
                            @endif
                            <span><strong class="text-dark">Satuan:</strong> {{ $selectedProduct->unit?->name ?? 'Pcs' }}</span>
                            <span><strong class="text-dark">Standar BOM:</strong> {{ $selectedProduct->std_kain_consumption ? $selectedProduct->std_kain_consumption.' Yard Kain' : 'N/A' }}</span>
                            <span><strong class="text-dark">HPP Standar:</strong> Rp {{ number_format($lastUnitCost, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- METRIC SUMMARY CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 text-success">
                        <i class="bi bi-box-arrow-in-down fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Total Mutasi Masuk</span>
                        <h4 class="fw-bold mb-0 text-success">+{{ number_format($totalIn, 2, ',', '.') }} <small class="fs-6 fw-normal text-muted">{{ $selectedProduct->unit?->symbol ?? 'pcs' }}</small></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3 text-danger">
                        <i class="bi bi-box-arrow-up fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Total Mutasi Keluar</span>
                        <h4 class="fw-bold mb-0 text-danger">-{{ number_format($totalOut, 2, ',', '.') }} <small class="fs-6 fw-normal text-muted">{{ $selectedProduct->unit?->symbol ?? 'pcs' }}</small></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 text-primary">
                        <i class="bi bi-boxes fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Saldo Akhir Berjalan</span>
                        <h4 class="fw-bold mb-0 text-primary">{{ number_format($runningBalance, 2, ',', '.') }} <small class="fs-6 fw-normal text-muted">{{ $selectedProduct->unit?->symbol ?? 'pcs' }}</small></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 text-warning">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Total Nilai Persediaan</span>
                        <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($runningBalance * $lastUnitCost, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEBARAN STOK FISIK DI 7 GUDANG KLAMBIBALI --}}
    @if(!$selectedWarehouseId)
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-building text-primary me-2"></i>Sebaran Saldo Fisik di 7 Gudang KlambiBali
                </h6>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Gudang</th>
                            <th>Nama Gudang</th>
                            <th>Tipe Gudang</th>
                            <th>Lokasi</th>
                            <th class="text-end">Stok Fisik</th>
                            <th class="text-end">HPP Satuan</th>
                            <th class="text-end">Total Nilai Aset</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockPerWarehouse as $spw)
                            <tr>
                                <td><span class="font-monospace fw-bold text-primary">{{ $spw['warehouse']->code }}</span></td>
                                <td class="fw-semibold">{{ $spw['warehouse']->name }}</td>
                                <td><span class="badge bg-secondary">{{ $spw['warehouse']->type }}</span></td>
                                <td class="small text-muted">{{ $spw['warehouse']->location }}</td>
                                <td class="text-end fw-bold {{ $spw['qty'] > 0 ? 'text-success' : 'text-muted' }}">
                                    {{ number_format($spw['qty'], 2, ',', '.') }} {{ $selectedProduct->unit?->symbol ?? 'pcs' }}
                                </td>
                                <td class="text-end small">Rp {{ number_format($lastUnitCost, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($spw['value'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- TABEL BUKU KARTU STOK (RUNNING BALANCE LEDGER) --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-journal-text text-primary me-2"></i>Rincian Mutasi Kronologis (Kartu Stok Berjalan)
                </h5>
                <span class="text-muted small">Mencatat riwayat mutasi masuk, keluar, sisa saldo, dan biaya HPP per transaksi</span>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                Total {{ $movements->count() }} Mutasi Tercatat
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>Tanggal & Waktu</th>
                        <th>No. Referensi / Dokumen</th>
                        <th>Varian / SKU</th>
                        <th>Gudang</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-end text-success">Masuk (+)</th>
                        <th class="text-end text-danger">Keluar (-)</th>
                        <th class="text-end fw-bold text-primary">Saldo Akhir</th>
                        <th class="text-end">HPP / Biaya</th>
                        <th class="text-end">Total Nilai</th>
                        <th>Keterangan / Alur Transaksi</th>
                        <th>Otorisator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $idx => $m)
                        <tr>
                            <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                            <td class="small text-nowrap">
                                <i class="bi bi-calendar3 me-1 text-muted"></i>{{ $m->movement_date ? $m->movement_date->format('d M Y H:i') : '-' }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace fw-bold">{{ $m->document_number }}</span>
                                <small class="text-muted d-block">{{ $m->reference_type }}</small>
                            </td>
                            <td class="small">
                                @if($m->variant)
                                    <span class="fw-semibold text-dark">{{ $m->variant->variant_name }}</span>
                                    <div class="text-muted font-monospace">{{ $m->variant->sku }}</div>
                                @else
                                    <span class="text-muted">{{ $m->product?->name }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                    {{ $m->warehouse?->name ?? 'Semua Gudang' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($m->type === 'IN')
                                    <span class="badge bg-success"><i class="bi bi-arrow-down-left me-1"></i>MASUK</span>
                                @elseif($m->type === 'OUT')
                                    <span class="badge bg-danger"><i class="bi bi-arrow-up-right me-1"></i>KELUAR</span>
                                @else
                                    <span class="badge bg-info text-dark">ADJUSTMENT</span>
                                @endif
                            </td>
                            <td class="text-end text-success fw-bold">
                                {{ (float)$m->qty_in > 0 ? '+'.number_format($m->qty_in, 2, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-danger fw-bold">
                                {{ (float)$m->qty_out > 0 ? '-'.number_format($m->qty_out, 2, ',', '.') : '-' }}
                            </td>
                            <td class="text-end fw-bold text-primary">
                                {{ number_format($m->calculated_balance, 2, ',', '.') }}
                            </td>
                            <td class="text-end small">
                                Rp {{ number_format($m->unit_cost, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-semibold small">
                                Rp {{ number_format($m->total_cost, 0, ',', '.') }}
                            </td>
                            <td class="small">
                                {{ $m->notes }}
                            </td>
                            <td class="small text-muted">
                                <i class="bi bi-person-check me-1"></i>{{ $m->user?->name ?? 'Admin Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                                Belum ada riwayat mutasi stok untuk produk dan kriteria filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="small text-muted">
                Audit Trail Kartu Stok Terproteksi - KlambiBali ERP Sistem Terintegrasi
            </span>
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Laporan Kartu Stok
            </button>
        </div>
    </div>
@stop
