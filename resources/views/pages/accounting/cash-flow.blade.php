@extends('adminlte::page')

@section('title', 'Laporan Arus Kas (Cash Flow) - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-cash-stack text-primary me-2"></i>Laporan Arus Kas (Cash Flow Statement)
                <span class="text-secondary fw-normal fs-5">/ Metode Langsung (Operasional Kas & Bank)</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Accounting & Laporan</li>
                    <li class="breadcrumb-item active" aria-current="page">Laporan Arus Kas</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Arus Kas
            </button>
            <a href="{{ route('accounting.laba-rugi') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-graph-up-arrow me-1"></i> Laba Rugi
            </a>
            <a href="{{ route('accounting.neraca') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-scale me-1"></i> Neraca
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-speedometer2"></i>
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- SUMMARY METRICS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Saldo Kas Awal Periode</span>
                    <h5 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalCashBeginning, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Total Kas Masuk Operasi</span>
                    <h5 class="fw-bold mb-0 text-success">+Rp {{ number_format($totalCashIn, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Total Kas Keluar Operasi</span>
                    <h5 class="fw-bold mb-0 text-danger">-Rp {{ number_format($totalCashOut, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Saldo Kas Akhir Periode</span>
                    <h5 class="fw-bold mb-0 text-primary">Rp {{ number_format($totalCashEnding, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- KERTAS KERJA LAPORAN ARUS KAS --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-4 border-0 text-center">
            <h4 class="fw-bold text-dark mb-1">KLAMBIBALI INDONESIA</h4>
            <h5 class="fw-bold text-primary mb-1">LAPORAN ARUS KAS (METODE LANGSUNG)</h5>
            <span class="text-muted small">
                Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($dateTo)->format('d F Y') }}
            </span>
        </div>

        <div class="card-body p-4 pt-0">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <tbody>
                        {{-- 1. ARUS KAS DARI AKTIVITAS OPERASI --}}
                        <tr class="table-light">
                            <td colspan="2" class="fw-bold text-dark fs-6 py-2">
                                <i class="bi bi-briefcase me-2 text-primary"></i>1. ARUS KAS DARI AKTIVITAS OPERASI
                            </td>
                            <td class="text-end fw-bold text-dark py-2"></td>
                        </tr>

                        {{-- Kas Masuk --}}
                        <tr>
                            <td colspan="2" class="fw-semibold text-success ps-4">&bull; Arus Kas Masuk Operasional:</td>
                            <td></td>
                        </tr>
                        @foreach($cashInOperating as $in)
                            <tr>
                                <td width="50"></td>
                                <td>{{ $in['title'] }}</td>
                                <td class="text-end fw-semibold text-success" width="220">
                                    Rp {{ number_format($in['amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach

                        {{-- Kas Keluar --}}
                        <tr>
                            <td colspan="2" class="fw-semibold text-danger ps-4 pt-3">&bull; Arus Kas Keluar Operasional:</td>
                            <td></td>
                        </tr>
                        @foreach($cashOutOperating as $out)
                            <tr>
                                <td></td>
                                <td>{{ $out['title'] }}</td>
                                <td class="text-end fw-semibold text-danger">
                                    (Rp {{ number_format($out['amount'], 0, ',', '.') }})
                                </td>
                            </tr>
                        @endforeach

                        <tr class="border-top border-bottom">
                            <td colspan="2" class="fw-bold text-dark ps-4">ARUS KAS BERSIH DARI AKTIVITAS OPERASI:</td>
                            <td class="text-end fw-bold text-primary fs-6">
                                Rp {{ number_format($netOperatingCash, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- SPACING --}}
                        <tr><td colspan="3" class="py-2"></td></tr>

                        {{-- 2. REKONSILIASI SALDO KAS & BANK --}}
                        <tr class="table-light">
                            <td colspan="2" class="fw-bold text-dark fs-6 py-2">
                                <i class="bi bi-bank me-2 text-info"></i>2. RINCIAN SALDO KAS & SETARA KAS AKHIR PERIODE
                            </td>
                            <td class="text-end fw-bold text-dark py-2"></td>
                        </tr>
                        @foreach($cashAccounts as $cash)
                            <tr>
                                <td class="font-monospace text-muted ps-4" colspan="2">
                                    [{{ $cash->code }}] {{ $cash->name }}
                                </td>
                                <td class="text-end fw-semibold">
                                    Rp {{ number_format($cash->calculated_balance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach

                        <tr class="border-top border-bottom table-primary bg-opacity-25">
                            <td colspan="2" class="fw-bold text-dark fs-6 ps-4">
                                TOTAL KAS & SETARA KAS AKHIR:
                            </td>
                            <td class="text-end fw-bold text-primary fs-5">
                                Rp {{ number_format($totalCashEnding, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
