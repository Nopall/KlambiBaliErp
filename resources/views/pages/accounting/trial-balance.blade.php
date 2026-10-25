@extends('adminlte::page')

@section('title', 'Neraca Saldo (Trial Balance) - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-calculator text-primary me-2"></i>Neraca Saldo (Trial Balance)
                <span class="text-secondary fw-normal fs-5">/ Uji Keseimbangan Debit & Kredit Seluruh Akun</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Accounting & Laporan</li>
                    <li class="breadcrumb-item active" aria-current="page">Neraca Saldo</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Neraca Saldo
            </button>
            <a href="{{ route('accounting.laba-rugi') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-graph-up-arrow me-1"></i> Laporan Laba Rugi
            </a>
            <a href="{{ route('accounting.neraca') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-scale me-1"></i> Neraca Keuangan
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-speedometer2"></i>
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- STATUS SEIMBANG --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-7">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle {{ $isBalanced ? 'bg-success' : 'bg-danger' }} bg-opacity-10 p-3 me-3 {{ $isBalanced ? 'text-success' : 'text-danger' }}">
                            <i class="bi {{ $isBalanced ? 'bi-check-all' : 'bi-exclamation-triangle' }} fs-1"></i>
                        </div>
                        <div>
                            <span class="badge {{ $isBalanced ? 'bg-success' : 'bg-danger' }} mb-1">
                                {{ $isBalanced ? 'NERACA SALDO SEIMBANG (PERFECT BALANCE)' : 'TERDAPAT SELISIH KESEIMBANGAN' }}
                            </span>
                            <h4 class="fw-bold mb-0 text-dark">
                                {{ $isBalanced ? 'Total Saldo Debit = Total Saldo Kredit' : 'Periksa Entri Jurnal Tidak Seimbang' }}
                            </h4>
                            <span class="small text-muted">Kaidah Standar Akuntansi Keuangan (SAK ETAP) terpenuhi 100%</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-5 text-md-end">
                    <div class="p-3 bg-light rounded-3 border d-inline-block text-start">
                        <div class="d-flex justify-content-between gap-4 mb-1">
                            <span class="text-muted small">Total Saldo Akhir Debit:</span>
                            <span class="fw-bold text-primary">Rp {{ number_format($totalDebitEnding, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between gap-4 mb-1">
                            <span class="text-muted small">Total Saldo Akhir Kredit:</span>
                            <span class="fw-bold text-success">Rp {{ number_format($totalCreditEnding, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between gap-4 border-top pt-1">
                            <span class="text-muted small">Selisih (Variance):</span>
                            <span class="fw-bold {{ $isBalanced ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format(abs($totalDebitEnding - $totalCreditEnding), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER PERIODE --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('accounting.trial-balance') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">Periode Dari</span>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">Sampai Dengan</span>
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-arrow-repeat me-1"></i> Hitung Ulang
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL NERACA SALDO --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-table text-primary me-2"></i>Neraca Saldo Penutupan Periode
                </h5>
                <span class="text-muted small">Menampilkan Saldo Awal, Mutasi Debit/Kredit, dan Saldo Akhir Komparatif</span>
            </div>
            <span class="badge bg-light text-dark border">
                33 Akun Chart of Accounts
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="80">Kode</th>
                        <th>Nama Akun</th>
                        <th>Kategori</th>
                        <th class="text-end">Saldo Awal (Rp)</th>
                        <th class="text-end text-primary">Mutasi Debit (Rp)</th>
                        <th class="text-end text-success">Mutasi Kredit (Rp)</th>
                        <th class="text-end bg-primary bg-opacity-10 text-primary">Akhir Debit (Rp)</th>
                        <th class="text-end bg-success bg-opacity-10 text-success">Akhir Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $r)
                        <tr>
                            <td><span class="font-monospace fw-bold text-dark">{{ $r['account']->code }}</span></td>
                            <td class="fw-semibold">
                                <a href="{{ route('accounting.general-ledger', ['account_id' => $r['account']->id]) }}" class="text-decoration-none text-dark">
                                    {{ $r['account']->name }}
                                </a>
                            </td>
                            <td>
                                @php
                                    $catBadge = match($r['account']->category) {
                                        'asset' => 'primary',
                                        'liability' => 'danger',
                                        'equity' => 'success',
                                        'revenue' => 'info text-dark',
                                        'cogs' => 'warning text-dark',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $catBadge }} small">{{ strtoupper($r['account']->category) }}</span>
                            </td>
                            <td class="text-end small">
                                Rp {{ number_format($r['opening_balance'], 0, ',', '.') }}
                            </td>
                            <td class="text-end small {{ $r['period_debit'] > 0 ? 'text-primary fw-semibold' : 'text-muted' }}">
                                {{ $r['period_debit'] > 0 ? number_format($r['period_debit'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end small {{ $r['period_credit'] > 0 ? 'text-success fw-semibold' : 'text-muted' }}">
                                {{ $r['period_credit'] > 0 ? number_format($r['period_credit'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end fw-semibold bg-primary bg-opacity-10 {{ $r['ending_debit'] > 0 ? 'text-primary' : 'text-muted' }}">
                                {{ $r['ending_debit'] > 0 ? number_format($r['ending_debit'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end fw-semibold bg-success bg-opacity-10 {{ $r['ending_credit'] > 0 ? 'text-success' : 'text-muted' }}">
                                {{ $r['ending_credit'] > 0 ? number_format($r['ending_credit'], 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold fs-6">
                    <tr>
                        <td colspan="4" class="text-end">TOTAL NERACA SALDO:</td>
                        <td class="text-end text-primary">Rp {{ number_format($totalDebitMovement, 0, ',', '.') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($totalCreditMovement, 0, ',', '.') }}</td>
                        <td class="text-end bg-primary bg-opacity-10 text-primary">Rp {{ number_format($totalDebitEnding, 0, ',', '.') }}</td>
                        <td class="text-end bg-success bg-opacity-10 text-success">Rp {{ number_format($totalCreditEnding, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop
