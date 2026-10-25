@extends('adminlte::page')

@section('title', 'Stok Multi-Gudang per SKU - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-collection text-primary me-2"></i>Stok Multi-Gudang per SKU & Varian
                <span class="text-secondary fw-normal fs-5">/ Monitoring 7 Gudang KlambiBali</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Gudang & Inventori</li>
                    <li class="breadcrumb-item active" aria-current="page">Stok Multi-Gudang</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.stock-card') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-journal-arrow-down me-1"></i> Buka Kartu Stok
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-speedometer2"></i>
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- SUMMARY METRICS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 text-primary">
                        <i class="bi bi-boxes fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Total Produk Terdaftar</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $products->count() }} Model / Bahan</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 text-success">
                        <i class="bi bi-building fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Jumlah Lokasi Gudang</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $warehouses->count() }} Gudang Aktif</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 text-warning">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Estimasi Total Nilai Aset Stok</span>
                        <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL MATRIKS GUDANG --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-grid-3x3 text-primary me-2"></i>Matriks Sebaran Stok Fisik 7 Gudang Resmi KlambiBali
            </h5>
            <span class="badge bg-light text-dark border px-3 py-2">
                Terintegrasi Riil dengan Database
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>Kode SKU</th>
                        <th>Nama Produk / Varian</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>HPP</th>
                        <th class="text-center bg-primary bg-opacity-10 text-primary">Total Stok</th>
                        @foreach($warehouses as $wh)
                            <th class="text-center small">{{ $wh->name }}</th>
                        @endforeach
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $idx => $prod)
                        <tr class="table-group-divider">
                            <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                            <td><span class="fw-bold text-primary font-monospace">{{ $prod->sku }}</span></td>
                            <td class="fw-bold">{{ $prod->name }}</td>
                            <td><span class="badge bg-secondary">{{ $prod->category?->name ?? '-' }}</span></td>
                            <td>{{ $prod->unit?->symbol ?? 'pcs' }}</td>
                            <td class="small">Rp {{ number_format($prod->hpp, 0, ',', '.') }}</td>
                            <td class="text-center fw-bold bg-primary bg-opacity-10 text-primary">
                                {{ number_format($prod->stocks->sum('qty'), 2, ',', '.') }}
                            </td>
                            @foreach($warehouses as $wh)
                                @php
                                    $whQty = $prod->stocks->where('warehouse_id', $wh->id)->sum('qty');
                                @endphp
                                <td class="text-center {{ $whQty > 0 ? 'fw-bold text-success' : 'text-muted small' }}">
                                    {{ $whQty > 0 ? number_format($whQty, 2, ',', '.') : '-' }}
                                </td>
                            @endforeach
                            <td class="text-center">
                                <a href="{{ route('inventory.stock-card', ['product_id' => $prod->id]) }}" class="btn btn-outline-primary btn-sm" title="Buka Kartu Stok">
                                    <i class="bi bi-journal-arrow-down me-1"></i> Kartu Stok
                                </a>
                            </td>
                        </tr>

                        {{-- Child Variants jika ada --}}
                        @foreach($prod->variants as $var)
                            <tr class="table-light">
                                <td></td>
                                <td><span class="text-muted font-monospace ps-3">&bull; {{ $var->sku }}</span></td>
                                <td class="ps-3 text-secondary">{{ $var->variant_name }}</td>
                                <td><span class="badge bg-light text-muted border">Varian</span></td>
                                <td>{{ $prod->unit?->symbol ?? 'pcs' }}</td>
                                <td class="small text-muted">Rp {{ number_format($prod->hpp, 0, ',', '.') }}</td>
                                <td class="text-center fw-bold text-dark">
                                    {{ number_format($var->stocks->sum('qty'), 2, ',', '.') }}
                                </td>
                                @foreach($warehouses as $wh)
                                    @php
                                        $whVarQty = $var->stocks->where('warehouse_id', $wh->id)->sum('qty');
                                    @endphp
                                    <td class="text-center {{ $whVarQty > 0 ? 'fw-bold text-primary' : 'text-muted small' }}">
                                        {{ $whVarQty > 0 ? number_format($whVarQty, 2, ',', '.') : '-' }}
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    <a href="{{ route('inventory.stock-card', ['product_id' => $prod->id, 'variant_id' => $var->id]) }}" class="btn btn-sm btn-link text-primary p-0" title="Buka Kartu Stok Varian">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
