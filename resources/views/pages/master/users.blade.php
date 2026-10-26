@extends('adminlte::page')

@section('title', 'User & Hak Akses (RBAC) | KlambiBali ERP')

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-center pb-2 border-bottom">
        <div>
            <h1 class="m-0 text-dark fw-bold" style="font-family: 'Playfair Display', Georgia, serif;">
                <i class="bi bi-shield-lock me-2" style="color: #c2ae79;"></i> Manajemen Pengguna & Hak Akses (RBAC)
            </h1>
            <small class="text-muted">
                Role-Based Access Control Sesuai Bab 2 PRD KlambiBali • Otorisasi 7 Role & 12 Modul Terisolasi
            </small>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Matriks
            </button>
            @can('access-users')
            <button type="button" class="btn btn-sm text-dark fw-bold" style="background: linear-gradient(135deg, #c2ae79, #a8945f);" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna Baru
            </button>
            @endcan
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid py-3">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #283023 !important;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3 text-white" style="background: #283023;">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Pengguna</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $users->count() }} User</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #c2ae79 !important;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3 text-dark" style="background: #c2ae79;">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Role Terdaftar</div>
                        <h4 class="fw-bold mb-0 text-dark">7 Role PRD</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #198754 !important;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3 bg-success text-white">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Status Pengguna</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $users->where('status', 'active')->count() }} Aktif</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #0dcaf0 !important;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3 bg-info text-white">
                        <i class="bi bi-lock-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Sistem RBAC</div>
                        <h4 class="fw-bold mb-0 text-dark">Enforced</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation Tabs --}}
    <ul class="nav nav-pills mb-3 gap-2" id="rbacTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 fw-semibold" id="users-tab" data-bs-toggle="pill" data-bs-target="#users-content" type="button" role="tab">
                <i class="bi bi-person-lines-fill me-1"></i> Daftar Pengguna ERP
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 fw-semibold" id="matrix-tab" data-bs-toggle="pill" data-bs-target="#matrix-content" type="button" role="tab">
                <i class="bi bi-table me-1"></i> Matriks Hak Akses (12 Modul x 7 Role)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 fw-semibold" id="guide-tab" data-bs-toggle="pill" data-bs-target="#guide-content" type="button" role="tab">
                <i class="bi bi-info-circle me-1"></i> Panduan Pengujian Multi-Role
            </button>
        </li>
    </ul>

    <div class="tab-content" id="rbacTabContent">

        {{-- TAB 1: DAFTAR PENGGUNA --}}
        <div class="tab-pane fade show active" id="users-content" role="tabpanel" aria-labelledby="users-tab">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold m-0 text-dark">
                        <i class="bi bi-people-fill me-2" style="color: #283023;"></i> Data Pengguna Aktif & Role
                    </h5>
                    <span class="badge bg-light text-dark border">Total: {{ $users->count() }} Pengguna</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase small" style="letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4">Pengguna</th>
                                    <th>Kontak / Email</th>
                                    <th>Role Akses</th>
                                    <th>Landing Page Default</th>
                                    <th>Status</th>
                                    <th>Terakhir Login</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->adminlte_image() }}" alt="{{ $user->name }}" class="rounded-circle me-3 border" width="40" height="40">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $user->name }}</div>
                                                <small class="text-muted">ID: #USR-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $user->email }}</div>
                                        <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $user->phone ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->role_badge }} px-3 py-2 rounded-pill fw-semibold">
                                            {{ $user->role_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <code class="text-primary small">{{ $user->default_route }}</code>
                                    </td>
                                    <td>
                                        @if($user->status === 'active')
                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                <i class="bi bi-check-circle me-1"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">
                                                <i class="bi bi-x-circle me-1"></i> Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $user->last_login_at ? $user->last_login_at->translatedFormat('d M Y, H:i') : 'Belum pernah login' }}
                                        </small>
                                    </td>
                                    <td class="text-end pe-4">
                                        @can('access-users')
                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editRoleModal{{ $user->id }}">
                                            <i class="bi bi-pencil-square me-1"></i> Ubah Role
                                        </button>
                                        @else
                                        <span class="text-muted small">Read-only</span>
                                        @endcan
                                    </td>
                                </tr>

                                {{-- Modal Edit Role --}}
                                @can('access-users')
                                <div class="modal fade" id="editRoleModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="{{ route('users.update-role', $user) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Ubah Role & Status: {{ $user->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Pilih Role Baru</label>
                                                        <select name="role" class="form-select" required>
                                                            @foreach($roleLabels as $key => $label)
                                                                <option value="{{ $key }}" {{ $user->role === $key ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Status Akun</label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Aktif</option>
                                                            <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Nonaktif / Diblokir</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endcan
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: MATRIKS HAK AKSES RBAC --}}
        <div class="tab-pane fade" id="matrix-content" role="tabpanel" aria-labelledby="matrix-tab">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold m-0 text-dark">
                            <i class="bi bi-shield-check me-2" style="color: #c2ae79;"></i> Matriks Otorisasi Modul Sistem ERP (RBAC Matrix)
                        </h5>
                        <small class="text-muted">Pemetaan izin akses setiap role terhadap 12 modul utama KlambiBali</small>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success">Akses Penuh</span>
                        <span class="badge bg-info">Lihat Saja</span>
                        <span class="badge bg-warning text-dark">Terbatas</span>
                        <span class="badge bg-secondary">Dibatasi</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 text-center small">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start ps-3" style="min-width: 200px;">Modul Sistem ERP</th>
                                    <th><span class="badge bg-danger">Owner</span></th>
                                    <th><span class="badge bg-warning text-dark">Purchasing</span></th>
                                    <th><span class="badge bg-secondary">Produksi/QC</span></th>
                                    <th><span class="badge bg-primary">Gudang</span></th>
                                    <th><span class="badge bg-info text-dark">Sales/CS</span></th>
                                    <th><span class="badge bg-success">Kasir POS</span></th>
                                    <th><span class="badge bg-dark">Finance/Akun</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modules as $moduleName => $rolesAccess)
                                <tr>
                                    <td class="text-start ps-3 fw-bold text-dark">{{ $moduleName }}</td>
                                    @foreach(['owner', 'purchasing', 'produksi', 'gudang', 'sales', 'kasir', 'finance'] as $rKey)
                                        @php
                                            $item = $rolesAccess[$rKey] ?? ['status' => 'none', 'label' => '—'];
                                            $badgeClass = match($item['status']) {
                                                'full' => 'bg-success',
                                                'view' => 'bg-info',
                                                'limited' => 'bg-warning text-dark',
                                                default => 'bg-light text-muted border',
                                            };
                                        @endphp
                                        <td>
                                            <span class="badge {{ $badgeClass }} px-2 py-1">
                                                {{ $item['label'] }}
                                            </span>
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: PANDUAN PENGUJIAN --}}
        <div class="tab-pane fade" id="guide-content" role="tabpanel" aria-labelledby="guide-tab">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-dark">
                        <i class="bi bi-lightbulb-fill me-2" style="color: #c2ae79;"></i> Panduan Uji Coba Multi-Role & Dynamic Menu Filtering
                    </h5>
                    <p class="text-muted">
                        Sistem KlambiBali ERP menerapkan penyaringan sidebar navigasi otomatis menggunakan <strong>Laravel Gate & Menu Filters</strong>.
                        Setiap akun yang login hanya akan melihat menu dan modul yang menjadi hak wewenangnya:
                    </p>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3 border h-100" style="background: #fdfdfd; border-left: 4px solid #198754 !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-success p-2 rounded-circle me-2"><i class="bi bi-display"></i></span>
                                    <h6 class="fw-bold m-0">1. Skenario Kasir Toko POS</h6>
                                </div>
                                <div class="small text-muted mb-2">Login: <code>kasir@klambibali.com</code> / <code>password</code></div>
                                <p class="small text-secondary mb-0">
                                    • Langsung diarahkan ke modul <strong>/pos</strong>.<br>
                                    • Sidebar hanya menampilkan menu <strong>Point of Sale (POS) & Riwayat Kasir</strong>.<br>
                                    • Menu Akuntansi, Pembelian, SPK Produksi, dan Master COA otomatis disembunyikan.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3 border h-100" style="background: #fdfdfd; border-left: 4px solid #0d6efd !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-primary p-2 rounded-circle me-2"><i class="bi bi-boxes"></i></span>
                                    <h6 class="fw-bold m-0">2. Skenario Kepala Gudang</h6>
                                </div>
                                <div class="small text-muted mb-2">Login: <code>gudang@klambibali.com</code> / <code>password</code></div>
                                <p class="small text-secondary mb-0">
                                    • Langsung diarahkan ke <strong>/inventory/stok</strong>.<br>
                                    • Mengelola stok 7 gudang KlambiBali, transfer antar-gudang, kartu stok riil, opname fisik, dan cetak barcode thermal.<br>
                                    • Modul Finansial dan Laba Rugi disembunyikan.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3 border h-100" style="background: #fdfdfd; border-left: 4px solid #6c757d !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-secondary p-2 rounded-circle me-2"><i class="bi bi-scissors"></i></span>
                                    <h6 class="fw-bold m-0">3. Skenario Produksi & QC</h6>
                                </div>
                                <div class="small text-muted mb-2">Login: <code>produksi@klambibali.com</code> / <code>password</code></div>
                                <p class="small text-secondary mb-0">
                                    • Langsung diarahkan ke <strong>/produksi/work-orders</strong>.<br>
                                    • Mengelola Perintah Kerja (WO), SPK Potong Bahan, Resep BOM, Rekonsiliasi Sisa Bahan Kain (1.56 yd), dan Inspeksi QC Transit.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3 border h-100" style="background: #fdfdfd; border-left: 4px solid #ffc107 !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-warning text-dark p-2 rounded-circle me-2"><i class="bi bi-cart3"></i></span>
                                    <h6 class="fw-bold m-0">4. Skenario Tim Purchasing</h6>
                                </div>
                                <div class="small text-muted mb-2">Login: <code>purchasing@klambibali.com</code> / <code>password</code></div>
                                <p class="small text-secondary mb-0">
                                    • Langsung diarahkan ke <strong>/purchasing/po</strong>.<br>
                                    • Pembuatan Purchase Order bahan baku / barang jadi, penerimaan barang GRN, dan pelacakan hutang supplier.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3 border h-100" style="background: #fdfdfd; border-left: 4px solid #0dcaf0 !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-info text-dark p-2 rounded-circle me-2"><i class="bi bi-bag-check"></i></span>
                                    <h6 class="fw-bold m-0">5. Skenario Sales & CS</h6>
                                </div>
                                <div class="small text-muted mb-2">Login: <code>sales@klambibali.com</code> / <code>password</code></div>
                                <p class="small text-secondary mb-0">
                                    • Mengelola order penjualan omnichannel, integrasi marketplace Shopee & TikTok, dan proses Retur Paket via pencarian 4 digit resi.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3 border h-100" style="background: #fdfdfd; border-left: 4px solid #212529 !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-dark p-2 rounded-circle me-2"><i class="bi bi-graph-up"></i></span>
                                    <h6 class="fw-bold m-0">6. Skenario Finance & Akuntansi</h6>
                                </div>
                                <div class="small text-muted mb-2">Login: <code>finance@klambibali.com</code> / <code>password</code></div>
                                <p class="small text-secondary mb-0">
                                    • Standar akuntansi SAK ETAP: Chart of Accounts (COA), Jurnal Umum, Buku Besar, Neraca Saldo, Laba Rugi 4 channel, Neraca, dan Arus Kas.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Tambah User Baru --}}
@can('access-users')
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addUserModalLabel">
                        <i class="bi bi-person-plus me-1" style="color: #c2ae79;"></i> Tambah Pengguna Sistem Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Pengguna <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: I Ketut Sudira" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Email (Username Login) <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="user@klambibali.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor WhatsApp / HP</label>
                        <input type="text" name="phone" class="form-control" placeholder="08123456789">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role Hak Akses (RBAC) <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="">-- Pilih Role Hak Akses --</option>
                            @foreach($roleLabels as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Menu sidebar akan otomatis disesuaikan dengan role ini.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kata Sandi Awal <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@stop
