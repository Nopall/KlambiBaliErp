<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Auth can be applied or left open for initial review
    }

    /**
     * Show the application dashboard.
     */
    public function index(): View
    {
        // Metrics data for KlambiBali ERP Dashboard
        $metrics = [
            'penjualan_bulan_ini' => 142850000,
            'pembelian_bulan_ini' => 68400000,
            'total_stok_jadi' => 1845,
            'produksi_aktif' => 12,
            'utang_supplier' => 18500000,
            'piutang_pelanggan' => 24150000,
            'pesanan_marketplace' => 46,
            'qc_pending' => 28,
        ];

        // Sales & Purchasing Trend Data (Last 6 Months)
        $chartTrend = [
            'labels' => ['Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober'],
            'sales' => [95, 110, 125, 138, 132, 142],
            'purchases' => [45, 52, 60, 58, 64, 68],
        ];

        // Multi-warehouse Stock Distribution
        $warehouseStock = [
            ['name' => 'Gudang Bahan Baku (Denpasar)', 'capacity' => '82%', 'items' => 450, 'status' => 'Optimal', 'color' => 'success'],
            ['name' => 'Gudang Produksi & Cutting', 'capacity' => '65%', 'items' => 310, 'status' => 'Berjalan', 'color' => 'primary'],
            ['name' => 'Gudang Barang Jadi (Kuta)', 'capacity' => '90%', 'items' => 1240, 'status' => 'Hampir Penuh', 'color' => 'warning'],
            ['name' => 'Gudang Display Toko / POS', 'capacity' => '48%', 'items' => 295, 'status' => 'Siap Jual', 'color' => 'info'],
        ];

        // Recent Work Orders
        $recentWorkOrders = [
            ['code' => 'WO-2026-1001', 'product' => 'Kebaya Bali Bordir Premium - Maroon', 'qty' => 50, 'target' => '28 Okt 2026', 'status' => 'Cutting', 'badge' => 'info'],
            ['code' => 'WO-2026-1002', 'product' => 'Kamen Endek Tenun Sutra - Hitam Emas', 'qty' => 100, 'target' => '30 Okt 2026', 'status' => 'Jahit', 'badge' => 'primary'],
            ['code' => 'WO-2026-1003', 'product' => 'Kemeja Safari Pria Katun Jepang - Putih', 'qty' => 75, 'target' => '02 Nov 2026', 'status' => 'Finishing & QC', 'badge' => 'warning'],
            ['code' => 'WO-2026-1004', 'product' => 'Selendang Obi Tradisional Motif Songket', 'qty' => 200, 'target' => '05 Nov 2026', 'status' => 'Siap Packing', 'badge' => 'success'],
        ];

        // Recent Sales Transactions (Omnichannel: Toko, POS, Shopee, TikTok Shop)
        $recentTransactions = [
            ['invoice' => 'INV-202610-089', 'channel' => 'Toko Kasir POS', 'customer' => 'Ni Wayan Sukmawati', 'total' => 1250000, 'payment' => 'QRIS', 'status' => 'Lunas'],
            ['invoice' => 'INV-202610-090', 'channel' => 'Shopee Marketplace', 'customer' => 'Dewi Lestari', 'total' => 680000, 'payment' => 'ShopeePay', 'status' => 'Diproses'],
            ['invoice' => 'INV-202610-091', 'channel' => 'TikTok Shop', 'customer' => 'Bagus Anom', 'total' => 450000, 'payment' => 'COD', 'status' => 'Dikirim'],
            ['invoice' => 'INV-202610-092', 'channel' => 'Pesanan Grosir B2B', 'customer' => 'Butik Sanur Indah', 'total' => 8400000, 'payment' => 'Transfer Bank', 'status' => 'DP 50%'],
            ['invoice' => 'INV-202610-093', 'channel' => 'Toko Kasir POS', 'customer' => 'I Made Suardana', 'total' => 920000, 'payment' => 'Tunai', 'status' => 'Lunas'],
        ];

        return view('home', compact(
            'metrics',
            'chartTrend',
            'warehouseStock',
            'recentWorkOrders',
            'recentTransactions'
        ));
    }
}
