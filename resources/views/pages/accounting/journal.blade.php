@extends('adminlte::page')

@section('title', 'Jurnal Umum Transaksi Otomatis - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-journal-bookmark text-primary me-2"></i>Jurnal Umum (General Journal)
                <span class="text-secondary fw-normal fs-5">/ Pencatatan Berpasangan (Double-Entry Bookkeeping)</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Accounting & Laporan</li>
                    <li class="breadcrumb-item active" aria-current="page">Jurnal Umum</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('accounting.trial-balance') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-calculator me-1"></i> Neraca Saldo
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
    {{-- STATUS KESEIMBANGAN JURNAL --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle {{ $isAllBalanced ? 'bg-success' : 'bg-danger' }} bg-opacity-10 p-3 me-3 {{ $isAllBalanced ? 'text-success' : 'text-danger' }}">
                            <i class="bi {{ $isAllBalanced ? 'bi-check-circle-fill' : 'bi-exclamation-octagon-fill' }} fs-2"></i>
                        </div>
                        <div>
                            <span class="badge {{ $isAllBalanced ? 'bg-success' : 'bg-danger' }} mb-1">
                                {{ $isAllBalanced ? 'STATUS: DOUBLE-ENTRY BALANCE' : 'STATUS: TIDAK SEIMBANG' }}
                            </span>
                            <h5 class="fw-bold mb-0 text-dark">
                                {{ $isAllBalanced ? 'Seluruh Entri Jurnal Seimbang (Total Debit = Total Kredit)' : 'Terdapat Selisih pada Entri Jurnal' }}
                            </h5>
                            <span class="small text-muted">Setiap transaksi di KlambiBali ERP terposting otomatis ke jurnal berpasangan</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <div class="d-inline-block p-3 rounded-3 bg-light border text-start">
                        <div class="d-flex justify-content-between gap-4 mb-1">
                            <span class="small text-muted">Total Seluruh Debit:</span>
                            <span class="fw-bold text-primary">Rp {{ number_format($totalDebitAll, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between gap-4">
                            <span class="small text-muted">Total Seluruh Kredit:</span>
                            <span class="fw-bold text-success">Rp {{ number_format($totalCreditAll, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('accounting.journal') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari nomor jurnal, referensi, atau uraian...">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">Dari</span>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">Sampai</span>
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-filter me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DAFTAR TRANSAKSI JURNAL --}}
    <div class="space-y-4">
        @forelse($journals as $j)
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fw-bold font-monospace text-primary fs-6">{{ $j->entry_number }}</span>
                            <span class="badge bg-secondary">{{ $j->reference_type }}</span>
                            <span class="badge bg-light text-dark border font-monospace">{{ $j->reference_number }}</span>
                            <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check2-circle me-1"></i>POSTED</span>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">{{ $j->description }}</h6>
                    </div>
                    <div class="text-end">
                        <span class="small text-muted d-block">
                            <i class="bi bi-calendar3 me-1"></i>{{ $j->entry_date ? $j->entry_date->format('d F Y') : '-' }}
                        </span>
                        <span class="small text-muted">
                            <i class="bi bi-person me-1"></i>{{ $j->user?->name ?? 'Sistem Otomatis' }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-0 table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="120">Kode Akun</th>
                                <th>Nama Akun COA</th>
                                <th>Memo / Keterangan Baris</th>
                                <th width="180" class="text-end">Debit (Rp)</th>
                                <th width="180" class="text-end">Kredit (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($j->lines as $line)
                                <tr>
                                    <td><span class="font-monospace fw-bold text-dark">{{ $line->account?->code ?? '-' }}</span></td>
                                    <td class="{{ (float)$line->credit > 0 ? 'ps-4' : 'fw-semibold text-dark' }}">
                                        {{ (float)$line->credit > 0 ? '↳ ' : '' }}{{ $line->account?->name ?? '-' }}
                                    </td>
                                    <td class="small text-muted">{{ $line->memo ?? '-' }}</td>
                                    <td class="text-end fw-semibold {{ (float)$line->debit > 0 ? 'text-primary' : 'text-muted' }}">
                                        {{ (float)$line->debit > 0 ? number_format($line->debit, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-end fw-semibold {{ (float)$line->credit > 0 ? 'text-success' : 'text-muted' }}">
                                        {{ (float)$line->credit > 0 ? number_format($line->credit, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-end">TOTAL ENTRI JURNAL:</td>
                                <td class="text-end text-primary">Rp {{ number_format($j->total_debit, 0, ',', '.') }}</td>
                                <td class="text-end text-success">Rp {{ number_format($j->total_credit, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm rounded-3 py-5 text-center text-muted">
                <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                Tidak ada entri jurnal yang ditemukan pada periode ini.
            </div>
        @endforelse
    </div>
@stop
