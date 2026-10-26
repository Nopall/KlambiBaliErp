@extends('adminlte::master')

@section('title', 'Login Portal ERP — KlambiBali')

@section('classes_body', 'min-vh-100 d-flex align-items-center justify-content-center p-3')

@section('body')
<div class="container py-4" style="max-width: 1080px;">
    {{-- Header Brand --}}
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center p-2 rounded-circle mb-2 shadow" 
             style="background: #283023; border: 2px solid #c2ae79; width: 84px; height: 84px;">
            <img src="{{ asset('images/logo-klambibali.png') }}" alt="KlambiBali Logo" class="img-fluid rounded-circle" style="max-height: 70px;">
        </div>
        <h2 class="mb-1 fw-bold text-white" style="font-family: 'Playfair Display', Georgia, serif; letter-spacing: 2px;">
            <span style="color: #c2ae79;">KLAMBI</span> BALI
        </h2>
        <div class="text-uppercase fw-semibold" style="color: #a8945f; letter-spacing: 3px; font-size: 12px;">
            Enterprise Resource Planning & Multi-Warehouse System
        </div>
        <div class="d-flex justify-content-center my-2">
            <span style="width: 60px; height: 2px; background: linear-gradient(90deg, transparent, #c2ae79, transparent);"></span>
        </div>
    </div>

    {{-- Main Login Card --}}
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="background: rgba(40, 48, 35, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(194, 174, 121, 0.3) !important;">
        <div class="row g-0">
            {{-- Form Login Column --}}
            <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-between" style="border-right: 1px solid rgba(194, 174, 121, 0.2);">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="fw-bold mb-1 text-white">Masuk ke Portal</h4>
                            <p class="text-muted small mb-0">Autentikasi role-based access control (RBAC)</p>
                        </div>
                        <span class="badge px-3 py-2 rounded-pill" style="background: rgba(194, 174, 121, 0.15); color: #c2ae79; border: 1px solid rgba(194, 174, 121, 0.4);">
                            <i class="bi bi-shield-lock me-1"></i> Terenkripsi
                        </span>
                    </div>

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show border-0 small mb-4 text-dark" role="alert" style="background: #fff3cd;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 small mb-4" role="alert">
                            <i class="bi bi-x-octagon-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" id="loginForm">
                        @csrf

                        {{-- Email Input --}}
                        <div class="mb-3">
                            <label for="email" class="form-label text-light small fw-semibold">Email Pengguna / Akun</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-secondary">
                                    <i class="bi bi-person-fill" style="color: #c2ae79;"></i>
                                </span>
                                <input type="email" name="email" id="email" 
                                       class="form-control bg-dark text-white border-secondary @error('email') is-invalid @enderror" 
                                       placeholder="nama@klambibali.com" 
                                       value="{{ old('email', 'admin@klambibali.com') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Password Input --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="password" class="form-label text-light small fw-semibold">Kata Sandi (Password)</label>
                                <span class="small text-muted" style="font-size: 11px;">Default: <code>password</code></span>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-secondary">
                                    <i class="bi bi-key-fill" style="color: #c2ae79;"></i>
                                </span>
                                <input type="password" name="password" id="password" 
                                       class="form-control bg-dark text-white border-secondary @error('password') is-invalid @enderror" 
                                       placeholder="Masukkan kata sandi" 
                                       value="password" required>
                                <button type="button" class="btn btn-outline-secondary border-secondary text-secondary" id="togglePasswordBtn" onclick="togglePasswordVisibility()">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Remember Me & Info --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input bg-dark border-secondary" type="checkbox" name="remember" id="remember" checked>
                                <label class="form-check-label text-muted small" for="remember">
                                    Ingat sesi perangkat ini
                                </label>
                            </div>
                            <button type="button" class="btn btn-link text-decoration-none p-0 small" style="color: #c2ae79;" data-bs-toggle="modal" data-bs-target="#rbacMatrixModal">
                                <i class="bi bi-table me-1"></i> Cek Matriks RBAC
                            </button>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn py-2 fw-bold text-dark shadow" id="btnSubmitLogin"
                                    style="background: linear-gradient(135deg, #c2ae79, #a8945f); border: none; font-size: 15px;">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk ke Sistem ERP
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Footer Info --}}
                <div class="pt-3 border-top border-secondary text-center">
                    <p class="text-muted small mb-0" style="font-size: 11px;">
                        KlambiBali ERP v1.0 • Standar SAK ETAP & 7 Gudang Terisolasi • Sesi Aman TLS
                    </p>
                </div>
            </div>

            {{-- Quick Role Switcher Column --}}
            <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-between" style="background: rgba(28, 34, 25, 0.7);">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0 text-white">
                            <i class="bi bi-people-fill me-2" style="color: #c2ae79;"></i> Pilih Role Akses Demo
                        </h5>
                        <span class="badge bg-secondary small">7 Hak Akses</span>
                    </div>
                    <p class="text-muted small mb-3">
                        Klik salah satu tombol peran di bawah untuk mencoba hak akses sidebar & otorisasi modul:
                    </p>

                    <div class="row g-2 mb-3">
                        {{-- 1. Owner --}}
                        <div class="col-12">
                            <div class="role-card p-2 rounded-3 border d-flex align-items-center justify-content-between cursor-pointer"
                                 onclick="selectRole('admin@klambibali.com', 'password', 'Owner / Super Admin', 'Akses penuh ke seluruh 12 modul ERP, Master COA, Laba Rugi, & Otorisasi', 'danger')"
                                 id="role-admin">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-danger rounded-circle p-2 me-2">
                                        <i class="bi bi-crown"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white small">Owner / Super Admin</div>
                                        <div class="text-muted" style="font-size: 11px;">admin@klambibali.com</div>
                                    </div>
                                </div>
                                <span class="badge bg-danger-subtle text-danger border border-danger small">Full 12 Modul</span>
                            </div>
                        </div>

                        {{-- 2. Kasir POS --}}
                        <div class="col-md-6">
                            <div class="role-card p-2 rounded-3 border d-flex align-items-center justify-content-between cursor-pointer"
                                 onclick="selectRole('kasir@klambibali.com', 'password', 'Kasir Toko (POS)', 'Hanya akses Kasir POS, Scan Barcode, Multi-Payment, & Tutup Kasir Toko Fisik (GD-TKO-01)', 'success')"
                                 id="role-kasir">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-success rounded-circle p-2 me-2">
                                        <i class="bi bi-display"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white small">Kasir POS</div>
                                        <div class="text-muted" style="font-size: 11px;">kasir@klambibali.com</div>
                                    </div>
                                </div>
                                <span class="badge bg-success small">Toko Fisik</span>
                            </div>
                        </div>

                        {{-- 3. Gudang --}}
                        <div class="col-md-6">
                            <div class="role-card p-2 rounded-3 border d-flex align-items-center justify-content-between cursor-pointer"
                                 onclick="selectRole('gudang@klambibali.com', 'password', 'Admin Gudang (Inventory)', 'Akses 7 Gudang, Stok Multi-Gudang per SKU, Transfer, Kartu Stok, & Opname Fisik', 'primary')"
                                 id="role-gudang">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-primary rounded-circle p-2 me-2">
                                        <i class="bi bi-boxes"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white small">Kepala Gudang</div>
                                        <div class="text-muted" style="font-size: 11px;">gudang@klambibali.com</div>
                                    </div>
                                </div>
                                <span class="badge bg-primary small">7 Gudang</span>
                            </div>
                        </div>

                        {{-- 4. Produksi & QC --}}
                        <div class="col-md-6">
                            <div class="role-card p-2 rounded-3 border d-flex align-items-center justify-content-between cursor-pointer"
                                 onclick="selectRole('produksi@klambibali.com', 'password', 'Admin Produksi & QC', 'Perintah WO, SPK Potong Bahan Baku, Resep BOM, Rekonsiliasi Sisa Bahan (1.56 yd), & QC Transit', 'secondary')"
                                 id="role-produksi">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-secondary rounded-circle p-2 me-2">
                                        <i class="bi bi-scissors"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white small">Produksi & QC</div>
                                        <div class="text-muted" style="font-size: 11px;">produksi@klambibali.com</div>
                                    </div>
                                </div>
                                <span class="badge bg-secondary small">SPK / BOM</span>
                            </div>
                        </div>

                        {{-- 5. Purchasing --}}
                        <div class="col-md-6">
                            <div class="role-card p-2 rounded-3 border d-flex align-items-center justify-content-between cursor-pointer"
                                 onclick="selectRole('purchasing@klambibali.com', 'password', 'Tim Purchasing', 'Pembuatan PO Bahan Baku & FG, Penerimaan GRN ke Gudang Bahan / Transit, & Hutang Supplier', 'warning')"
                                 id="role-purchasing">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-warning text-dark rounded-circle p-2 me-2">
                                        <i class="bi bi-cart3"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white small">Purchasing</div>
                                        <div class="text-muted" style="font-size: 11px;">purchasing@klambibali.com</div>
                                    </div>
                                </div>
                                <span class="badge bg-warning text-dark small">PO / GRN</span>
                            </div>
                        </div>

                        {{-- 6. Sales & CS --}}
                        <div class="col-md-6">
                            <div class="role-card p-2 rounded-3 border d-flex align-items-center justify-content-between cursor-pointer"
                                 onclick="selectRole('sales@klambibali.com', 'password', 'Admin Sales & CS', 'Transaksi Pesanan, Marketplace Shopee & TikTok, Pencarian Resi 4 Digit, & Return Paket', 'info')"
                                 id="role-sales">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-info text-dark rounded-circle p-2 me-2">
                                        <i class="bi bi-bag-check"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white small">Sales & CS</div>
                                        <div class="text-muted" style="font-size: 11px;">sales@klambibali.com</div>
                                    </div>
                                </div>
                                <span class="badge bg-info text-dark small">Omnichannel</span>
                            </div>
                        </div>

                        {{-- 7. Finance & Accounting --}}
                        <div class="col-md-6">
                            <div class="role-card p-2 rounded-3 border d-flex align-items-center justify-content-between cursor-pointer"
                                 onclick="selectRole('finance@klambibali.com', 'password', 'Finance & Accounting', 'COA, Jurnal Umum, Buku Besar, Neraca Saldo, Laba Rugi 4 Channel, Neraca, & Arus Kas', 'dark')"
                                 id="role-finance">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-dark border border-secondary text-white rounded-circle p-2 me-2">
                                        <i class="bi bi-graph-up"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white small">Finance & Akun</div>
                                        <div class="text-muted" style="font-size: 11px;">finance@klambibali.com</div>
                                    </div>
                                </div>
                                <span class="badge bg-dark border small">SAK ETAP</span>
                            </div>
                        </div>
                    </div>

                    {{-- Live Role Permissions Preview Box --}}
                    <div class="p-3 rounded-3" id="rolePreviewBox" style="background: rgba(0, 0, 0, 0.4); border: 1px dashed rgba(194, 174, 121, 0.5);">
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge bg-danger me-2" id="previewRoleBadge">Owner / Super Admin</span>
                            <span class="text-white small fw-bold" id="previewRoleTitle">Akses Penuh Seluruh Modul</span>
                        </div>
                        <p class="text-muted small mb-0" id="previewRoleDesc" style="font-size: 12px;">
                            Dapat mengakses 12 modul lengkap: Master Data, Purchasing, 7 Gudang & Kartu Stok, Produksi & BOM, QC & Barcode, Penjualan, Marketplace, POS Kasir, Retur Paket, Keuangan, dan Akuntansi SAK ETAP.
                        </p>
                    </div>
                </div>

                {{-- Direct Action --}}
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-light w-100 btn-sm py-2" onclick="loginSelectedRole()">
                        <i class="bi bi-box-arrow-in-right me-1" style="color: #c2ae79;"></i> Masuk Langsung dengan Role Terpilih
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Matriks Hak Akses RBAC Sesuai PRD --}}
<div class="modal fade" id="rbacMatrixModal" tabindex="-1" aria-labelledby="rbacMatrixModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content text-white" style="background: #232a1e; border: 1px solid #c2ae79;">
            <div class="modal-header border-bottom border-secondary">
                <div class="d-flex align-items-center">
                    <i class="bi bi-shield-check fs-4 me-2" style="color: #c2ae79;"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="rbacMatrixModalLabel">Matriks Hak Akses Pengguna (RBAC Matrix)</h5>
                        <small class="text-muted">Sesuai Bab 2 Product Requirements Document (PRD) KlambiBali ERP</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-dark table-hover table-bordered border-secondary align-middle text-center small mb-0">
                        <thead style="background: #171c14;">
                            <tr>
                                <th class="text-start" style="color: #c2ae79;">Peran (Role)</th>
                                <th>Dashboard</th>
                                <th>Master Data</th>
                                <th>Purchasing</th>
                                <th>7 Gudang & Stok</th>
                                <th>Produksi & BOM</th>
                                <th>QC & Barcode</th>
                                <th>Penjualan</th>
                                <th>Marketplace</th>
                                <th>Kasir POS</th>
                                <th>Retur Paket</th>
                                <th>Finance & Kas</th>
                                <th>Akuntansi SAK</th>
                                <th>Laporan & Ekspor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-start fw-bold text-danger">
                                    <i class="bi bi-crown me-1"></i> Owner / Super Admin
                                </td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-success">Full</span></td>
                            </tr>
                            <tr>
                                <td class="text-start fw-bold text-warning">
                                    <i class="bi bi-cart3 me-1"></i> Tim Purchasing
                                </td>
                                <td><span class="badge bg-info">Lihat</span></td>
                                <td><span class="badge bg-secondary">Bahan/Vendor</span></td>
                                <td><span class="badge bg-success">Full (PO/GRN)</span></td>
                                <td><span class="badge bg-secondary">GRN Penerimaan</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-info">Hutang PO</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-info">Lap. Pembelian</span></td>
                            </tr>
                            <tr>
                                <td class="text-start fw-bold text-light">
                                    <i class="bi bi-scissors me-1"></i> Admin Produksi & QC
                                </td>
                                <td><span class="badge bg-info">Lihat</span></td>
                                <td><span class="badge bg-secondary">Penjahit/BOM</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-secondary">Stok WIP/Transit</span></td>
                                <td><span class="badge bg-success">Full (WO/SPK)</span></td>
                                <td><span class="badge bg-success">Full (QC/Reject)</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-info">Lap. Produksi</span></td>
                            </tr>
                            <tr>
                                <td class="text-start fw-bold text-primary">
                                    <i class="bi bi-boxes me-1"></i> Admin Gudang
                                </td>
                                <td><span class="badge bg-info">Lihat</span></td>
                                <td><span class="badge bg-secondary">SKU/Produk</span></td>
                                <td><span class="badge bg-secondary">GRN Penerimaan</span></td>
                                <td><span class="badge bg-success">Full (7 Gudang)</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-info">Cetak Barcode</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-secondary">Transit Retur</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-info">Lap. Stok Gudang</span></td>
                            </tr>
                            <tr>
                                <td class="text-start fw-bold text-info">
                                    <i class="bi bi-bag-check me-1"></i> Admin Sales & CS
                                </td>
                                <td><span class="badge bg-info">Lihat</span></td>
                                <td><span class="badge bg-secondary">Pelanggan/Member</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-success">Full (4 Digit Resi)</span></td>
                                <td><span class="badge bg-success">Shopee & TikTok</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-success">Full (Retur)</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-info">Lap. Penjualan</span></td>
                            </tr>
                            <tr>
                                <td class="text-start fw-bold text-success">
                                    <i class="bi bi-display me-1"></i> Kasir Toko (POS)
                                </td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-success">Full (Kasir/Shift)</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                            </tr>
                            <tr>
                                <td class="text-start fw-bold text-warning">
                                    <i class="bi bi-cash-coin me-1"></i> Finance & Accounting
                                </td>
                                <td><span class="badge bg-info">Lihat</span></td>
                                <td><span class="badge bg-secondary">COA Akun</span></td>
                                <td><span class="badge bg-info">Hutang PO</span></td>
                                <td><span class="badge bg-info">Nilai HPP Stok</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-info">Piutang Penjualan</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-dark text-muted">—</span></td>
                                <td><span class="badge bg-success">Full (Kas/Bank)</span></td>
                                <td><span class="badge bg-success">Full (SAK ETAP)</span></td>
                                <td><span class="badge bg-success">Full Lap. Keuangan</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top border-secondary">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    body {
        background: radial-gradient(circle at 50% 20%, #2f3929 0%, #1c2217 60%, #12160f 100%) !important;
        font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .role-card {
        background: rgba(40, 48, 35, 0.6);
        border-color: rgba(194, 174, 121, 0.25) !important;
        transition: all 0.2s ease;
    }
    .role-card:hover {
        background: rgba(194, 174, 121, 0.15);
        border-color: #c2ae79 !important;
        transform: translateY(-2px);
    }
    .role-card.active-role {
        background: rgba(194, 174, 121, 0.25);
        border-color: #c2ae79 !important;
        box-shadow: 0 0 10px rgba(194, 174, 121, 0.3);
    }
</style>
@endpush

@push('js')
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    function selectRole(email, password, title, desc, badgeColor) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;

        document.querySelectorAll('.role-card').forEach(el => el.classList.remove('active-role'));
        
        const previewRoleBadge = document.getElementById('previewRoleBadge');
        const previewRoleTitle = document.getElementById('previewRoleTitle');
        const previewRoleDesc = document.getElementById('previewRoleDesc');

        previewRoleBadge.className = `badge bg-${badgeColor} me-2`;
        previewRoleBadge.textContent = title;
        previewRoleTitle.textContent = `Akses Terbatas: ${title}`;
        previewRoleDesc.textContent = desc;

        // Highlight matching card
        const rolePrefix = email.split('@')[0];
        const card = document.getElementById(`role-${rolePrefix}`);
        if (card) {
            card.classList.add('active-role');
        }
    }

    function loginSelectedRole() {
        document.getElementById('loginForm').submit();
    }

    // Set default active card
    document.addEventListener('DOMContentLoaded', function() {
        const adminCard = document.getElementById('role-admin');
        if (adminCard) {
            adminCard.classList.add('active-role');
        }
    });
</script>
@endpush
@endsection