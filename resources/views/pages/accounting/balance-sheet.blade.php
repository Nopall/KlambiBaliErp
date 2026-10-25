@extends('adminlte::page')

@section('title', 'Neraca Keuangan (Balance Sheet) - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-scale text-primary me-2"></i>Neraca Keuangan (Balance Sheet)
                <span class="text-secondary fw-normal fs-5">/ Persamaan Akuntansi (Aset = Kewajiban + Ekuitas)</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Accounting & Laporan</li>
                    <li class="breadcrumb-item active" aria-current="page">Neraca Keuangan</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Neraca
            </button>
            <a href="{{ route('accounting.laba-rugi') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-graph-up-arrow me-1"></i> Laba Rugi
            </a>
            <a href="{{ route('accounting.cash-flow') }}" class="btn btn-outline-info btn-sm">
                <i class="bi bi-cash-stack me-1"></i> Arus Kas
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-speedometer2"></i>
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- STATUS PERSAMAAN DASAR AKUNTANSI (ASET = KEWAJIBAN + EKUITAS) --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle {{ $isBalanced ? 'bg-success' : 'bg-danger' }} bg-opacity-10 p-3 me-3 {{ $isBalanced ? 'text-success' : 'text-danger' }}">
                            <i class="bi {{ $isBalanced ? 'bi-shield-check' : 'bi-exclamation-triangle' }} fs-1"></i>
                        </div>
                        <div>
                            <span class="badge {{ $isBalanced ? 'bg-success' : 'bg-danger' }} mb-1">
                                {{ $isBalanced ? 'STATUS: BALANCE (SEIMBANG)' : 'STATUS: TIDAK SEIMBANG' }}
                            </span>
                            <h4 class="fw-bold mb-0 text-dark">
                                {{ $isBalanced ? 'Aset = Kewajiban + Ekuitas' : 'Terdapat Selisih Pada Neraca' }}
                            </h4>
                            <span class="small text-muted">Kaidah Standar Akuntansi Keuangan Entitas Tanpa Akuntabilitas Publik (SAK ETAP)</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <div class="d-inline-flex gap-4 p-3 bg-light rounded-3 border text-start">
                        <div>
                            <span class="small text-muted d-block">TOTAL ASET:</span>
                            <h5 class="fw-bold text-primary mb-0">Rp {{ number_format($totalAssets, 0, ',', '.') }}</h5>
                        </div>
                        <div class="vr"></div>
                        <div>
                            <span class="small text-muted d-block">TOTAL KEWAJIBAN + EKUITAS:</span>
                            <h5 class="fw-bold text-success mb-0">Rp {{ number_format($totalLiabilitiesAndEquity, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FORMAT SKONTRO 2 KOLOM (KIRI: ASET, KANAN: KEWAJIBAN & EKUITAS) --}}
    <div class="row g-4">
        {{-- SISI KIRI: ASET --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-header bg-primary bg-opacity-10 py-3 border-0">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-wallet2 me-2"></i>AKTIVA / ASET
                    </h5>
                </div>

                <div class="card-body p-4">
                    {{-- 1. ASET LANCAR --}}
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-cash-coin me-1 text-primary"></i>A. ASET LANCAR (CURRENT ASSETS)
                    </h6>
                    <table class="table table-sm table-borderless align-middle mb-4">
                        <tbody>
                            @foreach($currentAssets as $item)
                                <tr>
                                    <td width="90" class="font-monospace text-muted small">{{ $item['account']->code }}</td>
                                    <td class="small">{{ $item['account']->name }}</td>
                                    <td class="text-end fw-semibold small" width="160">
                                        Rp {{ number_format($item['balance'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- 2. ASET TETAP --}}
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-building-gear me-1 text-primary"></i>B. ASET TETAP (FIXED ASSETS)
                    </h6>
                    <table class="table table-sm table-borderless align-middle mb-4">
                        <tbody>
                            @foreach($fixedAssets as $item)
                                <tr>
                                    <td width="90" class="font-monospace text-muted small">{{ $item['account']->code }}</td>
                                    <td class="small">{{ $item['account']->name }}</td>
                                    <td class="text-end fw-semibold small {{ $item['balance'] < 0 ? 'text-danger' : '' }}" width="160">
                                        {{ $item['balance'] < 0 ? '(Rp '.number_format(abs($item['balance']), 0, ',', '.').')' : 'Rp '.number_format($item['balance'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-primary bg-opacity-10 py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary">TOTAL AKTIVA (ASET):</h6>
                    <h5 class="fw-bold mb-0 text-primary">Rp {{ number_format($totalAssets, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        {{-- SISI KANAN: KEWAJIBAN & EKUITAS --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-header bg-success bg-opacity-10 py-3 border-0">
                    <h5 class="mb-0 fw-bold text-success">
                        <i class="bi bi-shield-check me-2"></i>PASIVA (KEWAJIBAN & EKUITAS)
                    </h5>
                </div>

                <div class="card-body p-4">
                    {{-- 1. KEWAJIBAN LANCAR --}}
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-receipt me-1 text-danger"></i>A. KEWAJIBAN LANCAR (LIABILITIES)
                    </h6>
                    <table class="table table-sm table-borderless align-middle mb-4">
                        <tbody>
                            @foreach($liabilities as $item)
                                <tr>
                                    <td width="90" class="font-monospace text-muted small">{{ $item['account']->code }}</td>
                                    <td class="small">{{ $item['account']->name }}</td>
                                    <td class="text-end fw-semibold small" width="160">
                                        Rp {{ number_format($item['balance'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="border-top">
                                <td colspan="2" class="fw-bold small ps-2">Total Kewajiban:</td>
                                <td class="text-end fw-bold text-danger small">
                                    Rp {{ number_format($totalLiabilities, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- 2. EKUITAS --}}
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-gem me-1 text-success"></i>B. EKUITAS PEMILIK (OWNER EQUITY)
                    </h6>
                    <table class="table table-sm table-borderless align-middle mb-4">
                        <tbody>
                            @foreach($equities as $item)
                                <tr>
                                    <td width="90" class="font-monospace text-muted small">{{ $item['account']->code }}</td>
                                    <td class="small">{{ $item['account']->name }}</td>
                                    <td class="text-end fw-semibold small" width="160">
                                        Rp {{ number_format($item['balance'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="border-top">
                                <td colspan="2" class="fw-bold small ps-2">Total Ekuitas:</td>
                                <td class="text-end fw-bold text-success small">
                                    Rp {{ number_format($totalEquity, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-success bg-opacity-10 py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-success">TOTAL PASIVA (KEWAJIBAN + EKUITAS):</h6>
                    <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($totalLiabilitiesAndEquity, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>
@stop
