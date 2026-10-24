@extends('adminlte::page')

@section('title', $config['moduleTitle'] . ' - ' . $config['subTitle'])

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi {{ $config['headerIcon'] }} text-primary me-2"></i>{{ $config['moduleTitle'] }}
                <span class="text-secondary fw-normal fs-5">/ {{ $config['subTitle'] }}</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">{{ $config['moduleTitle'] }}</li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $config['subTitle'] }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="exportData()">
                <i class="bi bi-file-earmark-excel text-success me-1"></i> Ekspor Excel
            </button>
            <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#formModal">
                <i class="bi bi-plus-circle me-1"></i> Tambah Data Baru
            </button>
            <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-speedometer2"></i>
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- SUMMARY METRICS CARDS --}}
    @if(!empty($config['summaryCards']))
        <div class="row g-3 mb-4">
            @foreach($config['summaryCards'] as $card)
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle bg-{{ $card['color'] }} bg-opacity-10 p-3 me-3 text-{{ $card['color'] }}">
                                <i class="bi {{ $card['icon'] }} fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold">{{ $card['title'] }}</span>
                                <h4 class="fw-bold mb-0 text-dark">{{ $card['value'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- MAIN DATA TABLE CARD --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0">
            <div class="row g-2 align-items-center justify-content-between">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="tableSearchInput" class="form-control border-start-0" placeholder="Cari data dalam tabel (ketik apa saja)...">
                    </div>
                </div>
                <div class="col-12 col-md-auto d-flex gap-2">
                    <select class="form-select form-select-sm" id="filterStatus" style="width: auto;">
                        <option value="">Semua Status</option>
                        <option value="Aktif">Aktif / Tersedia</option>
                        <option value="Menipis">Menipis / Warning</option>
                        <option value="Lunas">Lunas</option>
                    </select>
                    <button class="btn btn-outline-secondary btn-sm" onclick="location.reload()" title="Refresh Data">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0" id="mainDataTable">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        @foreach($config['columns'] as $col)
                            <th>{{ $col['label'] }}</th>
                        @endforeach
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($config['rows'] as $index => $row)
                        <tr class="table-row-item">
                            <td class="text-center text-muted small">{{ $index + 1 }}</td>
                            @foreach($config['columns'] as $col)
                                <td>
                                    @if($col['key'] === 'status' || $col['key'] === 'shipping_status' || $col['key'] === 'stage' || $col['key'] === 'action')
                                        <span class="badge bg-{{ $row['badge'] ?? 'secondary' }}">
                                            {{ $row[$col['key']] ?? '-' }}
                                        </span>
                                    @elseif($col['key'] === 'sku' || $col['key'] === 'code' || $col['key'] === 'po_number' || $col['key'] === 'wo_code' || $col['key'] === 'invoice' || $col['key'] === 'order_id' || $col['key'] === 'trx_code')
                                        <span class="fw-bold text-primary font-monospace">{{ $row[$col['key']] ?? '-' }}</span>
                                    @elseif($col['key'] === 'price' || $col['key'] === 'total' || $col['key'] === 'amount')
                                        <span class="fw-semibold text-dark">{{ $row[$col['key']] ?? '-' }}</span>
                                    @else
                                        {{ $row[$col['key']] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" title="Edit Data" onclick="editItem({{ json_encode($row) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" title="Hapus Data" onclick="deleteItem(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($config['columns']) + 2 }}" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Belum ada data untuk submodul ini. Klik tombol <strong>Tambah Data Baru</strong> di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="small text-muted" id="tableInfo">
                Menampilkan <strong>{{ count($config['rows']) }}</strong> data dari total {{ count($config['rows']) }} entri
            </span>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
            </ul>
        </div>
    </div>

    {{-- MODAL FORM TAMBAH / EDIT DATA --}}
    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="formModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>{{ $config['formTitle'] }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="dynamicForm" onsubmit="handleFormSubmit(event)">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            @foreach($config['formFields'] as $field)
                                @php
                                    $colSize = ($field['type'] === 'textarea') ? 'col-12' : 'col-md-6';
                                @endphp
                                <div class="{{ $colSize }}">
                                    <label class="form-label small fw-bold text-dark">
                                        {{ $field['label'] }}
                                        @if(!empty($field['required'])) <span class="text-danger">*</span> @endif
                                    </label>

                                    @if($field['type'] === 'select')
                                        <select class="form-select" name="{{ $field['name'] }}" @if(!empty($field['required'])) required @endif>
                                            <option value="">-- Pilih {{ $field['label'] }} --</option>
                                            @foreach($field['options'] as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($field['type'] === 'textarea')
                                        <textarea class="form-control" name="{{ $field['name'] }}" rows="3" placeholder="{{ $field['placeholder'] ?? '' }}" @if(!empty($field['required'])) required @endif></textarea>
                                    @elseif($field['type'] === 'date')
                                        <input type="date" class="form-control" name="{{ $field['name'] }}" value="{{ date('Y-m-d') }}" @if(!empty($field['required'])) required @endif>
                                    @else
                                        <input type="{{ $field['type'] }}" class="form-control" name="{{ $field['name'] }}" placeholder="{{ $field['placeholder'] ?? '' }}" @if(!empty($field['required'])) required @endif>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    // Live Search Filter in Table
    document.getElementById('tableSearchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#tableBody .table-row-item');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('tableInfo').innerHTML = `Menampilkan <strong>${visibleCount}</strong> data yang cocok`;
    });

    // Form submission handler
    function handleFormSubmit(e) {
        e.preventDefault();
        const modal = bootstrap.Modal.getInstance(document.getElementById('formModal'));
        if (modal) {
            modal.hide();
        }

        Swal.fire({
            title: 'Berhasil Disimpan!',
            text: 'Data baru berhasil dicatat ke dalam modul sistem ERP.',
            icon: 'success',
            confirmButtonColor: '#0d6efd'
        });

        // Reset form
        document.getElementById('dynamicForm').reset();
    }

    // Edit item simulation
    function editItem(row) {
        const modalElement = document.getElementById('formModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Memuat data formulir...',
            showConfirmButton: false,
            timer: 1500
        });
    }

    // Delete item with confirmation
    function deleteItem(btn) {
        Swal.fire({
            title: 'Hapus data ini?',
            text: 'Data yang dihapus tidak dapat dikembalikan lagi!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const tr = btn.closest('tr');
                tr.remove();
                Swal.fire('Terhapus!', 'Data berhasil dihapus dari tabel.', 'success');
            }
        });
    }

    // Export simulation
    function exportData() {
        Swal.fire({
            title: 'Sedang Menyiapkan Dokumen...',
            html: 'Mengekspor data ke format Excel (.xlsx)...',
            timer: 1600,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        }).then(() => {
            Swal.fire('Berhasil!', 'File Excel laporan telah siap diunduh.', 'success');
        });
    }
</script>
@stop
