@extends('adminlte::page')

@section('title', 'Laporan Laba Rugi Komprehensif - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-graph-up-arrow text-primary me-2"></i>Laporan Laba Rugi Komprehensif
                <span class="text-secondary fw-normal fs-5">/ Multi-Step SAK ETAP (4 Channel & Rincian HPP)</span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Accounting & Laporan</li>
                    <li class="breadcrumb-item active" aria-current="page">Laporan Laba Rugi</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Laba Rugi
            </button>
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
    {{-- SUMMARY METRICS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 text-primary">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Total Pendapatan Bersih</span>
                        <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 text-warning">
                        <i class="bi bi-calculator fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Beban Pokok Penjualan (HPP)</span>
                        <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalCogs, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3 text-info">
                        <i class="bi bi-pie-chart fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Laba Kotor (Gross Profit)</span>
                        <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($grossProfit, 0, ',', '.') }}</h4>
                        <small class="text-success fw-bold">Margin {{ number_format($grossMarginPercent, 1) }}%</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 text-success">
                        <i class="bi bi-trophy fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Laba Bersih Usaha (Net Profit)</span>
                        <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($netProfit, 0, ',', '.') }}</h4>
                        <small class="text-success fw-bold">Net Margin {{ number_format($netMarginPercent, 1) }}%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KERTAS KERJA LAPORAN LABA RUGI MULTI-STEP --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-4 border-0 text-center">
            <h4 class="fw-bold text-dark mb-1">KLAMBIBALI INDONESIA</h4>
            <h5 class="fw-bold text-primary mb-1">LAPORAN LABA RUGI KOMPREHENSIF (MULTI-STEP)</h5>
            <span class="text-muted small">
                Periode Berjalan: {{ \Carbon\Carbon::parse($dateFrom)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($dateTo)->format('d F Y') }}
            </span>
        </div>

        <div class="card-body p-4 pt-0">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <tbody>
                        {{-- 1. PENDAPATAN USAHA --}}
                        <tr class="table-light">
                            <td colspan="2" class="fw-bold text-dark fs-6 py-2">
                                <i class="bi bi-bag-check me-2 text-primary"></i>1. PENDAPATAN USAHA (OMNICHANNEL 4 CHANNEL)
                            </td>
                            <td class="text-end fw-bold text-dark py-2"></td>
                        </tr>
                        @foreach($revenueAccounts as $rev)
                            <tr>
                                <td width="100" class="font-monospace text-muted ps-4">{{ $rev->code }}</td>
                                <td>{{ $rev->name }}</td>
                                <td class="text-end fw-semibold" width="220">
                                    Rp {{ number_format($rev->calculated_balance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="border-top border-bottom">
                            <td colspan="2" class="fw-bold text-dark ps-4">TOTAL PENDAPATAN BERSIH USAHA:</td>
                            <td class="text-end fw-bold text-primary fs-6">
                                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- SPACING --}}
                        <tr><td colspan="3" class="py-2"></td></tr>

                        {{-- 2. HARGA POKOK PENJUALAN (HPP) --}}
                        <tr class="table-light">
                            <td colspan="2" class="fw-bold text-dark fs-6 py-2">
                                <i class="bi bi-scissors me-2 text-warning"></i>2. HARGA POKOK PENJUALAN (RINCIAN HPP BIAYA PRODUKSI)
                            </td>
                            <td class="text-end fw-bold text-dark py-2"></td>
                        </tr>
                        @foreach($cogsAccounts as $cog)
                            <tr>
                                <td class="font-monospace text-muted ps-4">{{ $cog->code }}</td>
                                <td>{{ $cog->name }}</td>
                                <td class="text-end fw-semibold text-danger">
                                    (Rp {{ number_format($cog->calculated_balance, 0, ',', '.') }})
                                </td>
                            </tr>
                        @endforeach
                        <tr class="border-top border-bottom">
                            <td colspan="2" class="fw-bold text-dark ps-4">TOTAL HARGA POKOK PENJUALAN (HPP):</td>
                            <td class="text-end fw-bold text-danger fs-6">
                                (Rp {{ number_format($totalCogs, 0, ',', '.') }})
                            </td>
                        </tr>

                        {{-- SPACING --}}
                        <tr><td colspan="3" class="py-2"></td></tr>

                        {{-- 3. LABA KOTOR --}}
                        <tr class="table-warning bg-opacity-25 border-top border-bottom">
                            <td colspan="2" class="fw-bold text-dark fs-6 py-3 ps-3">
                                <i class="bi bi-check-circle me-2 text-warning"></i>LABA KOTOR (GROSS PROFIT)
                            </td>
                            <td class="text-end fw-bold text-dark fs-5 py-3">
                                Rp {{ number_format($grossProfit, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- SPACING --}}
                        <tr><td colspan="3" class="py-2"></td></tr>

                        {{-- 4. BEBAN OPERASIONAL --}}
                        <tr class="table-light">
                            <td colspan="2" class="fw-bold text-dark fs-6 py-2">
                                <i class="bi bi-cash-coin me-2 text-secondary"></i>3. BEBAN OPERASIONAL & UMUM
                            </td>
                            <td class="text-end fw-bold text-dark py-2"></td>
                        </tr>
                        @foreach($expenseAccounts as $exp)
                            <tr>
                                <td class="font-monospace text-muted ps-4">{{ $exp->code }}</td>
                                <td>{{ $exp->name }}</td>
                                <td class="text-end fw-semibold text-danger">
                                    (Rp {{ number_format($exp->calculated_balance, 0, ',', '.') }})
                                </td>
                            </tr>
                        @endforeach
                        <tr class="border-top border-bottom">
                            <td colspan="2" class="fw-bold text-dark ps-4">TOTAL BEBAN OPERASIONAL:</td>
                            <td class="text-end fw-bold text-danger fs-6">
                                (Rp {{ number_format($totalExpense, 0, ',', '.') }})
                            </td>
                        </tr>

                        {{-- SPACING --}}
                        <tr><td colspan="3" class="py-3"></td></tr>

                        {{-- 5. LABA BERSIH USAHA --}}
                        <tr class="table-success bg-opacity-50 border-2 border-success">
                            <td colspan="2" class="fw-bold text-dark fs-5 py-3 ps-3">
                                <i class="bi bi-trophy-fill me-2 text-success"></i>LABA BERSIH USAHA (NET OPERATING PROFIT)
                            </td>
                            <td class="text-end fw-bold text-success fs-4 py-3">
                                Rp {{ number_format($netProfit, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3 border-top text-center text-muted small">
            Dokumen Keuangan Resmi Terverifikasi Sesuai SAK ETAP - KlambiBali ERP
        </div>
    </div>
@stop
