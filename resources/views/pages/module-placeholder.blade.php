@extends('adminlte::page')

@section('title', $moduleTitle . ' - ' . $subTitle)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-folder2-open text-primary me-2"></i>{{ $moduleTitle }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $subTitle }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-info-circle text-info me-2"></i>Modul: {{ $moduleTitle }} / {{ $subTitle }}
                    </h5>
                    <span class="badge bg-success-subtle text-success px-3 py-2">
                        <i class="bi bi-check-circle me-1"></i> Siap Dikonfigurasi
                    </span>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border d-flex align-items-center" role="alert">
                        <div class="fs-2 text-primary me-3">
                            <i class="bi bi-layers-half"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Struktur Navigasi & Modul Telah Terdaftar</h6>
                            <p class="mb-0 text-muted small">
                                Submodul ini telah terhubung ke sidebar AdminLTE <strong>KlambiBali ERP</strong> dan siap dikembangkan sesuai kebutuhan spesifik (migrasi database, model Eloquent, dan form transaksi).
                            </p>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <div class="card border bg-light h-100 p-3">
                                <h6 class="fw-bold text-dark"><i class="bi bi-check2-square text-success me-2"></i>Fitur Terkait:</h6>
                                <ul class="small text-muted mb-0 ps-3">
                                    <li>Pencatatan data & transaksi</li>
                                    <li>Validasi input & hak akses user</li>
                                    <li>Filter periode dan pencarian cepat</li>
                                    <li>Ekspor data ke format Excel / PDF</li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border bg-light h-100 p-3">
                                <h6 class="fw-bold text-dark"><i class="bi bi-hdd-network text-primary me-2"></i>Integrasi Sistem:</h6>
                                <ul class="small text-muted mb-0 ps-3">
                                    <li>Koneksi ke Multi-Gudang otomatis</li>
                                    <li>Pencatatan jurnal keuangan terpadu</li>
                                    <li>Audit trail & riwayat perubahan</li>
                                    <li>Notifikasi stok & approval level</li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border bg-light h-100 p-3">
                                <h6 class="fw-bold text-dark"><i class="bi bi-lightning-charge text-warning me-2"></i>Aksi Cepat:</h6>
                                <div class="d-grid gap-2 mt-2">
                                    <button class="btn btn-primary btn-sm" onclick="Swal.fire('Info', 'Formulir data baru dapat dibuka di tahap implementasi spesifik.', 'info')">
                                        <i class="bi bi-plus-circle me-1"></i> Tambah Data Baru
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="Swal.fire('Ekspor', 'Fitur ekspor Excel siap digunakan.', 'success')">
                                        <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
