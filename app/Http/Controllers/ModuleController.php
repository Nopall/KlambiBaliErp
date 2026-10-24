<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ModuleController extends Controller
{
    /**
     * Show dummy list and form tailored to the active ERP module/submodule.
     */
    public function show(string $module, ?string $submodule = null): View
    {
        $key = strtolower($module.'/'.($submodule ?? 'index'));

        $config = $this->getModuleConfig($module, $submodule, $key);

        return view('pages.module-dynamic', compact('module', 'submodule', 'config'));
    }

    /**
     * Build rich configuration, dummy data rows, and form fields matching SISTEMKLAMBIBALI.pdf.
     */
    private function getModuleConfig(string $module, ?string $submodule, string $key): array
    {
        $moduleTitle = ucwords(str_replace(['-', '_'], ' ', $module));
        $subTitle = $submodule ? ucwords(str_replace(['-', '_'], ' ', $submodule)) : 'Overview';

        $config = [
            'moduleTitle' => $moduleTitle,
            'subTitle' => $subTitle,
            'headerIcon' => 'bi-folder2-open',
            'summaryCards' => [],
            'columns' => [],
            'rows' => [],
            'formFields' => [],
            'formTitle' => 'Tambah '.$subTitle,
        ];

        switch ($key) {
            // ==========================================
            // 1. MASTER DATA: PRODUK & VARIAN
            // ==========================================
            case 'master/produk':
            case 'master/index':
                $config['headerIcon'] = 'bi-box-seam';
                $config['formTitle'] = 'Form Tambah Produk, SKU & Varian Baru';
                $config['summaryCards'] = [
                    ['title' => 'Total SKU Aktif', 'value' => '142 SKU', 'icon' => 'bi-boxes', 'color' => 'primary'],
                    ['title' => 'Kategori Produk', 'value' => '8 Kategori', 'icon' => 'bi-tags', 'color' => 'info'],
                    ['title' => 'Stok Menipis (<5)', 'value' => '3 Varian', 'icon' => 'bi-exclamation-triangle', 'color' => 'warning'],
                    ['title' => 'Nilai Persediaan FG', 'value' => 'Rp 229.400.000', 'icon' => 'bi-wallet2', 'color' => 'success'],
                ];
                $config['columns'] = [
                    ['key' => 'sku', 'label' => 'Kode SKU'],
                    ['key' => 'barcode', 'label' => 'Barcode'],
                    ['key' => 'name', 'label' => 'Nama Produk & Varian'],
                    ['key' => 'category', 'label' => 'Kategori'],
                    ['key' => 'bom_std', 'label' => 'Standar BOM'],
                    ['key' => 'hpp', 'label' => 'HPP (Modal)'],
                    ['key' => 'price', 'label' => 'Harga Jual'],
                    ['key' => 'stock', 'label' => 'Total Stok'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'sku' => 'KB-BRD-MRN-M', 'barcode' => '899100100101', 'name' => 'Kebaya Bali Bordir Maroon (Size M)', 'category' => 'Kebaya Bali', 'bom_std' => '1,56 Yard Kain', 'hpp' => 'Rp 185.000', 'price' => 'Rp 385.000', 'stock' => '24 pcs', 'status' => 'Tersedia', 'badge' => 'success'],
                    ['id' => 2, 'sku' => 'KB-BRD-MRN-L', 'barcode' => '899100100102', 'name' => 'Kebaya Bali Bordir Maroon (Size L)', 'category' => 'Kebaya Bali', 'bom_std' => '1,62 Yard Kain', 'hpp' => 'Rp 189.500', 'price' => 'Rp 385.000', 'stock' => '18 pcs', 'status' => 'Tersedia', 'badge' => 'success'],
                    ['id' => 3, 'sku' => 'KM-EDK-GLD-ALL', 'barcode' => '899100100103', 'name' => 'Kamen Endek Sutra Motif Wajik Emas', 'category' => 'Kamen & Bawahan', 'bom_std' => '2,50 Meter Kain', 'hpp' => 'Rp 220.000', 'price' => 'Rp 450.000', 'stock' => '15 pcs', 'status' => 'Tersedia', 'badge' => 'success'],
                    ['id' => 4, 'sku' => 'KMJ-SFR-WHT-XL', 'barcode' => '899100100104', 'name' => 'Kemeja Safari Pria Katun Putih (Size XL)', 'category' => 'Kemeja Safari', 'bom_std' => '1,80 Meter Kain', 'hpp' => 'Rp 125.000', 'price' => 'Rp 275.000', 'stock' => '4 pcs', 'status' => 'Menipis', 'badge' => 'warning'],
                    ['id' => 5, 'sku' => 'SLD-OBI-TRD-RED', 'barcode' => '899100100105', 'name' => 'Selendang Obi Songket Tradisional Merah Emas', 'category' => 'Aksesoris', 'bom_std' => '0,65 Yard Kain', 'hpp' => 'Rp 35.000', 'price' => 'Rp 85.000', 'stock' => '65 pcs', 'status' => 'Tersedia', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'sku', 'label' => 'Kode SKU (Unik)', 'type' => 'text', 'placeholder' => 'Contoh: KB-BRD-SGE-M', 'required' => true],
                    ['name' => 'barcode', 'label' => 'Nomor Barcode (EAN-13/Auto)', 'type' => 'text', 'placeholder' => '8991001xxxxx'],
                    ['name' => 'nama_produk', 'label' => 'Nama Produk & Model', 'type' => 'text', 'placeholder' => 'Nama model lengkap', 'required' => true],
                    ['name' => 'kategori', 'label' => 'Kategori Produk', 'type' => 'select', 'options' => ['Kebaya Bali', 'Kamen & Bawahan', 'Kemeja Safari', 'Aksesoris Tradisional', 'Kain Lembaran'], 'required' => true],
                    ['name' => 'warna', 'label' => 'Warna', 'type' => 'text', 'placeholder' => 'Maroon / Sage / Gold'],
                    ['name' => 'ukuran', 'label' => 'Ukuran / Varian', 'type' => 'select', 'options' => ['All Size', 'S', 'M', 'L', 'XL', 'XXL']],
                    ['name' => 'formula_bom', 'label' => 'Standar Pemakaian Bahan (BOM)', 'type' => 'text', 'placeholder' => 'Contoh: 1.56 yard kain brokat'],
                    ['name' => 'hpp', 'label' => 'Estimasi HPP Satuan (Rp)', 'type' => 'number', 'placeholder' => '185000', 'required' => true],
                    ['name' => 'harga_jual', 'label' => 'Harga Jual Satuan (Rp)', 'type' => 'number', 'placeholder' => '385000', 'required' => true],
                ];
                break;

                // ==========================================
                // 1. MASTER DATA: BAHAN BAKU & PENDUKUNG
                // ==========================================
            case 'master/bahan-baku':
                $config['headerIcon'] = 'bi-layers';
                $config['formTitle'] = 'Form Tambah Bahan Baku / Penolong (Mendukung Yard, Meter, Roll)';
                $config['summaryCards'] = [
                    ['title' => 'Stok Kain Brokat', 'value' => '100 Yard', 'icon' => 'bi-box-seam', 'color' => 'primary'],
                    ['title' => 'Stok Kain Endek', 'value' => '115 Meter', 'icon' => 'bi-layers-half', 'color' => 'success'],
                    ['title' => 'Benang & Renda', 'value' => '45 Roll', 'icon' => 'bi-disc', 'color' => 'info'],
                    ['title' => 'Nilai Bahan Baku', 'value' => 'Rp 58.600.000', 'icon' => 'bi-cash', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'code', 'label' => 'Kode Bahan'],
                    ['key' => 'name', 'label' => 'Nama Bahan'],
                    ['key' => 'unit', 'label' => 'Satuan'],
                    ['key' => 'supplier', 'label' => 'Supplier'],
                    ['key' => 'stock', 'label' => 'Stok Fisik'],
                    ['key' => 'unit_price', 'label' => 'Harga Beli Terakhir'],
                    ['key' => 'location', 'label' => 'Lokasi Gudang'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'code' => 'BB-KN-BRKT-01', 'name' => 'Kain Brokat Semi Prancis Maroon (Demo)', 'unit' => 'Yard', 'supplier' => 'CV Tekstil Bali Sejahtera', 'stock' => '100 Yard', 'unit_price' => 'Rp 75.000 / yard', 'location' => 'Gudang Penjahit (WIP)', 'status' => 'Optimal', 'badge' => 'success'],
                    ['id' => 2, 'code' => 'BB-KN-ENDK-02', 'name' => 'Kain Tenun Endek Sutra Klungkung', 'unit' => 'Meter', 'supplier' => 'Tenun Tradisional Sidemen', 'stock' => '115 Meter', 'unit_price' => 'Rp 140.000 / meter', 'location' => 'Gudang Bahan Baku', 'status' => 'Optimal', 'badge' => 'success'],
                    ['id' => 3, 'code' => 'BB-BNG-EMAS-03', 'name' => 'Benang Bordir Metalik Emas Kilap', 'unit' => 'Roll', 'supplier' => 'Toko Benang Sentosa Denpasar', 'stock' => '45 Roll', 'unit_price' => 'Rp 22.000 / roll', 'location' => 'Gudang Penjahit (WIP)', 'status' => 'Optimal', 'badge' => 'success'],
                    ['id' => 4, 'code' => 'BB-KNC-BATU-04', 'name' => 'Kancing Bungkus Motif Emas 12mm', 'unit' => 'Gross', 'supplier' => 'UD Pelengkap Garmen', 'stock' => '8 Gross', 'unit_price' => 'Rp 35.000 / gross', 'location' => 'Gudang Penjahit (WIP)', 'status' => 'Menipis', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'kode_bahan', 'label' => 'Kode Bahan Baku', 'type' => 'text', 'placeholder' => 'BB-KN-KATUN-01', 'required' => true],
                    ['name' => 'nama_bahan', 'label' => 'Nama Bahan', 'type' => 'text', 'placeholder' => 'Nama kain, lace, atau aksesoris', 'required' => true],
                    ['name' => 'satuan', 'label' => 'Satuan Ukuran', 'type' => 'select', 'options' => ['Yard', 'Meter', 'Pcs', 'Roll', 'Gross', 'Set', 'Kilogram'], 'required' => true],
                    ['name' => 'supplier_id', 'label' => 'Supplier Utama', 'type' => 'select', 'options' => ['CV Tekstil Bali Sejahtera', 'Tenun Tradisional Sidemen', 'Toko Benang Sentosa Denpasar', 'UD Pelengkap Garmen']],
                    ['name' => 'harga_beli', 'label' => 'Harga Beli Satuan (Rp)', 'type' => 'number', 'placeholder' => '75000', 'required' => true],
                ];
                break;

                // ==========================================
                // 2. PURCHASING: PURCHASE ORDER (PO)
                // ==========================================
            case 'purchasing/po':
            case 'purchasing/index':
                $config['headerIcon'] = 'bi-file-earmark-text';
                $config['formTitle'] = 'Form Purchase Order (PO) Bahan Baku & Barang Jadi';
                $config['summaryCards'] = [
                    ['title' => 'PO Aktif Bulan Ini', 'value' => '18 PO', 'icon' => 'bi-file-text', 'color' => 'primary'],
                    ['title' => 'Pembelian 100 Yard Kain', 'value' => 'PO-202610-041', 'icon' => 'bi-check-circle', 'color' => 'success'],
                    ['title' => 'Total Tagihan PO', 'value' => 'Rp 68.400.000', 'icon' => 'bi-cash-coin', 'color' => 'info'],
                    ['title' => 'Hutang Dagang Supplier', 'value' => 'Rp 18.500.000', 'icon' => 'bi-receipt', 'color' => 'danger'],
                ];
                $config['columns'] = [
                    ['key' => 'po_number', 'label' => 'Nomor PO'],
                    ['key' => 'date', 'label' => 'Tanggal PO'],
                    ['key' => 'supplier', 'label' => 'Supplier'],
                    ['key' => 'items', 'label' => 'Detail Item & Satuan'],
                    ['key' => 'total', 'label' => 'Total Nilai (Rp)'],
                    ['key' => 'allocated_cost', 'label' => 'Biaya Ongkir HPP'],
                    ['key' => 'payment_status', 'label' => 'Status Hutang'],
                    ['key' => 'shipping_status', 'label' => 'Penerimaan Fisik'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'po_number' => 'PO-202610-041', 'date' => '22 Okt 2026', 'supplier' => 'CV Tekstil Bali Sejahtera', 'items' => 'Kain Brokat Semi Prancis (100 Yard @ 75rb)', 'total' => 'Rp 7.500.000', 'allocated_cost' => 'Rp 150.000 (Ongkir)', 'payment_status' => 'Tempo 30 Hari (Hutang)', 'shipping_status' => 'Diterima Lengkap (100 Yd)', 'badge' => 'success'],
                    ['id' => 2, 'po_number' => 'PO-202610-042', 'date' => '23 Okt 2026', 'supplier' => 'Tenun Sidemen Klungkung', 'items' => 'Kain Endek Sutra Asli (80 Meter @ 140rb)', 'total' => 'Rp 11.200.000', 'allocated_cost' => 'Rp 100.000 (Ongkir)', 'payment_status' => 'DP 50%', 'shipping_status' => 'Diterima Sebagian (40/80m)', 'badge' => 'info'],
                    ['id' => 3, 'po_number' => 'PO-202610-043', 'date' => '24 Okt 2026', 'supplier' => 'Toko Benang Sentosa', 'items' => 'Benang Bordir Emas (50 Roll @ 22rb)', 'total' => 'Rp 1.100.000', 'allocated_cost' => 'Rp 25.000', 'payment_status' => 'Lunas', 'shipping_status' => 'Diterima Lengkap', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'po_number', 'label' => 'Nomor PO (Sistem)', 'type' => 'text', 'placeholder' => 'PO-202610-044', 'required' => true],
                    ['name' => 'tanggal', 'label' => 'Tanggal PO', 'type' => 'date', 'required' => true],
                    ['name' => 'supplier', 'label' => 'Supplier', 'type' => 'select', 'options' => ['CV Tekstil Bali Sejahtera', 'Tenun Tradisional Sidemen', 'Toko Benang Sentosa Denpasar', 'UD Pelengkap Garmen'], 'required' => true],
                    ['name' => 'jenis_pembelian', 'label' => 'Jenis Pembelian', 'type' => 'select', 'options' => ['Bahan Baku (Raw Material)', 'Barang Jadi (Finished Goods)'], 'required' => true],
                    ['name' => 'item_name', 'label' => 'Nama Item & Kuantitas', 'type' => 'text', 'placeholder' => 'Contoh: Kain Brokat Maroon (100 Yard)', 'required' => true],
                    ['name' => 'harga_total', 'label' => 'Total Harga Barang (Rp)', 'type' => 'number', 'placeholder' => '7500000', 'required' => true],
                    ['name' => 'ongkir_alokasi', 'label' => 'Alokasi Ongkir ke HPP Persediaan (Rp)', 'type' => 'number', 'placeholder' => '150000'],
                    ['name' => 'termin_bayar', 'label' => 'Termin Pembayaran', 'type' => 'select', 'options' => ['Tempo 30 Hari (Hutang Dagang)', 'Tempo 14 Hari', 'DP 50% + Pelunasan', 'Tunai']],
                ];
                break;

                // ==========================================
                // 3. GUDANG: DAFTAR 7 GUDANG (SESUAI BAB 3)
                // ==========================================
            case 'inventory/gudang':
                $config['headerIcon'] = 'bi-building';
                $config['formTitle'] = 'Form Master Data Gudang & Lokasi Penyimpanan';
                $config['summaryCards'] = [
                    ['title' => 'Gudang Penjahit (WIP)', 'value' => '100 Yard', 'icon' => 'bi-scissors', 'color' => 'primary'],
                    ['title' => 'Gudang Transit (QC)', 'value' => '60 Pcs', 'icon' => 'bi-clock-history', 'color' => 'warning'],
                    ['title' => 'Gudang Reject / Service', 'value' => '15 Pcs', 'icon' => 'bi-tools', 'color' => 'danger'],
                    ['title' => 'Gudang Utama (FG)', 'value' => '1.240 Pcs', 'icon' => 'bi-boxes', 'color' => 'success'],
                ];
                $config['columns'] = [
                    ['key' => 'code', 'label' => 'Kode Gudang'],
                    ['key' => 'name', 'label' => 'Nama Gudang Sesuai PDF'],
                    ['key' => 'function', 'label' => 'Fungsi Alur Perpindahan'],
                    ['key' => 'current_stock', 'label' => 'Stok Saat Ini'],
                    ['key' => 'status', 'label' => 'Status Operasional'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'code' => 'GD-WIP-01', 'name' => 'Gudang Penjahit (Raw Material / WIP)', 'function' => 'Bahan dialokasikan ke penjahit, 100 yard kain, sisa bahan, nama penjahit/vendor', 'current_stock' => '100 Yard Kain + 450 WIP', 'status' => 'Aktif Berjalan', 'badge' => 'primary'],
                    ['id' => 2, 'code' => 'GD-TRN-02', 'name' => 'Gudang Transit (Menunggu QC)', 'function' => 'Menampung produk selesai jahit yang masih menunggu inspeksi QC (belum siap jual)', 'current_stock' => '60 Pcs Kebaya', 'status' => 'Antre Inspeksi QC', 'badge' => 'warning'],
                    ['id' => 3, 'code' => 'GD-RJC-03', 'name' => 'Gudang Reject / Service', 'function' => 'Menampung produk gagal QC yang bisa diperbaiki, biaya service, keluar-masuk service', 'current_stock' => '15 Pcs Service', 'status' => 'Rework Jahitan', 'badge' => 'danger'],
                    ['id' => 4, 'code' => 'GD-SAL-04', 'name' => 'Gudang Sale (Barang Cacat)', 'function' => 'Barang tidak ekonomis diperbaiki, dipisahkan dari reguler, dijual cacat / clearance', 'current_stock' => '8 Pcs Cacat', 'status' => 'Clearance Sale', 'badge' => 'secondary'],
                    ['id' => 5, 'code' => 'GD-UTM-05', 'name' => 'Gudang Utama — Finished Goods', 'function' => 'Menampung produk lolos QC (55 pcs), siap diberi barcode & transfer ke Live/Store', 'current_stock' => '1.240 Pcs Barcode', 'status' => 'Siap Distribusi', 'badge' => 'success'],
                    ['id' => 6, 'code' => 'GD-LIV-06', 'name' => 'Gudang Live (Live Selling)', 'function' => 'Stok alokasi live selling TikTok & Shopee, mutasi tercatat tanpa hitung stok ganda', 'current_stock' => '280 Pcs Alokasi', 'status' => 'Live Streaming', 'badge' => 'info'],
                    ['id' => 7, 'code' => 'GD-STR-07', 'name' => 'Gudang Store (Outlet Toko Fisik)', 'function' => 'Menampung stok outlet dan terintegrasi POS, penjualan mengurangi stok gudang store', 'current_stock' => '295 Pcs Display', 'status' => 'Kasir POS Aktif', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'kode_gudang', 'label' => 'Kode Gudang', 'type' => 'text', 'placeholder' => 'GD-NEW-08', 'required' => true],
                    ['name' => 'nama_gudang', 'label' => 'Nama Gudang', 'type' => 'text', 'placeholder' => 'Nama lokasi gudang', 'required' => true],
                    ['name' => 'tipe_gudang', 'label' => 'Tipe / Fungsi Gudang', 'type' => 'select', 'options' => ['Raw Material & WIP (Penjahit)', 'Transit QC', 'Reject & Service', 'Sale (Cacat / Clearance)', 'Utama (Finished Goods)', 'Live Selling', 'Outlet Store (POS)']],
                    ['name' => 'penanggung_jawab', 'label' => 'Kepala Gudang / Penjahit', 'type' => 'text', 'placeholder' => 'Nama PIC'],
                ];
                break;

                // ==========================================
                // 3. INVENTORY: STOK & TRANSFER ANTAR-GUDANG
                // ==========================================
            case 'inventory/transfer':
            case 'inventory/stok':
            case 'inventory/index':
                $config['headerIcon'] = 'bi-arrow-left-right';
                $config['formTitle'] = 'Form Transfer Stok Antar-Gudang (Dokumen, Status & Audit Trail)';
                $config['summaryCards'] = [
                    ['title' => 'Gudang Utama (FG)', 'value' => '1.240 Pcs', 'icon' => 'bi-boxes', 'color' => 'success'],
                    ['title' => 'Gudang Live', 'value' => '280 Pcs', 'icon' => 'bi-tiktok', 'color' => 'info'],
                    ['title' => 'Gudang Store (POS)', 'value' => '295 Pcs', 'icon' => 'bi-shop', 'color' => 'primary'],
                    ['title' => 'Gudang Transit (QC)', 'value' => '60 Pcs', 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'doc_no', 'label' => 'No. Dokumen Transfer'],
                    ['key' => 'origin', 'label' => 'Gudang Asal'],
                    ['key' => 'destination', 'label' => 'Gudang Tujuan'],
                    ['key' => 'item', 'label' => 'Item & Kuantitas'],
                    ['key' => 'status', 'label' => 'Status Pengiriman'],
                    ['key' => 'user', 'label' => 'Otorisator'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'doc_no' => 'TRF-202610-011', 'origin' => 'Penerimaan / Supplier', 'destination' => 'Gudang Penjahit (Wayan Artana)', 'item' => 'Kain Brokat Semi Prancis (100 Yard)', 'status' => 'Diterima Penjahit', 'user' => 'Admin Gudang', 'badge' => 'success'],
                    ['id' => 2, 'doc_no' => 'TRF-202610-012', 'origin' => 'Gudang Penjahit (WIP)', 'destination' => 'Gudang Transit (QC)', 'item' => 'Kebaya Bali Bordir (60 Pcs Hasil Jahit)', 'status' => 'Diterima di Transit', 'user' => 'Wayan Artana (Penjahit)', 'badge' => 'warning'],
                    ['id' => 3, 'doc_no' => 'TRF-202610-013', 'origin' => 'Gudang Transit (QC)', 'destination' => 'Gudang Utama (Finished Goods)', 'item' => 'Kebaya Bali Bordir (55 Pcs Lolos QC)', 'status' => 'Diterima & Ber-Barcode', 'user' => 'Tim QC KlambiBali', 'badge' => 'success'],
                    ['id' => 4, 'doc_no' => 'TRF-202610-014', 'origin' => 'Gudang Utama (FG)', 'destination' => 'Gudang Live (Live Selling)', 'item' => 'Kebaya Bali Bordir Maroon M (20 Pcs)', 'status' => 'Siap Live Streaming', 'user' => 'Host Live', 'badge' => 'info'],
                    ['id' => 5, 'doc_no' => 'TRF-202610-015', 'origin' => 'Gudang Utama (FG)', 'destination' => 'Gudang Store (Outlet POS)', 'item' => 'Kebaya Bali Bordir Maroon M (30 Pcs)', 'status' => 'Display Kasir POS', 'user' => 'Kasir Toko', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'no_dokumen', 'label' => 'Nomor Dokumen Transfer', 'type' => 'text', 'placeholder' => 'TRF-202610-016', 'required' => true],
                    ['name' => 'gudang_asal', 'label' => 'Gudang Asal', 'type' => 'select', 'options' => ['Gudang Penjahit (Raw Material/WIP)', 'Gudang Transit (QC)', 'Gudang Utama (Finished Goods)', 'Gudang Live', 'Gudang Store (POS)', 'Gudang Reject/Service'], 'required' => true],
                    ['name' => 'gudang_tujuan', 'label' => 'Gudang Tujuan', 'type' => 'select', 'options' => ['Gudang Utama (Finished Goods)', 'Gudang Live', 'Gudang Store (POS)', 'Gudang Transit (QC)', 'Gudang Reject/Service', 'Gudang Sale'], 'required' => true],
                    ['name' => 'item_produk', 'label' => 'Pilih SKU / Bahan', 'type' => 'select', 'options' => ['KB-BRD-MRN-M - Kebaya Bali Bordir Maroon (Size M)', 'KM-EDK-GLD-ALL - Kamen Endek Sutra Motif Wajik Emas', 'BB-KN-BRKT-01 - Kain Brokat Semi Prancis (Yard)'], 'required' => true],
                    ['name' => 'qty', 'label' => 'Jumlah Qty', 'type' => 'number', 'placeholder' => '20', 'required' => true],
                    ['name' => 'catatan', 'label' => 'Alasan Transfer / Keterangan', 'type' => 'textarea', 'placeholder' => 'Alokasi stok live selling / restock display outlet...'],
                ];
                break;

                // ==========================================
                // 4. PRODUKSI: WORK ORDER (WO)
                // ==========================================
            case 'produksi/work-orders':
            case 'produksi/index':
                $config['headerIcon'] = 'bi-gear-wide-connected';
                $config['formTitle'] = 'Form Perintah Produksi (Work Order)';
                $config['summaryCards'] = [
                    ['title' => 'WO Demo Aktif (WO-001)', 'value' => '60 Kebaya', 'icon' => 'bi-gear', 'color' => 'primary'],
                    ['title' => 'Kain Diberikan', 'value' => '100 Yard', 'icon' => 'bi-scissors', 'color' => 'info'],
                    ['title' => 'Standar Pemakaian', 'value' => '1,56 Yd/Kebaya', 'icon' => 'bi-calculator', 'color' => 'success'],
                    ['title' => 'Sisa Kain Aktual', 'value' => '5,80 Yard', 'icon' => 'bi-pie-chart', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'wo_code', 'label' => 'No. Work Order'],
                    ['key' => 'product', 'label' => 'Model Kebaya'],
                    ['key' => 'vendor', 'label' => 'Penjahit / Vendor'],
                    ['key' => 'raw_material', 'label' => 'Bahan Dialokasikan'],
                    ['key' => 'std_formula', 'label' => 'Standar Kebutuhan'],
                    ['key' => 'target_qty', 'label' => 'Hasil Aktual'],
                    ['key' => 'stage', 'label' => 'Tahapan Saat Ini'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'wo_code' => 'WO-202610-001', 'product' => 'Kebaya Bali Bordir Maroon (Demo)', 'vendor' => 'I Wayan Artana (Gudang Penjahit)', 'raw_material' => '100 Yard Kain Brokat', 'std_formula' => '1,56 Yard / Pcs', 'target_qty' => '60 Pcs Selesai', 'stage' => 'Kirim ke Gudang Transit (QC)', 'badge' => 'warning'],
                    ['id' => 2, 'wo_code' => 'WO-202610-002', 'product' => 'Kamen Endek Sutra Hitam Emas', 'vendor' => 'Tenun Sidemen Klungkung', 'raw_material' => '80 Meter Kain Endek', 'std_formula' => '2,50 Meter / Pcs', 'target_qty' => 'Target 32 Pcs', 'stage' => 'Proses Jahit', 'badge' => 'primary'],
                    ['id' => 3, 'wo_code' => 'WO-202610-003', 'product' => 'Kemeja Safari Pria Katun Putih', 'vendor' => 'Sukawati Garment', 'raw_material' => '135 Meter Katun Jepang', 'std_formula' => '1,80 Meter / Pcs', 'target_qty' => 'Target 75 Pcs', 'stage' => 'Potong & Cutting', 'badge' => 'info'],
                ];
                $config['formFields'] = [
                    ['name' => 'wo_code', 'label' => 'Nomor Work Order (WO)', 'type' => 'text', 'placeholder' => 'WO-202610-004', 'required' => true],
                    ['name' => 'produk', 'label' => 'Model Kebaya Yang Diproduksi', 'type' => 'select', 'options' => ['Kebaya Bali Bordir Premium Maroon', 'Kamen Endek Sutra Motif Wajik Emas', 'Kemeja Safari Pria Katun Jepang Putih'], 'required' => true],
                    ['name' => 'penjahit', 'label' => 'Vendor / Penjahit Penerima Bahan', 'type' => 'select', 'options' => ['I Wayan Artana (Denpasar)', 'Vendor Jahit Sukawati', 'Butik Bordir Gianyar'], 'required' => true],
                    ['name' => 'bahan_diberikan', 'label' => 'Alokasi Bahan Kain (Yard/Meter)', 'type' => 'number', 'placeholder' => '100', 'required' => true],
                    ['name' => 'standar_pemakaian', 'label' => 'Standar Pemakaian Bahan per Pcs', 'type' => 'text', 'placeholder' => '1.56', 'required' => true],
                    ['name' => 'target_qty', 'label' => 'Target Hasil Produksi (Pcs)', 'type' => 'number', 'placeholder' => '60', 'required' => true],
                ];
                break;

                // ==========================================
                // 4. PRODUKSI: PEMAKAIAN BAHAN & SIMULASI 1,56 YARD (SESUAI BAB 4)
                // ==========================================
            case 'produksi/pemakaian-bahan':
            case 'produksi/bom':
                $config['headerIcon'] = 'bi-scissors';
                $config['formTitle'] = 'Formula Kebutuhan Bahan (BOM) & Rekonsiliasi Kain';
                $config['summaryCards'] = [
                    ['title' => 'Pembelian Kain', 'value' => '100 Yard', 'icon' => 'bi-box-seam', 'color' => 'primary'],
                    ['title' => 'Standar per Kebaya', 'value' => '1,56 Yard', 'icon' => 'bi-rulers', 'color' => 'info'],
                    ['title' => 'Pemakaian Standar (60 Pcs)', 'value' => '93,60 Yard', 'icon' => 'bi-check-all', 'color' => 'success'],
                    ['title' => 'Sisa Teoritis vs Aktual', 'value' => '6,40 Yd vs 5,80 Yd', 'icon' => 'bi-pie-chart', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'step_desc', 'label' => 'Komponen Perhitungan (Halaman 4 PDF)'],
                    ['key' => 'formula', 'label' => 'Rumus / Perhitungan'],
                    ['key' => 'result', 'label' => 'Hasil Angka'],
                    ['key' => 'status', 'label' => 'Catatan Fisik'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'step_desc' => 'Pembelian Kain Brokat Semi Prancis', 'formula' => 'Faktur PO-202610-041', 'result' => '100,00 Yard', 'status' => 'Masuk Gudang Penjahit', 'badge' => 'primary'],
                    ['id' => 2, 'step_desc' => 'Standar Kebutuhan Kain per Kebaya', 'formula' => 'Formula BOM Model Kebaya Bordir', 'result' => '1,56 Yard / pcs', 'status' => 'Standar Berlaku', 'badge' => 'info'],
                    ['id' => 3, 'step_desc' => 'Kapasitas Teoritis Kebaya', 'formula' => '100 yard ÷ 1,56 yard', 'result' => '64,10 Kebaya', 'status' => 'Estimasi Maksimal', 'badge' => 'secondary'],
                    ['id' => 4, 'step_desc' => 'Hasil Produksi Aktual Selesai', 'formula' => 'Laporan Hasil Jahit Wayan Artana', 'result' => '60,00 Kebaya', 'status' => 'Masuk Gudang Transit QC', 'badge' => 'success'],
                    ['id' => 5, 'step_desc' => 'Pemakaian Kain Standar', 'formula' => '60 kebaya × 1,56 yard', 'result' => '93,60 Yard', 'status' => 'Konsumsi Standar', 'badge' => 'primary'],
                    ['id' => 6, 'step_desc' => 'Sisa Kain Teoritis', 'formula' => '100 yard − 93,60 yard', 'result' => '6,40 Yard', 'status' => 'Tercatat di Sistem', 'badge' => 'info'],
                    ['id' => 7, 'step_desc' => 'Sisa Kain Aktual Fisik', 'formula' => 'Hasil Timbang/Ukur Fisik di Penjahit', 'result' => '5,80 Yard', 'status' => 'Di Gudang Penjahit (WIP)', 'badge' => 'success'],
                    ['id' => 8, 'step_desc' => 'Selisih Potongan / Waste / Perca', 'formula' => '6,40 yard (teoritis) − 5,80 yard (aktual)', 'result' => '0,60 Yard Waste', 'status' => 'Tercatat Selisih Produksi', 'badge' => 'danger'],
                ];
                $config['formFields'] = [
                    ['name' => 'wo_ref', 'label' => 'Referensi Work Order', 'type' => 'select', 'options' => ['WO-202610-001 (Kebaya Bali Bordir Maroon)'], 'required' => true],
                    ['name' => 'pemakaian_aktual', 'label' => 'Kain Aktual Digunakan (Yard)', 'type' => 'number', 'placeholder' => '94.20'],
                    ['name' => 'sisa_aktual', 'label' => 'Sisa Kain Aktual (Yard)', 'type' => 'number', 'placeholder' => '5.80', 'required' => true],
                    ['name' => 'lokasi_sisa', 'label' => 'Lokasi Penyimpanan Sisa Kain', 'type' => 'select', 'options' => ['Tetap di Gudang Penjahit (Untuk Batch Berikutnya)', 'Dikembalikan ke Gudang Bahan Baku']],
                    ['name' => 'keterangan_selisih', 'label' => 'Alasan Selisih / Waste', 'type' => 'textarea', 'placeholder' => 'Pola potong lekukan leher, perca kain sobek dari supplier, waste jahitan...'],
                ];
                break;

                // ==========================================
                // 4. PRODUKSI: PERHITUNGAN HPP (SESUAI BAB 4)
                // ==========================================
            case 'produksi/hpp':
                $config['headerIcon'] = 'bi-calculator';
                $config['formTitle'] = 'Rincian Perhitungan HPP per Model Kebaya';
                $config['summaryCards'] = [
                    ['title' => 'Biaya Kain (1,56 Yd)', 'value' => 'Rp 117.000', 'icon' => 'bi-tag', 'color' => 'primary'],
                    ['title' => 'Biaya Jahit', 'value' => 'Rp 45.000', 'icon' => 'bi-scissors', 'color' => 'info'],
                    ['title' => 'HPP Total Kebaya', 'value' => 'Rp 185.000', 'icon' => 'bi-wallet2', 'color' => 'success'],
                    ['title' => 'Harga Jual Eceran', 'value' => 'Rp 385.000', 'icon' => 'bi-cash', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'component', 'label' => 'Komponen Biaya Produksi HPP'],
                    ['key' => 'basis', 'label' => 'Dasar Perhitungan'],
                    ['key' => 'cost', 'label' => 'Nominal Biaya (Rp)'],
                    ['key' => 'share', 'label' => 'Porsi HPP'],
                    ['key' => 'status', 'label' => 'Kategori'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'component' => 'Biaya Kain Brokat Utama', 'basis' => '1,56 Yard × Rp 75.000/yard', 'cost' => 'Rp 117.000', 'share' => '63.2%', 'status' => 'Bahan Baku', 'badge' => 'primary'],
                    ['id' => 2, 'component' => 'Ongkos Jahit & Payet Penjahit', 'basis' => 'Tarif per potong kebaya bordir', 'cost' => 'Rp 45.000', 'share' => '24.3%', 'status' => 'Tenaga Kerja', 'badge' => 'info'],
                    ['id' => 3, 'component' => 'Aksesoris (Benang Emas & Kancing)', 'basis' => '0.2 roll benang + 8 kancing bungkus', 'cost' => 'Rp 18.000', 'share' => '9.7%', 'status' => 'Bahan Pendukung', 'badge' => 'warning'],
                    ['id' => 4, 'component' => 'Alokasi Biaya Overhead Produksi', 'basis' => 'Listrik, finishing, setrika uap, tag', 'cost' => 'Rp 5.000', 'share' => '2.8%', 'status' => 'Overhead', 'badge' => 'secondary'],
                    ['id' => 5, 'component' => 'TOTAL HPP PER KEBAYA JADI', 'basis' => '117.000 + 45.000 + 18.000 + 5.000', 'cost' => 'Rp 185.000', 'share' => '100%', 'status' => 'HPP Produk', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'sku', 'label' => 'Pilih SKU Produk', 'type' => 'select', 'options' => ['KB-BRD-MRN-M - Kebaya Bali Bordir Maroon M', 'KM-EDK-GLD-ALL - Kamen Endek Sutra'], 'required' => true],
                    ['name' => 'biaya_kain', 'label' => 'Biaya Kain per Unit (Rp)', 'type' => 'number', 'placeholder' => '117000', 'required' => true],
                    ['name' => 'biaya_jahit', 'label' => 'Biaya Ongkos Jahit (Rp)', 'type' => 'number', 'placeholder' => '45000', 'required' => true],
                    ['name' => 'biaya_aksesoris', 'label' => 'Biaya Aksesoris & Payet (Rp)', 'type' => 'number', 'placeholder' => '18000'],
                    ['name' => 'biaya_overhead', 'label' => 'Alokasi Overhead (Rp)', 'type' => 'number', 'placeholder' => '5000'],
                ];
                break;

                // ==========================================
                // 5. QC & BARCODE: INSPEKSI & PEMISAHAN GUDANG (SESUAI BAB 3 & 5)
                // ==========================================
            case 'qc/inspeksi':
            case 'qc/index':
            case 'qc/reject':
            case 'qc/barcode':
                $config['headerIcon'] = 'bi-patch-check';
                $config['formTitle'] = 'Inspeksi QC, Pemisahan Gudang & Penerbitan Barcode';
                $config['summaryCards'] = [
                    ['title' => 'Diperiksa di Transit', 'value' => '60 Pcs Kebaya', 'icon' => 'bi-search', 'color' => 'primary'],
                    ['title' => 'Lolos QC (Gudang Utama)', 'value' => '55 Pcs (91,7%)', 'icon' => 'bi-check-circle', 'color' => 'success'],
                    ['title' => 'Reject Service (Gudang Reject)', 'value' => '3 Pcs (5,0%)', 'icon' => 'bi-tools', 'color' => 'warning'],
                    ['title' => 'Cacat Sale (Gudang Sale)', 'value' => '2 Pcs (3,3%)', 'icon' => 'bi-x-octagon', 'color' => 'danger'],
                ];
                $config['columns'] = [
                    ['key' => 'inspect_no', 'label' => 'No. Inspeksi QC'],
                    ['key' => 'wo_ref', 'label' => 'Work Order'],
                    ['key' => 'product', 'label' => 'Model Produk'],
                    ['key' => 'inspected', 'label' => 'Diperiksa'],
                    ['key' => 'passed', 'label' => 'Lolos (Ke Gudang Utama)'],
                    ['key' => 'rework', 'label' => 'Service (Ke Gudang Reject)'],
                    ['key' => 'defect_sale', 'label' => 'Cacat (Ke Gudang Sale)'],
                    ['key' => 'status', 'label' => 'Tindak Lanjut Barcode'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'inspect_no' => 'QC-202610-001', 'wo_ref' => 'WO-202610-001', 'product' => 'Kebaya Bali Bordir Maroon M (60 Pcs)', 'inspected' => '60 Pcs', 'passed' => '55 Pcs (Grade A)', 'rework' => '3 Pcs (Jahit Lepas)', 'defect_sale' => '2 Pcs (Kain Robek)', 'status' => '55 Barcode Diterbitkan', 'badge' => 'success'],
                    ['id' => 2, 'inspect_no' => 'QC-202610-002', 'wo_ref' => 'WO-202610-002', 'product' => 'Kamen Endek Tenun Hitam Emas', 'inspected' => '32 Pcs', 'passed' => '32 Pcs (100%)', 'rework' => '0 Pcs', 'defect_sale' => '0 Pcs', 'status' => '32 Barcode Diterbitkan', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'ref_wo', 'label' => 'Batch Work Order (Gudang Transit)', 'type' => 'select', 'options' => ['WO-202610-001 (Kebaya Bali Bordir Maroon - 60 Pcs)'], 'required' => true],
                    ['name' => 'qty_inspected', 'label' => 'Total Diperiksa (Pcs)', 'type' => 'number', 'placeholder' => '60', 'required' => true],
                    ['name' => 'qty_passed', 'label' => 'Lolos QC -> Pindah ke Gudang Utama (Pcs)', 'type' => 'number', 'placeholder' => '55', 'required' => true],
                    ['name' => 'qty_rework', 'label' => 'Gagal QC (Bisa Diperbaiki) -> Pindah ke Gudang Reject/Service (Pcs)', 'type' => 'number', 'placeholder' => '3'],
                    ['name' => 'qty_defect', 'label' => 'Gagal QC (Tidak Ekonomis) -> Pindah ke Gudang Sale (Pcs)', 'type' => 'number', 'placeholder' => '2'],
                    ['name' => 'petugas_qc', 'label' => 'Pemeriksa QC', 'type' => 'text', 'placeholder' => 'Nama Staf Quality Control'],
                ];
                break;

                // ==========================================
                // 6. PENJUALAN REGULER, POS & RESI 4 DIGIT (SESUAI BAB 6 & 11)
                // ==========================================
            case 'penjualan/transaksi':
            case 'penjualan/pesanan':
            case 'penjualan/index':
                $config['headerIcon'] = 'bi-bag-check';
                $config['formTitle'] = 'Input Pesanan Penjualan (Mendukung Cari 4 Digit Terakhir Resi)';
                $config['summaryCards'] = [
                    ['title' => 'Penjualan Kasir POS', 'value' => 'Rp 25.500.000', 'icon' => 'bi-calculator', 'color' => 'success'],
                    ['title' => 'Penjualan Reguler/Reseller', 'value' => 'Rp 32.700.000', 'icon' => 'bi-card-checklist', 'color' => 'primary'],
                    ['title' => 'Pesanan Shopee & TikTok', 'value' => 'Rp 84.650.000', 'icon' => 'bi-shop', 'color' => 'warning'],
                    ['title' => 'Pencarian Resi 4 Digit', 'value' => 'Aktif', 'icon' => 'bi-search', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'invoice', 'label' => 'No. Invoice'],
                    ['key' => 'channel', 'label' => 'Channel Penjualan'],
                    ['key' => 'resi_ref', 'label' => 'No. Resi (4 Digit Terakhir)'],
                    ['key' => 'customer', 'label' => 'Nama Pembeli'],
                    ['key' => 'items', 'label' => 'Item Produk'],
                    ['key' => 'total', 'label' => 'Total Belanja'],
                    ['key' => 'payment_method', 'label' => 'Metode Pembayaran'],
                    ['key' => 'status', 'label' => 'Status Pesanan'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'invoice' => 'INV-202610-089', 'channel' => 'Toko Kasir POS', 'resi_ref' => 'Scan Barcode', 'customer' => 'Ni Wayan Sukmawati', 'items' => 'Kebaya Bordir Maroon M (1), Kamen Endek (1)', 'total' => 'Rp 835.000', 'payment_method' => 'Split (Cash 500rb + QRIS 335rb)', 'status' => 'Lunas', 'badge' => 'success'],
                    ['id' => 2, 'invoice' => 'INV-202610-090', 'channel' => 'Shopee Marketplace', 'resi_ref' => 'SPXID02919821 (...9821)', 'customer' => 'Dewi Lestari (Jakarta)', 'items' => 'Kebaya Bordir Maroon M (1)', 'total' => 'Rp 385.000', 'payment_method' => 'ShopeePay Settlement', 'status' => 'Diproses', 'badge' => 'warning'],
                    ['id' => 3, 'invoice' => 'INV-202610-091', 'channel' => 'TikTok Shop Live', 'resi_ref' => 'TKID892014410 (...4410)', 'customer' => 'Bagus Anom (Surabaya)', 'items' => 'Kamen Endek Tenun (1)', 'total' => 'Rp 450.000', 'payment_method' => 'COD TikTok', 'status' => 'Dikirim', 'badge' => 'info'],
                    ['id' => 4, 'invoice' => 'INV-202610-092', 'channel' => 'Penjualan Reguler B2B', 'resi_ref' => 'JNE-CGK-1088 (...1088)', 'customer' => 'Butik Sanur Indah', 'items' => 'Paket Kebaya Bordir (20 Pcs)', 'total' => 'Rp 7.700.000', 'payment_method' => 'Transfer BCA (DP 50%)', 'status' => 'Diproses', 'badge' => 'primary'],
                ];
                $config['formFields'] = [
                    ['name' => 'channel', 'label' => 'Channel Penjualan', 'type' => 'select', 'options' => ['Toko Kasir (Outlet Fisik)', 'Penjualan Reguler (Non-Marketplace)', 'Shopee Indonesia', 'TikTok Shop'], 'required' => true],
                    ['name' => 'resi_search', 'label' => '4 Digit Terakhir Resi (Alat Pencarian Cepat)', 'type' => 'text', 'placeholder' => 'Contoh: 9821'],
                    ['name' => 'no_resi_lengkap', 'label' => 'Nomor Resi Lengkap Ekspedisi', 'type' => 'text', 'placeholder' => 'SPXID02919821', 'required' => true],
                    ['name' => 'nama_pelanggan', 'label' => 'Nama Pelanggan', 'type' => 'text', 'placeholder' => 'Nama lengkap pembeli', 'required' => true],
                    ['name' => 'metode_bayar', 'label' => 'Metode Pembayaran', 'type' => 'select', 'options' => ['Cash (Tunai)', 'QRIS', 'Debit BCA / EDC', 'Split Payment (Cash + QRIS/Debit)', 'Transfer Bank', 'Tempo 30 Hari']],
                ];
                break;

                // ==========================================
                // 8. RETURN PAKET (SESUAI BAB 14)
                // ==========================================
            case 'retur/paket':
            case 'retur/transit':
            case 'retur/index':
            case 'return/index':
            case 'penjualan/retur':
                $config['headerIcon'] = 'bi-arrow-counterclockwise';
                $config['formTitle'] = 'Form Pemrosesan Return Paket (Shopee, TikTok, Reguler via Resi 4 Digit)';
                $config['summaryCards'] = [
                    ['title' => 'Paket Return Masuk', 'value' => '6 Paket', 'icon' => 'bi-box-arrow-in-left', 'color' => 'primary'],
                    ['title' => 'Gudang Transit Return', 'value' => '3 Paket Antre', 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
                    ['title' => 'Layak Jual (Gudang Utama)', 'value' => '2 Pcs Restock', 'icon' => 'bi-check-circle', 'color' => 'success'],
                    ['title' => 'Cacat (Reject / Sale)', 'value' => '1 Pcs Rework', 'icon' => 'bi-x-octagon', 'color' => 'danger'],
                ];
                $config['columns'] = [
                    ['key' => 'return_no', 'label' => 'No. Return'],
                    ['key' => 'channel', 'label' => 'Channel Asal'],
                    ['key' => 'resi', 'label' => 'No. Resi (4 Digit)'],
                    ['key' => 'product', 'label' => 'Produk Dikembalikan'],
                    ['key' => 'verified_condition', 'label' => 'Hasil Verifikasi Fisik'],
                    ['key' => 'allocated_wh', 'label' => 'Tujuan Alokasi Gudang'],
                    ['key' => 'status', 'label' => 'Status Return'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'return_no' => 'RET-202610-001', 'channel' => 'Shopee', 'resi' => 'SPXID02919821 (...9821)', 'product' => 'Kebaya Bali Bordir Maroon M (1)', 'verified_condition' => 'Kondisi Baik (Segel Utuh)', 'allocated_wh' => 'Gudang Utama (Stok Siap Jual +1)', 'status' => 'Selesai Restock', 'badge' => 'success'],
                    ['id' => 2, 'return_no' => 'RET-202610-002', 'channel' => 'TikTok Shop', 'resi' => 'TKID892014410 (...4410)', 'product' => 'Kamen Endek Tenun Sutra (1)', 'verified_condition' => 'Belum Diperiksa', 'allocated_wh' => 'Gudang Transit Return', 'status' => 'Menunggu Verifikasi', 'badge' => 'warning'],
                    ['id' => 3, 'return_no' => 'RET-202610-003', 'channel' => 'Reguler', 'resi' => 'JNE-CGK-1088 (...1088)', 'product' => 'Kebaya Bordir Maroon L (1)', 'verified_condition' => 'Kancing Lepas (Bisa Diperbaiki)', 'allocated_wh' => 'Gudang Reject / Service', 'status' => 'Masuk Service', 'badge' => 'danger'],
                ];
                $config['formFields'] = [
                    ['name' => 'resi_4digit', 'label' => 'Cari via 4 Digit Terakhir Resi', 'type' => 'text', 'placeholder' => 'Contoh: 9821', 'required' => true],
                    ['name' => 'no_resi_full', 'label' => 'Nomor Resi Lengkap', 'type' => 'text', 'placeholder' => 'SPXID02919821', 'required' => true],
                    ['name' => 'channel', 'label' => 'Channel Penjualan Asal', 'type' => 'select', 'options' => ['Shopee Indonesia', 'TikTok Shop', 'Penjualan Reguler'], 'required' => true],
                    ['name' => 'kondisi_barang', 'label' => 'Hasil Verifikasi Fisik Barang', 'type' => 'select', 'options' => ['Kondisi Baik (Masuk Gudang Utama / Siap Jual)', 'Belum Diperiksa (Masuk Gudang Transit Return)', 'Rusak Ringan Bisa Diperbaiki (Masuk Gudang Reject/Service)', 'Rusak Berat Tidak Bisa Diperbaiki (Masuk Gudang Sale)'], 'required' => true],
                    ['name' => 'catatan', 'label' => 'Alasan Return / Bukti Foto', 'type' => 'textarea', 'placeholder' => 'Salah ukuran dari pembeli, jahitan lepas di kurir...'],
                ];
                break;

                // ==========================================
                // 9 & 10. FINANCE & ACCOUNTING: JURNAL OTOMATIS & COA (SESUAI BAB 8 & 9)
                // ==========================================
            case 'accounting/jurnal':
            case 'accounting/index':
            case 'finance/arus-kas':
            case 'finance/index':
                $config['headerIcon'] = 'bi-journal-check';
                $config['formTitle'] = 'Jurnal Transaksi Akuntansi Otomatis & Umum';
                $config['summaryCards'] = [
                    ['title' => 'Saldo Kas & Bank', 'value' => 'Rp 182.400.000', 'icon' => 'bi-bank', 'color' => 'primary'],
                    ['title' => 'Hutang Supplier CV Tekstil', 'value' => 'Rp 7.500.000', 'icon' => 'bi-receipt', 'color' => 'danger'],
                    ['title' => 'Nilai Persediaan Tercatat', 'value' => 'Rp 384.500.000', 'icon' => 'bi-boxes', 'color' => 'success'],
                    ['title' => 'Laba Kotor Berdasarkan HPP', 'value' => 'Rp 74.200.000', 'icon' => 'bi-graph-up', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'ref_no', 'label' => 'No. Referensi Jurnal'],
                    ['key' => 'date', 'label' => 'Tanggal'],
                    ['key' => 'description', 'label' => 'Keterangan Transaksi Terintegrasi'],
                    ['key' => 'debit_acc', 'label' => 'Akun Debit'],
                    ['key' => 'credit_acc', 'label' => 'Akun Kredit'],
                    ['key' => 'amount', 'label' => 'Nominal (Rp)'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'ref_no' => 'JRN-202610-041', 'date' => '22 Okt 2026', 'description' => 'Pembelian 100 Yard Kain Brokat (PO-041)', 'debit_acc' => '1140 - Persediaan Bahan Baku', 'credit_acc' => '2110 - Hutang Usaha (CV Tekstil)', 'amount' => 'Rp 7.500.000', 'status' => 'Posting Otomatis', 'badge' => 'success'],
                    ['id' => 2, 'ref_no' => 'JRN-202610-042', 'date' => '22 Okt 2026', 'description' => 'Pengeluaran 100 Yard Kain ke Penjahit Wayan Artana', 'debit_acc' => '1145 - Barang Dalam Proses (WIP)', 'credit_acc' => '1140 - Persediaan Bahan Baku', 'amount' => 'Rp 7.020.000', 'status' => 'Posting Otomatis', 'badge' => 'primary'],
                    ['id' => 3, 'ref_no' => 'JRN-202610-043', 'date' => '24 Okt 2026', 'description' => 'Hasil 60 Kebaya Jadi & Alokasi HPP (WO-001)', 'debit_acc' => '1150 - Persediaan Barang Jadi', 'credit_acc' => '1145 - WIP & Biaya Jahit', 'amount' => 'Rp 10.175.000', 'status' => 'Posting Otomatis', 'badge' => 'success'],
                    ['id' => 4, 'ref_no' => 'JRN-202610-044', 'date' => '24 Okt 2026', 'description' => 'Penjualan Kasir POS Toko (INV-089)', 'debit_acc' => '1110 - Kas Toko / QRIS', 'credit_acc' => '4110 - Pendapatan Penjualan', 'amount' => 'Rp 1.250.000', 'status' => 'Posting Otomatis', 'badge' => 'info'],
                    ['id' => 5, 'ref_no' => 'JRN-202610-045', 'date' => '24 Okt 2026', 'description' => 'Pengakuan HPP Penjualan Kasir POS (INV-089)', 'debit_acc' => '5110 - Beban Pokok Penjualan (HPP)', 'credit_acc' => '1150 - Persediaan Barang Jadi', 'amount' => 'Rp 555.000', 'status' => 'Posting Otomatis', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'ref_no', 'label' => 'No. Bukti / Referensi', 'type' => 'text', 'placeholder' => 'JRN-202610-046', 'required' => true],
                    ['name' => 'tanggal', 'label' => 'Tanggal Jurnal', 'type' => 'date', 'required' => true],
                    ['name' => 'keterangan', 'label' => 'Keterangan Transaksi', 'type' => 'text', 'placeholder' => 'Penyesuaian stok opname / biaya operasional...', 'required' => true],
                    ['name' => 'akun_debit', 'label' => 'Akun Debit', 'type' => 'select', 'options' => ['1110 - Kas Kecil', '1120 - Bank BCA', '1140 - Persediaan Bahan Baku', '1150 - Persediaan Barang Jadi', '5110 - Beban Pokok Penjualan (HPP)', '6110 - Biaya Operasional'], 'required' => true],
                    ['name' => 'akun_kredit', 'label' => 'Akun Kredit', 'type' => 'select', 'options' => ['1110 - Kas Kecil', '1120 - Bank BCA', '2110 - Hutang Supplier', '1150 - Persediaan Barang Jadi', '4110 - Pendapatan Penjualan'], 'required' => true],
                    ['name' => 'nominal', 'label' => 'Nominal Rupiah (Rp)', 'type' => 'number', 'placeholder' => '0', 'required' => true],
                ];
                break;

                // ==========================================
                // 11. REPORTS & EXPORT: EKSPOR EXCEL (SESUAI BAB 12 & 13)
                // ==========================================
            case 'laporan/transaksi':
            case 'laporan/index':
            case 'laporan/stok':
            case 'laporan/keuangan':
            case 'laporan/produksi':
            case 'laporan/ekspor':
                $config['headerIcon'] = 'bi-file-earmark-bar-graph';
                $config['formTitle'] = 'Filter Laporan & Ekspor ke Microsoft Excel (.xlsx)';
                $config['summaryCards'] = [
                    ['title' => 'Ekspor Excel (.xlsx)', 'value' => 'Wajib Seluruh Modul', 'icon' => 'bi-file-earmark-excel', 'color' => 'success'],
                    ['title' => 'Laporan Selisih Bahan', 'value' => '6,40 Yd vs 5,80 Yd', 'icon' => 'bi-pie-chart', 'color' => 'warning'],
                    ['title' => 'Laporan per Channel', 'value' => '4 Channel', 'icon' => 'bi-diagram-3', 'color' => 'info'],
                    ['title' => 'Buku Besar & HPP', 'value' => 'Lengkap', 'icon' => 'bi-journal-medical', 'color' => 'primary'],
                ];
                $config['columns'] = [
                    ['key' => 'report_name', 'label' => 'Judul Laporan Sesuai Spesifikasi'],
                    ['key' => 'category', 'label' => 'Modul Sumber'],
                    ['key' => 'filters', 'label' => 'Dukungan Filter'],
                    ['key' => 'format', 'label' => 'Format Ekspor Wajib'],
                    ['key' => 'action', 'label' => 'Aksi Unduh'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'report_name' => 'Laporan Penjualan per Channel (Shopee, TikTok, Reguler, Toko Fisik)', 'category' => 'Sales & POS', 'filters' => 'Tanggal, Channel, Outlet, Kategori, SKU', 'format' => 'Excel (.xlsx) / CSV', 'action' => 'Unduh .xlsx', 'badge' => 'success'],
                    ['id' => 2, 'report_name' => 'Laporan Pembelian, Hutang Supplier & Histori Harga Beli', 'category' => 'Purchasing', 'filters' => 'Periode, Supplier, Bahan, Gudang Penerima', 'format' => 'Excel (.xlsx) / CSV', 'action' => 'Unduh .xlsx', 'badge' => 'success'],
                    ['id' => 3, 'report_name' => 'Laporan 7 Gudang, Mutasi Stok & Peringatan Stok Minimum/Mati', 'category' => 'Inventory', 'filters' => 'Gudang (Penjahit/Transit/Utama/Live/Store)', 'format' => 'Excel (.xlsx) / CSV', 'action' => 'Unduh .xlsx', 'badge' => 'success'],
                    ['id' => 4, 'report_name' => 'Laporan Produksi, Konsumsi Standar 1,56 Yd vs Aktual & Selisih Bahan', 'category' => 'Produksi & HPP', 'filters' => 'Model, Batch, Vendor Penjahit, Periode', 'format' => 'Excel (.xlsx) / CSV', 'action' => 'Unduh .xlsx', 'badge' => 'success'],
                    ['id' => 5, 'report_name' => 'Laporan Buku Besar, Neraca, Laba Rugi Berdasarkan HPP & Arus Kas', 'category' => 'Accounting', 'filters' => 'Periode Akuntansi, COA, Entitas', 'format' => 'Excel (.xlsx) / CSV', 'action' => 'Unduh .xlsx', 'badge' => 'success'],
                    ['id' => 6, 'report_name' => 'Laporan Return Paket & Verifikasi Fisik (Gudang Transit Return)', 'category' => 'Return Paket', 'filters' => 'Channel Asal, Resi, Kondisi Fisik', 'format' => 'Excel (.xlsx) / CSV', 'action' => 'Unduh .xlsx', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'pilih_laporan', 'label' => 'Pilih Format Laporan', 'type' => 'select', 'options' => ['Laporan Penjualan Omnichannel & Laba Kotor', 'Laporan Pembelian & Hutang Dagang Supplier', 'Laporan Stok 7 Gudang & Nilai Persediaan HPP', 'Laporan Produksi, Pemakaian Bahan 1,56 Yard & Sisa Kain', 'Laporan Laba Rugi Komprehensif', 'Laporan Return Paket'], 'required' => true],
                    ['name' => 'tgl_mulai', 'label' => 'Tanggal Awal', 'type' => 'date', 'required' => true],
                    ['name' => 'tgl_akhir', 'label' => 'Tanggal Akhir', 'type' => 'date', 'required' => true],
                    ['name' => 'channel_filter', 'label' => 'Filter Channel', 'type' => 'select', 'options' => ['Semua Channel', 'Shopee', 'TikTok Shop', 'Penjualan Reguler', 'Outlet Toko Fisik (POS)']],
                    ['name' => 'gudang_filter', 'label' => 'Filter Gudang', 'type' => 'select', 'options' => ['Semua Gudang', 'Gudang Penjahit (WIP)', 'Gudang Transit (QC)', 'Gudang Reject/Service', 'Gudang Sale (Cacat)', 'Gudang Utama (FG)', 'Gudang Live', 'Gudang Store']],
                    ['name' => 'format_file', 'label' => 'Format Dokumen Wajib', 'type' => 'select', 'options' => ['Microsoft Excel (.xlsx)', 'Comma Separated Values (.csv)', 'Dokumen PDF Cetak (.pdf)']],
                ];
                break;

                // ==========================================
                // DEFAULT GENERIC FALLBACK
                // ==========================================
            default:
                $config['headerIcon'] = 'bi-folder2-open';
                $config['formTitle'] = 'Form Input '.$subTitle;
                $config['summaryCards'] = [
                    ['title' => 'Total Record Data', 'value' => '64 Data', 'icon' => 'bi-folder', 'color' => 'primary'],
                    ['title' => 'Data Aktif', 'value' => '58 Data', 'icon' => 'bi-check2', 'color' => 'success'],
                    ['title' => 'Sesuai Spesifikasi PDF', 'value' => '100% Sesuai', 'icon' => 'bi-patch-check', 'color' => 'info'],
                    ['title' => 'Format Ekspor', 'value' => '.xlsx Aktif', 'icon' => 'bi-file-earmark-excel', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'code', 'label' => 'Kode / Referensi'],
                    ['key' => 'name', 'label' => 'Nama / Deskripsi'],
                    ['key' => 'category', 'label' => 'Kategori'],
                    ['key' => 'date', 'label' => 'Tanggal Input'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'code' => strtoupper(substr($module, 0, 3)).'-001', 'name' => 'Data '.$subTitle.' Terintegrasi 1', 'category' => $moduleTitle, 'date' => '24 Okt 2026', 'status' => 'Aktif', 'badge' => 'success'],
                    ['id' => 2, 'code' => strtoupper(substr($module, 0, 3)).'-002', 'name' => 'Data '.$subTitle.' Pendukung 2', 'category' => $moduleTitle, 'date' => '23 Okt 2026', 'status' => 'Aktif', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'kode', 'label' => 'Kode / ID', 'type' => 'text', 'placeholder' => 'Otomatis', 'required' => true],
                    ['name' => 'nama', 'label' => 'Nama / Keterangan', 'type' => 'text', 'placeholder' => 'Masukkan keterangan', 'required' => true],
                ];
                break;
        }

        return $config;
    }
}
