@extends('adminlte::page')

@section('title', 'Manajemen Return Paket (Reverse Logistics) - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-box-arrow-in-left text-primary me-2"></i>Manajemen Return Paket (Reverse Logistics)
                <span class="text-secondary fw-normal fs-5">/ Input 4 Digit Resi, Karantina & 3 Jalur Disposisi</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Return Paket</li>
                    <li class="breadcrumb-item active" aria-current="page">Input Return</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#createReturnModal">
                <i class="bi bi-plus-circle me-1"></i> Input Paket Return Baru
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-octagon me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- KOTAK ATURAN MANDAT PRD BAB 14 --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-8">
                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-shield-check text-success me-2"></i>Sistem Proteksi Return Paket KlambiBali:</h6>
                    <ul class="small text-muted mb-0 ps-3">
                        <li><strong>Pencarian Cepat 4 Digit Resi:</strong> Mempermudah input saat kurir mengantarkan tumpukan paket retur.</li>
                        <li><strong>Duplicate Return Lock:</strong> Mencegah satu resi/pesanan diretur dua kali atau dicatat sebagai penjualan baru.</li>
                        <li><strong>3 Jalur Disposisi Fisik:</strong> Kondisi Baik (Gudang Siap Jual), Rusak Bisa Diperbaiki (Gudang Reject), Rusak Parah (Gudang Sale Cacat).</li>
                    </ul>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <button class="btn btn-outline-primary btn-sm" onclick="focusSearchResi()">
                        <i class="bi bi-search me-1"></i> Cari 4 Digit Resi Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL DATA RETURN --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pemrosesan Paket Return
            </h5>
            <span class="badge bg-light text-dark border px-3 py-2">
                Total: {{ $returns->count() }} Paket Diverifikasi
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>No. Return</th>
                        <th>Pesanan Asal</th>
                        <th>No. Resi</th>
                        <th>Channel</th>
                        <th>Barang Dikembalikan</th>
                        <th class="text-center">Qty</th>
                        <th>Jalur Disposisi Gudang</th>
                        <th>Alasan Retur</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $idx => $r)
                        <tr>
                            <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                            <td>
                                <span class="fw-bold font-monospace text-primary">{{ $r->return_number }}</span>
                                <small class="text-muted d-block">{{ $r->created_at ? $r->created_at->format('d M Y H:i') : '-' }}</small>
                            </td>
                            <td>
                                <span class="fw-bold font-monospace text-dark">{{ $r->salesOrder?->order_number ?? '-' }}</span>
                                <small class="text-muted d-block">{{ $r->salesOrder?->customer?->name ?? 'Guest' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">{{ $r->tracking_number }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ strtoupper($r->channel) }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $r->product?->name }}</div>
                                <small class="text-muted font-monospace">{{ $r->product?->sku }}</small>
                            </td>
                            <td class="text-center fw-bold">{{ number_format($r->qty_returned, 0, ',', '.') }} pcs</td>
                            <td>
                                @if($r->disposition === 'good_ready_sale')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Kondisi Baik (Gd. Utama)</span>
                                @elseif($r->disposition === 'repair_reject')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-tools me-1"></i>Service (Gd. Reject)</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-octagon me-1"></i>Cacat (Gd. Sale)</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $r->reason }}</td>
                            <td class="small text-muted"><i class="bi bi-person me-1"></i>{{ $r->user?->name ?? 'Admin Retur' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                Belum ada data paket return yang diproses.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL INPUT RETURN --}}
    <div class="modal fade" id="createReturnModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('retur.paket.store') }}">
                    @csrf
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold"><i class="bi bi-box-arrow-in-left me-2"></i>Input & Verifikasi Paket Return (Reverse Logistics)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        {{-- 1. PENCARIAN 4 DIGIT RESI --}}
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="bi bi-search text-primary me-1"></i> Cari Cepat via 4 Digit Terakhir Resi / Nomor Pesanan Asal:
                            </label>
                            <div class="input-group">
                                <input type="text" id="resiSearchInput" class="form-control" placeholder="Ketik 4 digit terakhir nomor resi (misal: 1001 atau POS)...">
                                <button type="button" class="btn btn-primary" onclick="searchOrdersAjax()">
                                    <i class="bi bi-search me-1"></i> Cari Pesanan Asal
                                </button>
                            </div>
                            <div id="searchResultList" class="mt-2 list-group d-none">
                                {{-- Populated via AJAX --}}
                            </div>
                        </div>

                        {{-- 2. FIELD RETURN --}}
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">ID Pesanan Penjualan Terpilih:</label>
                                <input type="number" name="sales_order_id" id="selectedOrderId" class="form-control" placeholder="Pilih dari hasil pencarian di atas" readonly required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">No. Resi Lengkap:</label>
                                <input type="text" name="tracking_number" id="selectedTrackingNumber" class="form-control" placeholder="Nomor resi otomatis terisi" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">Produk / Kebaya Yang Diretur:</label>
                                <select name="product_id" id="returnProductId" class="form-select" required>
                                    @php
                                        $fProducts = \App\Models\Product::where('type', 'finished_goods')->get();
                                    @endphp
                                    @foreach($fProducts as $fp)
                                        <option value="{{ $fp->id }}">{{ $fp->name }} (SKU: {{ $fp->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">Jumlah Kuantitas Retur (Pcs):</label>
                                <input type="number" name="qty_returned" class="form-control fw-bold" value="1" min="1" required>
                            </div>

                            {{-- 3 JALUR DISPOSISI FISIK --}}
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">Keputusan 3 Jalur Disposisi Fisik Barang:</label>
                                <div class="row g-2">
                                    <div class="col-12 col-md-4">
                                        <div class="form-check p-3 border rounded-3 bg-success-subtle h-100">
                                            <input class="form-check-input" type="radio" name="condition_disposition" id="dispGood" value="good_ready_sale" checked>
                                            <label class="form-check-label fw-bold text-success" for="dispGood">
                                                1. Kondisi Baik &rarr; Gudang Utama / Store
                                            </label>
                                            <small class="text-muted d-block mt-1">Barang mulus, tag masih utuh. Siap dijual kembali.</small>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-check p-3 border rounded-3 bg-warning-subtle h-100">
                                            <input class="form-check-input" type="radio" name="condition_disposition" id="dispRepair" value="repair_reject">
                                            <label class="form-check-label fw-bold text-warning" for="dispRepair">
                                                2. Rusak Ringan &rarr; Gudang Reject/Service
                                            </label>
                                            <small class="text-muted d-block mt-1">Kancing lepas, jahitan perlu diperbaiki.</small>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-check p-3 border rounded-3 bg-danger-subtle h-100">
                                            <input class="form-check-input" type="radio" name="condition_disposition" id="dispDamaged" value="damaged_sale">
                                            <label class="form-check-label fw-bold text-danger" for="dispDamaged">
                                                3. Rusak Berat &rarr; Gudang Sale Cacat
                                            </label>
                                            <small class="text-muted d-block mt-1">Kain sobek/noda parah. Dijual clearance bertanda cacat.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small text-muted">Alasan Retur / Komplain Pembeli:</label>
                                <input type="text" name="reason" class="form-control" placeholder="Contoh: Salah ukuran, warna tidak sesuai ekspektasi, retur paket COD...">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="bi bi-check2-circle me-1"></i> Sahkan Penerimaan Retur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function focusSearchResi() {
        const modal = new bootstrap.Modal(document.getElementById('createReturnModal'));
        modal.show();
        setTimeout(() => document.getElementById('resiSearchInput').focus(), 500);
    }

    async function searchOrdersAjax() {
        const keyword = document.getElementById('resiSearchInput').value;
        if (!keyword || keyword.length < 3) {
            alert('Ketik minimal 3 karakter nomor resi atau pesanan!');
            return;
        }

        const list = document.getElementById('searchResultList');
        list.classList.remove('d-none');
        list.innerHTML = '<div class="p-2 text-muted small"><span class="spinner-border spinner-border-sm me-1"></span> Mencari pesanan asal...</div>';

        try {
            const resp = await fetch("{{ route('retur.search-order') }}?keyword=" + encodeURIComponent(keyword));
            const data = await resp.json();

            if (data.success && data.orders.length > 0) {
                list.innerHTML = '';
                data.orders.forEach(ord => {
                    const item = document.createElement('a');
                    item.className = 'list-group-item list-group-item-action py-2';
                    item.href = '#';
                    item.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-primary font-monospace">${ord.order_number}</strong>
                                <span class="badge bg-secondary ms-1">${ord.channel.toUpperCase()}</span>
                                <div class="small text-muted">Resi: <code>${ord.tracking_number || '-'}</code> | Pelanggan: ${ord.customer ? ord.customer.name : 'Guest'}</div>
                            </div>
                            <span class="badge bg-primary">Pilih Pesanan Ini</span>
                        </div>
                    `;
                    item.onclick = (e) => {
                        e.preventDefault();
                        selectOrder(ord);
                    };
                    list.appendChild(item);
                });
            } else {
                list.innerHTML = '<div class="p-2 text-danger small">Tidak ditemukan transaksi pesanan dengan kata kunci tersebut.</div>';
            }
        } catch (err) {
            list.innerHTML = '<div class="p-2 text-danger small">Error mencari pesanan: ' + err.message + '</div>';
        }
    }

    function selectOrder(ord) {
        document.getElementById('selectedOrderId').value = ord.id;
        document.getElementById('selectedTrackingNumber').value = ord.tracking_number || ord.order_number;
        document.getElementById('searchResultList').classList.add('d-none');
        alert(`Pesanan ${ord.order_number} berhasil dipilih! Silakan verifikasi kondisi fisik barang.`);
    }
</script>
@stop
