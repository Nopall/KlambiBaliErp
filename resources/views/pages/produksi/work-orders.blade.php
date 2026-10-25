@extends('adminlte::page')

@section('title', 'Perintah Produksi (Work Orders) - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-gear-wide-connected text-primary me-2"></i>Perintah Produksi (Work Order)
                <span class="text-secondary fw-normal fs-5">/ Formula BOM & Alokasi Penjahit</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Produksi & HPP</li>
                    <li class="breadcrumb-item active" aria-current="page">Work Orders</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#createWoModal">
                <i class="bi bi-plus-circle me-1"></i> Buat Work Order Baru
            </button>
            <a href="{{ route('produksi.material-issues') }}" class="btn btn-outline-info btn-sm">
                <i class="bi bi-send-check me-1"></i> SPK Bahan Penjahit
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

    {{-- TABEL WORK ORDER --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-kanban text-primary me-2"></i>Daftar Perintah Produksi Aktif (WO)
            </h5>
            <span class="badge bg-light text-dark border px-3 py-2">
                Total: {{ $workOrders->count() }} Batch Produksi
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>No. Work Order</th>
                        <th>Model Kebaya / Produk</th>
                        <th>Vendor Penjahit</th>
                        <th class="text-end">Alokasi Bahan</th>
                        <th class="text-center">Standar BOM</th>
                        <th class="text-center">Target Hasil</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi Alur</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workOrders as $idx => $wo)
                        <tr>
                            <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                            <td>
                                <span class="fw-bold font-monospace text-primary">{{ $wo->wo_number }}</span>
                                <small class="text-muted d-block">{{ $wo->start_date ? \Carbon\Carbon::parse($wo->start_date)->format('d M Y') : '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $wo->product?->name }}</div>
                                <small class="text-muted font-monospace">{{ $wo->product?->sku }}</small>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark"><i class="bi bi-person-gear me-1"></i>{{ $wo->tailor?->name }}</span>
                                <small class="text-muted d-block">{{ $wo->tailor?->phone }}</small>
                            </td>
                            <td class="text-end fw-bold text-primary">
                                {{ number_format($wo->allocated_raw_material_qty, 2, ',', '.') }} Yard
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info">{{ $wo->std_kain_consumption }} Yd / pcs</span>
                            </td>
                            <td class="text-center fw-bold text-dark">
                                {{ number_format($wo->target_qty, 0, ',', '.') }} pcs
                            </td>
                            <td class="text-center">
                                @if($wo->status === 'completed')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>SELESAI</span>
                                @elseif($wo->status === 'in_progress')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-scissors me-1"></i>PROSES JAHIT</span>
                                @else
                                    <span class="badge bg-secondary">DRAFT</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($wo->status === 'draft')
                                    <a href="{{ route('produksi.material-issues') }}" class="btn btn-outline-primary btn-sm" title="Kirim Bahan Kain SPK">
                                        <i class="bi bi-send me-1"></i> Terbitkan SPK
                                    </a>
                                @elseif($wo->status === 'in_progress')
                                    <a href="{{ route('produksi.reconciliation') }}" class="btn btn-outline-success btn-sm" title="Input Hasil & Sisa Kain">
                                        <i class="bi bi-check2-circle me-1"></i> Rekonsiliasi Hasil
                                    </a>
                                @else
                                    <a href="{{ route('qc.inspeksi') }}" class="btn btn-outline-secondary btn-sm" title="Lihat Status QC">
                                        <i class="bi bi-patch-check me-1"></i> Status QC
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                Belum ada perintah kerja produksi. Klik tombol <strong>Buat Work Order Baru</strong>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL CREATE WORK ORDER --}}
    <div class="modal fade" id="createWoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('produksi.work-orders.store') }}">
                    @csrf
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold"><i class="bi bi-gear-wide-connected me-2"></i>Form Buat Perintah Produksi (Work Order)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">Model Kebaya / Produk Jadi:</label>
                                <select name="product_id" class="form-select" required>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} (SKU: {{ $p->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">Vendor Penjahit Rekanan:</label>
                                <select name="tailor_id" class="form-select" required>
                                    @foreach($tailors as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }} (Tarif: Rp {{ number_format($t->sewing_rate_default, 0, ',', '.') }}/pcs)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-dark">Alokasi Bahan Kain (Yard):</label>
                                <input type="number" step="0.01" name="allocated_raw_material_qty" class="form-control" value="100" placeholder="100" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-dark">Standar BOM Kain per Pcs:</label>
                                <input type="number" step="0.01" name="std_kain_consumption" class="form-control" value="1.56" placeholder="1.56" required>
                                <small class="text-muted">Default acuan PRD: 1,56 Yard/pcs</small>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-dark">Target Hasil Produksi (Pcs):</label>
                                <input type="number" name="target_qty" class="form-control" value="60" placeholder="60" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-muted">Catatan Instruksi Potong & Jahit:</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Instruksi pola, warna benang, payet dan aksesoris..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="bi bi-check-circle me-1"></i> Simpan Work Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
