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
     * Build rich configuration, dummy data rows, and form fields for any submodule.
     */
    private function getModuleConfig(string $module, ?string $submodule, string $key): array
    {
        $moduleTitle = ucwords(str_replace(['-', '_'], ' ', $module));
        $subTitle = $submodule ? ucwords(str_replace(['-', '_'], ' ', $submodule)) : 'Overview';

        // Default skeleton
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
            // 1. MASTER DATA: PRODUK & VARIAN
            case 'master/produk':
            case 'master/index':
                $config['headerIcon'] = 'bi-box-seam';
                $config['formTitle'] = 'Form Tambah Produk & Varian Baru';
                $config['summaryCards'] = [
                    ['title' => 'Total SKU Produk', 'value' => '142 SKU', 'icon' => 'bi-boxes', 'color' => 'primary'],
                    ['title' => 'Kategori Aktif', 'value' => '8 Kategori', 'icon' => 'bi-tags', 'color' => 'info'],
                    ['title' => 'Stok Hampir Habis', 'value' => '5 Produk', 'icon' => 'bi-exclamation-triangle', 'color' => 'danger'],
                    ['title' => 'Nilai Persediaan', 'value' => 'Rp 384.500.000', 'icon' => 'bi-wallet2', 'color' => 'success'],
                ];
                $config['columns'] = [
                    ['key' => 'sku', 'label' => 'SKU / Barcode'],
                    ['key' => 'name', 'label' => 'Nama Produk & Varian'],
                    ['key' => 'category', 'label' => 'Kategori'],
                    ['key' => 'hpp', 'label' => 'HPP (Modal)'],
                    ['key' => 'price', 'label' => 'Harga Jual'],
                    ['key' => 'stock', 'label' => 'Total Stok'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'sku' => 'KB-BRD-MRN-M', 'name' => 'Kebaya Bordir Bali Premium Maroon (Size M)', 'category' => 'Kebaya Bali', 'hpp' => 'Rp 185.000', 'price' => 'Rp 385.000', 'stock' => '24 pcs', 'status' => 'Tersedia', 'badge' => 'success'],
                    ['id' => 2, 'sku' => 'KB-BRD-MRN-L', 'name' => 'Kebaya Bordir Bali Premium Maroon (Size L)', 'category' => 'Kebaya Bali', 'hpp' => 'Rp 185.000', 'price' => 'Rp 385.000', 'stock' => '18 pcs', 'status' => 'Tersedia', 'badge' => 'success'],
                    ['id' => 3, 'sku' => 'KM-EDK-GLD-ALL', 'name' => 'Kamen Endek Sutra Motif Wajik Emas', 'category' => 'Kamen & Bawahan', 'hpp' => 'Rp 220.000', 'price' => 'Rp 450.000', 'stock' => '15 pcs', 'status' => 'Tersedia', 'badge' => 'success'],
                    ['id' => 4, 'sku' => 'KMJ-SFR-WHT-XL', 'name' => 'Kemeja Safari Pria Katun Putih (Size XL)', 'category' => 'Kemeja Safari', 'hpp' => 'Rp 125.000', 'price' => 'Rp 275.000', 'stock' => '4 pcs', 'status' => 'Menipis', 'badge' => 'warning'],
                    ['id' => 5, 'sku' => 'SLD-OBI-TRD-RED', 'name' => 'Selendang Obi Songket Tradisional Merah Emas', 'category' => 'Aksesoris', 'hpp' => 'Rp 35.000', 'price' => 'Rp 85.000', 'stock' => '65 pcs', 'status' => 'Tersedia', 'badge' => 'success'],
                    ['id' => 6, 'sku' => 'UDG-BLI-BLK-ALL', 'name' => 'Udeng Bali Pria Setengah Jadi Hitam Poleng', 'category' => 'Aksesoris', 'hpp' => 'Rp 28.000', 'price' => 'Rp 65.000', 'stock' => '0 pcs', 'status' => 'Habis', 'badge' => 'danger'],
                ];
                $config['formFields'] = [
                    ['name' => 'sku', 'label' => 'Kode SKU / Barcode', 'type' => 'text', 'placeholder' => 'Contoh: KB-BRD-SGE-M', 'required' => true],
                    ['name' => 'nama_produk', 'label' => 'Nama Produk & Varian', 'type' => 'text', 'placeholder' => 'Nama lengkap beserta varian warna/ukuran', 'required' => true],
                    ['name' => 'kategori', 'label' => 'Kategori Produk', 'type' => 'select', 'options' => ['Kebaya Bali', 'Kamen & Bawahan', 'Kemeja Safari', 'Aksesoris Tradisional', 'Kain Lembaran'], 'required' => true],
                    ['name' => 'satuan', 'label' => 'Satuan Unit', 'type' => 'select', 'options' => ['Pcs', 'Meter', 'Roll', 'Set', 'Lusin'], 'required' => true],
                    ['name' => 'hpp', 'label' => 'Harga Pokok Produksi (HPP)', 'type' => 'number', 'placeholder' => '0', 'required' => true],
                    ['name' => 'harga_jual', 'label' => 'Harga Jual Satuan (Rp)', 'type' => 'number', 'placeholder' => '0', 'required' => true],
                    ['name' => 'stok_minimum', 'label' => 'Batas Peringatan Stok Minimum', 'type' => 'number', 'placeholder' => '5', 'required' => true],
                    ['name' => 'deskripsi', 'label' => 'Spesifikasi & Keterangan', 'type' => 'textarea', 'placeholder' => 'Tulis bahan kain, teknik bordir, ukuran detail...'],
                ];
                break;

                // 1. MASTER DATA: BAHAN BAKU & PENDUKUNG
            case 'master/bahan-baku':
                $config['headerIcon'] = 'bi-layers';
                $config['formTitle'] = 'Form Tambah Bahan Baku / Penolong';
                $config['summaryCards'] = [
                    ['title' => 'Total Jenis Bahan', 'value' => '48 Bahan', 'icon' => 'bi-layers-half', 'color' => 'primary'],
                    ['title' => 'Bahan Baku Kain', 'value' => '1.250 Meter', 'icon' => 'bi-box-seam', 'color' => 'success'],
                    ['title' => 'Bahan Benang & Renda', 'value' => '320 Roll', 'icon' => 'bi-disc', 'color' => 'info'],
                    ['title' => 'Perlu Restock', 'value' => '3 Bahan', 'icon' => 'bi-bell', 'color' => 'danger'],
                ];
                $config['columns'] = [
                    ['key' => 'code', 'label' => 'Kode Bahan'],
                    ['key' => 'name', 'label' => 'Nama Bahan Baku'],
                    ['key' => 'type', 'label' => 'Jenis'],
                    ['key' => 'supplier', 'label' => 'Supplier Utama'],
                    ['key' => 'stock', 'label' => 'Stok Tersedia'],
                    ['key' => 'unit_price', 'label' => 'Harga Beli Terakhir'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'code' => 'BB-KN-BRKT-01', 'name' => 'Kain Brokat Semi Prancis Maroon', 'type' => 'Bahan Utama', 'supplier' => 'CV Tekstil Bali Sejahtera', 'stock' => '240 Meter', 'unit_price' => 'Rp 65.000 / m', 'status' => 'Aman', 'badge' => 'success'],
                    ['id' => 2, 'code' => 'BB-KN-ENDK-02', 'name' => 'Kain Tenun Endek Klungkung Sutra', 'type' => 'Bahan Utama', 'supplier' => 'Tenun Tradisional Sidemen', 'stock' => '115 Meter', 'unit_price' => 'Rp 140.000 / m', 'status' => 'Aman', 'badge' => 'success'],
                    ['id' => 3, 'code' => 'BB-BNG-EMAS-03', 'name' => 'Benang Bordir Metalik Emas Kilap', 'type' => 'Bahan Penolong', 'supplier' => 'Toko Benang Sentosa Denpasar', 'stock' => '45 Roll', 'unit_price' => 'Rp 22.000 / roll', 'status' => 'Aman', 'badge' => 'success'],
                    ['id' => 4, 'code' => 'BB-KNC-BATU-04', 'name' => 'Kancing Bungkus Motif Emas 12mm', 'type' => 'Aksesoris Jahit', 'supplier' => 'UD Pelengkap Garmen', 'stock' => '8 Gross', 'unit_price' => 'Rp 35.000 / gross', 'status' => 'Menipis', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'kode_bahan', 'label' => 'Kode Bahan', 'type' => 'text', 'placeholder' => 'Contoh: BB-KN-KATUN-01', 'required' => true],
                    ['name' => 'nama_bahan', 'label' => 'Nama Bahan Baku', 'type' => 'text', 'placeholder' => 'Nama bahan baku atau bahan pendukung', 'required' => true],
                    ['name' => 'jenis', 'label' => 'Jenis Bahan', 'type' => 'select', 'options' => ['Bahan Utama (Kain)', 'Bahan Penolong (Benang/Furing)', 'Aksesoris (Kancing/Resleting)', 'Packaging (Plastik/Label/Box)'], 'required' => true],
                    ['name' => 'satuan', 'label' => 'Satuan', 'type' => 'select', 'options' => ['Meter', 'Roll', 'Yard', 'Gross', 'Pcs', 'Kilogram'], 'required' => true],
                    ['name' => 'supplier_id', 'label' => 'Supplier Langganan', 'type' => 'select', 'options' => ['CV Tekstil Bali Sejahtera', 'Tenun Tradisional Sidemen', 'Toko Benang Sentosa Denpasar', 'UD Pelengkap Garmen']],
                    ['name' => 'harga_beli', 'label' => 'Estimasi Harga Beli Satuan (Rp)', 'type' => 'number', 'placeholder' => '0'],
                    ['name' => 'stok_minimum', 'label' => 'Stok Minimum Peringatan', 'type' => 'number', 'placeholder' => '20'],
                ];
                break;

                // 2. PURCHASING & SUPPLIER: PURCHASE ORDER (PO)
            case 'purchasing/po':
            case 'purchasing/index':
                $config['headerIcon'] = 'bi-file-earmark-text';
                $config['formTitle'] = 'Form Buat Purchase Order (PO) Baru';
                $config['summaryCards'] = [
                    ['title' => 'Total PO Bulan Ini', 'value' => '24 PO', 'icon' => 'bi-file-text', 'color' => 'primary'],
                    ['title' => 'PO Menunggu Konfirmasi', 'value' => '4 PO', 'icon' => 'bi-clock-history', 'color' => 'warning'],
                    ['title' => 'PO Dikirim / Shipping', 'value' => '3 PO', 'icon' => 'bi-truck', 'color' => 'info'],
                    ['title' => 'Total Nominal PO', 'value' => 'Rp 68.400.000', 'icon' => 'bi-cash', 'color' => 'success'],
                ];
                $config['columns'] = [
                    ['key' => 'po_number', 'label' => 'Nomor PO'],
                    ['key' => 'date', 'label' => 'Tanggal PO'],
                    ['key' => 'supplier', 'label' => 'Nama Supplier'],
                    ['key' => 'items', 'label' => 'Item Dipesan'],
                    ['key' => 'total', 'label' => 'Total Tagihan'],
                    ['key' => 'payment_status', 'label' => 'Pembayaran'],
                    ['key' => 'shipping_status', 'label' => 'Status Barang'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'po_number' => 'PO-202610-041', 'date' => '22 Okt 2026', 'supplier' => 'CV Tekstil Bali Sejahtera', 'items' => 'Kain Brokat Semi Prancis (200m)', 'total' => 'Rp 13.000.000', 'payment_status' => 'Lunas', 'shipping_status' => 'Diterima Lengkap', 'badge' => 'success'],
                    ['id' => 2, 'po_number' => 'PO-202610-042', 'date' => '23 Okt 2026', 'supplier' => 'Tenun Sidemen Klungkung', 'items' => 'Kain Endek Sutra Asli (80m)', 'total' => 'Rp 11.200.000', 'payment_status' => 'DP 50%', 'shipping_status' => 'Dalam Pengiriman', 'badge' => 'info'],
                    ['id' => 3, 'po_number' => 'PO-202610-043', 'date' => '24 Okt 2026', 'supplier' => 'Toko Benang Sentosa', 'items' => 'Benang Bordir Emas (50 roll)', 'total' => 'Rp 1.100.000', 'payment_status' => 'Belum Dibayar', 'shipping_status' => 'Diproses Supplier', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'po_number', 'label' => 'Nomor PO (Otomatis)', 'type' => 'text', 'placeholder' => 'PO-202610-044', 'required' => true],
                    ['name' => 'tanggal', 'label' => 'Tanggal Order', 'type' => 'date', 'required' => true],
                    ['name' => 'supplier', 'label' => 'Pilih Supplier', 'type' => 'select', 'options' => ['CV Tekstil Bali Sejahtera', 'Tenun Tradisional Sidemen', 'Toko Benang Sentosa Denpasar', 'UD Pelengkap Garmen'], 'required' => true],
                    ['name' => 'gudang_tujuan', 'label' => 'Gudang Penerima', 'type' => 'select', 'options' => ['Gudang Bahan Baku (Denpasar)', 'Gudang Produksi & Cutting', 'Gudang Utama Kuta'], 'required' => true],
                    ['name' => 'termin_bayar', 'label' => 'Termin Pembayaran', 'type' => 'select', 'options' => ['Tunai (Cash)', 'Transfer Bank', 'Tempo 14 Hari', 'Tempo 30 Hari', 'DP 50%']],
                    ['name' => 'catatan', 'label' => 'Catatan Tambahan', 'type' => 'textarea', 'placeholder' => 'Instruksi pengiriman, warna khusus, dsb.'],
                ];
                break;

                // 3. MULTI-WAREHOUSE & INVENTORY
            case 'inventory/stok':
            case 'inventory/index':
                $config['headerIcon'] = 'bi-boxes';
                $config['formTitle'] = 'Form Penyesuaian / Transfer Stok Gudang';
                $config['summaryCards'] = [
                    ['title' => 'Gudang Denpasar (Bahan)', 'value' => '450 Item', 'icon' => 'bi-building', 'color' => 'primary'],
                    ['title' => 'Gudang Produksi (WIP)', 'value' => '310 Item', 'icon' => 'bi-gear', 'color' => 'info'],
                    ['title' => 'Gudang Kuta (Barang Jadi)', 'value' => '1.240 Item', 'icon' => 'bi-box', 'color' => 'success'],
                    ['title' => 'Toko Kasir (Display)', 'value' => '295 Item', 'icon' => 'bi-shop', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'sku', 'label' => 'SKU'],
                    ['key' => 'product', 'label' => 'Nama Item'],
                    ['key' => 'warehouse', 'label' => 'Lokasi Gudang'],
                    ['key' => 'qty', 'label' => 'Stok Fisik'],
                    ['key' => 'safety_stock', 'label' => 'Batas Min'],
                    ['key' => 'last_update', 'label' => 'Update Terakhir'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'sku' => 'KB-BRD-MRN-M', 'product' => 'Kebaya Bordir Bali Premium Maroon (M)', 'warehouse' => 'Gudang Kuta (Barang Jadi)', 'qty' => '24 pcs', 'safety_stock' => '5 pcs', 'last_update' => 'Hari ini 09:15', 'status' => 'Aman', 'badge' => 'success'],
                    ['id' => 2, 'sku' => 'KMJ-SFR-WHT-XL', 'product' => 'Kemeja Safari Pria Putih (XL)', 'warehouse' => 'Toko Display Kasir', 'qty' => '3 pcs', 'safety_stock' => '5 pcs', 'last_update' => 'Kemarin 14:20', 'status' => 'Kurang Dari Min', 'badge' => 'danger'],
                    ['id' => 3, 'sku' => 'BB-KN-BRKT-01', 'product' => 'Kain Brokat Semi Prancis Maroon', 'warehouse' => 'Gudang Bahan Baku (Denpasar)', 'qty' => '240 Meter', 'safety_stock' => '50 m', 'last_update' => '23 Okt 2026', 'status' => 'Optimal', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'jenis_aksi', 'label' => 'Jenis Aksi Inventori', 'type' => 'select', 'options' => ['Transfer Antar-Gudang', 'Penyesuaian Stok (Hasil Stok Opname)', 'Input Barang Masuk Manual', 'Input Barang Keluar Manual'], 'required' => true],
                    ['name' => 'gudang_asal', 'label' => 'Gudang Asal', 'type' => 'select', 'options' => ['Gudang Bahan Baku (Denpasar)', 'Gudang Produksi & Cutting', 'Gudang Kuta (Barang Jadi)', 'Toko Display Kasir']],
                    ['name' => 'gudang_tujuan', 'label' => 'Gudang Tujuan (Untuk Transfer)', 'type' => 'select', 'options' => ['Gudang Kuta (Barang Jadi)', 'Toko Display Kasir', 'Gudang Produksi & Cutting']],
                    ['name' => 'produk', 'label' => 'Pilih Produk / Bahan', 'type' => 'select', 'options' => ['Kebaya Bordir Bali Premium Maroon (Size M)', 'Kamen Endek Sutra Motif Wajik Emas', 'Kemeja Safari Pria Katun Putih (Size XL)', 'Kain Brokat Semi Prancis Maroon'], 'required' => true],
                    ['name' => 'jumlah', 'label' => 'Jumlah Qty', 'type' => 'number', 'placeholder' => '0', 'required' => true],
                    ['name' => 'keterangan', 'label' => 'Alasan & Keterangan', 'type' => 'textarea', 'placeholder' => 'Contoh: Restock display toko depan, koreksi selisih opname...'],
                ];
                break;

                // 4. PRODUCTION, BOM & HPP: WORK ORDERS
            case 'produksi/work-orders':
            case 'produksi/index':
                $config['headerIcon'] = 'bi-gear-wide-connected';
                $config['formTitle'] = 'Form Pembuatan Perintah Produksi (Work Order)';
                $config['summaryCards'] = [
                    ['title' => 'WO Aktif Berjalan', 'value' => '12 Order', 'icon' => 'bi-gear', 'color' => 'primary'],
                    ['title' => 'Target Produksi Bln Ini', 'value' => '850 Pcs', 'icon' => 'bi-graph-up', 'color' => 'info'],
                    ['title' => 'Selesai & Masuk QC', 'value' => '420 Pcs', 'icon' => 'bi-check2-circle', 'color' => 'success'],
                    ['title' => 'Rata-rata Efisiensi', 'value' => '96.4%', 'icon' => 'bi-pie-chart', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'wo_code', 'label' => 'No. Work Order'],
                    ['key' => 'product', 'label' => 'Produk Jadi'],
                    ['key' => 'qty_target', 'label' => 'Target Qty'],
                    ['key' => 'bom', 'label' => 'Resep BOM Digunakan'],
                    ['key' => 'start_date', 'label' => 'Mulai'],
                    ['key' => 'est_finish', 'label' => 'Batas Selesai'],
                    ['key' => 'stage', 'label' => 'Tahapan Saat Ini'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'wo_code' => 'WO-202610-001', 'product' => 'Kebaya Bali Bordir Premium Maroon', 'qty_target' => '50 pcs', 'bom' => 'BOM-KB-MRN-v1', 'start_date' => '20 Okt 2026', 'est_finish' => '28 Okt 2026', 'stage' => 'Proses Jahit & Payet', 'badge' => 'primary'],
                    ['id' => 2, 'wo_code' => 'WO-202610-002', 'product' => 'Kamen Endek Tenun Sutra Hitam Emas', 'qty_target' => '100 pcs', 'bom' => 'BOM-KM-EDK-v2', 'start_date' => '22 Okt 2026', 'est_finish' => '30 Okt 2026', 'stage' => 'Potong & Cutting', 'badge' => 'info'],
                    ['id' => 3, 'wo_code' => 'WO-202610-003', 'product' => 'Kemeja Safari Pria Katun Putih', 'qty_target' => '75 pcs', 'bom' => 'BOM-KMJ-SFR-v1', 'start_date' => '23 Okt 2026', 'est_finish' => '02 Nov 2026', 'stage' => 'Finishing & Setrika', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'no_wo', 'label' => 'Nomor Work Order (WO)', 'type' => 'text', 'placeholder' => 'WO-202610-004', 'required' => true],
                    ['name' => 'produk', 'label' => 'Produk Yang Akan Diproduksi', 'type' => 'select', 'options' => ['Kebaya Bali Bordir Premium Maroon', 'Kamen Endek Sutra Motif Wajik Emas', 'Kemeja Safari Pria Katun Jepang Putih', 'Selendang Obi Songket Tradisional'], 'required' => true],
                    ['name' => 'resep_bom', 'label' => 'Pilih Resep Bill of Materials (BOM)', 'type' => 'select', 'options' => ['BOM Kebaya Maroon (Kain 2m, Benang Emas 1/2 roll, Kancing 8 pcs)', 'BOM Kamen Endek Sutra (Kain Endek 2.5m, Perekat)', 'BOM Kemeja Safari (Kain Katun 1.8m, Kancing 7 pcs)'], 'required' => true],
                    ['name' => 'qty_order', 'label' => 'Target Jumlah Produksi (Pcs)', 'type' => 'number', 'placeholder' => '50', 'required' => true],
                    ['name' => 'tanggal_mulai', 'label' => 'Tanggal Mulai Kerja', 'type' => 'date', 'required' => true],
                    ['name' => 'tanggal_target', 'label' => 'Target Selesai Produksi', 'type' => 'date', 'required' => true],
                    ['name' => 'penanggung_jawab', 'label' => 'Kepala Penjahit / Supervisor', 'type' => 'text', 'placeholder' => 'Nama PIC Produksi'],
                ];
                break;

                // 5. QUALITY CONTROL & BARCODE
            case 'qc/inspeksi':
            case 'qc/index':
                $config['headerIcon'] = 'bi-patch-check';
                $config['formTitle'] = 'Form Pencatatan Inspeksi QC & Cetak Barcode';
                $config['summaryCards'] = [
                    ['title' => 'Produk Lolos QC', 'value' => '392 Pcs (93.3%)', 'icon' => 'bi-check-circle', 'color' => 'success'],
                    ['title' => 'Reject / Cacat', 'value' => '16 Pcs (3.8%)', 'icon' => 'bi-x-octagon', 'color' => 'danger'],
                    ['title' => 'Perlu Perbaikan', 'value' => '12 Pcs (2.9%)', 'icon' => 'bi-tools', 'color' => 'warning'],
                    ['title' => 'Barcode Tercetak', 'value' => '420 Label', 'icon' => 'bi-upc', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'inspect_code', 'label' => 'No. Inspeksi'],
                    ['key' => 'wo_ref', 'label' => 'Ref. WO'],
                    ['key' => 'product', 'label' => 'Item Produk'],
                    ['key' => 'qty_inspected', 'label' => 'Jumlah Cek'],
                    ['key' => 'passed', 'label' => 'Lolos QC'],
                    ['key' => 'rejected', 'label' => 'Reject'],
                    ['key' => 'action', 'label' => 'Tindakan'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'inspect_code' => 'QC-202610-019', 'wo_ref' => 'WO-202610-001', 'product' => 'Kebaya Bali Bordir Premium Maroon', 'qty_inspected' => '50 pcs', 'passed' => '47 pcs', 'rejected' => '3 pcs', 'action' => '3 Pcs Jahit Ulang', 'badge' => 'warning'],
                    ['id' => 2, 'inspect_code' => 'QC-202610-020', 'wo_ref' => 'WO-202610-002', 'product' => 'Kamen Endek Sutra Hitam Emas', 'qty_inspected' => '80 pcs', 'passed' => '80 pcs', 'rejected' => '0 pcs', 'action' => 'Siap Cetak Barcode & Packing', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'ref_wo', 'label' => 'Pilih Batch Work Order (WO)', 'type' => 'select', 'options' => ['WO-202610-001 (Kebaya Bali Bordir Premium)', 'WO-202610-002 (Kamen Endek Sutra)', 'WO-202610-003 (Kemeja Safari)'], 'required' => true],
                    ['name' => 'qty_dicek', 'label' => 'Total Jumlah Diperiksa (Pcs)', 'type' => 'number', 'placeholder' => '0', 'required' => true],
                    ['name' => 'qty_lolos', 'label' => 'Jumlah Lolos Kualitas (Grade A)', 'type' => 'number', 'placeholder' => '0', 'required' => true],
                    ['name' => 'qty_reject', 'label' => 'Jumlah Cacat / Reject', 'type' => 'number', 'placeholder' => '0'],
                    ['name' => 'qty_rework', 'label' => 'Jumlah Perlu Perbaikan', 'type' => 'number', 'placeholder' => '0'],
                    ['name' => 'kategori_cacat', 'label' => 'Jenis Kerusakan (Jika Ada)', 'type' => 'select', 'options' => ['Tidak Ada Cacat', 'Jahitan Lepas / Kerut', 'Bordir Loncat / Rusak', 'Noda Minyak / Kotor', 'Kain Sobek / Cacat Tenun']],
                    ['name' => 'petugas_qc', 'label' => 'Nama Inspektur QC', 'type' => 'text', 'placeholder' => 'Nama pemeriksa kualitas'],
                ];
                break;

                // 6. PENJUALAN & RETUR
            case 'penjualan/transaksi':
            case 'penjualan/index':
                $config['headerIcon'] = 'bi-bag-check';
                $config['formTitle'] = 'Form Input Pesanan / Penjualan Baru';
                $config['summaryCards'] = [
                    ['title' => 'Penjualan Hari Ini', 'value' => 'Rp 8.750.000', 'icon' => 'bi-cart', 'color' => 'success'],
                    ['title' => 'Pesanan Diproses', 'value' => '14 Order', 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
                    ['title' => 'Transaksi Lunas', 'value' => '128 Invoice', 'icon' => 'bi-check2-all', 'color' => 'primary'],
                    ['title' => 'Retur Masuk', 'value' => '2 Transaksi', 'icon' => 'bi-arrow-counterclockwise', 'color' => 'danger'],
                ];
                $config['columns'] = [
                    ['key' => 'invoice', 'label' => 'No. Invoice'],
                    ['key' => 'channel', 'label' => 'Channel'],
                    ['key' => 'customer', 'label' => 'Nama Pelanggan'],
                    ['key' => 'items', 'label' => 'Daftar Produk'],
                    ['key' => 'total', 'label' => 'Total Belanja'],
                    ['key' => 'payment', 'label' => 'Pembayaran'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'invoice' => 'INV-202610-091', 'channel' => 'Toko Kasir POS', 'customer' => 'Ni Wayan Sukmawati', 'items' => 'Kebaya Bordir (1), Kamen Endek (1)', 'total' => 'Rp 835.000', 'payment' => 'QRIS BCA', 'status' => 'Lunas', 'badge' => 'success'],
                    ['id' => 2, 'invoice' => 'INV-202610-092', 'channel' => 'Grosir / Reseller B2B', 'customer' => 'Butik Sanur Indah', 'items' => 'Paket Safari Putih (20 pcs)', 'total' => 'Rp 5.500.000', 'payment' => 'Transfer Bank (DP 50%)', 'status' => 'Diproses', 'badge' => 'primary'],
                    ['id' => 3, 'invoice' => 'INV-202610-093', 'channel' => 'Shopee Mall', 'customer' => 'Dewi Lestari (Jakarta)', 'items' => 'Selendang Obi Songket (2)', 'total' => 'Rp 170.000', 'payment' => 'ShopeePay', 'status' => 'Siap Dikirim', 'badge' => 'info'],
                ];
                $config['formFields'] = [
                    ['name' => 'customer_type', 'label' => 'Tipe Pelanggan', 'type' => 'select', 'options' => ['Retail / Pelanggan Langsung', 'Grosir / Toko Reseller', 'Marketplace / Online'], 'required' => true],
                    ['name' => 'customer_name', 'label' => 'Nama Pelanggan / Butik', 'type' => 'text', 'placeholder' => 'Nama lengkap pembeli', 'required' => true],
                    ['name' => 'telepon', 'label' => 'No. WhatsApp / HP', 'type' => 'text', 'placeholder' => '0812xxxxxxxx'],
                    ['name' => 'gudang_asal', 'label' => 'Keluarkan Stok Dari', 'type' => 'select', 'options' => ['Gudang Kuta (Barang Jadi)', 'Toko Display Kasir'], 'required' => true],
                    ['name' => 'metode_bayar', 'label' => 'Metode Pembayaran', 'type' => 'select', 'options' => ['Tunai (Cash)', 'QRIS', 'Transfer BCA', 'Transfer Mandiri', 'Kartu Debit / EDC', 'Tempo 30 Hari (Grosir)']],
                    ['name' => 'catatan', 'label' => 'Catatan Tambahan / Alamat Pengiriman', 'type' => 'textarea', 'placeholder' => 'Alamat ekspedisi atau catatan khusus...'],
                ];
                break;

                // 7. INTEGRASI MARKETPLACE
            case 'marketplace/pesanan':
            case 'marketplace/index':
            case 'marketplace/produk':
                $config['headerIcon'] = 'bi-shop';
                $config['formTitle'] = 'Sinkronisasi Manual Marketplace (Shopee & TikTok Shop)';
                $config['summaryCards'] = [
                    ['title' => 'Pesanan Shopee Hari Ini', 'value' => '28 Order', 'icon' => 'bi-bag', 'color' => 'warning'],
                    ['title' => 'Pesanan TikTok Shop', 'value' => '18 Order', 'icon' => 'bi-tiktok', 'color' => 'dark'],
                    ['title' => 'Total Omzet Marketplace', 'value' => 'Rp 14.820.000', 'icon' => 'bi-cash-coin', 'color' => 'success'],
                    ['title' => 'Status API Koneksi', 'value' => 'Terhubung Aktif', 'icon' => 'bi-wifi', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'order_id', 'label' => 'Order ID Marketplace'],
                    ['key' => 'platform', 'label' => 'Platform'],
                    ['key' => 'buyer', 'label' => 'Username Pembeli'],
                    ['key' => 'sku_item', 'label' => 'Produk Dipesan'],
                    ['key' => 'amount', 'label' => 'Nominal'],
                    ['key' => 'stock_sync', 'label' => 'Sinkron Stok'],
                    ['key' => 'order_status', 'label' => 'Status Pesanan'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'order_id' => '241024SHP98201', 'platform' => 'Shopee', 'buyer' => 'ayusavitri_bali', 'sku_item' => 'Kebaya Bordir Bali (M)', 'amount' => 'Rp 385.000', 'stock_sync' => 'Otomatis (-1)', 'order_status' => 'Perlu Dikirim', 'badge' => 'warning'],
                    ['id' => 2, 'order_id' => 'TT-89201928019', 'platform' => 'TikTok Shop', 'buyer' => 'putra_denpasar', 'sku_item' => 'Kamen Endek Tenun', 'amount' => 'Rp 450.000', 'stock_sync' => 'Otomatis (-1)', 'order_status' => 'Dalam Pengiriman', 'badge' => 'primary'],
                    ['id' => 3, 'order_id' => '241023SHP11029', 'platform' => 'Shopee', 'buyer' => 'kartika_fashion', 'sku_item' => 'Selendang Obi Songket (3)', 'amount' => 'Rp 255.000', 'stock_sync' => 'Otomatis (-3)', 'order_status' => 'Pesanan Selesai', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'platform', 'label' => 'Pilih Platform Marketplace', 'type' => 'select', 'options' => ['Shopee Indonesia', 'TikTok Shop Indonesia', 'Tokopedia'], 'required' => true],
                    ['name' => 'aksi_sync', 'label' => 'Tindakan Sinkronisasi', 'type' => 'select', 'options' => ['Tarik Pesanan Baru (Fetch Orders)', 'Perbarui Stok ERP ke Marketplace (Push Stock)', 'Sinkronisasi Status Resi Pengiriman']],
                    ['name' => 'periode', 'label' => 'Periode Penarikan Data', 'type' => 'select', 'options' => ['Hari Ini', '3 Hari Terakhir', '7 Hari Terakhir']],
                ];
                break;

                // 9. FINANCE & ACCOUNTING
            case 'finance/arus-kas':
            case 'finance/index':
            case 'finance/laba-rugi':
                $config['headerIcon'] = 'bi-cash-coin';
                $config['formTitle'] = 'Form Pencatatan Kas Masuk / Kas Keluar';
                $config['summaryCards'] = [
                    ['title' => 'Saldo Kas & Bank', 'value' => 'Rp 182.400.000', 'icon' => 'bi-bank', 'color' => 'primary'],
                    ['title' => 'Pemasukan Bulan Ini', 'value' => 'Rp 142.850.000', 'icon' => 'bi-arrow-down-left', 'color' => 'success'],
                    ['title' => 'Pengeluaran Operasional', 'value' => 'Rp 45.200.000', 'icon' => 'bi-arrow-up-right', 'color' => 'danger'],
                    ['title' => 'Estimasi Laba Bersih', 'value' => 'Rp 29.250.000', 'icon' => 'bi-graph-up-arrow', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'trx_code', 'label' => 'No. Referensi'],
                    ['key' => 'date', 'label' => 'Tanggal'],
                    ['key' => 'type', 'label' => 'Jenis'],
                    ['key' => 'category', 'label' => 'Kategori Akun'],
                    ['key' => 'amount', 'label' => 'Nominal (Rp)'],
                    ['key' => 'account', 'label' => 'Akun Kas / Bank'],
                    ['key' => 'note', 'label' => 'Keterangan'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'trx_code' => 'KM-202610-101', 'date' => '24 Okt 2026', 'type' => 'Pemasukan', 'category' => 'Pendapatan Kasir POS', 'amount' => 'Rp 4.520.000', 'account' => 'Kas Toko Utama', 'note' => 'Setoran transaksi kasir harian', 'badge' => 'success'],
                    ['id' => 2, 'trx_code' => 'KK-202610-045', 'date' => '23 Okt 2026', 'type' => 'Pengeluaran', 'category' => 'Biaya Listrik & Air Workshop', 'amount' => 'Rp 1.850.000', 'account' => 'Bank BCA Operasional', 'note' => 'Tagihan PLN & PDAM periode Okt', 'badge' => 'danger'],
                    ['id' => 3, 'trx_code' => 'KK-202610-046', 'date' => '22 Okt 2026', 'type' => 'Pengeluaran', 'category' => 'Pembayaran PO Kain Supplier', 'amount' => 'Rp 13.000.000', 'account' => 'Bank BCA Giro', 'note' => 'Pelunasan PO-202610-041 CV Tekstil', 'badge' => 'danger'],
                ];
                $config['formFields'] = [
                    ['name' => 'jenis_kas', 'label' => 'Jenis Transaksi Kas', 'type' => 'select', 'options' => ['Pemasukan (Uang Masuk)', 'Pengeluaran (Uang Keluar)', 'Transfer Kas Antar-Rekening'], 'required' => true],
                    ['name' => 'tanggal', 'label' => 'Tanggal Transaksi', 'type' => 'date', 'required' => true],
                    ['name' => 'kategori_akun', 'label' => 'Kategori Akun Keuangan', 'type' => 'select', 'options' => ['Pendapatan Penjualan', 'Pembelian Bahan Baku', 'Gaji & Upah Penjahit / Karyawan', 'Biaya Listrik, Air & Internet', 'Biaya Sewa Tempat / Gedung', 'Biaya Packaging & Kirim', 'Biaya Lain-lain'], 'required' => true],
                    ['name' => 'akun_kas', 'label' => 'Sumber / Tujuan Rekening', 'type' => 'select', 'options' => ['Kas Kecil Toko (Cash)', 'Bank BCA Operasional (Giro)', 'Bank Mandiri Usaha', 'QRIS Merchant Account'], 'required' => true],
                    ['name' => 'nominal', 'label' => 'Nominal Rupiah (Rp)', 'type' => 'number', 'placeholder' => '0', 'required' => true],
                    ['name' => 'keterangan', 'label' => 'Deskripsi / Bukti Transaksi', 'type' => 'textarea', 'placeholder' => 'Keterangan pembayaran, nomor nota fisik, dll.'],
                ];
                break;

                // 10. LAPORAN & EKSPOR
            case 'laporan/transaksi':
            case 'laporan/index':
            case 'laporan/stok':
            case 'laporan/keuangan':
            case 'laporan/ekspor':
                $config['headerIcon'] = 'bi-file-earmark-bar-graph';
                $config['formTitle'] = 'Filter & Parameter Ekspor Data ke Excel';
                $config['summaryCards'] = [
                    ['title' => 'Laporan Siap Ekspor', 'value' => '10 Format', 'icon' => 'bi-file-earmark-excel', 'color' => 'success'],
                    ['title' => 'Data Terupdate', 'value' => 'Real-time', 'icon' => 'bi-arrow-clockwise', 'color' => 'info'],
                    ['title' => 'Periode Default', 'value' => 'Bulan Berjalan', 'icon' => 'bi-calendar-range', 'color' => 'primary'],
                    ['title' => 'Format Output', 'value' => '.XLSX / .PDF', 'icon' => 'bi-printer', 'color' => 'warning'],
                ];
                $config['columns'] = [
                    ['key' => 'report_name', 'label' => 'Judul Laporan'],
                    ['key' => 'category', 'label' => 'Kategori Modul'],
                    ['key' => 'record_count', 'label' => 'Jumlah Record'],
                    ['key' => 'period', 'label' => 'Periode Default'],
                    ['key' => 'action', 'label' => 'Opsi Unduh'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'report_name' => 'Laporan Rekap Penjualan & Omzet', 'category' => 'Penjualan & POS', 'record_count' => '428 Transaksi', 'period' => '01 Okt - 24 Okt 2026', 'action' => 'Ekspor Excel', 'badge' => 'success'],
                    ['id' => 2, 'report_name' => 'Laporan Rekap Pembelian & Utang Supplier', 'category' => 'Purchasing', 'record_count' => '24 PO', 'period' => '01 Okt - 24 Okt 2026', 'action' => 'Ekspor Excel', 'badge' => 'success'],
                    ['id' => 3, 'report_name' => 'Laporan Saldo Stok & Mutasi Multi-Gudang', 'category' => 'Inventori', 'record_count' => '142 SKU', 'period' => 'Posisi Saat Ini', 'action' => 'Ekspor Excel', 'badge' => 'success'],
                    ['id' => 4, 'report_name' => 'Laporan Hasil Produksi & Kalkulasi HPP', 'category' => 'Produksi & BOM', 'record_count' => '12 Work Order', 'period' => '01 Okt - 24 Okt 2026', 'action' => 'Ekspor Excel', 'badge' => 'success'],
                    ['id' => 5, 'report_name' => 'Laporan Laba Rugi & Arus Kas Komprehensif', 'category' => 'Finance & Accounting', 'record_count' => '312 Jurnal', 'period' => 'Oktober 2026', 'action' => 'Ekspor Excel', 'badge' => 'success'],
                ];
                $config['formFields'] = [
                    ['name' => 'pilih_laporan', 'label' => 'Pilih Format Laporan', 'type' => 'select', 'options' => ['Laporan Penjualan & Omzet', 'Laporan Pembelian & PO', 'Laporan Kartu Stok per Gudang', 'Laporan Produksi & Efisiensi Bahan', 'Laporan Laba Rugi Transaksi'], 'required' => true],
                    ['name' => 'tgl_mulai', 'label' => 'Tanggal Awal', 'type' => 'date', 'required' => true],
                    ['name' => 'tgl_akhir', 'label' => 'Tanggal Akhir', 'type' => 'date', 'required' => true],
                    ['name' => 'format_file', 'label' => 'Format Dokumen', 'type' => 'select', 'options' => ['Microsoft Excel (.xlsx)', 'Dokumen PDF Cetak (.pdf)', 'Comma Separated Values (.csv)']],
                ];
                break;

                // DEFAULT GENERIC FALLBACK
            default:
                $config['headerIcon'] = 'bi-folder2-open';
                $config['formTitle'] = 'Form Input '.$subTitle;
                $config['summaryCards'] = [
                    ['title' => 'Total Record Data', 'value' => '64 Data', 'icon' => 'bi-folder', 'color' => 'primary'],
                    ['title' => 'Data Aktif', 'value' => '58 Data', 'icon' => 'bi-check2', 'color' => 'success'],
                    ['title' => 'Perlu Perhatian', 'value' => '6 Data', 'icon' => 'bi-exclamation-circle', 'color' => 'warning'],
                    ['title' => 'Status Sinkronisasi', 'value' => 'Terkoneksi', 'icon' => 'bi-hdd-network', 'color' => 'info'],
                ];
                $config['columns'] = [
                    ['key' => 'code', 'label' => 'Kode / ID'],
                    ['key' => 'name', 'label' => 'Nama / Deskripsi'],
                    ['key' => 'category', 'label' => 'Kategori'],
                    ['key' => 'date', 'label' => 'Tanggal Input'],
                    ['key' => 'status', 'label' => 'Status'],
                ];
                $config['rows'] = [
                    ['id' => 1, 'code' => strtoupper(substr($module, 0, 3)).'-001', 'name' => 'Contoh Data '.$subTitle.' Utama 1', 'category' => $moduleTitle, 'date' => '24 Okt 2026', 'status' => 'Aktif', 'badge' => 'success'],
                    ['id' => 2, 'code' => strtoupper(substr($module, 0, 3)).'-002', 'name' => 'Contoh Data '.$subTitle.' Pendukung 2', 'category' => $moduleTitle, 'date' => '23 Okt 2026', 'status' => 'Aktif', 'badge' => 'success'],
                    ['id' => 3, 'code' => strtoupper(substr($module, 0, 3)).'-003', 'name' => 'Contoh Data '.$subTitle.' Khusus 3', 'category' => $moduleTitle, 'date' => '22 Okt 2026', 'status' => 'Review', 'badge' => 'warning'],
                ];
                $config['formFields'] = [
                    ['name' => 'kode', 'label' => 'Kode / Nomor Referensi', 'type' => 'text', 'placeholder' => 'Otomatis atau manual', 'required' => true],
                    ['name' => 'nama', 'label' => 'Nama / Judul Data', 'type' => 'text', 'placeholder' => 'Masukkan nama lengkap', 'required' => true],
                    ['name' => 'kategori', 'label' => 'Kategori / Kelompok', 'type' => 'text', 'placeholder' => 'Kategori terkait'],
                    ['name' => 'keterangan', 'label' => 'Keterangan Lengkap', 'type' => 'textarea', 'placeholder' => 'Detail data atau catatan tambahan...'],
                ];
                break;
        }

        return $config;
    }
}
