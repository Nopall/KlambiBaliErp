@extends('adminlte::page')

@section('title', 'Pemeriksaan Kualitas (QC) & Pemisahan 3 Gudang - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-patch-check text-primary me-2"></i>Kendali Mutu (Quality Control)
                <span class="text-secondary fw-normal fs-5">/ Pemisahan 3 Gudang & Penerbitan Barcode</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">QC & Barcode</li>
                    <li class="breadcrumb-item active" aria-current="page">Pemeriksaan QC</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#qcModal">
                <i class="bi bi-check-all me-1"></i> Input Eksekusi Inspeksi QC
            </button>
            <a href="{{ route('inventory.stock-card') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-journal-arrow-down me-1"></i> Kartu Stok
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

    {{-- KOTAK ANTREAN GUDANG TRANSIT (QC) --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-warning bg-opacity-10 py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-hourglass-split text-warning me-2"></i>Antrean Barang di Gudang Transit (Menunggu Inspeksi QC)
                </h5>
                <span class="text-muted small">Barang di gudang ini belum dihitung sebagai stok siap jual sampai lolos verifikasi</span>
            </div>
            <span class="badge bg-warning text-dark border px-3 py-2">
                Isolasi Stok Transit Terproteksi
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>Kode SKU Produk</th>
                        <th>Model Kebaya / Produk</th>
                        <th>Kategori</th>
                        <th class="text-center">Jumlah Antrean</th>
                        <th class="text-end">Estimasi HPP Satuan</th>
                        <th class="text-end">Total Nilai Transit</th>
                        <th class="text-center">Aksi Inspeksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transitStocks as $idx => $ts)
                        <tr>
                            <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                            <td><span class="fw-bold font-monospace text-primary">{{ $ts->product?->sku }}</span></td>
                            <td class="fw-bold text-dark">{{ $ts->product?->name }}</td>
                            <td><span class="badge bg-secondary">{{ $ts->product?->category?->name ?? 'Kebaya Bali' }}</span></td>
                            <td class="text-center fw-bold fs-6 text-warning">
                                {{ number_format($ts->qty, 0, ',', '.') }} pcs
                            </td>
                            <td class="text-end small">Rp {{ number_format($ts->hpp, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($ts->qty * $ts->hpp, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm" onclick="prepareQcModal({{ $ts->product_id }}, '{{ $ts->product?->name }}', {{ $ts->qty }})">
                                    <i class="bi bi-check2-square me-1"></i> Eksekusi QC
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-check-circle fs-2 d-block mb-1 text-success"></i>
                                Tidak ada antrean barang di Gudang Transit. Seluruh produk telah diinspeksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TABEL HISTORI INSPEKSI QC --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-patch-check text-primary me-2"></i>Histori Keputusan QC & Alur Disposisi 3 Gudang
            </h5>
            <span class="badge bg-light text-dark border">
                Gudang Utama (FG) | Gudang Reject | Gudang Sale
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>No. Inspeksi</th>
                        <th>Model Kebaya</th>
                        <th class="text-center">Total Diperiksa</th>
                        <th class="text-center text-success fw-bold">Lolos (Gd. Utama)</th>
                        <th class="text-center text-warning fw-bold">Service (Gd. Reject)</th>
                        <th class="text-center text-danger fw-bold">Cacat (Gd. Sale)</th>
                        <th>Catatan / Alasan Reject</th>
                        <th>Petugas QC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inspections as $idx => $qc)
                        <tr>
                            <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                            <td>
                                <span class="fw-bold font-monospace text-primary">{{ $qc->inspection_number }}</span>
                                <small class="text-muted d-block">{{ $qc->created_at ? $qc->created_at->format('d M Y H:i') : '-' }}</small>
                            </td>
                            <td class="fw-semibold text-dark">{{ $qc->product?->name }}</td>
                            <td class="text-center fw-bold">{{ number_format($qc->qty_inspected, 0, ',', '.') }} pcs</td>
                            <td class="text-center text-success fw-bold">
                                {{ number_format($qc->qty_passed, 0, ',', '.') }} pcs
                                <span class="badge bg-success-subtle text-success d-block small">Terbit Barcode</span>
                            </td>
                            <td class="text-center text-warning fw-bold">
                                {{ number_format($qc->qty_reject_service, 0, ',', '.') }} pcs
                                <span class="badge bg-warning-subtle text-warning d-block small">Rework</span>
                            </td>
                            <td class="text-center text-danger fw-bold">
                                {{ number_format($qc->qty_defect_sale, 0, ',', '.') }} pcs
                                <span class="badge bg-danger-subtle text-danger d-block small">Clearance</span>
                            </td>
                            <td class="small text-muted">{{ $qc->reject_reason ?? $qc->notes }}</td>
                            <td class="small text-muted"><i class="bi bi-person-badge me-1"></i>{{ $qc->inspector?->name ?? 'Tim QC' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada data riwayat inspeksi QC.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL EKSEKUSI QC --}}
    <div class="modal fade" id="qcModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('qc.inspeksi.store') }}">
                    @csrf
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold"><i class="bi bi-patch-check me-2"></i>Form Keputusan Inspeksi QC & Pemisahan 3 Gudang</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Pilih Produk Dari Antrean Gudang Transit:</label>
                                <select name="product_id" id="modalProductId" class="form-select" required>
                                    @foreach($transitStocks as $ts)
                                        <option value="{{ $ts->product_id }}">
                                            {{ $ts->product?->name }} (Antrean: {{ $ts->qty }} pcs)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 3 JALUR PEMISAHAN GUDANG --}}
                            <div class="col-12 col-md-4">
                                <div class="p-3 bg-success-subtle rounded-3 border border-success">
                                    <label class="form-label fw-bold text-success mb-1">
                                        <i class="bi bi-check-circle me-1"></i> 1. Lolos QC (Gudang Utama FG):
                                    </label>
                                    <input type="number" name="qty_passed" id="qcPassed" class="form-control form-control-lg fw-bold text-success" value="55" min="0" required onkeyup="validateQcTotal()">
                                    <small class="text-muted d-block mt-1">Diterbitkan Barcode SKU & Siap Jual</small>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="p-3 bg-warning-subtle rounded-3 border border-warning">
                                    <label class="form-label fw-bold text-warning mb-1">
                                        <i class="bi bi-tools me-1"></i> 2. Gudang Reject / Service:
                                    </label>
                                    <input type="number" name="qty_reject" id="qcReject" class="form-control form-control-lg fw-bold text-warning" value="3" min="0" required onkeyup="validateQcTotal()">
                                    <small class="text-muted d-block mt-1">Perlu perbaikan jahitan / payet</small>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="p-3 bg-danger-subtle rounded-3 border border-danger">
                                    <label class="form-label fw-bold text-danger mb-1">
                                        <i class="bi bi-x-octagon me-1"></i> 3. Gudang Sale (Barang Cacat):
                                    </label>
                                    <input type="number" name="qty_defect_sale" id="qcSale" class="form-control form-control-lg fw-bold text-danger" value="2" min="0" required onkeyup="validateQcTotal()">
                                    <small class="text-muted d-block mt-1">Cacat motif / jual clearance</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Alasan Reject / Keterangan Kerusakan:</label>
                                <input type="text" name="reject_reason" class="form-control" value="3 pcs jahitan kendor samping, 2 pcs cacat kain bordir" placeholder="Contoh: Jahitan kendor, benang lepas, noda kain...">
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info py-2 small mb-0">
                                    <i class="bi bi-info-circle me-1"></i> Total dialokasikan: <strong id="totalQcAllocated">60 pcs</strong>. Sistem akan memotong Gudang Transit dan menjurnal penambahan barang jadi di 3 gudang terkait.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="bi bi-check2-circle me-1"></i> Sahkan Keputusan QC & Terbitkan Barcode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function prepareQcModal(prodId, prodName, qty) {
        document.getElementById('modalProductId').value = prodId;
        const modal = new bootstrap.Modal(document.getElementById('qcModal'));
        modal.show();
    }

    function validateQcTotal() {
        const passed = parseFloat(document.getElementById('qcPassed').value || 0);
        const reject = parseFloat(document.getElementById('qcReject').value || 0);
        const sale = parseFloat(document.getElementById('qcSale').value || 0);
        const total = passed + reject + sale;
        document.getElementById('totalQcAllocated').innerText = total + ' pcs';
    }
</script>
@stop
