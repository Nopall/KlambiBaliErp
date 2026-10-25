@extends('adminlte::page')

@section('title', 'Purchase Order (PO) - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-cart3 text-primary me-2"></i>Purchase Order (PO) Supplier
                <span class="text-secondary fw-normal fs-5">/ Pengadaan Bahan Baku (Yard/Meter) & Barang Jadi</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Purchasing</li>
                    <li class="breadcrumb-item active" aria-current="page">Purchase Order</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#createPoModal">
                <i class="bi bi-plus-circle me-1"></i> Terbitkan PO Baru
            </button>
            <a href="{{ route('purchasing.penerimaan') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-box-arrow-in-down me-1"></i> Penerimaan Barang (GRN)
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-speedometer2"></i>
            </a>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABEL PURCHASE ORDER --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>Daftar Purchase Order (PO) Aktif
            </h5>
            <span class="badge bg-light text-dark border px-3 py-2">
                Total: {{ $orders->count() }} Dokumen PO
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>No. PO</th>
                        <th>Nama Supplier</th>
                        <th>Bahan / Produk</th>
                        <th class="text-end">Jumlah Pesanan</th>
                        <th class="text-end">Jumlah Diterima</th>
                        <th class="text-end">Total Nilai PO (Rp)</th>
                        <th class="text-center">Status Barang</th>
                        <th class="text-center">Status Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $idx => $po)
                        <tr>
                            <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                            <td>
                                <span class="fw-bold font-monospace text-primary">{{ $po->po_number }}</span>
                                <small class="text-muted d-block">{{ $po->po_date ? \Carbon\Carbon::parse($po->po_date)->format('d M Y') : '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $po->supplier?->name }}</div>
                                <small class="text-muted">{{ $po->supplier?->contact_person }}</small>
                            </td>
                            <td>
                                @foreach($po->items as $item)
                                    <div>&bull; {{ $item->product?->name }}</div>
                                @endforeach
                            </td>
                            <td class="text-end fw-bold">
                                {{ number_format($po->items->sum('qty_ordered'), 0, ',', '.') }} Yard/pcs
                            </td>
                            <td class="text-end fw-bold text-success">
                                {{ number_format($po->items->sum('qty_received'), 0, ',', '.') }} Yard/pcs
                            </td>
                            <td class="text-end fw-bold text-dark">
                                Rp {{ number_format($po->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if($po->status === 'received')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>LENGKAP</span>
                                @elseif($po->status === 'partially_received')
                                    <span class="badge bg-warning text-dark">PARSIAL</span>
                                @else
                                    <span class="badge bg-secondary">MENUNGGU</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($po->payment_status === 'paid')
                                    <span class="badge bg-success">LUNAS</span>
                                @else
                                    <span class="badge bg-danger">BELUM LUNAS</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">Belum ada Purchase Order.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL CREATE PO --}}
    <div class="modal fade" id="createPoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('purchasing.po.store') }}">
                    @csrf
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold"><i class="bi bi-cart-plus me-2"></i>Terbitkan Purchase Order (PO) Baru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">Pilih Supplier Rekanan:</label>
                                <select name="supplier_id" class="form-select" required>
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }} (Tempo: {{ $s->term_days }} Hari)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">Bahan Baku / Kain Yang Dipesan:</label>
                                <select name="product_id" class="form-select" required>
                                    @foreach($rawMaterials as $rm)
                                        <option value="{{ $rm->id }}">
                                            [{{ $rm->sku }}] {{ $rm->name }} (Estimasi: Rp {{ number_format($rm->purchase_price, 0, ',', '.') }}/yd)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-dark">Jumlah Kuantitas (Yard/Pcs):</label>
                                <input type="number" step="0.01" name="qty" class="form-control form-control-lg fw-bold" value="100" placeholder="100" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-dark">Harga Beli Satuan (Rp):</label>
                                <input type="number" name="unit_price" class="form-control form-control-lg fw-bold" value="75000" placeholder="75000" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small text-muted">Gudang Penerimaan:</label>
                                <input type="text" class="form-control" value="Gudang Bahan Baku (GD-BB-01)" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-muted">Catatan Pemesanan:</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Catatan spesifikasi lebar kain, warna bordir, atau syarat tempo..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="bi bi-check-circle me-1"></i> Terbitkan Purchase Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
