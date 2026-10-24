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
        // Metrics data for KlambiBali ERP Dashboard (Sesuai Spesifikasi Dokumen)
        $metrics = [
            'penjualan_bulan_ini' => 142850000,
            'pembelian_bulan_ini' => 68400000,
            'total_stok_jadi' => 1845,
            'produksi_aktif' => 12,
            'utang_supplier' => 18500000, // Termasuk hutang pembelian 100 yard kain
            'piutang_pelanggan' => 24150000,
            'pesanan_marketplace' => 46,
            'qc_pending' => 28, // Stok di Gudang Transit menunggu inspeksi
            'stok_reject_service' => 15, // Stok di Gudang Reject / Service
            'stok_sale_cacat' => 8, // Stok di Gudang Sale (Clearance)
            'nilai_persediaan' => 384500000,
        ];

        // Rincian Penjualan per Channel (Sesuai Bab 1 & 6)
        $channelSales = [
            'shopee' => ['total' => 48200000, 'orders' => 112, 'share' => '33.7%'],
            'tiktok' => ['total' => 36450000, 'orders' => 84, 'share' => '25.5%'],
            'reguler' => ['total' => 32700000, 'orders' => 42, 'share' => '22.9%'],
            'store_pos' => ['total' => 25500000, 'orders' => 68, 'share' => '17.9%'],
        ];

        // Sales & Purchasing Trend Data (Last 6 Months)
        $chartTrend = [
            'labels' => ['Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober'],
            'sales' => [95, 110, 125, 138, 132, 142],
            'purchases' => [45, 52, 60, 58, 64, 68],
        ];

        // 7 Gudang Resmi KlambiBali Sesuai Bab 3 (Gudang & Alur Produksi)
        $warehouseStock = [
            [
                'code' => 'GD-WIP-01',
                'name' => '1. Gudang Penjahit (Raw Material & WIP)',
                'desc' => 'Bahan baku dialokasikan ke penjahit, kain 100 yard, sisa bahan & potongan',
                'capacity' => '72%',
                'items' => 450,
                'unit' => 'yard / WIP',
                'status' => 'Aktif Berjalan',
                'color' => 'primary',
            ],
            [
                'code' => 'GD-TRN-02',
                'name' => '2. Gudang Transit (Menunggu QC)',
                'desc' => 'Produk baru selesai jahit (60 kebaya) antre inspeksi kualitas',
                'capacity' => '45%',
                'items' => 60,
                'unit' => 'pcs kebaya',
                'status' => 'Antre QC',
                'color' => 'warning',
            ],
            [
                'code' => 'GD-RJC-03',
                'name' => '3. Gudang Reject / Service',
                'desc' => 'Produk gagal QC yang bisa diperbaiki (jahit ulang payet / kancing)',
                'capacity' => '20%',
                'items' => 15,
                'unit' => 'pcs service',
                'status' => 'Proses Service',
                'color' => 'danger',
            ],
            [
                'code' => 'GD-SAL-04',
                'name' => '4. Gudang Sale (Barang Cacat)',
                'desc' => 'Produk cacat tenun/sobek non-ekonomis, dijual label cacat / clearance',
                'capacity' => '15%',
                'items' => 8,
                'unit' => 'pcs defect',
                'status' => 'Stok Clearance',
                'color' => 'secondary',
            ],
            [
                'code' => 'GD-UTM-05',
                'name' => '5. Gudang Utama (Finished Goods)',
                'desc' => 'Produk lolos QC (55 pcs), siap barcode dan transfer ke Live/Store',
                'capacity' => '88%',
                'items' => 1240,
                'unit' => 'pcs barcode',
                'status' => 'Siap Distribusi',
                'color' => 'success',
            ],
            [
                'code' => 'GD-LIV-06',
                'name' => '6. Gudang Live (Live Selling)',
                'desc' => 'Alokasi stok untuk live streaming Shopee & TikTok (tanpa stok ganda)',
                'capacity' => '65%',
                'items' => 280,
                'unit' => 'pcs live',
                'status' => 'Siap Live',
                'color' => 'info',
            ],
            [
                'code' => 'GD-STR-07',
                'name' => '7. Gudang Store (Outlet POS Toko)',
                'desc' => 'Stok display toko fisik, terhubung langsung dengan kasir POS',
                'capacity' => '52%',
                'items' => 295,
                'unit' => 'pcs POS',
                'status' => 'Display Kasir',
                'color' => 'success',
            ],
        ];

        // 12 Skenario Demo Account Sesuai Halaman 9 Dokumen PDF
        $demoSteps = [
            ['step' => 1, 'title' => 'Beli 100 Yard Kain & Catat Hutang', 'desc' => 'PO-041: Beli 100 yard kain brokat @ Rp 75.000 ke CV Tekstil Bali. Hutang supplier Rp 7.500.000 dicatat.', 'status' => 'Selesai', 'url' => 'purchasing/po', 'badge' => 'success'],
            ['step' => 2, 'title' => 'Kirim Kain ke Penjahit (WIP)', 'desc' => 'Surat jalan pengeluaran 100 yard kain ke Penjahit Wayan Artana (Gudang Penjahit).', 'status' => 'Selesai', 'url' => 'produksi/pemakaian-bahan', 'badge' => 'success'],
            ['step' => 3, 'title' => 'Produksi 60 Kebaya (Standar 1,56 Yard)', 'desc' => 'WO-001: Standar pemakaian 1,56 yard/kebaya x 60 pcs = 93,60 yard terpakai.', 'status' => 'Selesai', 'url' => 'produksi/work-orders', 'badge' => 'success'],
            ['step' => 4, 'title' => 'Kalkulasi Sisa Kain 6,40 Yard vs Aktual', 'desc' => 'Sisa teoritis: 100 - 93,60 = 6,40 yard. Sisa fisik aktual: 5,80 yard (waste/potongan kain 0,60 yard).', 'status' => 'Selesai', 'url' => 'produksi/pemakaian-bahan', 'badge' => 'success'],
            ['step' => 5, 'title' => 'Perhitungan HPP Kebaya (Rp 185.000)', 'desc' => 'Kain (1,56 yd x 75rb = 117rb) + Ongkos Jahit (45rb) + Aksesoris (18rb) + Lain (5rb) = HPP Rp 185.000.', 'status' => 'Selesai', 'url' => 'produksi/hpp', 'badge' => 'success'],
            ['step' => 6, 'title' => '60 Kebaya Masuk Gudang Transit QC', 'desc' => 'Hasil jahit masuk Gudang Transit untuk inspeksi kualitas oleh tim QC.', 'status' => 'Selesai', 'url' => 'qc/inspeksi', 'badge' => 'success'],
            ['step' => 7, 'title' => 'Pemisahan Hasil QC (Lolos, Service, Sale)', 'desc' => '55 pcs Lolos QC, 3 pcs perlu perbaikan jahit (Gudang Reject/Service), 2 pcs cacat kain (Gudang Sale).', 'status' => 'Selesai', 'url' => 'qc/reject', 'badge' => 'success'],
            ['step' => 8, 'title' => 'Penerbitan Barcode Produk Lolos QC', 'desc' => 'Barcode SKU KB-BRD-MRN-M diterbitkan dan ditempel pada 55 pcs kebaya di Gudang Utama.', 'status' => 'Selesai', 'url' => 'qc/barcode', 'badge' => 'success'],
            ['step' => 9, 'title' => 'Transfer ke Gudang Live & Gudang Store', 'desc' => 'Transfer dari Gudang Utama: 20 pcs ke Gudang Live, 30 pcs ke Gudang Store/POS (sisa 5 pcs).', 'status' => 'Selesai', 'url' => 'inventory/transfer', 'badge' => 'success'],
            ['step' => 10, 'title' => 'Penjualan POS & Reguler (Resi 4 Digit)', 'desc' => 'Transaksi Kasir POS scan barcode, order reguler pencarian 4 digit resi, serta order Shopee & TikTok.', 'status' => 'Selesai', 'url' => 'pos', 'badge' => 'success'],
            ['step' => 11, 'title' => 'Pengurangan Stok & Pembaruan HPP', 'desc' => 'Stok Gudang Store berkurang otomatis, nilai HPP barang terjual tercatat akurat.', 'status' => 'Selesai', 'url' => 'inventory/stok', 'badge' => 'success'],
            ['step' => 12, 'title' => 'Jurnal Accounting & Laporan Keuangan', 'desc' => 'Jurnal otomatis pembelian, produksi, penjualan, hutang supplier, laba rugi, dan persediaan.', 'status' => 'Selesai', 'url' => 'accounting/jurnal', 'badge' => 'success'],
        ];

        // Recent Work Orders (dengan data simulasi kebaya 1,56 yard)
        $recentWorkOrders = [
            ['code' => 'WO-202610-001', 'product' => 'Kebaya Bali Bordir Maroon (BOM: 1,56 yard)', 'qty' => 60, 'target' => '28 Okt 2026', 'status' => 'Selesai & QC', 'badge' => 'success'],
            ['code' => 'WO-202610-002', 'product' => 'Kamen Endek Tenun Sutra - Hitam Emas', 'qty' => 100, 'target' => '30 Okt 2026', 'status' => 'Proses Jahit', 'badge' => 'primary'],
            ['code' => 'WO-202610-003', 'product' => 'Kemeja Safari Pria Katun Jepang - Putih', 'qty' => 75, 'target' => '02 Nov 2026', 'status' => 'Potong & Cutting', 'badge' => 'info'],
            ['code' => 'WO-202610-004', 'product' => 'Selendang Obi Tradisional Motif Songket', 'qty' => 200, 'target' => '05 Nov 2026', 'status' => 'Siap Packing', 'badge' => 'warning'],
        ];

        // Recent Omnichannel Transactions (Shopee, TikTok, Reguler via 4 digit resi, POS)
        $recentTransactions = [
            ['invoice' => 'INV-202610-089', 'channel' => 'Toko Kasir POS', 'ref' => 'Scan Barcode', 'customer' => 'Ni Wayan Sukmawati', 'total' => 1250000, 'payment' => 'Split (Cash+QRIS)', 'status' => 'Lunas'],
            ['invoice' => 'INV-202610-090', 'channel' => 'Shopee Marketplace', 'ref' => 'Resi: ...9821', 'customer' => 'Dewi Lestari (Jakarta)', 'total' => 680000, 'payment' => 'ShopeePay', 'status' => 'Diproses'],
            ['invoice' => 'INV-202610-091', 'channel' => 'TikTok Shop Live', 'ref' => 'Resi: ...4410', 'customer' => 'Bagus Anom (Surabaya)', 'total' => 450000, 'payment' => 'COD', 'status' => 'Dikirim'],
            ['invoice' => 'INV-202610-092', 'channel' => 'Penjualan Reguler B2B', 'ref' => 'Resi: ...1088', 'customer' => 'Butik Sanur Indah', 'total' => 8400000, 'payment' => 'Transfer BCA (DP)', 'status' => 'DP 50%'],
            ['invoice' => 'INV-202610-093', 'channel' => 'Toko Kasir POS', 'ref' => 'Scan Barcode', 'customer' => 'I Made Suardana', 'total' => 920000, 'payment' => 'Debit BCA', 'status' => 'Lunas'],
        ];

        return view('home', compact(
            'metrics',
            'channelSales',
            'chartTrend',
            'warehouseStock',
            'demoSteps',
            'recentWorkOrders',
            'recentTransactions'
        ));
    }
}
