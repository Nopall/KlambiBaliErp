@extends('adminlte::page')

@section('title', 'Pemakaian & Rekonsiliasi Sisa Kain (1,56 Yd) - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-scissors text-primary me-2"></i>Pemakaian & Rekonsiliasi Sisa Kain
                <span class="text-secondary fw-normal fs-5">/ Formula 1,56 Yard, Sisa Teoritis vs Aktual & Waste</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Produksi & HPP</li>
                    <li class="breadcrumb-item active" aria-current="page">Rekonsiliasi Bahan</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#reconcileModal">
                <i class="bi bi-check2-circle me-1"></i> Input Hasil Jahit & Rekonsiliasi Sisa
            </button>
            <a href="{{ route('qc.inspeksi') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-patch-check me-1"></i> Inspeksi QC Transit
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

    {{-- KOTAK ACUAN FORMULA DOKUMEN PRD BAB 4.2 --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-header bg-primary bg-opacity-10 py-3 border-0">
            <h6 class="mb-0 fw-bold text-primary">
                <i class="bi bi-calculator me-2"></i>Tabel Acuan Formula Produksi Kebaya Bali (Mandat PRD Bab 4.2)
            </h6>
        </div>
        <div class="card-body p-4 pt-3">
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Parameter Simulasi Produksi</th>
                            <th>Formula / Logika Perhitungan Sistem</th>
                            <th class="text-end">Nilai Acuan Demo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1. Pembelian / Alokasi Kain ke Penjahit</td>
                            <td>Input Surat Pengeluaran Bahan (SPK) ke Gudang Penjahit</td>
                            <td class="text-end fw-bold text-dark">100,00 Yard</td>
                        </tr>
                        <tr>
                            <td>2. Standar Kain per Kebaya (BOM)</td>
                            <td>Parameter BOM model kebaya terkait (dinamis per model)</td>
                            <td class="text-end fw-bold text-primary">1,56 Yard / pcs</td>
                        </tr>
                        <tr>
                            <td>3. Kapasitas Produksi Teoritis</td>
                            <td>Alokasi Kain ÷ Standar BOM (100 Yard ÷ 1,56 Yard)</td>
                            <td class="text-end fw-semibold">64,10 Kebaya</td>
                        </tr>
                        <tr>
                            <td>4. Hasil Produksi Aktual Selesai</td>
                            <td>Input fisik kebaya selesai jahit masuk ke Gudang Transit (QC)</td>
                            <td class="text-end fw-bold text-success">60 Kebaya</td>
                        </tr>
                        <tr>
                            <td>5. Pemakaian Kain Standar (60 pcs)</td>
                            <td>60 kebaya × 1,56 yard standar BOM</td>
                            <td class="text-end fw-semibold text-danger">93,60 Yard</td>
                        </tr>
                        <tr class="table-warning bg-opacity-25">
                            <td class="fw-bold">6. Sisa Kain Teoritis di Penjahit</td>
                            <td class="fw-bold">100 Yard − 93,60 Yard (Berdasarkan Standar)</td>
                            <td class="text-end fw-bold text-dark">6,40 Yard</td>
                        </tr>
                        <tr class="table-info bg-opacity-25">
                            <td class="fw-bold">7. Sisa Fisik Aktual & Waste Pola Potong</td>
                            <td class="fw-bold">Sisa Fisik diukur (5,80 Yd) & Selisih Waste (0,60 Yd)</td>
                            <td class="text-end fw-bold text-primary">Sisa 5,80 Yd | Waste 0,60 Yd</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- TABEL RIWAYAT REKONSILIASI --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Rekonsiliasi Pemakaian Bahan & Pengiriman ke Transit
            </h5>
            <span class="badge bg-light text-dark border px-3 py-2">
                Otomatis Menghitung Waste & Masuk Gudang Transit QC
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>Batch Rekon</th>
                        <th>No. Work Order</th>
                        <th>Model Kebaya</th>
                        <th class="text-center">Hasil Jahit</th>
                        <th class="text-end">Kain Standar</th>
                        <th class="text-end">Sisa Teoritis</th>
                        <th class="text-end text-success">Sisa Aktual Fisik</th>
                        <th class="text-end text-danger">Waste / Potongan</th>
                        <th>Tindakan Sisa Kain</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $idx => $h)
                        <tr>
                            <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                            <td><span class="fw-bold font-monospace text-primary">{{ $h->batch_number }}</span></td>
                            <td><span class="font-monospace text-dark">{{ $h->workOrder?->wo_number }}</span></td>
                            <td class="fw-semibold">{{ $h->workOrder?->product?->name }}</td>
                            <td class="text-center fw-bold text-success">{{ number_format($h->finished_qty, 0, ',', '.') }} pcs</td>
                            <td class="text-end">{{ number_format($h->std_fabric_used, 2, ',', '.') }} Yd</td>
                            <td class="text-end">{{ number_format($h->theoretical_leftover, 2, ',', '.') }} Yd</td>
                            <td class="text-end fw-bold text-success">{{ number_format($h->actual_leftover, 2, ',', '.') }} Yd</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($h->waste_fabric, 2, ',', '.') }} Yd</td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                    {{ $h->leftover_action === 'keep_at_tailor' ? 'Tersimpan di Gudang Penjahit' : 'Diretur ke Bahan Baku' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                Belum ada riwayat rekonsiliasi bahan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL REKONSILIASI --}}
    <div class="modal fade" id="reconcileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('produksi.reconciliation.store') }}">
                    @csrf
                    <div class="modal-header bg-success text-white py-3">
                        <h5 class="modal-title fw-bold"><i class="bi bi-scissors me-2"></i>Input Hasil Jahit & Rekonsiliasi Sisa Kain</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Pilih Work Order Aktif (Dalam Proses Jahit):</label>
                                <select name="work_order_id" class="form-select" id="woSelect" required onchange="updateCalc()">
                                    @foreach($workOrders as $w)
                                        <option value="{{ $w->id }}" data-kain="{{ $w->allocated_raw_material_qty }}" data-bom="{{ $w->std_kain_consumption }}" data-target="{{ $w->target_qty }}">
                                            [{{ $w->wo_number }}] {{ $w->product?->name }} (Penjahit: {{ $w->tailor?->name }} | Alokasi: {{ $w->allocated_raw_material_qty }} Yd | BOM: {{ $w->std_kain_consumption }} Yd)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-dark">Hasil Kebaya Selesai Jahit (Pcs):</label>
                                <input type="number" name="finished_qty" id="finishedQty" class="form-control form-control-lg fw-bold" value="60" min="1" required onkeyup="updateCalc()">
                                <small class="text-muted">Akan masuk ke Gudang Transit (QC)</small>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-dark">Sisa Fisik Kain di Penjahit (Yard):</label>
                                <input type="number" step="0.01" name="actual_leftover_fabric" id="actualLeftover" class="form-control form-control-lg fw-bold text-success" value="5.80" min="0" required onkeyup="updateCalc()">
                                <small class="text-muted">Diukur langsung dari fisik sisa kain</small>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-dark">Tindakan Sisa Kain:</label>
                                <select name="leftover_action" class="form-select form-select-lg">
                                    <option value="keep_at_tailor">Tetap di Gudang Penjahit (Next PO)</option>
                                    <option value="return_to_bb">Diretur ke Gudang Bahan Baku</option>
                                </select>
                            </div>

                            {{-- SIMULASI KALKULATOR LIVE --}}
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3 border">
                                    <h6 class="fw-bold mb-2 small text-dark"><i class="bi bi-info-circle text-primary me-1"></i>Ringkasan Perhitungan Otomatis:</h6>
                                    <div class="row g-2 small">
                                        <div class="col-6 col-md-3">
                                            <span class="text-muted d-block">Pemakaian Standar:</span>
                                            <strong id="calcStdUsed" class="text-primary">93,60 Yard</strong>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <span class="text-muted d-block">Sisa Kain Teoritis:</span>
                                            <strong id="calcTheoLeftover" class="text-dark">6,40 Yard</strong>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <span class="text-muted d-block">Sisa Fisik Diinput:</span>
                                            <strong id="calcActualLeftover" class="text-success">5,80 Yard</strong>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <span class="text-muted d-block">Waste / Potongan:</span>
                                            <strong id="calcWaste" class="text-danger">0,60 Yard</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm fw-bold"><i class="bi bi-check-circle me-1"></i> Sahkan & Kirim ke Gudang Transit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function updateCalc() {
        const select = document.getElementById('woSelect');
        const opt = select.options[select.selectedIndex];
        if (!opt) return;

        const totalKain = parseFloat(opt.getAttribute('data-kain') || 100);
        const bom = parseFloat(opt.getAttribute('data-bom') || 1.56);
        const finished = parseFloat(document.getElementById('finishedQty').value || 0);
        const actual = parseFloat(document.getElementById('actualLeftover').value || 0);

        const stdUsed = finished * bom;
        const theoLeftover = Math.max(0, totalKain - stdUsed);
        const waste = Math.max(0, theoLeftover - actual);

        document.getElementById('calcStdUsed').innerText = stdUsed.toFixed(2) + ' Yard';
        document.getElementById('calcTheoLeftover').innerText = theoLeftover.toFixed(2) + ' Yard';
        document.getElementById('calcActualLeftover').innerText = actual.toFixed(2) + ' Yard';
        document.getElementById('calcWaste').innerText = waste.toFixed(2) + ' Yard';
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateCalc();
    });
</script>
@stop
