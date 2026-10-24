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
            // 1. MASTER DATA: PRODUK, BARCODE & SKU
            // ==========================================
            case 'master/produk':
            case 'master/index':
                $config['headerIcon'] = 'bi-box-seam';
                $config['formTitle'] = 'Form Tambah Produk, SKU & Barcode Baru';
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
                    ['name' => 'formula_bom', 'label' => 'Standar Pemakaian Bahan (BOM)', 'type' => 'text', 'placeholder' => 'Contoh: 1.56 yard kain brokat'],
                    ['name' => 'hpp', 'label' => 'Estimasi HPP Satuan (Rp)', 'type' => 'number', 'placeholder' => '185000', 'required' => true],
                    ['name' => 'harga_jual', 'label' => 'Harga Jual Satuan (Rp)', 'type' => 'number', 'placeholder' => '385000', 'required' => true],
                ];
                break;

                // ==========================================
                // 1. MASTER DATA: KATEGORI & SUBKATEGORI
                // ==========================================
            case 'master/kategori':
                $config['headerIcon'] = 'bi-tags';
                $config['formTitle'] = 'Form Tambah Kategori & Subkategori Produk';
                $config['summaryCards'] = [
                    ['title' => 'Total Kategori', 'value' => '6 Kategori', 'icon' => 'bi-tags', 'color' => 'primary'],
                    ['title' => 'Total Subkategori', 'value' => '18 Subkategori', 'icon' => 'bi-tag', 'color' => 'info'],
                    ['title' => 'Kategori Terlaris', 'value' => 'Kebaya Bali', 'icon' => 'bi-star', 'color' => 'success'],
                    ['title' => 'Total Produk', 'value' => '142 SKU', 'icon' => 'bi-boxes', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'cat_code', 'label' => 'Kode Kategori'],
                    ['key' => 'name', 'label' => 'Nama Kategori Utama'],
                    ['key' => 'subcategories', 'label' => 'Daftar Subkategori'],
                    ['key' => 'item_count', 'label' => 'Jumlah SKU'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'cat_code' => 'CAT-01', 'name' => 'Kebaya Bali', 'subcategories' => 'Kebaya Bordir Tradisional, Kebaya Brokat Halus, Kebaya Wisuda Modern', 'item_count' => '48 SKU', 'status' => 'Aktif', 'badge' => 'success'],
                    ['id' => 2, 'cat_code' => 'CAT-02', 'name' => 'Kamen & Bawahan', 'subcategories' => 'Kamen Endek Sutra, Kamen Songket Bali, Kamen Lembaran', 'item_count' => '36 SKU', 'status' => 'Aktif', 'badge' => 'success'],
                    ['id' => 3, 'cat_code' => 'CAT-03', 'name' => 'Kemeja Safari', 'subcategories' => 'Safari Katun Jepang, Safari Bordir Adat', 'item_count' => '22 SKU', 'status' => 'Aktif', 'badge' => 'success'],
                    ['id' => 4, 'cat_code' => 'CAT-04', 'name' => 'Aksesoris Tradisional', 'subcategories' => 'Selendang Obi, Udeng Bali, Sabuk Anteng, Bros Kebaya', 'item_count' => '36 SKU', 'status' => 'Aktif', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'cat_code', 'label' => 'Kode Kategori', 'type' => 'text', 'placeholder' => 'CAT-05', 'required' => true],
                    ['name' => 'nama_kategori', 'label' => 'Nama Kategori', 'type' => 'text', 'placeholder' => 'Contoh: Kebaya Pengantin', 'required' => true],
                    ['name' => 'subkategori', 'label' => 'Daftar Subkategori (Pisahkan Koma)', 'type' => 'text', 'placeholder' => 'Subkategori A, Subkategori B...'],
                ];
                break;

                // ==========================================
                // 1. MASTER DATA: BAHAN BAKU & PENDUKUNG (YARD/METER/ROLL/GROSS)
                // ==========================================
            case 'master/bahan-baku':
                $config['headerIcon'] = 'bi-layers';
                $config['formTitle'] = 'Form Tambah Bahan Baku / Penolong (Mendukung Yard, Meter, Roll, Gross)';
                $config['summaryCards'] = [
                    ['title' => 'Stok Kain Brokat', 'value' => '100 Yard', 'icon' => 'bi-box-seam', 'color' => 'primary'],
                    ['title' => 'Stok Kain Endek', 'value' => '115 Meter', 'icon' => 'bi-layers-half', 'color' => 'success'],
                    ['title' => 'Benang Bordir', 'value' => '45 Roll', 'icon' => 'bi-disc', 'color' => 'info'],
                    ['title' => 'Nilai Bahan Baku', 'value' => 'Rp 58.600.000', 'icon' => 'bi-cash', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'code', 'label' => 'Kode Bahan'],
                    ['key' => 'name', 'label' => 'Nama Bahan'],
                    ['key' => 'unit', 'label' => 'Satuan'],
                    ['key' => 'supplier', 'label' => 'Supplier Utama'],
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
                // 1. MASTER DATA: DATA SUPPLIER (SESUAI BAB 2 & 10)
                // ==========================================
            case 'master/supplier':
                $config['headerIcon'] = 'bi-truck';
                $config['formTitle'] = 'Form Master Data Supplier & Rekening';
                $config['summaryCards'] = [
                    ['title' => 'Supplier Aktif', 'value' => '4 Mitra', 'icon' => 'bi-truck', 'color' => 'primary'],
                    ['title' => 'Total Pembelian', 'value' => 'Rp 68.400.000', 'icon' => 'bi-cart-check', 'color' => 'success'],
                    ['title' => 'Hutang Berjalan', 'value' => 'Rp 18.500.000', 'icon' => 'bi-receipt', 'color' => 'danger'],
                    ['title' => 'Supplier Kain Utama', 'value' => 'CV Tekstil Bali', 'icon' => 'bi-building', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'code', 'label' => 'Kode Supplier'],
                    ['key' => 'name', 'label' => 'Nama Perusahaan / Supplier'],
                    ['key' => 'category', 'label' => 'Kategori Pasokan'],
                    ['key' => 'top', 'label' => 'Termin Pembayaran (TOP)'],
                    ['key' => 'total_po', 'label' => 'Histori PO'],
                    ['key' => 'current_debt', 'label' => 'Sisa Hutang'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'code' => 'SUP-01', 'name' => 'CV Tekstil Bali Sejahtera', 'category' => 'Kain Brokat Semi Prancis (Yard)', 'top' => 'Tempo 30 Hari', 'total_po' => '12 PO (Rp 45.000.000)', 'current_debt' => 'Rp 7.500.000 (PO-041)', 'status' => 'Mitra Utama', 'badge' => 'success'],
                    ['id' => 2, 'code' => 'SUP-02', 'name' => 'Tenun Tradisional Sidemen', 'category' => 'Kain Tenun Endek Sutra (Meter)', 'top' => 'DP 50% + Tempo 14 Hari', 'total_po' => '6 PO (Rp 22.400.000)', 'current_debt' => 'Rp 5.600.000 (PO-042)', 'status' => 'Mitra Utama', 'badge' => 'success'],
                    ['id' => 3, 'code' => 'SUP-03', 'name' => 'Toko Benang Sentosa Denpasar', 'category' => 'Benang Bordir Metalik & Renda', 'top' => 'Tunai / COD', 'total_po' => '8 PO (Rp 8.800.000)', 'current_debt' => 'Rp 0 (Lunas)', 'status' => 'Lunas', 'badge' => 'info'],
                    ['id' => 4, 'code' => 'SUP-04', 'name' => 'UD Pelengkap Garmen', 'category' => 'Kancing Bungkus, Resleting & Label', 'top' => 'Tempo 14 Hari', 'total_po' => '5 PO (Rp 5.400.000)', 'current_debt' => 'Rp 5.400.000', 'status' => 'Jatuh Tempo', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'code', 'label' => 'Kode Supplier', 'type' => 'text', 'placeholder' => 'SUP-05', 'required' => true],
                    ['name' => 'nama', 'label' => 'Nama Supplier / Perusahaan', 'type' => 'text', 'placeholder' => 'PT Tekstil Jaya', 'required' => true],
                    ['name' => 'kategori', 'label' => 'Kategori Pasokan', 'type' => 'select', 'options' => ['Kain & Brokat', 'Kain Tenun Tradisional', 'Benang & Aksesoris', 'Packaging & Label']],
                    ['name' => 'termin_top', 'label' => 'Termin Pembayaran Default', 'type' => 'select', 'options' => ['Tempo 30 Hari', 'Tempo 14 Hari', 'DP 50% + Pelunasan', 'Tunai (Cash/Transfer)']],
                    ['name' => 'no_rekening', 'label' => 'Nomor Rekening Bank Supplier', 'type' => 'text', 'placeholder' => 'BCA 1420xxxxxx an CV Tekstil'],
                ];
                break;

                // ==========================================
                // 1. MASTER DATA: VENDOR PENJAHIT (SESUAI BAB 3, 4 & 10)
                // ==========================================
            case 'master/penjahit':
                $config['headerIcon'] = 'bi-person-gear';
                $config['formTitle'] = 'Form Master Data Vendor & Penjahit Produksi';
                $config['summaryCards'] = [
                    ['title' => 'Total Vendor Penjahit', 'value' => '8 Penjahit', 'icon' => 'bi-people', 'color' => 'primary'],
                    ['title' => 'Kapasitas Produksi', 'value' => '650 Pcs / Bln', 'icon' => 'bi-speedometer2', 'color' => 'success'],
                    ['title' => 'Penjahit Demo (Wayan)', 'value' => '60 Pcs Selesai', 'icon' => 'bi-check2-circle', 'color' => 'info'],
                    ['title' => 'Ongkos Jahit Standar', 'value' => 'Rp 45.000 / pcs', 'icon' => 'bi-cash', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'code', 'label' => 'Kode Penjahit'],
                    ['key' => 'name', 'label' => 'Nama Penjahit / Vendor'],
                    ['key' => 'specialty', 'label' => 'Spesialisasi Jahitan'],
                    ['key' => 'capacity', 'label' => 'Kapasitas'],
                    ['key' => 'std_rate', 'label' => 'Tarif Ongkos Jahit'],
                    ['key' => 'location', 'label' => 'Alamat / Workshop'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'code' => 'PJT-01', 'name' => 'I Wayan Artana (Penjahit Utama Demo)', 'specialty' => 'Kebaya Bordir Bali & Payet Manual', 'capacity' => '80 pcs / bln', 'std_rate' => 'Rp 45.000 / pcs', 'location' => 'Gudang Penjahit (Denpasar Barat)', 'status' => 'Aktif (60 Pcs Selesai)', 'badge' => 'success'],
                    ['id' => 2, 'code' => 'PJT-02', 'name' => 'I Made Kertayasa', 'specialty' => 'Kebaya Wisuda Brokat Halus', 'capacity' => '60 pcs / bln', 'std_rate' => 'Rp 42.000 / pcs', 'location' => 'Gianyar', 'status' => 'Aktif', 'badge' => 'success'],
                    ['id' => 3, 'code' => 'PJT-03', 'name' => 'Vendor Sukawati Garment', 'specialty' => 'Kamen Endek & Kemeja Safari', 'capacity' => '150 pcs / bln', 'std_rate' => 'Rp 35.000 / pcs', 'location' => 'Sukawati', 'status' => 'Aktif', 'badge' => 'success'],
                    ['id' => 4, 'code' => 'PJT-04', 'name' => 'Ni Luh Putu Handayani', 'specialty' => 'Selendang Obi & Aksesoris', 'capacity' => '200 pcs / bln', 'std_rate' => 'Rp 15.000 / pcs', 'location' => 'Klungkung', 'status' => 'Aktif', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'code', 'label' => 'Kode Penjahit', 'type' => 'text', 'placeholder' => 'PJT-05', 'required' => true],
                    ['name' => 'nama', 'label' => 'Nama Penjahit / Vendor', 'type' => 'text', 'placeholder' => 'Nama lengkap penjahit', 'required' => true],
                    ['name' => 'spesialisasi', 'label' => 'Spesialisasi', 'type' => 'text', 'placeholder' => 'Kebaya bordir / Kamen / Safari'],
                    ['name' => 'tarif_jahit', 'label' => 'Tarif Ongkos Jahit Satuan (Rp)', 'type' => 'number', 'placeholder' => '45000', 'required' => true],
                    ['name' => 'no_hp', 'label' => 'No. WhatsApp / HP', 'type' => 'text', 'placeholder' => '0812xxxxxxxx'],
                ];
                break;

                // ==========================================
                // 1. MASTER DATA: PELANGGAN & MEMBER (SESUAI BAB 10 & 11)
                // ==========================================
            case 'master/pelanggan':
                $config['headerIcon'] = 'bi-people';
                $config['formTitle'] = 'Form Master Data Pelanggan, Member & Reseller';
                $config['summaryCards'] = [
                    ['title' => 'Total Member Terdaftar', 'value' => '248 Member', 'icon' => 'bi-people', 'color' => 'primary'],
                    ['title' => 'Member Platinum (>1.000 Poin)', 'value' => '42 Member', 'icon' => 'bi-star', 'color' => 'warning'],
                    ['title' => 'Reseller / Mitra B2B', 'value' => '12 Butik', 'icon' => 'bi-shop', 'color' => 'success'],
                    ['title' => 'Total Poin Member', 'value' => '58.400 Poin', 'icon' => 'bi-award', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'member_code', 'label' => 'Customer / Member ID'],
                    ['key' => 'name', 'label' => 'Nama Pelanggan'],
                    ['key' => 'type', 'label' => 'Tipe Customer'],
                    ['key' => 'tier', 'label' => 'Level Member'],
                    ['key' => 'points', 'label' => 'Poin Aktif'],
                    ['key' => 'phone', 'label' => 'No. Telepon / WA'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'member_code' => 'MBR-001', 'name' => 'Ni Wayan Sukmawati', 'type' => 'Retail / Toko POS', 'tier' => 'Platinum (Diskon 5%)', 'points' => '1.250 Poin', 'phone' => '081234567890', 'status' => 'Member Aktif', 'badge' => 'success'],
                    ['id' => 2, 'member_code' => 'MBR-002', 'name' => 'I Made Suardana', 'type' => 'Retail / Toko POS', 'tier' => 'Gold (Diskon 3%)', 'points' => '480 Poin', 'phone' => '081987654321', 'status' => 'Member Aktif', 'badge' => 'success'],
                    ['id' => 3, 'member_code' => 'B2B-011', 'name' => 'Butik Sanur Indah (Ibu Desak)', 'type' => 'Reseller Grosir B2B', 'tier' => 'Mitra Reseller (Tempo 14 Hari)', 'points' => '4.200 Poin', 'phone' => '081338899001', 'status' => 'Reseller Aktif', 'badge' => 'primary'],
                    ['id' => 4, 'member_code' => 'ONL-042', 'name' => 'Dewi Lestari (Jakarta)', 'type' => 'Shopee / Online', 'tier' => 'Silver (Reguler)', 'points' => '120 Poin', 'phone' => '081299887766', 'status' => 'Customer Online', 'badge' => 'info'],
                ];
                $config['formFields'] = [
                    ['name' => 'member_code', 'label' => 'Customer ID / Nomor Member', 'type' => 'text', 'placeholder' => 'MBR-003', 'required' => true],
                    ['name' => 'nama', 'label' => 'Nama Lengkap Pelanggan', 'type' => 'text', 'placeholder' => 'Nama pelanggan', 'required' => true],
                    ['name' => 'tipe', 'label' => 'Tipe Pelanggan', 'type' => 'select', 'options' => ['Retail Toko Kasir', 'Reseller Grosir B2B', 'Marketplace Online']],
                    ['name' => 'level_member', 'label' => 'Level Member', 'type' => 'select', 'options' => ['Silver (Reguler)', 'Gold (Diskon 3%)', 'Platinum (Diskon 5%)', 'Reseller Khusus']],
                    ['name' => 'telepon', 'label' => 'No. Handphone / WhatsApp', 'type' => 'text', 'placeholder' => '0812xxxxxxxx', 'required' => true],
                ];
                break;

                // ==========================================
                // 1. MASTER DATA: USER & HAK AKSES (SESUAI BAB 10)
                // ==========================================
            case 'master/users':
                $config['headerIcon'] = 'bi-shield-lock';
                $config['formTitle'] = 'Form Tambah User Sistem & Pengaturan Hak Akses (RBAC)';
                $config['summaryCards'] = [
                    ['title' => 'Pengguna Sistem', 'value' => '6 User Aktif', 'icon' => 'bi-people', 'color' => 'primary'],
                    ['title' => 'Role Hak Akses', 'value' => '6 Role Terpisah', 'icon' => 'bi-shield-check', 'color' => 'success'],
                    ['title' => 'Audit Trail Otorisasi', 'value' => 'Aktif Tercatat', 'icon' => 'bi-key', 'color' => 'info'],
                    ['title' => 'Kasir POS Aktif', 'value' => '2 Kasir', 'icon' => 'bi-display', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'name', 'label' => 'Nama Pengguna'],
                    ['key' => 'email', 'label' => 'Email / Username'],
                    ['key' => 'role', 'label' => 'Role Hak Akses'],
                    ['key' => 'access_scope', 'label' => 'Cakupan Modul'],
                    ['key' => 'status', 'label' => 'Status Akun'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'name' => 'Administrator KlambiBali', 'email' => 'admin@klambibali.com', 'role' => 'Super Administrator', 'access_scope' => 'Akses Penuh Seluruh Modul ERP', 'status' => 'Aktif', 'badge' => 'success'],
                    ['id' => 2, 'name' => 'Ni Wayan Sukerti (Kasir POS)', 'email' => 'kasir@klambibali.com', 'role' => 'Kasir Toko (POS)', 'access_scope' => 'POS Kasir, Scan Barcode, Tutup Kasir', 'status' => 'Aktif', 'badge' => 'info'],
                    ['id' => 3, 'name' => 'I Ketut Sudira (Kepala Gudang)', 'email' => 'gudang@klambibali.com', 'role' => 'Staff Inventory', 'access_scope' => '7 Gudang, Transfer Stok, Kartu Stok, Opname', 'status' => 'Aktif', 'badge' => 'primary'],
                    ['id' => 4, 'name' => 'Ni Kadek Ayu (Supervisor QC)', 'email' => 'qc@klambibali.com', 'role' => 'Quality Control', 'access_scope' => 'Inspeksi Transit, Reject Rework, Cetak Barcode', 'status' => 'Aktif', 'badge' => 'warning'],
                    ['id' => 5, 'name' => 'I Made Dharmawan (Finance)', 'email' => 'accounting@klambibali.com', 'role' => 'Finance & Accounting', 'access_scope' => 'Kas/Bank, COA, Jurnal, Buku Besar, Laba Rugi', 'status' => 'Aktif', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'nama', 'label' => 'Nama Pengguna', 'type' => 'text', 'placeholder' => 'Nama lengkap', 'required' => true],
                    ['name' => 'email', 'label' => 'Alamat Email', 'type' => 'email', 'placeholder' => 'user@klambibali.com', 'required' => true],
                    ['name' => 'role', 'label' => 'Pilih Role Hak Akses', 'type' => 'select', 'options' => ['Super Administrator', 'Kasir Toko (POS)', 'Kepala Gudang / Logistik', 'Supervisor Quality Control', 'Finance & Accounting', 'Operator Produksi / Jahit']],
                    ['name' => 'password', 'label' => 'Kata Sandi Awal', 'type' => 'password', 'required' => true],
                ];
                break;

                // ==========================================
                // 1 & 11. MASTER DATA & ACCOUNTING: CHART OF ACCOUNTS (COA)
                // ==========================================
            case 'master/coa':
            case 'accounting/coa':
                $config['headerIcon'] = 'bi-diagram-3';
                $config['formTitle'] = 'Form Tambah Master Chart of Accounts (COA)';
                $config['summaryCards'] = [
                    ['title' => 'Total Akun COA', 'value' => '32 Akun', 'icon' => 'bi-diagram-3', 'color' => 'primary'],
                    ['title' => 'Akun Persediaan (1140-56)', 'value' => '7 Gudang Terpetakan', 'icon' => 'bi-boxes', 'color' => 'success'],
                    ['title' => 'Hutang Dagang (2110)', 'value' => 'Rp 18.500.000', 'icon' => 'bi-receipt', 'color' => 'danger'],
                    ['title' => 'Status Mapping Jurnal', 'value' => 'Otomatis Aktif', 'icon' => 'bi-check-all', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'account_code', 'label' => 'Kode Akun'],
                    ['key' => 'account_name', 'label' => 'Nama Akun'],
                    ['key' => 'category', 'label' => 'Kelompok Akun'],
                    ['key' => 'normal_balance', 'label' => 'Posisi Normal'],
                    ['key' => 'balance', 'label' => 'Saldo Saat Ini'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'account_code' => '1110', 'account_name' => 'Kas Toko / Kasir Fisik', 'category' => '1. Aset Lancar', 'normal_balance' => 'Debit', 'balance' => 'Rp 25.500.000', 'status' => 'Kasir POS', 'badge' => 'success'],
                    ['id' => 2, 'account_code' => '1120', 'account_name' => 'Bank BCA Giro Operasional', 'category' => '1. Aset Lancar', 'normal_balance' => 'Debit', 'balance' => 'Rp 112.400.000', 'status' => 'Operasional', 'badge' => 'primary'],
                    ['id' => 3, 'account_code' => '1125', 'account_name' => 'Rekening Penampungan (Shopee & TikTok Escrow)', 'category' => '1. Aset Lancar', 'normal_balance' => 'Debit', 'balance' => 'Rp 44.500.000', 'status' => 'Marketplace', 'badge' => 'info'],
                    ['id' => 4, 'account_code' => '1130', 'account_name' => 'Piutang Usaha (Pelanggan Reseller/B2B)', 'category' => '1. Aset Lancar', 'normal_balance' => 'Debit', 'balance' => 'Rp 24.150.000', 'status' => 'Piutang', 'badge' => 'warning'],
                    ['id' => 5, 'account_code' => '1140', 'account_name' => 'Persediaan Bahan Baku (Kain Yard/Meter)', 'category' => '1. Aset Lancar (Gudang)', 'normal_balance' => 'Debit', 'balance' => 'Rp 58.600.000', 'status' => 'Persediaan', 'badge' => 'success'],
                    ['id' => 6, 'account_code' => '1145', 'account_name' => 'Persediaan Barang Dalam Proses (Gudang Penjahit WIP)', 'category' => '1. Aset Lancar (Gudang)', 'normal_balance' => 'Debit', 'balance' => 'Rp 42.500.000', 'status' => 'WIP Penjahit', 'badge' => 'primary'],
                    ['id' => 7, 'account_code' => '1150', 'account_name' => 'Persediaan Barang Jadi - Gudang Utama', 'category' => '1. Aset Lancar (Gudang)', 'normal_balance' => 'Debit', 'balance' => 'Rp 229.400.000', 'status' => 'Barang Jadi FG', 'badge' => 'success'],
                    ['id' => 8, 'account_code' => '1151', 'account_name' => 'Persediaan Barang Jadi - Gudang Store POS', 'category' => '1. Aset Lancar (Gudang)', 'normal_balance' => 'Debit', 'balance' => 'Rp 54.575.000', 'status' => 'Display Store', 'badge' => 'success'],
                    ['id' => 9, 'account_code' => '1152', 'account_name' => 'Persediaan Barang Jadi - Gudang Live Selling', 'category' => '1. Aset Lancar (Gudang)', 'normal_balance' => 'Debit', 'balance' => 'Rp 51.800.000', 'status' => 'Live Selling', 'badge' => 'info'],
                    ['id' => 10, 'account_code' => '1155', 'account_name' => 'Persediaan Barang Reject & Service (Gudang Reject)', 'category' => '1. Aset Lancar (Gudang)', 'normal_balance' => 'Debit', 'balance' => 'Rp 2.775.000', 'status' => 'Reject Rework', 'badge' => 'warning'],
                    ['id' => 11, 'account_code' => '1156', 'account_name' => 'Persediaan Barang Cacat (Gudang Sale Clearance)', 'category' => '1. Aset Lancar (Gudang)', 'normal_balance' => 'Debit', 'balance' => 'Rp 1.200.000', 'status' => 'Barang Cacat', 'badge' => 'danger'],
                    ['id' => 12, 'account_code' => '2110', 'account_name' => 'Hutang Dagang Supplier (Bahan Baku & Kain)', 'category' => '2. Kewajiban Lancar', 'normal_balance' => 'Kredit', 'balance' => 'Rp 18.500.000', 'status' => 'Hutang Dagang', 'badge' => 'danger'],
                    ['id' => 13, 'account_code' => '2120', 'account_name' => 'Hutang Biaya Jahit Penjahit', 'category' => '2. Kewajiban Lancar', 'normal_balance' => 'Kredit', 'balance' => 'Rp 5.400.000', 'status' => 'Upah Jahit', 'badge' => 'danger'],
                    ['id' => 14, 'account_code' => '3100', 'account_name' => 'Modal Pemilik KlambiBali', 'category' => '3. Ekuitas', 'normal_balance' => 'Kredit', 'balance' => 'Rp 350.000.000', 'status' => 'Modal', 'badge' => 'primary'],
                    ['id' => 15, 'account_code' => '3200', 'account_name' => 'Laba Ditahan (Retained Earnings)', 'category' => '3. Ekuitas', 'normal_balance' => 'Kredit', 'balance' => 'Rp 95.275.000', 'status' => 'Ekuitas', 'badge' => 'primary'],
                    ['id' => 16, 'account_code' => '4110', 'account_name' => 'Pendapatan Penjualan Toko Kasir POS', 'category' => '4. Pendapatan', 'normal_balance' => 'Kredit', 'balance' => 'Rp 25.500.000', 'status' => 'Omzet Toko', 'badge' => 'success'],
                    ['id' => 17, 'account_code' => '4120', 'account_name' => 'Pendapatan Penjualan Shopee Marketplace', 'category' => '4. Pendapatan', 'normal_balance' => 'Kredit', 'balance' => 'Rp 48.200.000', 'status' => 'Omzet Shopee', 'badge' => 'success'],
                    ['id' => 18, 'account_code' => '4130', 'account_name' => 'Pendapatan Penjualan TikTok Shop', 'category' => '4. Pendapatan', 'normal_balance' => 'Kredit', 'balance' => 'Rp 36.450.000', 'status' => 'Omzet TikTok', 'badge' => 'success'],
                    ['id' => 19, 'account_code' => '4140', 'account_name' => 'Pendapatan Penjualan Reguler / Non-Marketplace', 'category' => '4. Pendapatan', 'normal_balance' => 'Kredit', 'balance' => 'Rp 32.700.000', 'status' => 'Omzet Reguler', 'badge' => 'success'],
                    ['id' => 20, 'account_code' => '5110', 'account_name' => 'Beban Pokok Penjualan (HPP) Kebaya Bali', 'category' => '5. Harga Pokok Penjualan', 'normal_balance' => 'Debit', 'balance' => 'Rp 68.650.000', 'status' => 'HPP Terjual', 'badge' => 'warning'],
                    ['id' => 21, 'account_code' => '6110', 'account_name' => 'Beban Gaji Karyawan Toko & Admin', 'category' => '6. Beban Operasional', 'normal_balance' => 'Debit', 'balance' => 'Rp 18.500.000', 'status' => 'Biaya Operasi', 'badge' => 'danger'],
                    ['id' => 22, 'account_code' => '6120', 'account_name' => 'Beban Sewa Outlet & Workshop', 'category' => '6. Beban Operasional', 'normal_balance' => 'Debit', 'balance' => 'Rp 12.000.000', 'status' => 'Biaya Operasi', 'badge' => 'danger'],
                    ['id' => 23, 'account_code' => '6130', 'account_name' => 'Beban Fee Layanan & Komisi Marketplace (Shopee/TikTok)', 'category' => '6. Beban Operasional', 'normal_balance' => 'Debit', 'balance' => 'Rp 6.200.000', 'status' => 'Fee Platform', 'badge' => 'danger'],
                ];
                $config['formFields'] = [
                    ['name' => 'kode_akun', 'label' => 'Kode Akun (Contoh: 1110)', 'type' => 'text', 'placeholder' => '1160', 'required' => true],
                    ['name' => 'nama_akun', 'label' => 'Nama Akun Akuntansi', 'type' => 'text', 'placeholder' => 'Nama akun', 'required' => true],
                    ['name' => 'kelompok_akun', 'label' => 'Klasifikasi / Kategori', 'type' => 'select', 'options' => ['1. Aset Lancar', '1. Aset Tetap', '2. Kewajiban Lancar', '2. Kewajiban Jangka Panjang', '3. Ekuitas', '4. Pendapatan Penjualan', '5. Beban Pokok Penjualan (HPP)', '6. Beban Operasional'], 'required' => true],
                    ['name' => 'posisi_normal', 'label' => 'Posisi Normal Saldo', 'type' => 'select', 'options' => ['Debit', 'Kredit'], 'required' => true],
                    ['name' => 'saldo_awal', 'label' => 'Saldo Awal (Rp)', 'type' => 'number', 'placeholder' => '0'],
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
                // 2. PURCHASING: PENERIMAAN BARANG & QC MASUK (SESUAI BAB 2)
                // ==========================================
            case 'purchasing/penerimaan':
                $config['headerIcon'] = 'bi-box-arrow-in-down';
                $config['formTitle'] = 'Form Penerimaan Barang, Pemeriksaan Kuantitas & Alokasi Biaya';
                $config['summaryCards'] = [
                    ['title' => 'Penerimaan Bulan Ini', 'value' => '16 Surat Jalan', 'icon' => 'bi-box-arrow-in-down', 'color' => 'success'],
                    ['title' => 'Bahan Baku Diterima', 'value' => '100 Yard Kain', 'icon' => 'bi-layers', 'color' => 'primary'],
                    ['title' => 'Penerimaan Sebagian', 'value' => '2 PO Menunggu Sisa', 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
                    ['title' => 'Alokasi Ongkir ke HPP', 'value' => 'Rp 1.450.000', 'icon' => 'bi-cash', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'receive_no', 'label' => 'No. Penerimaan (GRN)'],
                    ['key' => 'po_ref', 'label' => 'Ref. PO'],
                    ['key' => 'supplier', 'label' => 'Supplier'],
                    ['key' => 'item_received', 'label' => 'Item & Kuantitas Diterima'],
                    ['key' => 'warehouse_dest', 'label' => 'Gudang Tujuan Masuk'],
                    ['key' => 'shipping_cost', 'label' => 'Biaya Ongkir Masuk HPP'],
                    ['key' => 'status', 'label' => 'Status Penerimaan'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'receive_no' => 'GRN-202610-021', 'po_ref' => 'PO-202610-041', 'supplier' => 'CV Tekstil Bali Sejahtera', 'item_received' => 'Kain Brokat Semi Prancis (100 Yard Utuh)', 'warehouse_dest' => 'Gudang Penjahit (Wayan Artana)', 'shipping_cost' => 'Rp 150.000 (Dialokasikan)', 'status' => 'Diterima Lengkap 100%', 'badge' => 'success'],
                    ['id' => 2, 'receive_no' => 'GRN-202610-022', 'po_ref' => 'PO-202610-042', 'supplier' => 'Tenun Sidemen Klungkung', 'item_received' => 'Kain Endek Sutra Asli (40 dari 80 Meter)', 'warehouse_dest' => 'Gudang Bahan Baku (Denpasar)', 'shipping_cost' => 'Rp 50.000 (Proporsional)', 'status' => 'Penerimaan Sebagian (50%)', 'badge' => 'info'],
                    ['id' => 3, 'receive_no' => 'GRN-202610-023', 'po_ref' => 'PO-202610-043', 'supplier' => 'Toko Benang Sentosa', 'item_received' => 'Benang Bordir Metalik (50 Roll)', 'warehouse_dest' => 'Gudang Penjahit (WIP)', 'shipping_cost' => 'Rp 25.000', 'status' => 'Diterima Lengkap 100%', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'ref_po', 'label' => 'Pilih Nomor PO', 'type' => 'select', 'options' => ['PO-202610-041 - CV Tekstil Bali (100 Yard)', 'PO-202610-042 - Tenun Sidemen (Sisa 40 Meter)'], 'required' => true],
                    ['name' => 'tgl_terima', 'label' => 'Tanggal Penerimaan', 'type' => 'date', 'required' => true],
                    ['name' => 'qty_aktual', 'label' => 'Jumlah Aktual Diterima', 'type' => 'number', 'placeholder' => '100', 'required' => true],
                    ['name' => 'gudang_masuk', 'label' => 'Masuk ke Gudang', 'type' => 'select', 'options' => ['Gudang Penjahit (Raw Material/WIP)', 'Gudang Bahan Baku (Denpasar)', 'Gudang Transit (QC)'], 'required' => true],
                    ['name' => 'ongkir_masuk', 'label' => 'Biaya Terkait / Ongkir Masuk HPP (Rp)', 'type' => 'number', 'placeholder' => '150000'],
                ];
                break;

                // ==========================================
                // 2. PURCHASING: RIWAYAT & HISTORI HARGA BELI (SESUAI BAB 2)
                // ==========================================
            case 'purchasing/riwayat':
                $config['headerIcon'] = 'bi-clock-history';
                $config['formTitle'] = 'Histori Harga Pembelian per Bahan & Rekam Jejak Supplier';
                $config['summaryCards'] = [
                    ['title' => 'Histori Harga Kain Brokat', 'value' => 'Rp 75.000 / yard', 'icon' => 'bi-tag', 'color' => 'primary'],
                    ['title' => 'Histori Harga Kain Endek', 'value' => 'Rp 140.000 / meter', 'icon' => 'bi-tag-fill', 'color' => 'info'],
                    ['title' => 'Trend Harga Bahan', 'value' => 'Stabil (0% Kenaikan)', 'icon' => 'bi-graph-down', 'color' => 'success'],
                    ['title' => 'Supplier Langganan', 'value' => 'CV Tekstil Bali', 'icon' => 'bi-truck', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'date', 'label' => 'Tanggal PO'],
                    ['key' => 'material_name', 'label' => 'Nama Bahan Baku'],
                    ['key' => 'supplier', 'label' => 'Supplier'],
                    ['key' => 'unit', 'label' => 'Satuan'],
                    ['key' => 'unit_price', 'label' => 'Harga Beli Satuan'],
                    ['key' => 'allocated_fee', 'label' => 'Biaya Alokasi Ongkir'],
                    ['key' => 'effective_hpp', 'label' => 'HPP Riil Bahan'],
                    ['key' => 'trend', 'label' => 'Trend Harga'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'date' => '22 Okt 2026', 'material_name' => 'Kain Brokat Semi Prancis Maroon', 'supplier' => 'CV Tekstil Bali Sejahtera', 'unit' => 'Yard', 'unit_price' => 'Rp 75.000', 'allocated_fee' => 'Rp 1.500 / yard', 'effective_hpp' => 'Rp 76.500 / yard', 'trend' => 'Stabil', 'badge' => 'success'],
                    ['id' => 2, 'date' => '10 Sep 2026', 'material_name' => 'Kain Brokat Semi Prancis Maroon', 'supplier' => 'CV Tekstil Bali Sejahtera', 'unit' => 'Yard', 'unit_price' => 'Rp 75.000', 'allocated_fee' => 'Rp 1.500 / yard', 'effective_hpp' => 'Rp 76.500 / yard', 'trend' => 'Stabil', 'badge' => 'success'],
                    ['id' => 3, 'date' => '23 Okt 2026', 'material_name' => 'Kain Tenun Endek Sutra Asli', 'supplier' => 'Tenun Tradisional Sidemen', 'unit' => 'Meter', 'unit_price' => 'Rp 140.000', 'allocated_fee' => 'Rp 1.250 / meter', 'effective_hpp' => 'Rp 141.250 / meter', 'trend' => 'Stabil', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'filter_bahan', 'label' => 'Pilih Bahan Baku', 'type' => 'select', 'options' => ['Kain Brokat Semi Prancis (Yard)', 'Kain Tenun Endek Sutra (Meter)', 'Benang Bordir Metalik (Roll)']],
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
                // 3. INVENTORY: STOK MULTI-GUDANG PER SKU (BAB 7)
                // ==========================================
            case 'inventory/stok':
                $config['headerIcon'] = 'bi-collection';
                $config['formTitle'] = 'Filter Stok Multi-Gudang per SKU & Kategori';
                $config['summaryCards'] = [
                    ['title' => 'Stok Siap Jual (Utama+Store+Live)', 'value' => '1.815 Pcs', 'icon' => 'bi-check-circle', 'color' => 'success'],
                    ['title' => 'Stok Dalam Transit (QC)', 'value' => '60 Pcs', 'icon' => 'bi-clock-history', 'color' => 'warning'],
                    ['title' => 'Stok Reject & Service', 'value' => '15 Pcs', 'icon' => 'bi-tools', 'color' => 'danger'],
                    ['title' => 'Stok Barang Cacat (Sale)', 'value' => '8 Pcs', 'icon' => 'bi-tag', 'color' => 'secondary'],
                ];
                $config['columns'] = [
                    ['key' => 'sku', 'label' => 'Kode SKU'],
                    ['key' => 'name', 'label' => 'Nama Produk / Varian'],
                    ['key' => 'gd_utama', 'label' => 'Gudang Utama (FG)'],
                    ['key' => 'gd_store', 'label' => 'Gudang Store (POS)'],
                    ['key' => 'gd_live', 'label' => 'Gudang Live'],
                    ['key' => 'gd_transit', 'label' => 'Gudang Transit (QC)'],
                    ['key' => 'gd_reject', 'label' => 'Reject / Service'],
                    ['key' => 'gd_sale', 'label' => 'Gudang Sale (Cacat)'],
                    ['key' => 'total_all', 'label' => 'Total Fisik'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'sku' => 'KB-BRD-MRN-M', 'name' => 'Kebaya Bali Bordir Maroon M (Demo)', 'gd_utama' => '5 pcs', 'gd_store' => '29 pcs', 'gd_live' => '20 pcs', 'gd_transit' => '0 pcs', 'gd_reject' => '3 pcs', 'gd_sale' => '2 pcs', 'total_all' => '59 pcs', 'badge' => 'success'],
                    ['id' => 2, 'sku' => 'KM-EDK-GLD-ALL', 'name' => 'Kamen Endek Sutra Motif Wajik Emas', 'gd_utama' => '15 pcs', 'gd_store' => '12 pcs', 'gd_live' => '10 pcs', 'gd_transit' => '0 pcs', 'gd_reject' => '0 pcs', 'gd_sale' => '0 pcs', 'total_all' => '37 pcs', 'badge' => 'success'],
                    ['id' => 3, 'sku' => 'KMJ-SFR-WHT-XL', 'name' => 'Kemeja Safari Pria Katun Putih XL', 'gd_utama' => '4 pcs', 'gd_store' => '2 pcs', 'gd_live' => '5 pcs', 'gd_transit' => '0 pcs', 'gd_reject' => '1 pcs', 'gd_sale' => '0 pcs', 'total_all' => '12 pcs', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'filter_kategori', 'label' => 'Filter Berdasarkan Kategori', 'type' => 'select', 'options' => ['Semua Kategori', 'Kebaya Bali', 'Kamen & Bawahan', 'Kemeja Safari', 'Aksesoris']],
                ];
                break;

                // ==========================================
                // 3. INVENTORY: TRANSFER ANTAR-GUDANG (BAB 3)
                // ==========================================
            case 'inventory/transfer':
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
                // 4. PRODUKSI: HASIL PRODUKSI & SURAT JALAN TRANSIT (BAB 3 & 4)
                // ==========================================
            case 'produksi/hasil-produksi':
                $config['headerIcon'] = 'bi-check2-circle';
                $config['formTitle'] = 'Form Pencatatan Hasil Produksi Selesai & Masuk Gudang Transit';
                $config['summaryCards'] = [
                    ['title' => 'Hasil Produksi Demo', 'value' => '60 Pcs Kebaya', 'icon' => 'bi-box-seam', 'color' => 'success'],
                    ['title' => 'Gudang Penampung', 'value' => 'Gudang Transit (QC)', 'icon' => 'bi-clock-history', 'color' => 'warning'],
                    ['title' => 'Status Barang', 'value' => 'Belum Siap Jual', 'icon' => 'bi-shield-exclamation', 'color' => 'danger'],
                    ['title' => 'Tahap Berikutnya', 'value' => 'Inspeksi Tim QC', 'icon' => 'bi-patch-check', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'finish_code', 'label' => 'No. Bukti Selesai'],
                    ['key' => 'wo_ref', 'label' => 'No. Work Order'],
                    ['key' => 'product', 'label' => 'Model Produk Jadi'],
                    ['key' => 'vendor', 'label' => 'Penjahit'],
                    ['key' => 'qty_finished', 'label' => 'Jumlah Selesai'],
                    ['key' => 'destination', 'label' => 'Gudang Penerima'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'finish_code' => 'PRD-202610-001', 'wo_ref' => 'WO-202610-001', 'product' => 'Kebaya Bali Bordir Maroon M', 'vendor' => 'I Wayan Artana', 'qty_finished' => '60 Pcs', 'destination' => 'Gudang Transit (Menunggu QC)', 'status' => 'Antre Pemeriksaan Kualitas', 'badge' => 'warning'],
                    ['id' => 2, 'finish_code' => 'PRD-202610-002', 'wo_ref' => 'WO-202610-002', 'product' => 'Kamen Endek Tenun Hitam Emas', 'vendor' => 'Tenun Sidemen Klungkung', 'qty_finished' => '32 Pcs', 'destination' => 'Gudang Transit (Menunggu QC)', 'status' => 'Antre Pemeriksaan Kualitas', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'wo_ref', 'label' => 'Pilih Work Order', 'type' => 'select', 'options' => ['WO-202610-001 - Kebaya Bali Bordir Maroon (Target 60 Pcs)'], 'required' => true],
                    ['name' => 'qty_selesai', 'label' => 'Jumlah Selesai Jahit (Pcs)', 'type' => 'number', 'placeholder' => '60', 'required' => true],
                    ['name' => 'tgl_selesai', 'label' => 'Tanggal Penyerahan Hasil', 'type' => 'date', 'required' => true],
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
                // 5. QC: GUDANG REJECT & BIAYA SERVICE (BAB 3 & 5)
                // ==========================================
            case 'qc/reject':
            case 'qc/perbaikan':
                $config['headerIcon'] = 'bi-tools';
                $config['formTitle'] = 'Manajemen Produk Reject, Alur Rework & Biaya Service';
                $config['summaryCards'] = [
                    ['title' => 'Stok di Gudang Reject', 'value' => '15 Pcs', 'icon' => 'bi-tools', 'color' => 'danger'],
                    ['title' => 'Demo Kebaya (WO-001)', 'value' => '3 Pcs Rework', 'icon' => 'bi-gear', 'color' => 'warning'],
                    ['title' => 'Biaya Service per Pcs', 'value' => 'Rp 15.000', 'icon' => 'bi-cash', 'color' => 'info'],
                    ['title' => 'Jadwal QC Ulang', 'value' => '25 Okt 2026', 'icon' => 'bi-calendar-check', 'color' => 'success'],
                ];
                $config['columns'] = [
                    ['key' => 'service_no', 'label' => 'No. Tiket Service'],
                    ['key' => 'product', 'label' => 'Item Produk'],
                    ['key' => 'defect_type', 'label' => 'Alasan Reject'],
                    ['key' => 'qty', 'label' => 'Kuantitas'],
                    ['key' => 'repair_cost', 'label' => 'Biaya Service'],
                    ['key' => 'service_vendor', 'label' => 'Penjahit Rework'],
                    ['key' => 'status', 'label' => 'Status Service'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'service_no' => 'SRV-202610-001', 'product' => 'Kebaya Bali Bordir Maroon M (WO-001)', 'defect_type' => 'Jahitan samping lepas & payet kendor', 'qty' => '3 pcs', 'repair_cost' => 'Rp 45.000 (@ 15rb)', 'service_vendor' => 'I Wayan Artana', 'status' => 'Sedang Jahit Ulang', 'badge' => 'warning'],
                    ['id' => 2, 'service_no' => 'SRV-202610-002', 'product' => 'Kemeja Safari Pria Katun Putih', 'defect_type' => 'Kancing leher miring', 'qty' => '1 pcs', 'repair_cost' => 'Rp 10.000', 'service_vendor' => 'Sukawati Garment', 'status' => 'Selesai QC Ulang (Lolos)', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'ref_reject', 'label' => 'Pilih Batch Reject', 'type' => 'select', 'options' => ['SRV-202610-001 - Kebaya Bali Maroon (3 Pcs)'], 'required' => true],
                    ['name' => 'biaya_service', 'label' => 'Biaya Service Total (Rp)', 'type' => 'number', 'placeholder' => '45000', 'required' => true],
                    ['name' => 'tindakan', 'label' => 'Tindakan QC Ulang', 'type' => 'select', 'options' => ['Lolos QC Ulang -> Transfer ke Gudang Utama (FG)', 'Gagal Tetap -> Transfer ke Gudang Sale (Cacat)']],
                ];
                break;

                // ==========================================
                // 5. QC: KELOLA SKU & CETAK BARCODE (BAB 5)
                // ==========================================
            case 'qc/barcode':
                $config['headerIcon'] = 'bi-upc-scan';
                $config['formTitle'] = 'Penerbitan Barcode & Cetak Label Produk Lolos QC';
                $config['summaryCards'] = [
                    ['title' => 'Barcode Diterbitkan', 'value' => '55 Label (WO-001)', 'icon' => 'bi-printer', 'color' => 'success'],
                    ['title' => 'Format Barcode', 'value' => 'EAN-13 / Code-128', 'icon' => 'bi-upc', 'color' => 'primary'],
                    ['title' => 'Pencegahan Duplikasi', 'value' => 'Aktif (Strict SKU)', 'icon' => 'bi-shield-check', 'color' => 'info'],
                    ['title' => 'Gudang Penampung', 'value' => 'Gudang Utama (FG)', 'icon' => 'bi-boxes', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'sku', 'label' => 'Kode SKU'],
                    ['key' => 'barcode_num', 'label' => 'Nomor Barcode'],
                    ['key' => 'product_name', 'label' => 'Nama Produk & Ukuran'],
                    ['key' => 'batch', 'label' => 'Batch Produksi'],
                    ['key' => 'qty_printed', 'label' => 'Qty Cetak Label'],
                    ['key' => 'status', 'label' => 'Status Distribusi'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'sku' => 'KB-BRD-MRN-M', 'barcode_num' => '899100100101', 'product_name' => 'Kebaya Bali Bordir Maroon (Size M)', 'batch' => 'WO-202610-001 (55 Pcs Lolos)', 'qty_printed' => '55 Stiker Barcode', 'status' => 'Masuk Gudang Utama', 'badge' => 'success'],
                    ['id' => 2, 'sku' => 'KM-EDK-GLD-ALL', 'barcode_num' => '899100100103', 'product_name' => 'Kamen Endek Sutra Motif Wajik Emas', 'batch' => 'WO-202610-002 (32 Pcs Lolos)', 'qty_printed' => '32 Stiker Barcode', 'status' => 'Masuk Gudang Utama', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'sku', 'label' => 'Pilih Produk / SKU', 'type' => 'select', 'options' => ['KB-BRD-MRN-M - Kebaya Bali Maroon M (55 Pcs Lolos QC)'], 'required' => true],
                    ['name' => 'qty_cetak', 'label' => 'Jumlah Lembar Barcode Dicetak', 'type' => 'number', 'placeholder' => '55', 'required' => true],
                    ['name' => 'printer', 'label' => 'Printer Thermal Barcode', 'type' => 'select', 'options' => ['Thermal Barcode Printer Toko (USB)', 'Xprinter XP-420B (Label 50x30mm)']],
                ];
                break;

                // ==========================================
                // 6. PENJUALAN REGULER & TRANSAKSI (SESUAI BAB 6)
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
                // 6. PENJUALAN: PEMBAYARAN & PIUTANG (BAB 6)
                // ==========================================
            case 'penjualan/pembayaran':
                $config['headerIcon'] = 'bi-wallet2';
                $config['formTitle'] = 'Histori Pembayaran Transaksi Penjualan & Pelunasan';
                $config['summaryCards'] = [
                    ['title' => 'Pembayaran Kasir POS', 'value' => 'Rp 25.500.000', 'icon' => 'bi-calculator', 'color' => 'success'],
                    ['title' => 'Pencairan Shopee/TikTok', 'value' => 'Rp 84.650.000', 'icon' => 'bi-shop', 'color' => 'info'],
                    ['title' => 'Piutang B2B Berjalan', 'value' => 'Rp 24.150.000', 'icon' => 'bi-clock', 'color' => 'warning'],
                    ['title' => 'Metode Split Payment', 'value' => '18 Transaksi', 'icon' => 'bi-pie-chart', 'color' => 'primary'],
                ];
                $config['columns'] = [
                    ['key' => 'receipt_no', 'label' => 'No. Bukti Bayar'],
                    ['key' => 'inv_ref', 'label' => 'No. Invoice'],
                    ['key' => 'channel', 'label' => 'Channel'],
                    ['key' => 'customer', 'label' => 'Pelanggan'],
                    ['key' => 'amount', 'label' => 'Nominal Bayar'],
                    ['key' => 'method', 'label' => 'Metode Pembayaran'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'receipt_no' => 'PAY-202610-101', 'inv_ref' => 'INV-202610-089', 'channel' => 'Toko Kasir POS', 'customer' => 'Ni Wayan Sukmawati', 'amount' => 'Rp 835.000', 'method' => 'Split (Cash 500rb + QRIS 335rb)', 'status' => 'Lunas Masuk Kas', 'badge' => 'success'],
                    ['id' => 2, 'receipt_no' => 'PAY-202610-102', 'inv_ref' => 'INV-202610-092', 'channel' => 'Penjualan Reguler B2B', 'customer' => 'Butik Sanur Indah', 'amount' => 'Rp 4.200.000', 'method' => 'Transfer Bank BCA (DP 50%)', 'status' => 'DP Terbayar', 'badge' => 'info'],
                ];
                $config['formFields'] = [
                    ['name' => 'inv_ref', 'label' => 'Nomor Invoice', 'type' => 'text', 'placeholder' => 'INV-202610-093', 'required' => true],
                    ['name' => 'nominal', 'label' => 'Nominal Pembayaran', 'type' => 'number', 'placeholder' => '0', 'required' => true],
                    ['name' => 'metode', 'label' => 'Metode Pembayaran', 'type' => 'select', 'options' => ['Cash (Tunai)', 'QRIS', 'Debit BCA / EDC', 'Split Payment (Cash + QRIS)', 'Transfer Bank BCA']],
                ];
                break;

                // ==========================================
                // 7. INTEGRASI MARKETPLACE (SESUAI BAB 6.2)
                // ==========================================
            case 'marketplace/pesanan':
            case 'marketplace/produk':
            case 'marketplace/koneksi':
            case 'marketplace/index':
                $config['headerIcon'] = 'bi-shop';
                $config['formTitle'] = 'Sinkronisasi Pesanan, Stok & Settlement Marketplace (Shopee & TikTok Shop)';
                $config['summaryCards'] = [
                    ['title' => 'Pesanan Shopee', 'value' => '112 Order (Rp 48,2 Jt)', 'icon' => 'bi-bag', 'color' => 'warning'],
                    ['title' => 'Pesanan TikTok Shop', 'value' => '84 Order (Rp 36,4 Jt)', 'icon' => 'bi-tiktok', 'color' => 'dark'],
                    ['title' => 'Sinkron Stok Otomatis', 'value' => 'Gudang Live & FG', 'icon' => 'bi-arrow-repeat', 'color' => 'success'],
                    ['title' => 'Status Settlement Bank', 'value' => 'Terkoneksi BCA', 'icon' => 'bi-bank', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'order_id', 'label' => 'Order ID Marketplace'],
                    ['key' => 'platform', 'label' => 'Platform'],
                    ['key' => 'resi', 'label' => 'No. Resi (4 Digit)'],
                    ['key' => 'sku_item', 'label' => 'Produk Dipesan'],
                    ['key' => 'gross_amount', 'label' => 'Nilai Kotor'],
                    ['key' => 'platform_fee', 'label' => 'Biaya Layanan'],
                    ['key' => 'net_settlement', 'label' => 'Pencairan Bersih Bank'],
                    ['key' => 'order_status', 'label' => 'Status Pesanan'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'order_id' => '241024SHP98201', 'platform' => 'Shopee', 'resi' => 'SPXID02919821 (...9821)', 'sku_item' => 'KB-BRD-MRN-M - Kebaya Maroon (1)', 'gross_amount' => 'Rp 385.000', 'platform_fee' => 'Rp 19.250 (5%)', 'net_settlement' => 'Rp 365.750', 'order_status' => 'Perlu Dikirim', 'badge' => 'warning'],
                    ['id' => 2, 'order_id' => 'TT-89201928019', 'platform' => 'TikTok Shop', 'resi' => 'TKID892014410 (...4410)', 'sku_item' => 'KM-EDK-GLD-ALL - Kamen Endek (1)', 'gross_amount' => 'Rp 450.000', 'platform_fee' => 'Rp 27.000 (6%)', 'net_settlement' => 'Rp 423.000', 'order_status' => 'Dalam Pengiriman', 'badge' => 'primary'],
                    ['id' => 3, 'order_id' => '241023SHP11029', 'platform' => 'Shopee', 'resi' => 'SPXID02915502 (...5502)', 'sku_item' => 'SLD-OBI-TRD-RED - Selendang (3)', 'gross_amount' => 'Rp 255.000', 'platform_fee' => 'Rp 12.750', 'net_settlement' => 'Rp 242.250', 'order_status' => 'Selesai & Cair ke Bank', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'platform', 'label' => 'Pilih Marketplace', 'type' => 'select', 'options' => ['Shopee Indonesia', 'TikTok Shop Indonesia']],
                    ['name' => 'aksi', 'label' => 'Aksi Sinkronisasi', 'type' => 'select', 'options' => ['Tarik Pesanan Baru (Fetch Orders)', 'Kirim Stok ERP ke Marketplace (Push Stock)', 'Rekonsiliasi Settlement Pencairan Bank']],
                ];
                break;

                // ==========================================
                // 8. POS: RIWAYAT & REKONSILIASI KASIR (BAB 11)
                // ==========================================
            case 'pos/riwayat':
                $config['headerIcon'] = 'bi-receipt-cutoff';
                $config['formTitle'] = 'Histori Transaksi Kasir POS, Audit Trail Void & Tutup Kasir';
                $config['summaryCards'] = [
                    ['title' => 'Penjualan Kasir Hari Ini', 'value' => 'Rp 8.350.000', 'icon' => 'bi-calculator', 'color' => 'success'],
                    ['title' => 'Tunai Fisik Kasir', 'value' => 'Rp 4.520.000', 'icon' => 'bi-cash', 'color' => 'primary'],
                    ['title' => 'Non-Tunai (QRIS & EDC)', 'value' => 'Rp 3.830.000', 'icon' => 'bi-qr-code', 'color' => 'info'],
                    ['title' => 'Transaksi Void (Batal)', 'value' => '1 Transaksi (PIN OTP)', 'icon' => 'bi-shield-lock', 'color' => 'danger'],
                ];
                $config['columns'] = [
                    ['key' => 'trx_no', 'label' => 'No. Struk POS'],
                    ['key' => 'time', 'label' => 'Waktu Transaksi'],
                    ['key' => 'customer', 'label' => 'Customer / Member'],
                    ['key' => 'total_sale', 'label' => 'Total Belanja'],
                    ['key' => 'split_detail', 'label' => 'Rincian Pembayaran'],
                    ['key' => 'cashier', 'label' => 'Nama Kasir'],
                    ['key' => 'status', 'label' => 'Status Transaksi'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'trx_no' => 'POS-202610-089', 'time' => '24 Okt 14:45', 'customer' => 'Ni Wayan Sukmawati (MBR-001 Platinum)', 'total_sale' => 'Rp 835.000', 'split_detail' => 'Split (Tunai 500rb + QRIS BCA 335rb)', 'cashier' => 'Ni Wayan Sukerti', 'status' => 'Lunas & Struk Dicetak', 'badge' => 'success'],
                    ['id' => 2, 'trx_no' => 'POS-202610-088', 'time' => '24 Okt 13:10', 'customer' => 'I Made Suardana (MBR-002 Gold)', 'total_sale' => 'Rp 920.000', 'split_detail' => 'Debit BCA EDC', 'cashier' => 'Ni Wayan Sukerti', 'status' => 'Lunas', 'badge' => 'success'],
                    ['id' => 3, 'trx_no' => 'POS-202610-087', 'time' => '24 Okt 11:20', 'customer' => 'Pelanggan Umum', 'total_sale' => 'Rp 385.000', 'split_detail' => 'Dibatalkan Supervisor (OTP #882190)', 'cashier' => 'Ni Wayan Sukerti', 'status' => 'Void / Dibatalkan Otorisasi', 'badge' => 'danger'],
                ];
                $config['formFields'] = [
                    ['name' => 'tgl_kasir', 'label' => 'Pilih Tanggal Tutup Kasir', 'type' => 'date', 'required' => true],
                    ['name' => 'uang_fisik', 'label' => 'Hasil Hitung Uang Fisik Kasir (Rp)', 'type' => 'number', 'placeholder' => '4520000', 'required' => true],
                ];
                break;

                // ==========================================
                // 9. RETURN PAKET (SESUAI BAB 14)
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
                // 10. FINANCE: BUKU KAS & BANK (SESUAI BAB 8)
                // ==========================================
            case 'finance/arus-kas':
            case 'finance/index':
                $config['headerIcon'] = 'bi-cash-coin';
                $config['formTitle'] = 'Buku Kas & Bank, Pencatatan Mutasi Keuangan';
                $config['summaryCards'] = [
                    ['title' => 'Saldo Kas Toko Fisik (1110)', 'value' => 'Rp 25.500.000', 'icon' => 'bi-calculator', 'color' => 'success'],
                    ['title' => 'Saldo Bank BCA Giro (1120)', 'value' => 'Rp 112.400.000', 'icon' => 'bi-bank', 'color' => 'primary'],
                    ['title' => 'Saldo Bank Mandiri (1121)', 'value' => 'Rp 44.500.000', 'icon' => 'bi-credit-card', 'color' => 'info'],
                    ['title' => 'Total Kas & Setara Kas', 'value' => 'Rp 182.400.000', 'icon' => 'bi-wallet2', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'trx_no', 'label' => 'No. Transaksi'],
                    ['key' => 'date', 'label' => 'Tanggal'],
                    ['key' => 'type', 'label' => 'Jenis'],
                    ['key' => 'account', 'label' => 'Akun Kas / Bank'],
                    ['key' => 'category', 'label' => 'Kategori Transaksi (PDF Bab 8)'],
                    ['key' => 'amount', 'label' => 'Nominal (Rp)'],
                    ['key' => 'description', 'label' => 'Uraian Bukti Transaksi'],
                    ['key' => 'status', 'label' => 'Jurnal'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'trx_no' => 'BKM-202610-091', 'date' => '24 Okt 2026', 'type' => 'Pemasukan (+)', 'account' => 'Kas Toko (1110)', 'category' => 'Pendapatan Penjualan Toko POS', 'amount' => 'Rp 4.520.000', 'description' => 'Setoran transaksi kasir harian', 'status' => 'Posting Otomatis', 'badge' => 'success'],
                    ['id' => 2, 'trx_no' => 'BKK-202610-041', 'date' => '23 Okt 2026', 'type' => 'Pengeluaran (-)', 'account' => 'Bank BCA Giro (1120)', 'category' => 'Pembayaran Hutang Supplier', 'amount' => 'Rp 5.000.000', 'description' => 'Pelunasan parsial PO-041 CV Tekstil Bali', 'status' => 'Posting Otomatis', 'badge' => 'danger'],
                    ['id' => 3, 'trx_no' => 'BKK-202610-042', 'date' => '24 Okt 2026', 'type' => 'Pengeluaran (-)', 'account' => 'Bank BCA Giro (1120)', 'category' => 'Biaya Operasional (Listrik & Air)', 'amount' => 'Rp 2.850.000', 'description' => 'Tagihan PLN Workshop & Toko Kuta', 'status' => 'Posting Otomatis', 'badge' => 'danger'],
                ];
                $config['formFields'] = [
                    ['name' => 'jenis_mutasi', 'label' => 'Jenis Transaksi Kas/Bank', 'type' => 'select', 'options' => ['Pemasukan (Uang Masuk)', 'Pengeluaran (Uang Keluar)', 'Transfer Kas Antar-Rekening'], 'required' => true],
                    ['name' => 'rekening_akun', 'label' => 'Pilih Akun Kas / Bank', 'type' => 'select', 'options' => ['Kas Toko Kasir Fisik (1110)', 'Bank BCA Giro Operasional (1120)', 'Bank Mandiri Rekening Usaha (1121)'], 'required' => true],
                    ['name' => 'kategori_transaksi', 'label' => 'Kategori Transaksi (Sesuai Bab 8)', 'type' => 'select', 'options' => ['Pembelian Persediaan / Bahan Baku', 'Pembayaran Hutang Supplier', 'Biaya Operasional', 'Uang Muka (DP)', 'Penerimaan Penjualan'], 'required' => true],
                    ['name' => 'nominal', 'label' => 'Nominal Rupiah (Rp)', 'type' => 'number', 'placeholder' => '0', 'required' => true],
                    ['name' => 'keterangan', 'label' => 'Keterangan Bukti Transaksi', 'type' => 'text', 'placeholder' => 'Nomor nota / bukti transfer...', 'required' => true],
                ];
                break;

                // ==========================================
                // 10. FINANCE: HUTANG USAHA & AGING JATUH TEMPO (SESUAI BAB 8)
                // ==========================================
            case 'finance/utang-piutang':
            case 'purchasing/utang':
                $config['headerIcon'] = 'bi-receipt';
                $config['formTitle'] = 'Form Jadwal Pembayaran Hutang Supplier & Aging Schedule';
                $config['summaryCards'] = [
                    ['title' => 'Total Hutang Supplier', 'value' => 'Rp 18.500.000', 'icon' => 'bi-receipt', 'color' => 'danger'],
                    ['title' => 'Jatuh Tempo < 7 Hari', 'value' => 'Rp 7.500.000', 'icon' => 'bi-alarm', 'color' => 'warning'],
                    ['title' => 'Sudah Dibayar (DP/Lunas)', 'value' => 'Rp 49.900.000', 'icon' => 'bi-check2-circle', 'color' => 'success'],
                    ['title' => 'Jumlah Supplier Aktif', 'value' => '4 Supplier', 'icon' => 'bi-truck', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'invoice_no', 'label' => 'No. Faktur / PO'],
                    ['key' => 'supplier', 'label' => 'Nama Supplier'],
                    ['key' => 'date', 'label' => 'Tanggal Transaksi'],
                    ['key' => 'due_date', 'label' => 'Jatuh Tempo'],
                    ['key' => 'total_bill', 'label' => 'Total Tagihan'],
                    ['key' => 'paid', 'label' => 'Sudah Dibayar'],
                    ['key' => 'remaining', 'label' => 'Sisa Hutang'],
                    ['key' => 'aging_status', 'label' => 'Status Jatuh Tempo'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'invoice_no' => 'PO-202610-041', 'supplier' => 'CV Tekstil Bali Sejahtera', 'date' => '22 Okt 2026', 'due_date' => '21 Nov 2026 (Tempo 30 Hari)', 'total_bill' => 'Rp 7.500.000', 'paid' => 'Rp 0', 'remaining' => 'Rp 7.500.000', 'aging_status' => 'Belum Jatuh Tempo (Kain 100 Yd)', 'badge' => 'warning'],
                    ['id' => 2, 'invoice_no' => 'PO-202610-042', 'supplier' => 'Tenun Tradisional Sidemen', 'date' => '23 Okt 2026', 'due_date' => '06 Nov 2026 (Tempo 14 Hari)', 'total_bill' => 'Rp 11.200.000', 'paid' => 'Rp 5.600.000 (DP 50%)', 'remaining' => 'Rp 5.600.000', 'aging_status' => 'Belum Jatuh Tempo', 'badge' => 'info'],
                    ['id' => 3, 'invoice_no' => 'PO-202610-038', 'supplier' => 'UD Pelengkap Garmen', 'date' => '10 Okt 2026', 'due_date' => '24 Okt 2026 (Hari Ini)', 'total_bill' => 'Rp 5.400.000', 'paid' => 'Rp 0', 'remaining' => 'Rp 5.400.000', 'aging_status' => 'Jatuh Tempo Hari Ini!', 'badge' => 'danger'],
                ];
                $config['formFields'] = [
                    ['name' => 'ref_po', 'label' => 'Pilih Tagihan PO Supplier', 'type' => 'select', 'options' => ['PO-202610-041 - CV Tekstil Bali (Rp 7.500.000)', 'PO-202610-042 - Tenun Sidemen (Sisa Rp 5.600.000)', 'PO-202610-038 - UD Pelengkap Garmen (Rp 5.400.000)'], 'required' => true],
                    ['name' => 'tgl_bayar', 'label' => 'Tanggal Pembayaran', 'type' => 'date', 'required' => true],
                    ['name' => 'jumlah_bayar', 'label' => 'Jumlah Bayar / Pelunasan (Rp)', 'type' => 'number', 'placeholder' => '7500000', 'required' => true],
                    ['name' => 'rekening_sumber', 'label' => 'Dibayar Dari Rekening', 'type' => 'select', 'options' => ['Bank BCA Giro Operasional (1120)', 'Bank Mandiri Usaha (1121)', 'Kas Toko (1110)'], 'required' => true],
                    ['name' => 'bukti_transfer', 'label' => 'Nomor Bukti Transfer / Referensi Bank', 'type' => 'text', 'placeholder' => 'TRF-BCA-981290'],
                ];
                break;

                // ==========================================
                // 10. FINANCE: PIUTANG PELANGGAN (SESUAI BAB 8)
                // ==========================================
            case 'finance/piutang':
                $config['headerIcon'] = 'bi-credit-card-2-front';
                $config['formTitle'] = 'Form Pencatatan & Penagihan Piutang Pelanggan B2B / Reseller';
                $config['summaryCards'] = [
                    ['title' => 'Total Piutang Berjalan', 'value' => 'Rp 24.150.000', 'icon' => 'bi-credit-card', 'color' => 'primary'],
                    ['title' => 'Pelanggan Reseller', 'value' => '3 Toko Butik', 'icon' => 'bi-shop', 'color' => 'info'],
                    ['title' => 'Jatuh Tempo Pekan Ini', 'value' => 'Rp 8.400.000', 'icon' => 'bi-alarm', 'color' => 'warning'],
                    ['title' => 'Pelunasan Bulan Ini', 'value' => 'Rp 45.200.000', 'icon' => 'bi-check-circle', 'color' => 'success'],
                ];
                $config['columns'] = [
                    ['key' => 'inv_ref', 'label' => 'No. Invoice Penjualan'],
                    ['key' => 'customer', 'label' => 'Nama Butik / Reseller'],
                    ['key' => 'date', 'label' => 'Tanggal Order'],
                    ['key' => 'due_date', 'label' => 'Jatuh Tempo'],
                    ['key' => 'total_sale', 'label' => 'Total Belanja'],
                    ['key' => 'paid', 'label' => 'Terbayar (DP)'],
                    ['key' => 'balance', 'label' => 'Sisa Piutang'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'inv_ref' => 'INV-202610-092', 'customer' => 'Butik Sanur Indah', 'date' => '24 Okt 2026', 'due_date' => '07 Nov 2026 (Tempo 14 Hari)', 'total_sale' => 'Rp 8.400.000', 'paid' => 'Rp 4.200.000 (DP 50%)', 'balance' => 'Rp 4.200.000', 'status' => 'DP Terbayar', 'badge' => 'info'],
                    ['id' => 2, 'inv_ref' => 'INV-202610-078', 'customer' => 'Koleksi Busana Seminyak', 'date' => '15 Okt 2026', 'due_date' => '29 Okt 2026', 'total_sale' => 'Rp 12.500.000', 'paid' => 'Rp 0', 'balance' => 'Rp 12.500.000', 'status' => 'Menunggu Pelunasan', 'badge' => 'warning'],
                    ['id' => 3, 'inv_ref' => 'INV-202610-065', 'customer' => 'Grosir Kebaya Ubud', 'date' => '08 Okt 2026', 'due_date' => '22 Okt 2026', 'total_sale' => 'Rp 7.450.000', 'paid' => 'Rp 0', 'balance' => 'Rp 7.450.000', 'status' => 'Jatuh Tempo Lewat 2 Hari', 'badge' => 'danger'],
                ];
                $config['formFields'] = [
                    ['name' => 'customer_id', 'label' => 'Pilih Pelanggan / Reseller', 'type' => 'select', 'options' => ['Butik Sanur Indah', 'Koleksi Busana Seminyak', 'Grosir Kebaya Ubud'], 'required' => true],
                    ['name' => 'tgl_bayar', 'label' => 'Tanggal Penerimaan Uang', 'type' => 'date', 'required' => true],
                    ['name' => 'jumlah_terima', 'label' => 'Nominal Uang Diterima (Rp)', 'type' => 'number', 'placeholder' => '4200000', 'required' => true],
                    ['name' => 'rekening_masuk', 'label' => 'Uang Masuk ke Akun', 'type' => 'select', 'options' => ['Bank BCA Giro Operasional (1120)', 'Kas Toko (1110)'], 'required' => true],
                ];
                break;

                // ==========================================
                // 10. FINANCE: BIAYA OPERASIONAL (SESUAI BAB 8)
                // ==========================================
            case 'finance/biaya-operasional':
                $config['headerIcon'] = 'bi-journal-text';
                $config['formTitle'] = 'Form Pencatatan Biaya Operasional & Bukti Transaksi';
                $config['summaryCards'] = [
                    ['title' => 'Biaya Operasional Bulan Ini', 'value' => 'Rp 44.950.000', 'icon' => 'bi-cash-coin', 'color' => 'danger'],
                    ['title' => 'Gaji & Upah Karyawan', 'value' => 'Rp 18.500.000', 'icon' => 'bi-people', 'color' => 'primary'],
                    ['title' => 'Sewa Gedung & Outlet', 'value' => 'Rp 12.000.000', 'icon' => 'bi-building', 'color' => 'info'],
                    ['title' => 'Fee Platform Marketplace', 'value' => 'Rp 6.200.000', 'icon' => 'bi-shop', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'voucher_no', 'label' => 'No. Bukti Pengeluaran'],
                    ['key' => 'date', 'label' => 'Tanggal'],
                    ['key' => 'category', 'label' => 'Kategori Beban (COA)'],
                    ['key' => 'description', 'label' => 'Uraian Biaya'],
                    ['key' => 'payment_source', 'label' => 'Dibayar Dari'],
                    ['key' => 'amount', 'label' => 'Nominal (Rp)'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'voucher_no' => 'BKK-202610-011', 'date' => '24 Okt 2026', 'category' => '6130 - Beban Listrik, Air & Internet', 'description' => 'Tagihan PLN Workshop Konveksi & Outlet Denpasar', 'payment_source' => 'Bank BCA Giro Operasional', 'amount' => 'Rp 2.850.000', 'status' => 'Lunas', 'badge' => 'success'],
                    ['id' => 2, 'voucher_no' => 'BKK-202610-012', 'date' => '23 Okt 2026', 'category' => '6150 - Beban Packaging & Ekspedisi', 'description' => 'Pembelian 1.000 pcs Plastik Klip Kebaya & Stiker Barcode', 'payment_source' => 'Kas Toko (Kasir Fisik)', 'amount' => 'Rp 2.400.000', 'status' => 'Lunas', 'badge' => 'success'],
                    ['id' => 3, 'voucher_no' => 'BKK-202610-013', 'date' => '24 Okt 2026', 'category' => '6160 - Biaya Perbaikan (Service Reject)', 'description' => 'Ongkos Rework Jahit Ulang 3 Pcs Kebaya Reject Gudang Service', 'payment_source' => 'Kas Toko (Kasir Fisik)', 'amount' => 'Rp 45.000', 'status' => 'Lunas (Rework)', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'no_bukti', 'label' => 'Nomor Bukti Kas Keluar', 'type' => 'text', 'placeholder' => 'BKK-202610-014', 'required' => true],
                    ['name' => 'tgl_biaya', 'label' => 'Tanggal Transaksi', 'type' => 'date', 'required' => true],
                    ['name' => 'kategori_coa', 'label' => 'Kategori Beban Akuntansi', 'type' => 'select', 'options' => ['6110 - Beban Gaji & Upah Karyawan', '6120 - Beban Sewa Outlet & Workshop', '6130 - Beban Listrik, Air & Internet', '6140 - Beban Fee Layanan Marketplace', '6150 - Beban Packaging & Ekspedisi', '6160 - Beban Perbaikan Produk (Service Rework)'], 'required' => true],
                    ['name' => 'keterangan', 'label' => 'Uraian Pengeluaran', 'type' => 'text', 'placeholder' => 'Tuliskan deskripsi biaya...', 'required' => true],
                    ['name' => 'nominal', 'label' => 'Nominal Rupiah (Rp)', 'type' => 'number', 'placeholder' => '0', 'required' => true],
                    ['name' => 'sumber_dana', 'label' => 'Sumber Kas / Bank', 'type' => 'select', 'options' => ['Bank BCA Giro Operasional (1120)', 'Kas Toko (Kasir Fisik) (1110)'], 'required' => true],
                ];
                break;

                // ==========================================
                // 10. FINANCE: REKONSILIASI BANK (SESUAI BAB 8)
                // ==========================================
            case 'finance/rekonsiliasi':
                $config['headerIcon'] = 'bi-bank';
                $config['formTitle'] = 'Rekonsiliasi Mutasi Rekening Bank vs Pencatatan Transaksi ERP';
                $config['summaryCards'] = [
                    ['title' => 'Saldo Rekening Koran BCA', 'value' => 'Rp 112.400.000', 'icon' => 'bi-bank', 'color' => 'primary'],
                    ['title' => 'Saldo Buku Besar Kas/Bank', 'value' => 'Rp 112.400.000', 'icon' => 'bi-journal-check', 'color' => 'success'],
                    ['title' => 'Selisih Rekonsiliasi', 'value' => 'Rp 0 (Cocok / Matched)', 'icon' => 'bi-check-all', 'color' => 'info'],
                    ['title' => 'Transaksi Terverifikasi', 'value' => '100% Cocok', 'icon' => 'bi-shield-check', 'color' => 'success'],
                ];
                $config['columns'] = [
                    ['key' => 'trx_date', 'label' => 'Tanggal'],
                    ['key' => 'bank_desc', 'label' => 'Deskripsi Mutasi Rekening Bank'],
                    ['key' => 'erp_ref', 'label' => 'Ref. Transaksi ERP'],
                    ['key' => 'amount_in', 'label' => 'Mutasi Masuk (Kredit Bank)'],
                    ['key' => 'amount_out', 'label' => 'Mutasi Keluar (Debit Bank)'],
                    ['key' => 'match_status', 'label' => 'Status Rekonsiliasi'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'trx_date' => '24 Okt 2026', 'bank_desc' => 'TRF SETTLEMENT SHOPEEPAY INDONESIA', 'erp_ref' => 'Settlement Shopee 24 Okt', 'amount_in' => 'Rp 14.820.000', 'amount_out' => '-', 'match_status' => 'Cocok Otomatis', 'badge' => 'success'],
                    ['id' => 2, 'trx_date' => '24 Okt 2026', 'bank_desc' => 'TRF M-BANKING NI WAYAN SUKMAWATI', 'erp_ref' => 'INV-202610-089 (POS Kasir)', 'amount_in' => 'Rp 335.000', 'amount_out' => '-', 'match_status' => 'Cocok Otomatis', 'badge' => 'success'],
                    ['id' => 3, 'trx_date' => '23 Okt 2026', 'bank_desc' => 'LLG PEMBAYARAN KAIN CV TEKSTIL BALI', 'erp_ref' => 'PO-202610-041 (Pelunasan Parsial)', 'amount_in' => '-', 'amount_out' => 'Rp 5.000.000', 'match_status' => 'Cocok Otomatis', 'badge' => 'success'],
                    ['id' => 4, 'trx_date' => '22 Okt 2026', 'bank_desc' => 'DEBET OTOMATIS BIAYA LISTRIK PLN & AIR', 'erp_ref' => 'Beban Utilitas Okt', 'amount_in' => '-', 'amount_out' => 'Rp 1.850.000', 'match_status' => 'Cocok Otomatis', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'rekening_bank', 'label' => 'Pilih Akun Bank', 'type' => 'select', 'options' => ['Bank BCA Giro Operasional (1120)', 'Bank Mandiri Usaha (1121)'], 'required' => true],
                    ['name' => 'periode_rek', 'label' => 'Periode Rekening Koran', 'type' => 'date', 'required' => true],
                    ['name' => 'file_rekening', 'label' => 'Unggah Mutasi Bank (.csv / .xlsx)', 'type' => 'text', 'placeholder' => 'mutasi_bca_okt2026.csv'],
                ];
                break;

                // ==========================================
                // 11. ACCOUNTING: JURNAL TRANSAKSI OTOMATIS (BAB 8 & 9)
                // ==========================================
            case 'accounting/jurnal':
            case 'accounting/index':
                $config['headerIcon'] = 'bi-journal-check';
                $config['formTitle'] = 'Jurnal Transaksi Akuntansi Otomatis & Terintegrasi';
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
                    ['name' => 'akun_debit', 'label' => 'Akun Debit', 'type' => 'select', 'options' => ['1110 - Kas Toko / Kasir Fisik', '1120 - Bank BCA Giro Operasional', '1140 - Persediaan Bahan Baku', '1150 - Persediaan Barang Jadi', '5110 - Beban Pokok Penjualan (HPP)', '6110 - Beban Operasional'], 'required' => true],
                    ['name' => 'akun_kredit', 'label' => 'Akun Kredit', 'type' => 'select', 'options' => ['1110 - Kas Toko / Kasir Fisik', '1120 - Bank BCA Giro Operasional', '2110 - Hutang Dagang Supplier', '1150 - Persediaan Barang Jadi', '4110 - Pendapatan Penjualan'], 'required' => true],
                    ['name' => 'nominal', 'label' => 'Nominal Rupiah (Rp)', 'type' => 'number', 'placeholder' => '0', 'required' => true],
                ];
                break;

                // ==========================================
                // 11. ACCOUNTING: BUKU BESAR / GENERAL LEDGER (BAB 9)
                // ==========================================
            case 'accounting/buku-besar':
                $config['headerIcon'] = 'bi-journal-text';
                $config['formTitle'] = 'Filter Buku Besar (General Ledger) per Akun COA';
                $config['summaryCards'] = [
                    ['title' => 'Akun Aktif Terbuka', 'value' => '1140 - Persediaan Bahan', 'icon' => 'bi-folder', 'color' => 'primary'],
                    ['title' => 'Saldo Awal Akun', 'value' => 'Rp 58.120.000', 'icon' => 'bi-wallet', 'color' => 'secondary'],
                    ['title' => 'Total Mutasi Debit', 'value' => 'Rp 7.500.000 (PO-041)', 'icon' => 'bi-arrow-down-left', 'color' => 'success'],
                    ['title' => 'Total Mutasi Kredit', 'value' => 'Rp 7.020.000 (WIP)', 'icon' => 'bi-arrow-up-right', 'color' => 'danger'],
                ];
                $config['columns'] = [
                    ['key' => 'date', 'label' => 'Tanggal'],
                    ['key' => 'ref_jurnal', 'label' => 'No. Jurnal'],
                    ['key' => 'description', 'label' => 'Keterangan Transaksi'],
                    ['key' => 'debit', 'label' => 'Debit (Rp)'],
                    ['key' => 'credit', 'label' => 'Kredit (Rp)'],
                    ['key' => 'balance', 'label' => 'Saldo Berjalan (Rp)'],
                    ['key' => 'status', 'label' => 'Audit Trail'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'date' => '01 Okt 2026', 'ref_jurnal' => 'JRN-202610-001', 'description' => 'Saldo Awal Periode Persediaan Bahan Baku', 'debit' => 'Rp 58.120.000', 'credit' => '-', 'balance' => 'Rp 58.120.000', 'status' => 'Saldo Awal', 'badge' => 'secondary'],
                    ['id' => 2, 'date' => '22 Okt 2026', 'ref_jurnal' => 'JRN-202610-041', 'description' => 'Pembelian 100 Yard Kain Brokat Semi Prancis (PO-041)', 'debit' => 'Rp 7.500.000', 'credit' => '-', 'balance' => 'Rp 65.620.000', 'status' => 'Pembelian Bahan', 'badge' => 'success'],
                    ['id' => 3, 'date' => '22 Okt 2026', 'ref_jurnal' => 'JRN-202610-042', 'description' => 'Pengeluaran 100 Yard Kain ke Penjahit Wayan Artana (WO-001)', 'debit' => '-', 'credit' => 'Rp 7.020.000', 'balance' => 'Rp 58.600.000', 'status' => 'Transfer ke BDP/WIP', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'pilih_akun', 'label' => 'Pilih Akun Buku Besar', 'type' => 'select', 'options' => ['1140 - Persediaan Bahan Baku', '1110 - Kas Toko POS', '1120 - Bank BCA Giro', '1150 - Persediaan Barang Jadi', '2110 - Hutang Supplier', '4110 - Pendapatan Penjualan', '5110 - HPP Kebaya'], 'required' => true],
                    ['name' => 'tgl_awal', 'label' => 'Dari Tanggal', 'type' => 'date', 'required' => true],
                    ['name' => 'tgl_akhir', 'label' => 'Sampai Tanggal', 'type' => 'date', 'required' => true],
                ];
                break;

                // ==========================================
                // 11. ACCOUNTING: NERACA SALDO / TRIAL BALANCE (BAB 9)
                // ==========================================
            case 'accounting/neraca-saldo':
                $config['headerIcon'] = 'bi-calculator';
                $config['formTitle'] = 'Neraca Saldo (Trial Balance) Periode Berjalan';
                $config['summaryCards'] = [
                    ['title' => 'Total Debit', 'value' => 'Rp 452.800.000', 'icon' => 'bi-arrow-down-left', 'color' => 'primary'],
                    ['title' => 'Total Kredit', 'value' => 'Rp 452.800.000', 'icon' => 'bi-arrow-up-right', 'color' => 'success'],
                    ['title' => 'Status Keseimbangan', 'value' => 'SEIMBANG (Balanced)', 'icon' => 'bi-check-circle', 'color' => 'success'],
                    ['title' => 'Selisih Pembukuan', 'value' => 'Rp 0', 'icon' => 'bi-shield-check', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'acc_code', 'label' => 'Kode Akun'],
                    ['key' => 'acc_name', 'label' => 'Nama Akun Perkiraan'],
                    ['key' => 'debit_bal', 'label' => 'Saldo Debit (Rp)'],
                    ['key' => 'credit_bal', 'label' => 'Saldo Kredit (Rp)'],
                    ['key' => 'status', 'label' => 'Kelompok'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'acc_code' => '1110', 'acc_name' => 'Kas Toko / Kasir Fisik', 'debit_bal' => 'Rp 25.500.000', 'credit_bal' => '-', 'status' => 'Aset Lancar', 'badge' => 'primary'],
                    ['id' => 2, 'acc_code' => '1120', 'acc_name' => 'Bank BCA Giro Operasional', 'debit_bal' => 'Rp 112.400.000', 'credit_bal' => '-', 'status' => 'Aset Lancar', 'badge' => 'primary'],
                    ['id' => 3, 'acc_code' => '1125', 'acc_name' => 'Rekening Escrow Marketplace', 'debit_bal' => 'Rp 44.500.000', 'credit_bal' => '-', 'status' => 'Aset Lancar', 'badge' => 'primary'],
                    ['id' => 4, 'acc_code' => '1130', 'acc_name' => 'Piutang Usaha Pelanggan Reseller', 'debit_bal' => 'Rp 24.150.000', 'credit_bal' => '-', 'status' => 'Aset Lancar', 'badge' => 'primary'],
                    ['id' => 5, 'acc_code' => '1140', 'acc_name' => 'Persediaan Bahan Baku (Yard/Meter)', 'debit_bal' => 'Rp 58.600.000', 'credit_bal' => '-', 'status' => 'Persediaan', 'badge' => 'primary'],
                    ['id' => 6, 'acc_code' => '1145', 'acc_name' => 'Persediaan BDP (Gudang Penjahit WIP)', 'debit_bal' => 'Rp 42.500.000', 'credit_bal' => '-', 'status' => 'Persediaan', 'badge' => 'primary'],
                    ['id' => 7, 'acc_code' => '1150', 'acc_name' => 'Persediaan Barang Jadi (Gudang Utama FG)', 'debit_bal' => 'Rp 229.400.000', 'credit_bal' => '-', 'status' => 'Persediaan', 'badge' => 'primary'],
                    ['id' => 8, 'acc_code' => '2110', 'acc_name' => 'Hutang Dagang Supplier Bahan Baku', 'debit_bal' => '-', 'credit_bal' => 'Rp 18.500.000', 'status' => 'Kewajiban', 'badge' => 'danger'],
                    ['id' => 9, 'acc_code' => '2120', 'acc_name' => 'Hutang Ongkos Jahit Penjahit', 'debit_bal' => '-', 'credit_bal' => 'Rp 5.400.000', 'status' => 'Kewajiban', 'badge' => 'danger'],
                    ['id' => 10, 'acc_code' => '3100', 'acc_name' => 'Modal Pemilik KlambiBali', 'debit_bal' => '-', 'credit_bal' => 'Rp 350.000.000', 'status' => 'Ekuitas', 'badge' => 'info'],
                    ['id' => 11, 'acc_code' => '4110-40', 'acc_name' => 'Pendapatan Penjualan Omnichannel', 'debit_bal' => '-', 'credit_bal' => 'Rp 142.850.000', 'status' => 'Pendapatan', 'badge' => 'success'],
                    ['id' => 12, 'acc_code' => '5110', 'acc_name' => 'Beban Pokok Penjualan (HPP)', 'debit_bal' => 'Rp 68.650.000', 'credit_bal' => '-', 'status' => 'HPP', 'badge' => 'warning'],
                    ['id' => 13, 'acc_code' => '6110-60', 'acc_name' => 'Beban Operasional Usaha', 'debit_bal' => 'Rp 44.950.000', 'credit_bal' => '-', 'status' => 'Beban', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'periode_neraca', 'label' => 'Periode Neraca Saldo', 'type' => 'select', 'options' => ['Per 24 Oktober 2026', 'Per 30 September 2026', 'Per 31 Agustus 2026']],
                    ['name' => 'format_dokumen', 'label' => 'Ekspor ke Dokumen', 'type' => 'select', 'options' => ['Microsoft Excel (.xlsx)', 'PDF Cetak']],
                ];
                break;

                // ==========================================
                // 10 & 11. FINANCE & ACCOUNTING: LAPORAN LABA RUGI KOMPREHENSIF (BAB 8 & 9)
                // ==========================================
            case 'accounting/laba-rugi':
            case 'finance/laba-rugi':
                $config['headerIcon'] = 'bi-graph-up-arrow';
                $config['formTitle'] = 'Filter Periode & Ekspor Laporan Laba Rugi Komprehensif';
                $config['summaryCards'] = [
                    ['title' => 'Penjualan Bersih', 'value' => 'Rp 142.850.000', 'icon' => 'bi-cart-check', 'color' => 'success'],
                    ['title' => 'Beban Pokok Penjualan (HPP)', 'value' => 'Rp 68.650.000', 'icon' => 'bi-box', 'color' => 'warning'],
                    ['title' => 'Laba Kotor (Margin 51,9%)', 'value' => 'Rp 74.200.000', 'icon' => 'bi-wallet2', 'color' => 'primary'],
                    ['title' => 'Laba Bersih Usaha (Net)', 'value' => 'Rp 29.250.000', 'icon' => 'bi-trophy', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'line_item', 'label' => 'Pos Akuntansi Laba Rugi (Standar Akuntansi)'],
                    ['key' => 'cat_header', 'label' => 'Kelompok'],
                    ['key' => 'amount', 'label' => 'Nominal (Rp)'],
                    ['key' => 'percentage', 'label' => '% Omzet'],
                    ['key' => 'status', 'label' => 'Keterangan'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'line_item' => 'Penjualan Shopee Marketplace (Net)', 'cat_header' => 'I. Pendapatan Penjualan', 'amount' => 'Rp 48.200.000', 'percentage' => '33,7%', 'status' => 'Omnichannel', 'badge' => 'primary'],
                    ['id' => 2, 'line_item' => 'Penjualan TikTok Shop Live Streaming (Net)', 'cat_header' => 'I. Pendapatan Penjualan', 'amount' => 'Rp 36.450.000', 'percentage' => '25,5%', 'status' => 'Omnichannel', 'badge' => 'primary'],
                    ['id' => 3, 'line_item' => 'Penjualan Reguler / Reseller B2B', 'cat_header' => 'I. Pendapatan Penjualan', 'amount' => 'Rp 32.700.000', 'percentage' => '22,9%', 'status' => 'Omnichannel', 'badge' => 'primary'],
                    ['id' => 4, 'line_item' => 'Penjualan Toko Fisik (Kasir POS Outlet)', 'cat_header' => 'I. Pendapatan Penjualan', 'amount' => 'Rp 25.500.000', 'percentage' => '17,9%', 'status' => 'Omnichannel', 'badge' => 'primary'],
                    ['id' => 5, 'line_item' => 'TOTAL PENDAPATAN PENJUALAN BERSIH', 'cat_header' => 'I. Pendapatan Penjualan', 'amount' => 'Rp 142.850.000', 'percentage' => '100,0%', 'status' => 'Subtotal', 'badge' => 'success'],
                    ['id' => 6, 'line_item' => 'Persediaan Awal Barang Jadi (01 Okt)', 'cat_header' => 'II. Beban Pokok Penjualan (HPP)', 'amount' => 'Rp 120.400.000', 'percentage' => '-', 'status' => 'Persediaan Awal', 'badge' => 'secondary'],
                    ['id' => 7, 'line_item' => 'Harga Pokok Produksi (Bahan 1,56 Yd + Jahit + Aksesoris)', 'cat_header' => 'II. Beban Pokok Penjualan (HPP)', 'amount' => 'Rp 177.650.000', 'percentage' => '-', 'status' => 'Biaya Produksi', 'badge' => 'info'],
                    ['id' => 8, 'line_item' => 'Persediaan Akhir Barang Jadi (Posisi 24 Okt)', 'cat_header' => 'II. Beban Pokok Penjualan (HPP)', 'amount' => '(Rp 229.400.000)', 'percentage' => '-', 'status' => 'Persediaan Akhir', 'badge' => 'secondary'],
                    ['id' => 9, 'line_item' => 'TOTAL BEBAN POKOK PENJUALAN (HPP)', 'cat_header' => 'II. Beban Pokok Penjualan (HPP)', 'amount' => 'Rp 68.650.000', 'percentage' => '48,1%', 'status' => 'HPP Terjual', 'badge' => 'warning'],
                    ['id' => 10, 'line_item' => 'LABA KOTOR (GROSS PROFIT)', 'cat_header' => 'III. Laba Kotor', 'amount' => 'Rp 74.200.000', 'percentage' => '51,9%', 'status' => 'Laba Kotor Usaha', 'badge' => 'success'],
                    ['id' => 11, 'line_item' => 'Beban Gaji Karyawan Toko & Staf Admin', 'cat_header' => 'IV. Beban Operasional', 'amount' => 'Rp 18.500.000', 'percentage' => '13,0%', 'status' => 'Operasional', 'badge' => 'danger'],
                    ['id' => 12, 'line_item' => 'Beban Sewa Outlet Toko & Workshop', 'cat_header' => 'IV. Beban Operasional', 'amount' => 'Rp 12.000.000', 'percentage' => '8,4%', 'status' => 'Operasional', 'badge' => 'danger'],
                    ['id' => 13, 'line_item' => 'Beban Fee Layanan & Administrasi Marketplace (Shopee/TikTok)', 'cat_header' => 'IV. Beban Operasional', 'amount' => 'Rp 6.200.000', 'percentage' => '4,3%', 'status' => 'Fee Platform', 'badge' => 'danger'],
                    ['id' => 14, 'line_item' => 'Beban Listrik, Air & Internet', 'cat_header' => 'IV. Beban Operasional', 'amount' => 'Rp 2.850.000', 'percentage' => '2,0%', 'status' => 'Utilitas', 'badge' => 'danger'],
                    ['id' => 15, 'line_item' => 'Beban Packaging, Plastik & Label Barcode', 'cat_header' => 'IV. Beban Operasional', 'amount' => 'Rp 2.400.000', 'percentage' => '1,7%', 'status' => 'Packaging', 'badge' => 'danger'],
                    ['id' => 16, 'line_item' => 'Biaya Service / Rework Jahitan Gudang Reject', 'cat_header' => 'IV. Beban Operasional', 'amount' => 'Rp 1.800.000', 'percentage' => '1,3%', 'status' => 'Rework Reject', 'badge' => 'warning'],
                    ['id' => 17, 'line_item' => 'Biaya Kerugian Barang Cacat Gudang Sale', 'cat_header' => 'IV. Beban Operasional', 'amount' => 'Rp 1.200.000', 'percentage' => '0,8%', 'status' => 'Kerugian Defect', 'badge' => 'warning'],
                    ['id' => 18, 'line_item' => 'TOTAL BEBAN OPERASIONAL', 'cat_header' => 'IV. Beban Operasional', 'amount' => 'Rp 44.950.000', 'percentage' => '31,5%', 'status' => 'Total Beban', 'badge' => 'danger'],
                    ['id' => 19, 'line_item' => 'LABA BERSIH USAHA (NET OPERATING PROFIT)', 'cat_header' => 'V. Laba Bersih', 'amount' => 'Rp 29.250.000', 'percentage' => '20,5%', 'status' => 'Laba Bersih Final', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'periode_bulan', 'label' => 'Pilih Periode Bulan', 'type' => 'select', 'options' => ['Oktober 2026', 'September 2026', 'Agustus 2026', 'Juli 2026', 'Tahun Berjalan 2026'], 'required' => true],
                    ['name' => 'channel_scope', 'label' => 'Cakupan Channel Penjualan', 'type' => 'select', 'options' => ['Konsolidasi Seluruh Channel (Omnichannel)', 'Hanya Shopee Marketplace', 'Hanya TikTok Shop', 'Hanya Toko Kasir POS', 'Hanya Penjualan Reguler']],
                    ['name' => 'format_export', 'label' => 'Format Unduh Dokumen', 'type' => 'select', 'options' => ['Microsoft Excel (.xlsx)', 'Dokumen PDF Cetak (.pdf)', 'CSV']],
                ];
                break;

                // ==========================================
                // 11. ACCOUNTING: NERACA / BALANCE SHEET (BAB 9)
                // ==========================================
            case 'accounting/neraca':
                $config['headerIcon'] = 'bi-scale';
                $config['formTitle'] = 'Laporan Neraca Keuangan (Balance Sheet)';
                $config['summaryCards'] = [
                    ['title' => 'Total Aset Lancar', 'value' => 'Rp 480.950.000', 'icon' => 'bi-wallet2', 'color' => 'primary'],
                    ['title' => 'Total Kewajiban (Hutang)', 'value' => 'Rp 23.900.000', 'icon' => 'bi-receipt', 'color' => 'danger'],
                    ['title' => 'Total Ekuitas Pemilik', 'value' => 'Rp 457.050.000', 'icon' => 'bi-building-check', 'color' => 'success'],
                    ['title' => 'Persamaan Akuntansi', 'value' => 'ASET = HUTANG + MODAL', 'icon' => 'bi-check-circle', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'category_header', 'label' => 'Klasifikasi Neraca'],
                    ['key' => 'account_detail', 'label' => 'Nama Akun / Komponen'],
                    ['key' => 'sub_amount', 'label' => 'Rincian Nilai (Rp)'],
                    ['key' => 'total_amount', 'label' => 'Total Kelompok (Rp)'],
                    ['key' => 'status', 'label' => 'Catatan Fisik'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'category_header' => 'ASET LANCAR', 'account_detail' => 'Kas Toko & Rekening Bank (BCA + Mandiri)', 'sub_amount' => 'Rp 137.900.000', 'total_amount' => '-', 'status' => 'Kas & Bank', 'badge' => 'primary'],
                    ['id' => 2, 'category_header' => 'ASET LANCAR', 'account_detail' => 'Rekening Penampungan Marketplace (Escrow)', 'sub_amount' => 'Rp 44.500.000', 'total_amount' => '-', 'status' => 'Shopee / TikTok', 'badge' => 'info'],
                    ['id' => 3, 'category_header' => 'ASET LANCAR', 'account_detail' => 'Piutang Usaha Pelanggan Reseller', 'sub_amount' => 'Rp 24.150.000', 'total_amount' => '-', 'status' => 'Piutang B2B', 'badge' => 'warning'],
                    ['id' => 4, 'category_header' => 'ASET LANCAR', 'account_detail' => 'Persediaan Bahan Baku (Yard & Meter)', 'sub_amount' => 'Rp 58.600.000', 'total_amount' => '-', 'status' => 'Bahan Baku', 'badge' => 'success'],
                    ['id' => 5, 'category_header' => 'ASET LANCAR', 'account_detail' => 'Persediaan BDP (Gudang Penjahit WIP)', 'sub_amount' => 'Rp 42.500.000', 'total_amount' => '-', 'status' => 'WIP Penjahit', 'badge' => 'success'],
                    ['id' => 6, 'category_header' => 'ASET LANCAR', 'account_detail' => 'Persediaan Barang Jadi (Utama + Store + Live + Cacat)', 'sub_amount' => 'Rp 335.775.000', 'total_amount' => '-', 'status' => 'Finished Goods HPP', 'badge' => 'success'],
                    ['id' => 7, 'category_header' => 'TOTAL ASET', 'account_detail' => 'TOTAL KESELURUHAN ASET AKTIVA', 'sub_amount' => '-', 'total_amount' => 'Rp 480.950.000', 'status' => 'Total Aset', 'badge' => 'primary'],
                    ['id' => 8, 'category_header' => 'KEWAJIBAN LANCAR', 'account_detail' => 'Hutang Dagang Supplier (CV Tekstil Bali)', 'sub_amount' => 'Rp 18.500.000', 'total_amount' => '-', 'status' => 'Tempo 30 Hari', 'badge' => 'danger'],
                    ['id' => 9, 'category_header' => 'KEWAJIBAN LANCAR', 'account_detail' => 'Hutang Biaya Jahit Penjahit', 'sub_amount' => 'Rp 5.400.000', 'total_amount' => 'Rp 23.900.000', 'status' => 'Upah Jahit', 'badge' => 'danger'],
                    ['id' => 10, 'category_header' => 'EKUITAS PEMILIK', 'account_detail' => 'Modal Disetor Pemilik', 'sub_amount' => 'Rp 350.000.000', 'total_amount' => '-', 'status' => 'Modal Disetor', 'badge' => 'info'],
                    ['id' => 11, 'category_header' => 'EKUITAS PEMILIK', 'account_detail' => 'Laba Ditahan & Laba Berjalan', 'sub_amount' => 'Rp 107.050.000', 'total_amount' => 'Rp 457.050.000', 'status' => 'Akumulasi Laba', 'badge' => 'success'],
                    ['id' => 12, 'category_header' => 'TOTAL PASIVA', 'account_detail' => 'TOTAL KEWAJIBAN + EKUITAS', 'sub_amount' => '-', 'total_amount' => 'Rp 480.950.000', 'status' => 'SEIMBANG (Matched)', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'per_tanggal', 'label' => 'Posisi Neraca per Tanggal', 'type' => 'date', 'required' => true],
                    ['name' => 'metode_penilaian', 'label' => 'Metode Penilaian Persediaan Sesuai PDF', 'type' => 'select', 'options' => ['HPP Kalkulasi Standar Produksi (BOM + Jahit + Aksesoris)', 'FIFO']],
                ];
                break;

                // ==========================================
                // 11. ACCOUNTING: LAPORAN ARUS KAS (BAB 8 & 9)
                // ==========================================
            case 'accounting/arus-kas':
                $config['headerIcon'] = 'bi-cash-stack';
                $config['formTitle'] = 'Laporan Arus Kas (Metode Langsung)';
                $config['summaryCards'] = [
                    ['title' => 'Arus Kas Operasional', 'value' => '+Rp 48.650.000', 'icon' => 'bi-arrow-down-left', 'color' => 'success'],
                    ['title' => 'Arus Kas Investasi', 'value' => '-Rp 15.000.000', 'icon' => 'bi-tools', 'color' => 'warning'],
                    ['title' => 'Arus Kas Pendanaan', 'value' => 'Rp 0', 'icon' => 'bi-bank', 'color' => 'secondary'],
                    ['title' => 'Saldo Kas & Bank Akhir', 'value' => 'Rp 182.400.000', 'icon' => 'bi-wallet2', 'color' => 'primary'],
                ];
                $config['columns'] = [
                    ['key' => 'activity_group', 'label' => 'Aktivitas Arus Kas'],
                    ['key' => 'item_desc', 'label' => 'Rincian Arus Kas Masuk / Keluar'],
                    ['key' => 'amount', 'label' => 'Nominal Rupiah (Rp)'],
                    ['key' => 'status', 'label' => 'Klasifikasi'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'activity_group' => 'Aktivitas Operasi', 'item_desc' => 'Penerimaan Kas dari Pelanggan POS, Shopee & TikTok', 'amount' => '+Rp 138.650.000', 'status' => 'Kas Masuk Penjualan', 'badge' => 'success'],
                    ['id' => 2, 'activity_group' => 'Aktivitas Operasi', 'item_desc' => 'Pembayaran Kas ke Supplier Bahan Baku Kain', 'amount' => '-Rp 45.050.000', 'status' => 'Kas Keluar Bahan', 'badge' => 'danger'],
                    ['id' => 3, 'activity_group' => 'Aktivitas Operasi', 'item_desc' => 'Pembayaran Ongkos Jahit Penjahit Wayan Artana', 'amount' => '-Rp 18.000.000', 'status' => 'Kas Keluar Jahit', 'badge' => 'danger'],
                    ['id' => 4, 'activity_group' => 'Aktivitas Operasi', 'item_desc' => 'Pembayaran Biaya Operasional (Gaji, Listrik, Sewa)', 'amount' => '-Rp 26.950.000', 'status' => 'Kas Keluar Operasi', 'badge' => 'danger'],
                    ['id' => 5, 'activity_group' => 'Aktivitas Operasi', 'item_desc' => 'ARUS KAS BERSIH DARI AKTIVITAS OPERASIONAL', 'amount' => '+Rp 48.650.000', 'status' => 'Arus Kas Bersih', 'badge' => 'success'],
                    ['id' => 6, 'activity_group' => 'Aktivitas Investasi', 'item_desc' => 'Pembelian Mesin Jahit & Barcode Scanner Baru', 'amount' => '-Rp 15.000.000', 'status' => 'Investasi Aset', 'badge' => 'warning'],
                    ['id' => 7, 'activity_group' => 'Posisi Kas Akhir', 'item_desc' => 'TOTAL SALDO KAS DAN SETARA KAS AKHIR PERIODE', 'amount' => 'Rp 182.400.000', 'status' => 'Kas & Bank Riil', 'badge' => 'primary'],
                ];
                $config['formFields'] = [
                    ['name' => 'periode_kas', 'label' => 'Periode Laporan Arus Kas', 'type' => 'select', 'options' => ['Bulan Oktober 2026', 'Kuartal III 2026', 'Tahun Berjalan 2026']],
                ];
                break;

                // ==========================================
                // 3. INVENTORY: STOK OPNAME & ADJUSTMENT (BAB 7)
                // ==========================================
            case 'inventory/opname':
                $config['headerIcon'] = 'bi-clipboard-check';
                $config['formTitle'] = 'Form Stock Opname & Penyesuaian Selisih (Alasan Wajib & Otorisasi)';
                $config['summaryCards'] = [
                    ['title' => 'Jadwal Opname Terakhir', 'value' => '24 Okt 2026', 'icon' => 'bi-calendar-check', 'color' => 'primary'],
                    ['title' => 'Tingkat Akurasi Fisik', 'value' => '99,2%', 'icon' => 'bi-check-circle', 'color' => 'success'],
                    ['title' => 'Selisih Perca / Potongan', 'value' => '0,60 Yard', 'icon' => 'bi-scissors', 'color' => 'warning'],
                    ['title' => 'Otorisasi Koreksi', 'value' => 'Wajib Spv', 'icon' => 'bi-shield-lock', 'color' => 'danger'],
                ];
                $config['columns'] = [
                    ['key' => 'opname_no', 'label' => 'No. Opname'],
                    ['key' => 'warehouse', 'label' => 'Gudang Lokasi'],
                    ['key' => 'item_name', 'label' => 'Nama Item / SKU'],
                    ['key' => 'system_qty', 'label' => 'Stok Sistem'],
                    ['key' => 'actual_qty', 'label' => 'Fisik Aktual'],
                    ['key' => 'diff_qty', 'label' => 'Selisih (+/-)'],
                    ['key' => 'reason', 'label' => 'Alasan Wajib'],
                    ['key' => 'status', 'label' => 'Status Otorisasi'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'opname_no' => 'OPN-202610-001', 'warehouse' => 'Gudang Penjahit (WIP)', 'item_name' => 'BB-KN-BRKT-01 - Kain Brokat Semi Prancis', 'system_qty' => '6,40 Yard (Teoritis)', 'actual_qty' => '5,80 Yard (Fisik)', 'diff_qty' => '-0,60 Yard', 'reason' => 'Sisa kain perca potongan leher & waste pola jahitan', 'status' => 'Disetujui Supervisor', 'badge' => 'warning'],
                    ['id' => 2, 'opname_no' => 'OPN-202610-002', 'warehouse' => 'Gudang Store (POS Toko)', 'item_name' => 'KB-BRD-MRN-M - Kebaya Bali Maroon', 'system_qty' => '30 Pcs', 'actual_qty' => '30 Pcs', 'diff_qty' => '0 (Sesuai)', 'reason' => 'Pencocokan fisik display kasir', 'status' => 'Selesai Cocok', 'badge' => 'success'],
                    ['id' => 3, 'opname_no' => 'OPN-202610-003', 'warehouse' => 'Gudang Utama (FG)', 'item_name' => 'KM-EDK-GLD-ALL - Kamen Endek', 'system_qty' => '15 Pcs', 'actual_qty' => '15 Pcs', 'diff_qty' => '0 (Sesuai)', 'reason' => 'Pemeriksaan stok barcode', 'status' => 'Selesai Cocok', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'gudang', 'label' => 'Pilih Gudang Opname', 'type' => 'select', 'options' => ['Gudang Penjahit (WIP)', 'Gudang Transit (QC)', 'Gudang Utama (FG)', 'Gudang Live', 'Gudang Store (POS)', 'Gudang Reject/Service', 'Gudang Sale'], 'required' => true],
                    ['name' => 'sku', 'label' => 'SKU / Bahan', 'type' => 'select', 'options' => ['BB-KN-BRKT-01 - Kain Brokat Maroon', 'KB-BRD-MRN-M - Kebaya Bali Maroon M'], 'required' => true],
                    ['name' => 'stok_fisik', 'label' => 'Jumlah Dihitung Fisik', 'type' => 'number', 'placeholder' => '0', 'required' => true],
                    ['name' => 'alasan_wajib', 'label' => 'Alasan Penyesuaian (Wajib Diisi)', 'type' => 'textarea', 'placeholder' => 'Tuliskan penyebab selisih fisik vs sistem secara detail...', 'required' => true],
                    ['name' => 'pin_otorisasi', 'label' => 'PIN / Otorisasi Supervisor', 'type' => 'text', 'placeholder' => 'Kode persetujuan', 'required' => true],
                ];
                break;

                // ==========================================
                // 3. INVENTORY: PERINGATAN STOK MINIMUM & STOK MATI (BAB 7)
                // ==========================================
            case 'inventory/minimum':
                $config['headerIcon'] = 'bi-exclamation-triangle';
                $config['formTitle'] = 'Peringatan Stok Minimum, Stok Lambat Bergerak & Stok Mati';
                $config['summaryCards'] = [
                    ['title' => 'Stok Menipis (< Min)', 'value' => '4 SKU', 'icon' => 'bi-bell', 'color' => 'danger'],
                    ['title' => 'Lambat Bergerak (>30 Hr)', 'value' => '3 Model', 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
                    ['title' => 'Stok Mati (>90 Hari)', 'value' => '1 Model (Udeng)', 'icon' => 'bi-x-octagon', 'color' => 'dark'],
                    ['title' => 'Saran Tindakan', 'value' => 'Restock / Cuci Gudang', 'icon' => 'bi-lightbulb', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'sku', 'label' => 'Kode SKU'],
                    ['key' => 'product_name', 'label' => 'Nama Produk / Bahan'],
                    ['key' => 'warehouse', 'label' => 'Lokasi Gudang'],
                    ['key' => 'current_stock', 'label' => 'Stok Fisik'],
                    ['key' => 'min_buffer', 'label' => 'Batas Min'],
                    ['key' => 'days_no_sale', 'label' => 'Hari Tanpa Penjualan'],
                    ['key' => 'recommendation', 'label' => 'Rekomendasi Sistem'],
                    ['key' => 'status', 'label' => 'Level Peringatan'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'sku' => 'KMJ-SFR-WHT-XL', 'product_name' => 'Kemeja Safari Pria Katun Putih (XL)', 'warehouse' => 'Gudang Store (POS)', 'current_stock' => '2 pcs', 'min_buffer' => '5 pcs', 'days_no_sale' => '1 hari', 'recommendation' => 'Buat Perintah Jahit (WO) Baru Segera', 'status' => 'Segera Restock', 'badge' => 'danger'],
                    ['id' => 2, 'sku' => 'BB-KNC-BATU-04', 'product_name' => 'Kancing Bungkus Motif Emas 12mm', 'warehouse' => 'Gudang Penjahit (WIP)', 'current_stock' => '8 Gross', 'min_buffer' => '15 Gross', 'days_no_sale' => '3 hari', 'recommendation' => 'Buat PO Pembelian ke Supplier', 'status' => 'Segera Beli', 'badge' => 'warning'],
                    ['id' => 3, 'sku' => 'UDG-BLI-BLK-ALL', 'product_name' => 'Udeng Bali Pria Setengah Jadi Hitam Poleng', 'warehouse' => 'Gudang Utama (FG)', 'current_stock' => '40 pcs', 'min_buffer' => '10 pcs', 'days_no_sale' => '94 hari', 'recommendation' => 'Diskon Bundling / Cuci Gudang Live', 'status' => 'Stok Mati (>90 Hari)', 'badge' => 'danger'],
                    ['id' => 4, 'sku' => 'SLD-OBI-TRD-SLV', 'product_name' => 'Selendang Obi Songket Silver', 'warehouse' => 'Gudang Live', 'current_stock' => '25 pcs', 'min_buffer' => '10 pcs', 'days_no_sale' => '42 hari', 'recommendation' => 'Sorot di Live Streaming TikTok', 'status' => 'Lambat Bergerak', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'filter_kategori', 'label' => 'Filter Klasifikasi Peringatan', 'type' => 'select', 'options' => ['Semua Peringatan', 'Stok Minimum Menipis', 'Stok Lambat Bergerak (>30 Hari)', 'Stok Mati (>90 Hari)']],
                ];
                break;

                // ==========================================
                // 10. FINANCE: BIAYA OPERASIONAL (SESUAI BAB 8)
                // ==========================================
            case 'finance/biaya-operasional':
                $config['headerIcon'] = 'bi-journal-text';
                $config['formTitle'] = 'Form Pencatatan Biaya Operasional & Bukti Transaksi';
                $config['summaryCards'] = [
                    ['title' => 'Biaya Operasional Bulan Ini', 'value' => 'Rp 44.950.000', 'icon' => 'bi-cash-coin', 'color' => 'danger'],
                    ['title' => 'Gaji & Upah Karyawan', 'value' => 'Rp 18.500.000', 'icon' => 'bi-people', 'color' => 'primary'],
                    ['title' => 'Sewa Gedung & Outlet', 'value' => 'Rp 12.000.000', 'icon' => 'bi-building', 'color' => 'info'],
                    ['title' => 'Fee Platform Marketplace', 'value' => 'Rp 6.200.000', 'icon' => 'bi-shop', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'voucher_no', 'label' => 'No. Bukti Pengeluaran'],
                    ['key' => 'date', 'label' => 'Tanggal'],
                    ['key' => 'category', 'label' => 'Kategori Beban (COA)'],
                    ['key' => 'description', 'label' => 'Uraian Biaya'],
                    ['key' => 'payment_source', 'label' => 'Dibayar Dari'],
                    ['key' => 'amount', 'label' => 'Nominal (Rp)'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'voucher_no' => 'BKK-202610-011', 'date' => '24 Okt 2026', 'category' => '6130 - Beban Listrik, Air & Internet', 'description' => 'Tagihan PLN Workshop Konveksi & Outlet Denpasar', 'payment_source' => 'Bank BCA Giro Operasional', 'amount' => 'Rp 2.850.000', 'status' => 'Lunas', 'badge' => 'success'],
                    ['id' => 2, 'voucher_no' => 'BKK-202610-012', 'date' => '23 Okt 2026', 'category' => '6150 - Beban Packaging & Ekspedisi', 'description' => 'Pembelian 1.000 pcs Plastik Klip Kebaya & Stiker Barcode', 'payment_source' => 'Kas Toko (Kasir Fisik)', 'amount' => 'Rp 2.400.000', 'status' => 'Lunas', 'badge' => 'success'],
                    ['id' => 3, 'voucher_no' => 'BKK-202610-013', 'date' => '24 Okt 2026', 'category' => '6160 - Biaya Perbaikan (Service Reject)', 'description' => 'Ongkos Rework Jahit Ulang 3 Pcs Kebaya Reject Gudang Service', 'payment_source' => 'Kas Toko (Kasir Fisik)', 'amount' => 'Rp 45.000', 'status' => 'Lunas (Rework)', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'no_bukti', 'label' => 'Nomor Bukti Kas Keluar', 'type' => 'text', 'placeholder' => 'BKK-202610-014', 'required' => true],
                    ['name' => 'tgl_biaya', 'label' => 'Tanggal Transaksi', 'type' => 'date', 'required' => true],
                    ['name' => 'kategori_coa', 'label' => 'Kategori Beban Akuntansi', 'type' => 'select', 'options' => ['6110 - Beban Gaji & Upah Karyawan', '6120 - Beban Sewa Outlet & Workshop', '6130 - Beban Listrik, Air & Internet', '6140 - Beban Fee Layanan Marketplace', '6150 - Beban Packaging & Ekspedisi', '6160 - Beban Perbaikan Produk (Service Rework)'], 'required' => true],
                    ['name' => 'keterangan', 'label' => 'Uraian Pengeluaran', 'type' => 'text', 'placeholder' => 'Tuliskan deskripsi biaya...', 'required' => true],
                    ['name' => 'nominal', 'label' => 'Nominal Rupiah (Rp)', 'type' => 'number', 'placeholder' => '0', 'required' => true],
                    ['name' => 'sumber_dana', 'label' => 'Sumber Kas / Bank', 'type' => 'select', 'options' => ['Bank BCA Giro Operasional (1120)', 'Kas Toko (Kasir Fisik) (1110)'], 'required' => true],
                ];
                break;

                // ==========================================
                // 12. REPORTS: LAPORAN PENJUALAN PER CHANNEL (BAB 12.1)
                // ==========================================
            case 'laporan/transaksi':
            case 'laporan/index':
                $config['headerIcon'] = 'bi-file-earmark-bar-graph';
                $config['formTitle'] = 'Filter & Parameter Ekspor Laporan Penjualan Omnichannel (.xlsx)';
                $config['summaryCards'] = [
                    ['title' => 'Total Penjualan Bersih', 'value' => 'Rp 142.850.000', 'icon' => 'bi-cart-check', 'color' => 'success'],
                    ['title' => 'Produk Terlaris', 'value' => 'Kebaya Maroon M', 'icon' => 'bi-star', 'color' => 'primary'],
                    ['title' => 'Total Transaksi', 'value' => '306 Order', 'icon' => 'bi-receipt', 'color' => 'info'],
                    ['title' => 'Format Ekspor Wajib', 'value' => 'Excel (.xlsx)', 'icon' => 'bi-file-earmark-excel', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'channel_name', 'label' => 'Channel Penjualan'],
                    ['key' => 'trx_count', 'label' => 'Jumlah Transaksi'],
                    ['key' => 'unit_sold', 'label' => 'Unit Terjual'],
                    ['key' => 'gross_omzet', 'label' => 'Penjualan Kotor (Rp)'],
                    ['key' => 'net_omzet', 'label' => 'Penjualan Bersih (Rp)'],
                    ['key' => 'cogs_val', 'label' => 'Total HPP (Rp)'],
                    ['key' => 'gross_profit', 'label' => 'Laba Kotor (Rp)'],
                    ['key' => 'margin_pct', 'label' => 'Margin Laba'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'channel_name' => 'Shopee Marketplace', 'trx_count' => '112 Order', 'unit_sold' => '148 Pcs', 'gross_omzet' => 'Rp 48.200.000', 'net_omzet' => 'Rp 48.200.000', 'cogs_val' => 'Rp 23.150.000', 'gross_profit' => 'Rp 25.050.000', 'margin_pct' => '52,0%', 'badge' => 'warning'],
                    ['id' => 2, 'channel_name' => 'TikTok Shop Live Streaming', 'trx_count' => '84 Order', 'unit_sold' => '110 Pcs', 'gross_omzet' => 'Rp 36.450.000', 'net_omzet' => 'Rp 36.450.000', 'cogs_val' => 'Rp 17.500.000', 'gross_profit' => 'Rp 18.950.000', 'margin_pct' => '52,0%', 'badge' => 'primary'],
                    ['id' => 3, 'channel_name' => 'Penjualan Reguler / Reseller B2B', 'trx_count' => '42 Order', 'unit_sold' => '98 Pcs', 'gross_omzet' => 'Rp 32.700.000', 'net_omzet' => 'Rp 32.700.000', 'cogs_val' => 'Rp 15.700.000', 'gross_profit' => 'Rp 17.000.000', 'margin_pct' => '52,0%', 'badge' => 'info'],
                    ['id' => 4, 'channel_name' => 'Toko Fisik (Outlet POS Kasir)', 'trx_count' => '68 Order', 'unit_sold' => '72 Pcs', 'gross_omzet' => 'Rp 25.500.000', 'net_omzet' => 'Rp 25.500.000', 'cogs_val' => 'Rp 12.300.000', 'gross_profit' => 'Rp 13.200.000', 'margin_pct' => '51,8%', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'periode_awal', 'label' => 'Tanggal Awal', 'type' => 'date', 'required' => true],
                    ['name' => 'periode_akhir', 'label' => 'Tanggal Akhir', 'type' => 'date', 'required' => true],
                    ['name' => 'filter_channel', 'label' => 'Filter Channel', 'type' => 'select', 'options' => ['Semua Channel', 'Shopee', 'TikTok Shop', 'Penjualan Reguler', 'Outlet Toko Fisik (POS)']],
                ];
                break;

                // ==========================================
                // 12. REPORTS: LAPORAN PEMBELIAN & HUTANG (BAB 12.2)
                // ==========================================
            case 'laporan/pembelian':
                $config['headerIcon'] = 'bi-receipt-cutoff';
                $config['formTitle'] = 'Laporan Pembelian Bahan Baku, Barang Jadi & Hutang Supplier';
                $config['summaryCards'] = [
                    ['title' => 'Pembelian Bulan Ini', 'value' => 'Rp 68.400.000', 'icon' => 'bi-cart', 'color' => 'primary'],
                    ['title' => 'Total Hutang Supplier', 'value' => 'Rp 18.500.000', 'icon' => 'bi-receipt', 'color' => 'danger'],
                    ['title' => 'Kain Brokat (100 Yd)', 'value' => 'PO-202610-041', 'icon' => 'bi-box-seam', 'color' => 'success'],
                    ['title' => 'Format Ekspor Wajib', 'value' => 'Excel (.xlsx)', 'icon' => 'bi-file-earmark-excel', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'supplier_name', 'label' => 'Nama Supplier'],
                    ['key' => 'category_item', 'label' => 'Kategori Bahan'],
                    ['key' => 'po_count', 'label' => 'Jumlah PO'],
                    ['key' => 'total_purchased', 'label' => 'Total Pembelian (Rp)'],
                    ['key' => 'paid_val', 'label' => 'Terbayar (Rp)'],
                    ['key' => 'debt_val', 'label' => 'Sisa Hutang (Rp)'],
                    ['key' => 'status', 'label' => 'Status Jatuh Tempo'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'supplier_name' => 'CV Tekstil Bali Sejahtera', 'category_item' => 'Kain Brokat Semi Prancis (Yard)', 'po_count' => '4 PO', 'total_purchased' => 'Rp 28.500.000', 'paid_val' => 'Rp 21.000.000', 'debt_val' => 'Rp 7.500.000 (PO-041)', 'status' => 'Tempo 30 Hari', 'badge' => 'warning'],
                    ['id' => 2, 'supplier_name' => 'Tenun Tradisional Sidemen', 'category_item' => 'Kain Tenun Endek Sutra (Meter)', 'po_count' => '3 PO', 'total_purchased' => 'Rp 22.400.000', 'paid_val' => 'Rp 16.800.000', 'debt_val' => 'Rp 5.600.000 (PO-042)', 'status' => 'Tempo 14 Hari', 'badge' => 'info'],
                    ['id' => 3, 'supplier_name' => 'Toko Benang Sentosa', 'category_item' => 'Benang Bordir & Perlengkapan', 'po_count' => '5 PO', 'total_purchased' => 'Rp 8.800.000', 'paid_val' => 'Rp 8.800.000', 'debt_val' => 'Rp 0', 'status' => 'Lunas', 'badge' => 'success'],
                    ['id' => 4, 'supplier_name' => 'UD Pelengkap Garmen', 'category_item' => 'Kancing Bungkus & Resleting', 'po_count' => '3 PO', 'total_purchased' => 'Rp 8.700.000', 'paid_val' => 'Rp 3.300.000', 'debt_val' => 'Rp 5.400.000', 'status' => 'Jatuh Tempo Hari Ini', 'badge' => 'danger'],
                ];
                $config['formFields'] = [
                    ['name' => 'filter_supplier', 'label' => 'Pilih Supplier', 'type' => 'select', 'options' => ['Semua Supplier', 'CV Tekstil Bali Sejahtera', 'Tenun Tradisional Sidemen', 'Toko Benang Sentosa']],
                    ['name' => 'tgl_awal', 'label' => 'Dari Tanggal', 'type' => 'date', 'required' => true],
                    ['name' => 'tgl_akhir', 'label' => 'Sampai Tanggal', 'type' => 'date', 'required' => true],
                ];
                break;

                // ==========================================
                // 12. REPORTS: LAPORAN 7 GUDANG & NILAI PERSEDIAAN HPP (BAB 12.3)
                // ==========================================
            case 'laporan/stok':
                $config['headerIcon'] = 'bi-file-earmark-spreadsheet';
                $config['formTitle'] = 'Laporan Saldo Persediaan 7 Gudang Berdasarkan HPP';
                $config['summaryCards'] = [
                    ['title' => 'Nilai Persediaan Total', 'value' => 'Rp 384.500.000', 'icon' => 'bi-wallet2', 'color' => 'success'],
                    ['title' => 'Gudang Penjahit (WIP)', 'value' => 'Rp 42.500.000', 'icon' => 'bi-scissors', 'color' => 'primary'],
                    ['title' => 'Gudang Utama (FG)', 'value' => 'Rp 229.400.000', 'icon' => 'bi-boxes', 'color' => 'info'],
                    ['title' => 'Stok Cacat (Sale)', 'value' => 'Rp 1.200.000', 'icon' => 'bi-tag', 'color' => 'danger'],
                ];
                $config['columns'] = [
                    ['key' => 'wh_code', 'label' => 'Kode Gudang'],
                    ['key' => 'wh_name', 'label' => 'Nama Gudang Sesuai PDF'],
                    ['key' => 'stock_qty', 'label' => 'Kuantitas Stok'],
                    ['key' => 'valuation_method', 'label' => 'Metode Penilaian'],
                    ['key' => 'total_valuation', 'label' => 'Nilai Persediaan HPP (Rp)'],
                    ['key' => 'status', 'label' => 'Status Akuntansi'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'wh_code' => 'GD-WIP-01', 'wh_name' => 'Gudang Penjahit (Raw Material & WIP)', 'stock_qty' => '100 Yard Kain + 450 WIP', 'valuation_method' => 'Harga Beli Riil + Biaya Ongkir', 'total_valuation' => 'Rp 42.500.000', 'status' => 'Akun COA 1145', 'badge' => 'primary'],
                    ['id' => 2, 'wh_code' => 'GD-TRN-02', 'wh_name' => 'Gudang Transit (Menunggu QC)', 'stock_qty' => '60 Pcs Kebaya', 'valuation_method' => 'HPP Produksi (185rb/pcs)', 'total_valuation' => 'Rp 11.100.000', 'status' => 'Akun COA 1145', 'badge' => 'warning'],
                    ['id' => 3, 'wh_code' => 'GD-RJC-03', 'wh_name' => 'Gudang Reject / Service', 'stock_qty' => '15 Pcs Service', 'valuation_method' => 'HPP Awal + Biaya Service', 'total_valuation' => 'Rp 2.775.000', 'status' => 'Akun COA 1155', 'badge' => 'danger'],
                    ['id' => 4, 'wh_code' => 'GD-SAL-04', 'wh_name' => 'Gudang Sale (Barang Cacat)', 'stock_qty' => '8 Pcs Cacat', 'valuation_method' => 'Nilai Realisasi Bersih (NRV)', 'total_valuation' => 'Rp 1.200.000', 'status' => 'Akun COA 1156', 'badge' => 'secondary'],
                    ['id' => 5, 'wh_code' => 'GD-UTM-05', 'wh_name' => 'Gudang Utama (Finished Goods Lolos QC)', 'stock_qty' => '1.240 Pcs Barcode', 'valuation_method' => 'HPP Standar Produksi (185rb)', 'total_valuation' => 'Rp 229.400.000', 'status' => 'Akun COA 1150', 'badge' => 'success'],
                    ['id' => 6, 'wh_code' => 'GD-LIV-06', 'wh_name' => 'Gudang Live (Live Selling TikTok/Shopee)', 'stock_qty' => '280 Pcs', 'valuation_method' => 'HPP Standar Produksi (185rb)', 'total_valuation' => 'Rp 51.800.000', 'status' => 'Akun COA 1152', 'badge' => 'info'],
                    ['id' => 7, 'wh_code' => 'GD-STR-07', 'wh_name' => 'Gudang Store (Outlet Toko Kasir POS)', 'stock_qty' => '295 Pcs', 'valuation_method' => 'HPP Standar Produksi (185rb)', 'total_valuation' => 'Rp 54.575.000', 'status' => 'Akun COA 1151', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'per_tgl', 'label' => 'Posisi Saldo per Tanggal', 'type' => 'date', 'required' => true],
                    ['name' => 'format_ekspor', 'label' => 'Format Dokumen Unduh', 'type' => 'select', 'options' => ['Microsoft Excel (.xlsx)', 'Dokumen PDF Cetak (.pdf)']],
                ];
                break;

                // ==========================================
                // 12. REPORTS: LAPORAN PRODUKSI & SELISIH BAHAN (BAB 12.3)
                // ==========================================
            case 'laporan/produksi':
                $config['headerIcon'] = 'bi-scissors';
                $config['formTitle'] = 'Laporan Produksi, Konsumsi Bahan Standar 1,56 Yard vs Aktual & Selisih Waste';
                $config['summaryCards'] = [
                    ['title' => 'Total Produksi Selesai', 'value' => '60 Pcs Kebaya', 'icon' => 'bi-check2-circle', 'color' => 'success'],
                    ['title' => 'Kain Standar Terpakai', 'value' => '93,60 Yard', 'icon' => 'bi-rulers', 'color' => 'primary'],
                    ['title' => 'Sisa Kain Aktual', 'value' => '5,80 Yard Fisik', 'icon' => 'bi-pie-chart', 'color' => 'info'],
                    ['title' => 'Selisih Perca / Waste', 'value' => '0,60 Yard (-0,64%)', 'icon' => 'bi-scissors', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'wo_code', 'label' => 'No. Work Order'],
                    ['key' => 'model', 'label' => 'Model Kebaya'],
                    ['key' => 'vendor_penjahit', 'label' => 'Vendor Penjahit'],
                    ['key' => 'kain_diberikan', 'label' => 'Kain Awal Diberikan'],
                    ['key' => 'output_qty', 'label' => 'Hasil Produksi'],
                    ['key' => 'std_consumption', 'label' => 'Konsumsi Standar (1,56 Yd)'],
                    ['key' => 'actual_waste', 'label' => 'Sisa Aktual & Waste'],
                    ['key' => 'total_hpp', 'label' => 'Total HPP Batch'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'wo_code' => 'WO-202610-001', 'model' => 'Kebaya Bali Bordir Maroon M', 'vendor_penjahit' => 'I Wayan Artana (Denpasar)', 'kain_diberikan' => '100,00 Yard', 'output_qty' => '60 Pcs', 'std_consumption' => '93,60 Yard (60 × 1,56 yd)', 'actual_waste' => 'Sisa: 5,80 Yd (Waste 0,60 Yd)', 'total_hpp' => 'Rp 11.100.000 (@ 185rb)', 'badge' => 'success'],
                    ['id' => 2, 'wo_code' => 'WO-202610-002', 'model' => 'Kamen Endek Sutra Hitam Emas', 'vendor_penjahit' => 'Tenun Sidemen Klungkung', 'kain_diberikan' => '80,00 Meter', 'output_qty' => '32 Pcs', 'std_consumption' => '80,00 Meter (32 × 2,5 m)', 'actual_waste' => 'Sisa: 0,00 M (Waste 0,00 M)', 'total_hpp' => 'Rp 7.040.000 (@ 220rb)', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'filter_penjahit', 'label' => 'Pilih Vendor Penjahit', 'type' => 'select', 'options' => ['Semua Penjahit', 'I Wayan Artana', 'Tenun Sidemen Klungkung']],
                ];
                break;

                // ==========================================
                // 12. REPORTS: LAPORAN KEUANGAN & EKSPOR EXCEL (BAB 12.4 & 13)
                // ==========================================
            case 'laporan/keuangan':
            case 'laporan/ekspor':
                $config['headerIcon'] = 'bi-file-earmark-excel';
                $config['formTitle'] = 'Ketentuan Ekspor Laporan Lengkap ke Microsoft Excel (.xlsx)';
                $config['summaryCards'] = [
                    ['title' => 'Ekspor Seluruh Modul', 'value' => 'Wajib .xlsx (.csv Opsional)', 'icon' => 'bi-file-earmark-excel', 'color' => 'success'],
                    ['title' => 'Kelengkapan Data', 'value' => 'Detail & Ringkasan', 'icon' => 'bi-table', 'color' => 'primary'],
                    ['title' => 'Filter Parameter', 'value' => 'Tanggal, Gudang, SKU, Channel', 'icon' => 'bi-funnel', 'color' => 'info'],
                    ['title' => 'Dukungan Olah Excel', 'value' => 'Rumus & Formula Siap Olah', 'icon' => 'bi-calculator', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'report_name', 'label' => 'Modul & Format Laporan (Sesuai Bab 13 PDF)'],
                    ['key' => 'scope', 'label' => 'Cakupan Kolom Data'],
                    ['key' => 'filter_support', 'label' => 'Parameter Filter Didukung'],
                    ['key' => 'export_format', 'label' => 'Format Unduh'],
                    ['key' => 'action', 'label' => 'Aksi Unduh'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'report_name' => '1. Laporan Penjualan Omnichannel (Shopee, TikTok, Reguler, POS)', 'scope' => 'Nomor Resi, Invoice, SKU, Qty, Harga, Diskon, HPP, Laba Kotor', 'filter_support' => 'Tanggal, Channel, Outlet, Kategori, SKU', 'export_format' => 'Microsoft Excel (.xlsx)', 'action' => 'Unduh .xlsx', 'badge' => 'success'],
                    ['id' => 2, 'report_name' => '2. Laporan Pembelian & Hutang Dagang Supplier', 'scope' => 'No PO, Faktur, Supplier, Satuan Yard/Meter, Harga Beli, Jatuh Tempo', 'filter_support' => 'Periode, Supplier, Gudang Penerima', 'export_format' => 'Microsoft Excel (.xlsx)', 'action' => 'Unduh .xlsx', 'badge' => 'success'],
                    ['id' => 3, 'report_name' => '3. Laporan Kartu Stok & Mutasi Saldo 7 Gudang', 'scope' => 'Waktu, Dokumen, Gudang Asal/Tujuan, Masuk, Keluar, Saldo, Nilai HPP', 'filter_support' => 'Gudang (Penjahit/Transit/Utama/Live/Store), SKU', 'export_format' => 'Microsoft Excel (.xlsx)', 'action' => 'Unduh .xlsx', 'badge' => 'success'],
                    ['id' => 4, 'report_name' => '4. Laporan Produksi, Konsumsi 1,56 Yard vs Aktual & Selisih Bahan', 'scope' => 'WO, Model, Kain Awal, Hasil Jadi, Sisa Kain, Selisih Waste, HPP', 'filter_support' => 'Model, Penjahit, Batch Produksi', 'export_format' => 'Microsoft Excel (.xlsx)', 'action' => 'Unduh .xlsx', 'badge' => 'success'],
                    ['id' => 5, 'report_name' => '5. Laporan Laba Rugi Komprehensif Berdasarkan HPP', 'scope' => 'Penjualan per Channel, HPP Terjual, Laba Kotor, Beban Operasional, Net', 'filter_support' => 'Periode Akuntansi, Channel', 'export_format' => 'Microsoft Excel (.xlsx)', 'action' => 'Unduh .xlsx', 'badge' => 'success'],
                    ['id' => 6, 'report_name' => '6. Laporan Buku Besar, Neraca Saldo & Neraca Keuangan', 'scope' => 'Akun COA, Saldo Awal, Debit, Kredit, Saldo Akhir Seimbang', 'filter_support' => 'Periode Akuntansi, Klasifikasi Akun', 'export_format' => 'Microsoft Excel (.xlsx)', 'action' => 'Unduh .xlsx', 'badge' => 'success'],
                    ['id' => 7, 'report_name' => '7. Laporan Return Paket & Verifikasi Gudang Transit Return', 'scope' => 'No Return, 4 Digit Resi, Verifikasi Kondisi, Alokasi Gudang, Nilai HPP', 'filter_support' => 'Channel Asal, Status Kondisi', 'export_format' => 'Microsoft Excel (.xlsx)', 'action' => 'Unduh .xlsx', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'pilih_format', 'label' => 'Pilih Jenis Laporan Yang Akan Diekspor', 'type' => 'select', 'options' => ['Laporan Penjualan Omnichannel', 'Laporan Pembelian & Hutang Supplier', 'Laporan Kartu Stok 7 Gudang', 'Laporan Produksi & Sisa Kain 1,56 Yard', 'Laporan Laba Rugi Komprehensif', 'Laporan Neraca Saldo & Buku Besar'], 'required' => true],
                    ['name' => 'tgl_dari', 'label' => 'Tanggal Awal Filter', 'type' => 'date', 'required' => true],
                    ['name' => 'tgl_sampai', 'label' => 'Tanggal Akhir Filter', 'type' => 'date', 'required' => true],
                    ['name' => 'format_file', 'label' => 'Format Ekspor Wajib', 'type' => 'select', 'options' => ['Microsoft Excel (.xlsx)', 'Comma Separated Values (.csv)', 'Dokumen PDF Cetak (.pdf)']],
                ];
                break;

                // ==========================================
                // DEFAULT SAFE FALLBACK
                // ==========================================
            default:
                $config['headerIcon'] = 'bi-folder2-open';
                $config['formTitle'] = 'Form Input Data '.$subTitle;
                $config['summaryCards'] = [
                    ['title' => 'Modul Terintegrasi', 'value' => $moduleTitle, 'icon' => 'bi-folder', 'color' => 'primary'],
                    ['title' => 'Submodul Aktif', 'value' => $subTitle, 'icon' => 'bi-check2', 'color' => 'success'],
                    ['title' => 'Standar Dokumen', 'value' => 'Sesuai PDF', 'icon' => 'bi-patch-check', 'color' => 'info'],
                    ['title' => 'Format Ekspor', 'value' => 'Excel (.xlsx)', 'icon' => 'bi-file-earmark-excel', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'code', 'label' => 'Kode / ID Referensi'],
                    ['key' => 'name', 'label' => 'Nama / Uraian Transaksi'],
                    ['key' => 'category', 'label' => 'Modul'],
                    ['key' => 'date', 'label' => 'Tanggal'],
                    ['key' => 'status', 'label' => 'Status Operasional'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'code' => strtoupper(substr($module, 0, 3)).'-001', 'name' => 'Transaksi '.$subTitle.' Terverifikasi', 'category' => $moduleTitle, 'date' => '24 Okt 2026', 'status' => 'Aktif', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'kode', 'label' => 'Kode / Referensi', 'type' => 'text', 'placeholder' => 'Otomatis', 'required' => true],
                    ['name' => 'nama', 'label' => 'Nama / Keterangan', 'type' => 'text', 'placeholder' => 'Keterangan lengkap', 'required' => true],
                ];
                break;
        }

        return $config;
    }
}
