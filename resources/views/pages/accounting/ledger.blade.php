@extends('adminlte::page')

@section('title', 'Buku Besar (General Ledger) - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-journal-text text-primary me-2"></i>Buku Besar (General Ledger)
                <span class="text-secondary fw-normal fs-5">/ Riwayat Mutasi per Akun COA</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Accounting & Laporan</li>
                    <li class="breadcrumb-item active" aria-current="page">Buku Besar</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Ledger
            </button>
            <a href="{{ route('accounting.trial-balance') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-calculator me-1"></i> Neraca Saldo
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-speedometer2"></i>
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- FILTER FORM --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('accounting.general-ledger') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-5">
                        <label class="form-label small fw-semibold text-muted">Pilih Akun Chart of Accounts (COA)</label>
                        <select name="account_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ $selectedAccountId == $acc->id ? 'selected' : '' }}>
                                    [{{ $acc->code }}] {{ $acc->name }} ({{ strtoupper($acc->category) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold text-muted">Tanggal Mulai</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm" onchange="this.form.submit()">
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold text-muted">Tanggal Akhir</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm" onchange="this.form.submit()">
                    </div>

                    <div class="col-12 col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100" title="Cari">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- KARTU INFO AKUN TERPILIH --}}
    @if($selectedAccount)
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col">
                        <span class="badge bg-secondary mb-1">{{ strtoupper($selectedAccount->category) }}</span>
                        <span class="badge bg-light text-dark border">Posisi Normal: {{ strtoupper($selectedAccount->normal_balance) }}</span>
                        <h4 class="fw-bold mb-0 text-dark">
                            <span class="text-primary font-monospace">[{{ $selectedAccount->code }}]</span> {{ $selectedAccount->name }}
                        </h4>
                    </div>
                    <div class="col-auto text-end">
                        <span class="small text-muted d-block">Saldo Akhir Berjalan:</span>
                        <h3 class="fw-bold mb-0 {{ $runningBalance < 0 ? 'text-danger' : 'text-primary' }}">
                            Rp {{ number_format($runningBalance, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- SUMMARY METRICS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Saldo Awal Akun</span>
                    <h5 class="fw-bold mb-0 text-dark">Rp {{ number_format($openingBalance, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Total Mutasi Debit</span>
                    <h5 class="fw-bold mb-0 text-primary">Rp {{ number_format($totalDebit, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Total Mutasi Kredit</span>
                    <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($totalCredit, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Saldo Akhir Periode</span>
                    <h5 class="fw-bold mb-0 text-dark">Rp {{ number_format($runningBalance, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL BUKU BESAR MUTASI --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-list-ul text-primary me-2"></i>Buku Besar Transaksi Akun [{{ $selectedAccount?->code }}]
            </h5>
            <span class="badge bg-light text-dark border">
                Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>Tanggal</th>
                        <th>No. Bukti Jurnal</th>
                        <th>Keterangan / Memo</th>
                        <th class="text-end text-primary">Debit (Rp)</th>
                        <th class="text-end text-success">Kredit (Rp)</th>
                        <th class="text-end fw-bold">Saldo Berjalan (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Baris Saldo Awal --}}
                    <tr class="table-light">
                        <td class="text-center text-muted">&bull;</td>
                        <td class="fw-semibold">{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</td>
                        <td><span class="badge bg-secondary">SALDO-AWAL</span></td>
                        <td class="fst-italic text-muted">Saldo Awal Buku Besar Sebelum Periode Ini</td>
                        <td class="text-end text-muted">-</td>
                        <td class="text-end text-muted">-</td>
                        <td class="text-end fw-bold text-dark">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
                    </tr>

                    @forelse($lines as $index => $l)
                        <tr>
                            <td class="text-center text-muted small">{{ $index + 1 }}</td>
                            <td class="small text-nowrap">
                                {{ $l->journalEntry?->entry_date ? $l->journalEntry->entry_date->format('d M Y') : '-' }}
                            </td>
                            <td>
                                <span class="fw-bold font-monospace text-primary">{{ $l->journalEntry?->entry_number ?? '-' }}</span>
                                <small class="text-muted d-block">{{ $l->journalEntry?->reference_type }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $l->journalEntry?->description }}</div>
                                @if($l->memo)
                                    <small class="text-muted">{{ $l->memo }}</small>
                                @endif
                            </td>
                            <td class="text-end fw-semibold {{ (float)$l->debit > 0 ? 'text-primary' : 'text-muted' }}">
                                {{ (float)$l->debit > 0 ? number_format($l->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end fw-semibold {{ (float)$l->credit > 0 ? 'text-success' : 'text-muted' }}">
                                {{ (float)$l->credit > 0 ? number_format($l->credit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end fw-bold {{ $l->running_balance < 0 ? 'text-danger' : 'text-dark' }}">
                                Rp {{ number_format($l->running_balance, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Tidak ada mutasi transaksi pada rentang tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">TOTAL MUTASI PERIODE & SALDO AKHIR:</td>
                        <td class="text-end text-primary">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                        <td class="text-end text-dark">Rp {{ number_format($runningBalance, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop
