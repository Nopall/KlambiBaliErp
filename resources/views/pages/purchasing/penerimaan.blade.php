@extends('adminlte::page')

@section('title', 'Penerimaan Barang (Goods Receipt / GRN) - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-box-arrow-in-down text-primary me-2"></i>Penerimaan Barang (Goods Receipt / GRN)
                <span class="text-secondary fw-normal fs-5">/ Verifikasi Fisik Datang & Penambahan Stok Gudang</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Purchasing</li>
                    <li class="breadcrumb-item active" aria-current="page">Penerimaan Barang</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#receiveModal">
                <i class="bi bi-check2-circle me-1"></i> Input Penerimaan Barang Masuk
            </button>
            <a href="{{ route('purchasing.po') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-cart3 me-1"></i> Purchase Orders
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

    {{-- TABEL ANTREAN PO MENUNGGU PENERIMAAN --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-primary bg-opacity-10 py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="bi bi-truck me-2"></i>Antrean PO Menunggu Pengiriman dari Supplier
            </h5>
            <span class="badge bg-light text-dark border">
                Mendukung Penerimaan Penuh Maupun Sebagian (Partial)
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>No. PO</th>
                        <th>Supplier</th>
                        <th>Bahan Dipesan</th>
                        <th class="text-end">Dipesan</th>
                        <th class="text-end text-success">Sudah Diterima</th>
                        <th class="text-end text-warning">Sisa Belum Kirim</th>
                        <th class="text-center">Aksi Terima</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPos as $idx => $p)
                        @php
                            $item = $p->items->first();
                            $sisa = $item ? ($item->qty_ordered - $item->qty_received) : 0;
                        @endphp
                        <tr>
                            <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                            <td><span class="fw-bold font-monospace text-primary">{{ $p->po_number }}</span></td>
                            <td class="fw-semibold text-dark">{{ $p->supplier?->name }}</td>
                            <td>{{ $item?->product?->name }}</td>
                            <td class="text-end fw-bold">{{ number_format($item?->qty_ordered ?? 0, 0, ',', '.') }} Yard</td>
                            <td class="text-end fw-bold text-success">{{ number_format($item?->qty_received ?? 0, 0, ',', '.') }} Yard</td>
                            <td class="text-end fw-bold text-warning">{{ number_format($sisa, 0, ',', '.') }} Yard</td>
                            <td class="text-center">
                                <button class="btn btn-success btn-sm" onclick="prepareReceiveModal({{ $p->id }}, '{{ $p->po_number }}', {{ $sisa }})">
                                    <i class="bi bi-box-arrow-in-down me-1"></i> Terima Barang
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Tidak ada PO yang menunggu pengiriman saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TABEL RIWAYAT PENERIMAAN GRN --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-journal-check text-primary me-2"></i>Histori Dokumen Penerimaan Barang (GRN)
            </h5>
            <span class="badge bg-light text-dark border">
                Tercatat di Kartu Stok & Menjurnal Hutang Dagang
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>No. GRN</th>
                        <th>No. PO Terkait</th>
                        <th>Supplier</th>
                        <th>Gudang Masuk</th>
                        <th>Tanggal Penerimaan</th>
                        <th>Status</th>
                        <th>Penerima</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $idx => $r)
                        <tr>
                            <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                            <td><span class="fw-bold font-monospace text-primary">{{ $r->grn_number }}</span></td>
                            <td><span class="font-monospace text-dark">{{ $r->purchaseOrder?->po_number }}</span></td>
                            <td class="fw-semibold">{{ $r->purchaseOrder?->supplier?->name }}</td>
                            <td><span class="badge bg-secondary">{{ $r->warehouse?->name }}</span></td>
                            <td class="small">{{ $r->received_date ? \Carbon\Carbon::parse($r->received_date)->format('d M Y') : '-' }}</td>
                            <td><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>DITERIMA LENGKAP</span></td>
                            <td class="small text-muted"><i class="bi bi-person me-1"></i>{{ $r->receiver?->name ?? 'Admin Gudang' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat penerimaan barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL INPUT PENERIMAAN --}}
    <div class="modal fade" id="receiveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('purchasing.penerimaan.store') }}">
                    @csrf
                    <div class="modal-header bg-success text-white py-3">
                        <h5 class="modal-title fw-bold"><i class="bi bi-box-arrow-in-down me-2"></i>Form Konfirmasi Penerimaan Barang Masuk</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Pilih Purchase Order (PO):</label>
                            <select name="purchase_order_id" id="modalPoSelect" class="form-select" required>
                                @foreach($pendingPos as $p)
                                    @php $it = $p->items->first(); @endphp
                                    <option value="{{ $p->id }}">
                                        [{{ $p->po_number }}] {{ $p->supplier?->name }} - {{ $it?->product?->name }} (Sisa: {{ ($it->qty_ordered - $it->qty_received) }} Yd)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Jumlah Kuantitas Yang Diterima Sekarang (Yard/Pcs):</label>
                            <input type="number" step="0.01" name="qty_received" id="modalQtyReceived" class="form-control form-control-lg fw-bold" value="100" required>
                            <small class="text-muted">Akan langsung menambah stok fisik di Gudang Bahan Baku & mencatat di Kartu Stok</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">Catatan Surat Jalan Kurir / Nomor Resi Pengiriman:</label>
                            <input type="text" name="notes" class="form-control" placeholder="Nomor Surat Jalan SJ-xxx atau nama supir...">
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm fw-bold"><i class="bi bi-check2-circle me-1"></i> Sahkan Penerimaan Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function prepareReceiveModal(poId, poNumber, sisaQty) {
        document.getElementById('modalPoSelect').value = poId;
        document.getElementById('modalQtyReceived').value = sisaQty;
        const modal = new bootstrap.Modal(document.getElementById('receiveModal'));
        modal.show();
    }
</script>
@stop
