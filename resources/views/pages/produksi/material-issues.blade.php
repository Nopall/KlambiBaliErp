@extends('adminlte::page')

@section('title', 'Surat Pengeluaran Bahan (SPK) - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-send-check text-primary me-2"></i>Surat Pengeluaran Bahan (SPK)
                <span class="text-secondary fw-normal fs-5">/ Alokasi Kain ke Gudang Penjahit (WIP)</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Produksi & HPP</li>
                    <li class="breadcrumb-item active" aria-current="page">SPK Pengeluaran Bahan</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#createSpkModal">
                <i class="bi bi-plus-circle me-1"></i> Terbitkan SPK Baru
            </button>
            <a href="{{ route('produksi.work-orders') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-kanban me-1"></i> Work Orders
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

    {{-- TABEL DAFTAR SPK --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>Riwayat Surat Pengeluaran Bahan (SPK)
            </h5>
            <span class="badge bg-light text-dark border px-3 py-2">
                Otomatis Memotong Gudang Bahan Baku & Menjurnal WIP
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>No. SPK</th>
                        <th>No. Work Order</th>
                        <th>Penjahit Penerima</th>
                        <th>Bahan Kain Dialokasikan</th>
                        <th>Gudang Asal &rarr; Tujuan</th>
                        <th class="text-end">Jumlah Dikirim</th>
                        <th class="text-center">Status</th>
                        <th>Otorisator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($issues as $idx => $spk)
                        <tr>
                            <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                            <td>
                                <span class="fw-bold font-monospace text-primary">{{ $spk->issue_number }}</span>
                                <small class="text-muted d-block">{{ $spk->issue_date ? \Carbon\Carbon::parse($spk->issue_date)->format('d M Y') : '-' }}</small>
                            </td>
                            <td>
                                <span class="font-monospace fw-semibold">{{ $spk->workOrder?->wo_number }}</span>
                                <small class="text-muted d-block">{{ $spk->workOrder?->product?->name }}</small>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $spk->tailor?->name }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $spk->rawMaterial?->name }}</div>
                                <small class="text-muted font-monospace">{{ $spk->rawMaterial?->sku }}</small>
                            </td>
                            <td class="small">
                                <span class="badge bg-light text-dark border">{{ $spk->sourceWarehouse?->name }}</span>
                                <i class="bi bi-arrow-right mx-1 text-primary"></i>
                                <span class="badge bg-warning bg-opacity-25 text-dark border">{{ $spk->destinationWarehouse?->name }}</span>
                            </td>
                            <td class="text-end fw-bold text-primary">
                                {{ number_format($spk->qty_issued, 2, ',', '.') }} Yard
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>DITERIMA PENJAHIT</span>
                            </td>
                            <td class="small text-muted">
                                <i class="bi bi-person-check me-1"></i>{{ $spk->user?->name ?? 'Admin Produksi' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                Belum ada dokumen pengeluaran bahan ke penjahit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL CREATE SPK --}}
    <div class="modal fade" id="createSpkModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('produksi.material-issues.store') }}">
                    @csrf
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold"><i class="bi bi-send-check me-2"></i>Terbitkan Surat Pengeluaran Bahan (SPK)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">Pilih Work Order (WO) Terkait:</label>
                                <select name="work_order_id" class="form-select" required>
                                    @if($workOrders->isEmpty())
                                        <option value="" disabled selected>-- Belum ada Work Order (WO) yang tersedia --</option>
                                    @else
                                        <option value="" disabled selected>-- Pilih Work Order (WO) Terkait --</option>
                                        @foreach($workOrders as $w)
                                            <option value="{{ $w->id }}">
                                                [{{ $w->wo_number }}] {{ $w->product?->name }} - Penjahit: {{ $w->tailor?->name }} (Target: {{ $w->target_qty }} pcs | Status: {{ strtoupper(str_replace('_', ' ', $w->status)) }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @if($workOrders->isEmpty())
                                    <small class="text-danger mt-1 d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i> Belum ada WO aktif. Silakan buat di menu <a href="{{ route('produksi.work-orders') }}" class="fw-bold">Perintah Kerja (WO)</a>.
                                    </small>
                                @endif
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">Pilih Bahan Baku Kain Yang Dikeluarkan:</label>
                                <select name="raw_material_id" class="form-select" required>
                                    @foreach($rawMaterials as $rm)
                                        <option value="{{ $rm->id }}">
                                            [{{ $rm->sku }}] {{ $rm->name }} (Harga: Rp {{ number_format($rm->purchase_price, 0, ',', '.') }}/yd)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">Jumlah Yard Kain Yang Dikeluarkan:</label>
                                <input type="number" step="0.01" name="qty_issued" class="form-control form-control-lg fw-bold" value="100" placeholder="100" required>
                                <small class="text-muted">Akan dipotong dari Gudang Bahan Baku dan dipindah ke Gudang Penjahit WIP</small>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted">Lokasi Gudang Penjahit Tujuan:</label>
                                <input type="text" class="form-control" value="Gudang Penjahit (Raw Material & WIP)" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-muted">Catatan Serah Terima Bahan:</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Nomor roll kain, kondisi fisik kain, dll..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="bi bi-send me-1"></i> Sahkan & Kirim Bahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
