@extends('adminlte::page')

@section('title', 'Riwayat Transaksi POS Kasir - KlambiBali ERP')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 fw-bold text-dark">
                <i class="bi bi-receipt-cutoff text-success me-2"></i>Riwayat Transaksi Kasir Toko (POS)
            </h1>
            <p class="text-muted small mb-0">Daftar Transaksi Toko Fisik, Struk & Rekonsiliasi Kasir</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-display me-1"></i> Buka Kasir POS
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-speedometer2"></i>
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-card-checklist text-primary me-2"></i>Data Penjualan Toko Fisik (POS)
            </h5>
            <span class="badge bg-light text-dark border px-3 py-2">
                Total Transaksi: {{ $orders->total() }}
            </span>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>No. Order / Struk</th>
                        <th>Waktu Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Item Terjual</th>
                        <th class="text-center">Metode Bayar</th>
                        <th class="text-end">Total Belanja (Rp)</th>
                        <th>Kasir</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $idx => $ord)
                        <tr>
                            <td class="text-center text-muted small">{{ $orders->firstItem() + $idx }}</td>
                            <td>
                                <span class="fw-bold font-monospace text-primary">{{ $ord->order_number }}</span>
                            </td>
                            <td class="small text-nowrap">
                                <i class="bi bi-clock me-1 text-muted"></i>{{ $ord->created_at ? $ord->created_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $ord->customer?->name ?? 'Pelanggan Umum (Guest)' }}</span>
                                @if($ord->customer?->tier)
                                    <small class="badge bg-info-subtle text-info d-block">{{ $ord->customer->tier }}</small>
                                @endif
                            </td>
                            <td class="small">
                                @foreach($ord->items as $it)
                                    <div>&bull; {{ $it->item_name }} ({{ $it->qty }} pcs)</div>
                                @endforeach
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ strtoupper($ord->payment_method) }}</span>
                            </td>
                            <td class="text-end fw-bold text-dark">
                                Rp {{ number_format($ord->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="small text-muted">
                                <i class="bi bi-person me-1"></i>{{ $ord->user?->name ?? 'Kasir Toko' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>LUNAS</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada riwayat transaksi kasir POS.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <span class="small text-muted">KlambiBali POS System</span>
            {{ $orders->links() }}
        </div>
    </div>
@stop
