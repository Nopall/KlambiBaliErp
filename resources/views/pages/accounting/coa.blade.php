@extends('adminlte::page')

@section('title', 'Chart of Accounts (COA) - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-diagram-3 text-primary me-2"></i>Bagan Akun Standar (Chart of Accounts)
                <span class="text-secondary fw-normal fs-5">/ Standar Akuntansi Keuangan (SAK ETAP)</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Accounting & Laporan</li>
                    <li class="breadcrumb-item active" aria-current="page">Chart of Accounts</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('accounting.journal') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-journal-bookmark me-1"></i> Jurnal Umum
            </a>
            <a href="{{ route('accounting.general-ledger') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-journal-text me-1"></i> Buku Besar
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-speedometer2"></i>
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- SUMMARY RINGKASAN SALDO PER KELOMPOK AKUN --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">1. Total Aset</span>
                    <h5 class="fw-bold mb-0 text-primary">Rp {{ number_format($totalAssets, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">2. Total Kewajiban</span>
                    <h5 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalLiabilities, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">3. Total Ekuitas</span>
                    <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($totalEquity, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">4. Pendapatan</span>
                    <h5 class="fw-bold mb-0 text-info">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">5. Total HPP</span>
                    <h5 class="fw-bold mb-0 text-warning">Rp {{ number_format($totalCogs, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">6. Beban Usaha</span>
                    <h5 class="fw-bold mb-0 text-secondary">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL CHART OF ACCOUNTS --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-list-columns-reverse text-primary me-2"></i>Daftar Master Chart of Accounts (33 Akun Standar)
                </h5>
                <span class="text-muted small">Hierarki 5 Level Akun: Aset, Kewajiban, Ekuitas, Pendapatan, HPP, dan Beban</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('accounting.coa') }}" class="btn btn-sm {{ empty($categoryFilter) ? 'btn-primary' : 'btn-outline-primary' }}">Semua</a>
                <a href="{{ route('accounting.coa', ['category' => 'asset']) }}" class="btn btn-sm {{ $categoryFilter === 'asset' ? 'btn-primary' : 'btn-outline-primary' }}">1. Aset</a>
                <a href="{{ route('accounting.coa', ['category' => 'liability']) }}" class="btn btn-sm {{ $categoryFilter === 'liability' ? 'btn-primary' : 'btn-outline-primary' }}">2. Kewajiban</a>
                <a href="{{ route('accounting.coa', ['category' => 'equity']) }}" class="btn btn-sm {{ $categoryFilter === 'equity' ? 'btn-primary' : 'btn-outline-primary' }}">3. Ekuitas</a>
                <a href="{{ route('accounting.coa', ['category' => 'revenue']) }}" class="btn btn-sm {{ $categoryFilter === 'revenue' ? 'btn-primary' : 'btn-outline-primary' }}">4. Pendapatan</a>
                <a href="{{ route('accounting.coa', ['category' => 'cogs']) }}" class="btn btn-sm {{ $categoryFilter === 'cogs' ? 'btn-primary' : 'btn-outline-primary' }}">5. HPP</a>
                <a href="{{ route('accounting.coa', ['category' => 'expense']) }}" class="btn btn-sm {{ $categoryFilter === 'expense' ? 'btn-primary' : 'btn-outline-primary' }}">6. Beban</a>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="100">Kode Akun</th>
                        <th>Nama Akun</th>
                        <th>Kategori Standar</th>
                        <th class="text-center">Posisi Normal</th>
                        <th class="text-end">Saldo Awal</th>
                        <th class="text-end">Saldo Berjalan</th>
                        <th class="text-center">Tipe Kas/Bank</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $acc)
                        <tr>
                            <td><span class="fw-bold font-monospace text-primary fs-6">{{ $acc->code }}</span></td>
                            <td class="fw-semibold text-dark">{{ $acc->name }}</td>
                            <td>
                                @php
                                    $badgeClass = match($acc->category) {
                                        'asset' => 'primary',
                                        'liability' => 'danger',
                                        'equity' => 'success',
                                        'revenue' => 'info text-dark',
                                        'cogs' => 'warning text-dark',
                                        'expense' => 'secondary',
                                        default => 'light text-dark'
                                    };
                                    $catName = match($acc->category) {
                                        'asset' => '1. ASET',
                                        'liability' => '2. KEWAJIBAN',
                                        'equity' => '3. EKUITAS',
                                        'revenue' => '4. PENDAPATAN',
                                        'cogs' => '5. HPP',
                                        'expense' => '6. BEBAN OPERASIONAL',
                                        default => strtoupper($acc->category)
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $catName }}</span>
                            </td>
                            <td class="text-center">
                                @if($acc->normal_balance === 'debit')
                                    <span class="badge bg-light text-primary border">DEBIT (D)</span>
                                @else
                                    <span class="badge bg-light text-success border">KREDIT (K)</span>
                                @endif
                            </td>
                            <td class="text-end small">
                                Rp {{ number_format($acc->opening_balance, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold {{ $acc->current_balance < 0 ? 'text-danger' : 'text-dark' }}">
                                Rp {{ number_format($acc->current_balance, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if($acc->is_cash_bank)
                                    <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-cash me-1"></i>Kas / Bank</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('accounting.general-ledger', ['account_id' => $acc->id]) }}" class="btn btn-outline-secondary btn-sm" title="Lihat Buku Besar">
                                    <i class="bi bi-journal-text me-1"></i> Buku Besar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
