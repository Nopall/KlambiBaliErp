@extends('adminlte::master')

@section('title', 'Login ERP — KlambiBali')

@section('classes_body', 'login-page d-flex align-items-center justify-content-center py-5')

@section('body')
<div class="login-wrapper w-100 p-3" style="max-width: 460px;">

    {{-- Brand Header --}}
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm mb-2" 
             style="background: #283023; border: 2px solid #c2ae79; width: 68px; height: 68px;">
            <img src="{{ asset('images/logo-klambibali.png') }}" alt="KlambiBali Logo" class="img-fluid rounded-circle" style="max-height: 52px;">
        </div>
        <h2 class="mb-0 fw-bold" style="font-family: 'Playfair Display', Georgia, serif; letter-spacing: 2px;">
            <span style="color: #9e8548;">KLAMBI</span> <span style="color: #283023;">BALI</span>
        </h2>
        <div class="text-uppercase fw-semibold" style="color: #7a7364; letter-spacing: 2.5px; font-size: 11px;">
            Enterprise Resource Planning
        </div>
        <div class="d-flex justify-content-center mt-2">
            <span style="width: 48px; height: 2px; background: linear-gradient(90deg, transparent, #c2ae79, transparent);"></span>
        </div>
    </div>

    {{-- Main Login Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" 
         style="background: #ffffff; border: 1px solid #e5dfd3 !important; border-top: 3px solid #c2ae79 !important;">
        
        <div class="card-body p-4 p-sm-5">
            <div class="mb-4">
                <h4 class="fw-bold mb-1" style="color: #283023; font-family: 'Playfair Display', serif;">Masuk ke Portal</h4>
                <p class="text-muted small mb-0">Silakan masukkan kredensial akun Anda</p>
            </div>

            {{-- Flash Alerts --}}
            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show border-0 small py-2 px-3 mb-3" role="alert" style="background: #fff8e6; color: #7a5e10; border-left: 3px solid #f59e0b !important;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('warning') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 small py-2 px-3 mb-3" role="alert" style="background: #fef2f2; color: #991b1b; border-left: 3px solid #ef4444 !important;">
                    <i class="bi bi-x-octagon-fill me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Form Login --}}
            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf

                {{-- Email Input --}}
                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold text-secondary mb-1">Email / Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted" style="border-color: #dcd6cb;">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" 
                               class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                               style="border-color: #dcd6cb;"
                               placeholder="user@klambibali.com" 
                               value="{{ old('email', 'admin@klambibali.com') }}" required autofocus>
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1" style="font-size: 12px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password Input --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label small fw-semibold text-secondary mb-0">Kata Sandi</label>
                        <span class="text-muted" style="font-size: 11px;">Default: <code>password</code></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted" style="border-color: #dcd6cb;">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" 
                               class="form-control border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror" 
                               style="border-color: #dcd6cb;"
                               placeholder="••••••••" 
                               value="password" required>
                        <button type="button" class="input-group-text bg-light border-start-0 text-muted" 
                                style="border-color: #dcd6cb; cursor: pointer;" 
                                onclick="togglePasswordVisibility()">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1" style="font-size: 12px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember Me & RBAC Modal Link --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" checked style="cursor: pointer;">
                        <label class="form-check-label text-muted small" for="remember" style="cursor: pointer; font-size: 13px;">
                            Ingat saya
                        </label>
                    </div>
                    <a href="javascript:void(0)" class="text-decoration-none small fw-semibold" style="color: #9e8548; font-size: 12px;" data-bs-toggle="modal" data-bs-target="#rbacMatrixModal">
                        <i class="bi bi-shield-check me-1"></i> Matriks Hak Akses
                    </a>
                </div>

                {{-- Submit Button --}}
                <div class="d-grid mb-4">
                    <button type="submit" class="btn py-2 fw-bold text-white shadow-sm" 
                            style="background-color: #283023; border: 1px solid #1a2016; font-size: 14px; letter-spacing: 0.5px; transition: all 0.2s ease;">
                        <i class="bi bi-box-arrow-in-right me-1" style="color: #c2ae79;"></i> Masuk ke Sistem ERP
                    </button>
                </div>
            </form>

            {{-- Quick Demo Role Dropdown (Clean & Ergonomic) --}}
            <div class="pt-3 border-top" style="border-color: #ede7dc !important;">
                <label for="demoRoleSelect" class="form-label text-uppercase fw-semibold text-muted d-flex align-items-center justify-content-between mb-2" style="font-size: 11px; letter-spacing: 1px;">
                    <span><i class="bi bi-person-badge me-1"></i> Uji Coba Akun / Role:</span>
                    <span class="badge bg-light text-dark border">7 Role</span>
                </label>
                
                <select class="form-select form-select-sm" id="demoRoleSelect" onchange="switchRole(this.value)" style="border-color: #dcd6cb; font-size: 13px;">
                    <option value="admin" selected>👑 Owner / Super Admin (admin@klambibali.com)</option>
                    <option value="kasir">💳 Kasir Toko POS (kasir@klambibali.com)</option>
                    <option value="gudang">📦 Kepala Gudang (gudang@klambibali.com)</option>
                    <option value="produksi">✂️ Produksi & QC (produksi@klambibali.com)</option>
                    <option value="purchasing">🛒 Tim Purchasing (purchasing@klambibali.com)</option>
                    <option value="sales">🛍️ Sales & CS (sales@klambibali.com)</option>
                    <option value="finance">📊 Finance & Akuntansi (finance@klambibali.com)</option>
                </select>

                {{-- Live Role Capability Badge --}}
                <div class="mt-2 p-2 rounded-2 small" id="roleInfoBox" 
                     style="background-color: #fbf9f5; border: 1px solid #ede6da; font-size: 11.5px; color: #5c5549;">
                    <div class="d-flex align-items-center mb-1">
                        <span class="badge bg-danger me-2" id="roleBadge">Owner</span>
                        <strong id="roleTitle" style="color: #283023;">Akses Penuh 12 Modul</strong>
                    </div>
                    <div id="roleDesc" class="text-muted">
                        Dapat mengelola seluruh modul ERP, otorisasi stok, tutup buku, dan konfigurasi master.
                    </div>
                </div>
            </div>

        </div>

        {{-- Card Footer --}}
        <div class="card-footer py-2 text-center bg-light border-top" style="border-color: #ede7dc !important;">
            <small class="text-muted" style="font-size: 11px;">
                KlambiBali ERP v1.0 • Standar SAK ETAP & 7 Gudang Terisolasi
            </small>
        </div>
    </div>
</div>

{{-- Modal Matriks Hak Akses RBAC --}}
<div class="modal fade" id="rbacMatrixModal" tabindex="-1" aria-labelledby="rbacMatrixModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-2 me-2" style="background: #283023; color: #c2ae79;">
                        <i class="bi bi-shield-check fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="rbacMatrixModalLabel">Matriks Hak Akses Sistem (RBAC)</h5>
                        <small class="text-muted">Spesifikasi Kebutuhan Bab 2 PRD KlambiBali ERP</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="table-responsive bg-white rounded-3 shadow-sm border">
                    <table class="table table-hover align-middle mb-0 text-center small">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start ps-3 py-3" style="min-width: 220px; color: #283023;">Modul Sistem</th>
                                <th><span class="badge bg-danger">Owner</span></th>
                                <th><span class="badge bg-warning text-dark">Purchasing</span></th>
                                <th><span class="badge bg-secondary">Produksi/QC</span></th>
                                <th><span class="badge bg-primary">Gudang</span></th>
                                <th><span class="badge bg-info text-dark">Sales/CS</span></th>
                                <th><span class="badge bg-success">Kasir POS</span></th>
                                <th><span class="badge bg-dark">Finance</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-start ps-3 fw-semibold">1. Dashboard Utama Real-Time</td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-info text-dark">Lihat</span></td>
                                <td><span class="badge bg-info text-dark">Lihat</span></td>
                                <td><span class="badge bg-info text-dark">Lihat</span></td>
                                <td><span class="badge bg-info text-dark">Lihat</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-info text-dark">Lihat</span></td>
                            </tr>
                            <tr>
                                <td class="text-start ps-3 fw-semibold">2. Master Data (Produk, COA, User)</td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-warning text-dark">Bahan/Vendor</span></td>
                                <td><span class="badge bg-warning text-dark">BOM/Penjahit</span></td>
                                <td><span class="badge bg-warning text-dark">SKU Produk</span></td>
                                <td><span class="badge bg-warning text-dark">Pelanggan</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-warning text-dark">COA</span></td>
                            </tr>
                            <tr>
                                <td class="text-start ps-3 fw-semibold">3. Purchasing & Penerimaan (GRN)</td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-success">Full (PO/GRN)</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-info text-dark">Terima GRN</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-info text-dark">Hutang PO</span></td>
                            </tr>
                            <tr>
                                <td class="text-start ps-3 fw-semibold">4. 7 Gudang & Kartu Stok Riil</td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-info text-dark">Stok Bahan</span></td>
                                <td><span class="badge bg-info text-dark">WIP/Transit</span></td>
                                <td><span class="badge bg-success">Full 7 Gudang</span></td>
                                <td><span class="badge bg-info text-dark">Siap Jual</span></td>
                                <td><span class="badge bg-warning text-dark">Stok Toko</span></td>
                                <td><span class="badge bg-info text-dark">Nilai HPP</span></td>
                            </tr>
                            <tr>
                                <td class="text-start ps-3 fw-semibold">5. Produksi, SPK, Resep BOM & Sisa</td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-success">Full (WO/SPK)</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-info text-dark">Cek HPP</span></td>
                            </tr>
                            <tr>
                                <td class="text-start ps-3 fw-semibold">6. Quality Control & Barcode SKU</td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-success">QC Transit/Reject</span></td>
                                <td><span class="badge bg-info text-dark">Cetak Barcode</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                            </tr>
                            <tr>
                                <td class="text-start ps-3 fw-semibold">7. Penjualan & Marketplace Sync</td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-success">Full Omnichannel</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-info text-dark">Piutang</span></td>
                            </tr>
                            <tr>
                                <td class="text-start ps-3 fw-semibold">8. Point of Sale (POS Toko Fisik)</td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-success">Full Kasir/Shift</span></td>
                                <td><span class="badge bg-info text-dark">Kas Toko</span></td>
                            </tr>
                            <tr>
                                <td class="text-start ps-3 fw-semibold">9. Retur Paket (4 Digit Resi)</td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-info text-dark">Fisik GD-TRN-03</span></td>
                                <td><span class="badge bg-success">Input & Verifikasi</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-info text-dark">Klaim</span></td>
                            </tr>
                            <tr>
                                <td class="text-start ps-3 fw-semibold">10. Finance, Kas & Bank</td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-success">Full Kas/Bank</span></td>
                            </tr>
                            <tr>
                                <td class="text-start ps-3 fw-semibold">11. Akuntansi Standar SAK ETAP</td>
                                <td><span class="badge bg-success">Full</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-success">Full SAK ETAP</span></td>
                            </tr>
                            <tr>
                                <td class="text-start ps-3 fw-semibold">12. Laporan & Ekspor Excel (.xlsx)</td>
                                <td><span class="badge bg-success">Semua</span></td>
                                <td><span class="badge bg-info text-dark">Lap. Pembelian</span></td>
                                <td><span class="badge bg-info text-dark">Lap. Produksi</span></td>
                                <td><span class="badge bg-info text-dark">Lap. Gudang</span></td>
                                <td><span class="badge bg-info text-dark">Lap. Penjualan</span></td>
                                <td><span class="badge bg-light text-muted border">—</span></td>
                                <td><span class="badge bg-success">Lap. Finansial</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-white border-top py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    html, body.login-page {
        background-color: #f4f2ec !important;
        background-image: radial-gradient(at 50% 0%, #ffffff 0%, #f4f2ec 75%) !important;
        min-height: 100vh;
        font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #283023;
    }
    .form-control:focus, .form-select:focus {
        border-color: #c2ae79 !important;
        box-shadow: 0 0 0 3px rgba(194, 174, 121, 0.25) !important;
    }
    .btn:hover {
        opacity: 0.95;
    }
</style>
@endpush

@push('js')
<script>
    const roleDefinitions = {
        admin: {
            email: 'admin@klambibali.com',
            badge: 'Owner',
            badgeClass: 'bg-danger',
            title: 'Akses Penuh 12 Modul',
            desc: 'Dapat mengelola seluruh modul ERP, otorisasi stok, tutup buku, dan konfigurasi master.'
        },
        kasir: {
            email: 'kasir@klambibali.com',
            badge: 'Kasir POS',
            badgeClass: 'bg-success',
            title: 'Khusus Kasir Toko Fisik (GD-TKO-01)',
            desc: 'Hanya dapat membuka Point of Sale (POS), scan barcode, terima pembayaran, & tutup kasir harian.'
        },
        gudang: {
            email: 'gudang@klambibali.com',
            badge: 'Kepala Gudang',
            badgeClass: 'bg-primary',
            title: 'Akses 7 Gudang & Kartu Stok',
            desc: 'Mengelola stok multi-gudang, transfer antar-gudang, kartu stok riil, opname fisik, & cetak barcode.'
        },
        produksi: {
            email: 'produksi@klambibali.com',
            badge: 'Produksi & QC',
            badgeClass: 'bg-secondary',
            title: 'Perintah Kerja (WO) & Inspeksi QC',
            desc: 'Mengelola SPK potong kain, resep BOM, rekonsiliasi sisa bahan (1.56 yd), & inspeksi QC transit.'
        },
        purchasing: {
            email: 'purchasing@klambibali.com',
            badge: 'Purchasing',
            badgeClass: 'bg-warning text-dark',
            title: 'Purchase Order & Penerimaan GRN',
            desc: 'Membuat PO bahan baku/FG, penerimaan barang ke gudang transit/bahan, & pelacakan hutang supplier.'
        },
        sales: {
            email: 'sales@klambibali.com',
            badge: 'Sales & CS',
            badgeClass: 'bg-info text-dark',
            title: 'Penjualan & Retur 4 Digit Resi',
            desc: 'Mengelola pesanan reguler, sinkronisasi Shopee/TikTok, & proses reverse logistics retur paket.'
        },
        finance: {
            email: 'finance@klambibali.com',
            badge: 'Finance & Akuntansi',
            badgeClass: 'bg-dark',
            title: 'Akuntansi SAK ETAP & Laporan',
            desc: 'Mengelola buku kas/bank, COA, jurnal umum, buku besar, neraca saldo, laba rugi 4 channel, & arus kas.'
        }
    };

    function switchRole(roleKey) {
        const role = roleDefinitions[roleKey];
        if (!role) return;

        document.getElementById('email').value = role.email;
        document.getElementById('password').value = 'password';

        const badge = document.getElementById('roleBadge');
        badge.className = `badge ${role.badgeClass} me-2`;
        badge.textContent = role.badge;

        document.getElementById('roleTitle').textContent = role.title;
        document.getElementById('roleDesc').textContent = role.desc;
    }

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
</script>
@endpush
@endsection