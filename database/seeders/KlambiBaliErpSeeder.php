<?php

namespace Database\Seeders;

use App\Models\BillOfMaterial;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Tailor;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KlambiBaliErpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedUsers();
            $this->seedWarehouses();
            $this->seedUnitsAndCategories();
            $this->seedPartners();
            $this->seedProductsAndVariants();
            $this->seedChartOfAccounts();
            $this->seedDemoScenarioAndKartuStok();
        });
    }

    private function seedUsers(): void
    {
        User::firstOrCreate(['email' => 'admin@klambibali.com'], [
            'name' => 'Administrator KlambiBali',
            'password' => bcrypt('password'),
        ]);

        User::firstOrCreate(['email' => 'kasir@klambibali.com'], [
            'name' => 'Ni Wayan Sukerti (Kasir POS)',
            'password' => bcrypt('password'),
        ]);

        User::firstOrCreate(['email' => 'gudang@klambibali.com'], [
            'name' => 'I Ketut Sudira (Kepala Gudang)',
            'password' => bcrypt('password'),
        ]);

        User::firstOrCreate(['email' => 'qc@klambibali.com'], [
            'name' => 'Ni Kadek Ayu (Supervisor QC)',
            'password' => bcrypt('password'),
        ]);

        User::firstOrCreate(['email' => 'finance@klambibali.com'], [
            'name' => 'I Made Dharmawan (Finance & Accounting)',
            'password' => bcrypt('password'),
        ]);
    }

    private function seedWarehouses(): void
    {
        $warehouses = [
            ['code' => 'GD-BB-01', 'name' => 'Gudang Bahan Baku (Kain & Trimming)', 'type' => 'raw_material', 'location' => 'Workshop Pusat Sanur', 'pic_name' => 'I Ketut Sudira'],
            ['code' => 'GD-WIP-02', 'name' => 'Gudang Penjahit (Raw Material & WIP)', 'type' => 'wip_penjahit', 'location' => 'Vendor Penjahit Wayan Artana', 'pic_name' => 'I Wayan Artana'],
            ['code' => 'GD-TRN-03', 'name' => 'Gudang Transit (Menunggu QC - Belum Siap Jual)', 'type' => 'transit_qc', 'location' => 'Ruang Karantina & QC Sanur', 'pic_name' => 'Ni Kadek Ayu'],
            ['code' => 'GD-RJC-04', 'name' => 'Gudang Reject / Service (Rework Jahitan)', 'type' => 'reject_service', 'location' => 'Workshop Perbaikan Denpasar', 'pic_name' => 'Ni Kadek Ayu'],
            ['code' => 'GD-SAL-05', 'name' => 'Gudang Sale (Clearance Barang Cacat)', 'type' => 'sale_defect', 'location' => 'Outlet Sanur (Lantai 2)', 'pic_name' => 'Ni Wayan Sukerti'],
            ['code' => 'GD-UTM-06', 'name' => 'Gudang Utama (Finished Goods Lolos QC)', 'type' => 'utama_fg', 'location' => 'Gudang Induk Sanur', 'pic_name' => 'I Ketut Sudira'],
            ['code' => 'GD-LIV-07', 'name' => 'Gudang Live (Stok Sesi Live TikTok & Shopee)', 'type' => 'live_stream', 'location' => 'Studio Live Streaming Sanur', 'pic_name' => 'Host Live Team'],
            ['code' => 'GD-STR-08', 'name' => 'Gudang Store (Display Outlet Kasir POS)', 'type' => 'store_pos', 'location' => 'Butik & Toko Fisik Sanur', 'pic_name' => 'Ni Wayan Sukerti'],
        ];

        foreach ($warehouses as $wh) {
            Warehouse::firstOrCreate(['code' => $wh['code']], $wh);
        }
    }

    private function seedUnitsAndCategories(): void
    {
        $units = [
            ['code' => 'YD', 'name' => 'Yard', 'symbol' => 'yd'],
            ['code' => 'MTR', 'name' => 'Meter', 'symbol' => 'm'],
            ['code' => 'PCS', 'name' => 'Pieces', 'symbol' => 'pcs'],
            ['code' => 'ROLL', 'name' => 'Roll', 'symbol' => 'roll'],
        ];

        foreach ($units as $u) {
            Unit::firstOrCreate(['code' => $u['code']], $u);
        }

        $categories = [
            ['code' => 'CAT-KB', 'name' => 'Kebaya Bali'],
            ['code' => 'CAT-KM', 'name' => 'Kamen & Bawahan Tradisional'],
            ['code' => 'CAT-KS', 'name' => 'Kemeja Safari Pria'],
            ['code' => 'CAT-BB', 'name' => 'Bahan Baku Kain'],
            ['code' => 'CAT-ACC', 'name' => 'Aksesoris & Trimming'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['code' => $cat['code']], $cat);
        }
    }

    private function seedPartners(): void
    {
        $suppliers = [
            ['code' => 'SUP-001', 'name' => 'CV Tekstil Bali Sejahtera', 'contact_person' => 'Bpk. Hendra Gunawan', 'phone' => '081234567811', 'term_days' => 30],
            ['code' => 'SUP-002', 'name' => 'Tenun Tradisional Sidemen', 'contact_person' => 'Ibu Kadek Wati', 'phone' => '081987654322', 'term_days' => 14],
            ['code' => 'SUP-003', 'name' => 'UD Pelengkap Garmen', 'contact_person' => 'Bpk. Sugiyanto', 'phone' => '081338899033', 'term_days' => 14],
        ];

        foreach ($suppliers as $s) {
            Supplier::firstOrCreate(['code' => $s['code']], $s);
        }

        $tailors = [
            ['code' => 'TLR-001', 'name' => 'I Wayan Artana (Denpasar)', 'phone' => '081239887700', 'sewing_rate_default' => 45000, 'status' => 'aktif'],
            ['code' => 'TLR-002', 'name' => 'Tenun Sidemen Klungkung', 'phone' => '081877665544', 'sewing_rate_default' => 35000, 'status' => 'aktif'],
            ['code' => 'TLR-003', 'name' => 'Sukawati Garment', 'phone' => '081999888777', 'sewing_rate_default' => 40000, 'status' => 'aktif'],
        ];

        foreach ($tailors as $t) {
            Tailor::firstOrCreate(['code' => $t['code']], $t);
        }

        $customers = [
            ['member_code' => 'MBR-001', 'name' => 'Ni Wayan Sukmawati', 'type' => 'retail', 'tier' => 'Platinum (Diskon 5%)', 'points' => 1250, 'phone' => '081234567890'],
            ['member_code' => 'MBR-002', 'name' => 'I Made Suardana', 'type' => 'retail', 'tier' => 'Gold (Diskon 3%)', 'points' => 480, 'phone' => '081987654321'],
            ['member_code' => 'B2B-011', 'name' => 'Butik Sanur Indah (Ibu Desak)', 'type' => 'reseller_b2b', 'tier' => 'Mitra Reseller (Tempo 14 Hari)', 'points' => 4200, 'phone' => '081338899001'],
            ['member_code' => 'ONL-042', 'name' => 'Dewi Lestari (Jakarta)', 'type' => 'marketplace', 'tier' => 'Silver (Reguler)', 'points' => 120, 'phone' => '081299887766'],
        ];

        foreach ($customers as $c) {
            Customer::firstOrCreate(['member_code' => $c['member_code']], $c);
        }
    }

    private function seedProductsAndVariants(): void
    {
        $yd = Unit::where('code', 'YD')->first();
        $mtr = Unit::where('code', 'MTR')->first();
        $pcs = Unit::where('code', 'PCS')->first();
        $catKb = Category::where('code', 'CAT-KB')->first();
        $catKm = Category::where('code', 'CAT-KM')->first();
        $catKs = Category::where('code', 'CAT-KS')->first();
        $catBb = Category::where('code', 'CAT-BB')->first();

        // Raw Material (Kain Brokat Semi Prancis)
        $kainBrokat = Product::firstOrCreate(['sku' => 'BB-KN-BRKT-01'], [
            'name' => 'Kain Brokat Semi Prancis Maroon',
            'category_id' => $catBb->id,
            'unit_id' => $yd->id,
            'type' => 'raw_material',
            'purchase_price' => 75000,
            'selling_price' => 0,
            'hpp' => 75000,
            'min_stock' => 20,
            'description' => 'Bahan baku utama kebaya bali bordir (Satuan Yard)',
        ]);

        // Raw Material (Kain Tenun Endek)
        $kainEndek = Product::firstOrCreate(['sku' => 'BB-KN-EDK-02'], [
            'name' => 'Kain Tenun Endek Sutra Hitam Emas',
            'category_id' => $catBb->id,
            'unit_id' => $mtr->id,
            'type' => 'raw_material',
            'purchase_price' => 65000,
            'selling_price' => 0,
            'hpp' => 65000,
            'min_stock' => 15,
            'description' => 'Bahan tenun khas Klungkung (Satuan Meter)',
        ]);

        // Finished Goods (Kebaya Bali Bordir Maroon - Model Demo)
        $kebayaBordir = Product::firstOrCreate(['sku' => 'KB-BRD-MRN'], [
            'name' => 'Kebaya Bali Bordir Premium Maroon',
            'model' => 'Bordir Bali Klasik',
            'category_id' => $catKb->id,
            'unit_id' => $pcs->id,
            'type' => 'finished_goods',
            'purchase_price' => 0,
            'selling_price' => 385000,
            'std_kain_consumption' => 1.56,
            'sewing_cost' => 45000,
            'accessories_cost' => 18000,
            'overhead_cost' => 5000,
            'hpp' => 185000,
            'min_stock' => 15,
            'barcode' => '899100100101',
            'description' => 'Kebaya bordir katun brokat premium dengan payet emas',
        ]);

        // Variants for Kebaya Bordir
        $variants = [
            ['sku' => 'KB-BRD-MRN-S', 'variant_name' => 'Maroon - Size S', 'color' => 'Maroon', 'size' => 'S', 'barcode' => '899100100111'],
            ['sku' => 'KB-BRD-MRN-M', 'variant_name' => 'Maroon - Size M (Demo SKU)', 'color' => 'Maroon', 'size' => 'M', 'barcode' => '899100100112'],
            ['sku' => 'KB-BRD-MRN-L', 'variant_name' => 'Maroon - Size L', 'color' => 'Maroon', 'size' => 'L', 'barcode' => '899100100113'],
            ['sku' => 'KB-BRD-MRN-XL', 'variant_name' => 'Maroon - Size XL', 'color' => 'Maroon', 'size' => 'XL', 'barcode' => '899100100114'],
        ];

        foreach ($variants as $v) {
            ProductVariant::firstOrCreate(['sku' => $v['sku']], array_merge($v, ['product_id' => $kebayaBordir->id]));
        }

        // BOM Kebaya Bordir (1.56 Yard Kain Brokat)
        BillOfMaterial::firstOrCreate(
            ['product_id' => $kebayaBordir->id, 'raw_material_id' => $kainBrokat->id],
            [
                'standard_quantity' => 1.56,
                'unit_id' => $yd->id,
                'unit_cost' => 117000,
                'notes' => '1.56 yard x Rp 75.000 = Rp 117.000',
            ]
        );

        // Finished Goods (Kamen Endek Sutra)
        $kamenEndek = Product::firstOrCreate(['sku' => 'KM-EDK-GLD'], [
            'name' => 'Kamen Endek Sutra Motif Wajik Emas',
            'model' => 'Kamen Tradisional',
            'category_id' => $catKm->id,
            'unit_id' => $pcs->id,
            'type' => 'finished_goods',
            'purchase_price' => 0,
            'selling_price' => 450000,
            'std_kain_consumption' => 2.50,
            'sewing_cost' => 35000,
            'accessories_cost' => 15000,
            'overhead_cost' => 0,
            'hpp' => 212500,
            'min_stock' => 10,
            'barcode' => '899100200101',
        ]);

        ProductVariant::firstOrCreate(['sku' => 'KM-EDK-GLD-ALL'], [
            'product_id' => $kamenEndek->id,
            'variant_name' => 'Wajik Emas - All Size',
            'color' => 'Emas',
            'size' => 'ALL',
            'barcode' => '899100200111',
        ]);

        // Finished Goods (Kemeja Safari Pria Katun)
        $safari = Product::firstOrCreate(['sku' => 'KMJ-SFR-WHT'], [
            'name' => 'Kemeja Safari Pria Katun Jepang Putih',
            'model' => 'Safari Formal',
            'category_id' => $catKs->id,
            'unit_id' => $pcs->id,
            'type' => 'finished_goods',
            'purchase_price' => 0,
            'selling_price' => 325000,
            'std_kain_consumption' => 1.80,
            'sewing_cost' => 40000,
            'accessories_cost' => 16000,
            'overhead_cost' => 0,
            'hpp' => 155000,
            'min_stock' => 10,
            'barcode' => '899100300101',
        ]);

        ProductVariant::firstOrCreate(['sku' => 'KMJ-SFR-WHT-XL'], [
            'product_id' => $safari->id,
            'variant_name' => 'Putih - Size XL',
            'color' => 'Putih',
            'size' => 'XL',
            'barcode' => '899100300114',
        ]);
    }

    private function seedChartOfAccounts(): void
    {
        $coaList = [
            // 1. ASET LANCAR
            ['code' => '1110', 'name' => 'Kas Toko / Kasir Fisik', 'category' => 'asset', 'normal_balance' => 'debit', 'is_cash_bank' => true, 'opening_balance' => 25500000],
            ['code' => '1120', 'name' => 'Bank BCA Giro Operasional', 'category' => 'asset', 'normal_balance' => 'debit', 'is_cash_bank' => true, 'opening_balance' => 112400000],
            ['code' => '1125', 'name' => 'Rekening Penampungan (Shopee & TikTok Escrow)', 'category' => 'asset', 'normal_balance' => 'debit', 'is_cash_bank' => true, 'opening_balance' => 44500000],
            ['code' => '1130', 'name' => 'Piutang Usaha (Pelanggan Reseller/B2B)', 'category' => 'asset', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 24150000],
            ['code' => '1140', 'name' => 'Persediaan Bahan Baku (Gudang Kain & Aksesoris)', 'category' => 'asset', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 58600000],
            ['code' => '1145', 'name' => 'Persediaan Barang Dalam Proses (Gudang Penjahit WIP)', 'category' => 'asset', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 42500000],
            ['code' => '1150', 'name' => 'Persediaan Barang Jadi - Gudang Utama', 'category' => 'asset', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 229400000],
            ['code' => '1151', 'name' => 'Persediaan Barang Jadi - Gudang Store POS', 'category' => 'asset', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 54575000],
            ['code' => '1152', 'name' => 'Persediaan Barang Jadi - Gudang Live Selling', 'category' => 'asset', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 51800000],
            ['code' => '1155', 'name' => 'Persediaan Barang Reject & Service (Gudang Reject)', 'category' => 'asset', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 2775000],
            ['code' => '1156', 'name' => 'Persediaan Barang Cacat (Gudang Sale Clearance)', 'category' => 'asset', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 1200000],
            ['code' => '1210', 'name' => 'Aset Tetap Mesin & Perlengkapan Outlet', 'category' => 'asset', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 45000000],
            ['code' => '1220', 'name' => 'Akumulasi Penyusutan Aset Tetap', 'category' => 'asset', 'normal_balance' => 'credit', 'is_cash_bank' => false, 'opening_balance' => 9000000],

            // 2. KEWAJIBAN LANCAR
            ['code' => '2110', 'name' => 'Hutang Dagang Supplier (Bahan Baku & Kain)', 'category' => 'liability', 'normal_balance' => 'credit', 'is_cash_bank' => false, 'opening_balance' => 18500000],
            ['code' => '2120', 'name' => 'Hutang Ongkos Jahit Penjahit', 'category' => 'liability', 'normal_balance' => 'credit', 'is_cash_bank' => false, 'opening_balance' => 5400000],
            ['code' => '2130', 'name' => 'Hutang Beban Operasional / Utilitas', 'category' => 'liability', 'normal_balance' => 'credit', 'is_cash_bank' => false, 'opening_balance' => 2200000],

            // 3. EKUITAS
            ['code' => '3100', 'name' => 'Modal Pemilik KlambiBali', 'category' => 'equity', 'normal_balance' => 'credit', 'is_cash_bank' => false, 'opening_balance' => 350000000],
            ['code' => '3200', 'name' => 'Laba Ditahan (Retained Earnings)', 'category' => 'equity', 'normal_balance' => 'credit', 'is_cash_bank' => false, 'opening_balance' => 95275000],
            ['code' => '3300', 'name' => 'Laba Periode Berjalan', 'category' => 'equity', 'normal_balance' => 'credit', 'is_cash_bank' => false, 'opening_balance' => 29250000],

            // 4. PENDAPATAN
            ['code' => '4110', 'name' => 'Pendapatan Penjualan Toko Kasir POS', 'category' => 'revenue', 'normal_balance' => 'credit', 'is_cash_bank' => false, 'opening_balance' => 29000000],
            ['code' => '4120', 'name' => 'Pendapatan Penjualan Shopee Marketplace', 'category' => 'revenue', 'normal_balance' => 'credit', 'is_cash_bank' => false, 'opening_balance' => 58500000],
            ['code' => '4130', 'name' => 'Pendapatan Penjualan TikTok Shop', 'category' => 'revenue', 'normal_balance' => 'credit', 'is_cash_bank' => false, 'opening_balance' => 39000000],
            ['code' => '4140', 'name' => 'Pendapatan Penjualan Reguler / Non-Marketplace', 'category' => 'revenue', 'normal_balance' => 'credit', 'is_cash_bank' => false, 'opening_balance' => 16500000],

            // 5. BEBAN POKOK PENJUALAN (HPP)
            ['code' => '5110', 'name' => 'HPP - Pemakaian Bahan Baku (Kain Brokat)', 'category' => 'cogs', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 37440000],
            ['code' => '5120', 'name' => 'HPP - Ongkos Jahit Maklon Penjahit', 'category' => 'cogs', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 14400000],
            ['code' => '5130', 'name' => 'HPP - Aksesoris, Furing & Trimming', 'category' => 'cogs', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 5760000],
            ['code' => '5140', 'name' => 'HPP - Overhead Pabrikasi & Finishing', 'category' => 'cogs', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 1600000],

            // 6. BEBAN OPERASIONAL
            ['code' => '6110', 'name' => 'Beban Gaji Karyawan Toko & Staff', 'category' => 'expense', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 18500000],
            ['code' => '6120', 'name' => 'Beban Sewa Outlet Butik & Workshop', 'category' => 'expense', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 6000000],
            ['code' => '6130', 'name' => 'Beban Fee Layanan & Komisi Marketplace', 'category' => 'expense', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 8850000],
            ['code' => '6140', 'name' => 'Beban Listrik, Air & Internet', 'category' => 'expense', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 2200000],
            ['code' => '6150', 'name' => 'Beban Kemasan, Polymailer & Packaging', 'category' => 'expense', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 2900000],
            ['code' => '6160', 'name' => 'Beban Biaya Service Barang Reject & Cacat', 'category' => 'expense', 'normal_balance' => 'debit', 'is_cash_bank' => false, 'opening_balance' => 1000000],
        ];

        foreach ($coaList as $c) {
            ChartOfAccount::firstOrCreate(['code' => $c['code']], $c);
        }
    }

    private function seedDemoScenarioAndKartuStok(): void
    {
        $admin = User::first();
        $gdBb = Warehouse::where('code', 'GD-BB-01')->first();
        $gdWip = Warehouse::where('code', 'GD-WIP-02')->first();
        $gdTrn = Warehouse::where('code', 'GD-TRN-03')->first();
        $gdRjc = Warehouse::where('code', 'GD-RJC-04')->first();
        $gdSal = Warehouse::where('code', 'GD-SAL-05')->first();
        $gdUtm = Warehouse::where('code', 'GD-UTM-06')->first();
        $gdLiv = Warehouse::where('code', 'GD-LIV-07')->first();
        $gdStr = Warehouse::where('code', 'GD-STR-08')->first();

        $kainBrokat = Product::where('sku', 'BB-KN-BRKT-01')->first();
        $kebaya = Product::where('sku', 'KB-BRD-MRN')->first();
        $varM = ProductVariant::where('sku', 'KB-BRD-MRN-M')->first();
        $supplier = Supplier::where('code', 'SUP-001')->first();
        $tailor = Tailor::where('code', 'TLR-001')->first();

        // -----------------------------------------------------------------
        // LANGKAH 1: Pembelian 100 Yard Kain via PO-202610-041 ke CV Tekstil Bali
        // -----------------------------------------------------------------
        $po = PurchaseOrder::firstOrCreate(['po_number' => 'PO-202610-041'], [
            'supplier_id' => $supplier->id,
            'warehouse_id' => $gdBb->id,
            'po_date' => '2026-10-18',
            'due_date' => '2026-11-17',
            'status' => 'received',
            'subtotal' => 7500000,
            'grand_total' => 7500000,
            'payment_status' => 'unpaid',
            'notes' => 'Pembelian 100 Yard Kain Brokat Semi Prancis (Demo Account)',
            'user_id' => $admin->id,
        ]);

        PurchaseOrderItem::firstOrCreate(['purchase_order_id' => $po->id, 'product_id' => $kainBrokat->id], [
            'qty_ordered' => 100,
            'qty_received' => 100,
            'unit_price' => 75000,
            'total_price' => 7500000,
        ]);

        // Kartu Stok: Masuk 100 Yard ke Gudang Bahan Baku
        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-18 09:15:00'),
            'document_number' => 'PO-202610-041',
            'reference_type' => 'PO',
            'reference_id' => $po->id,
            'product_id' => $kainBrokat->id,
            'variant_id' => null,
            'warehouse_id' => $gdBb->id,
            'type' => 'IN',
            'qty_in' => 100,
            'qty_out' => 0,
            'balance' => 100,
            'unit_cost' => 75000,
            'total_cost' => 7500000,
            'notes' => 'Penerimaan Pembelian 100 Yard Kain Brokat (Masuk Gudang Bahan Baku)',
            'user_id' => $admin->id,
        ]);

        // Jurnal Otomatis Langkah 1: Persediaan Bahan Baku (D) vs Hutang Dagang Supplier (K)
        $accPersediaanBb = ChartOfAccount::where('code', '1140')->first();
        $accHutangSupplier = ChartOfAccount::where('code', '2110')->first();
        $this->createJournal(
            'JV-202610-001',
            '2026-10-18',
            'PO',
            'PO-202610-041',
            'Pembelian 100 Yard Kain Brokat Semi Prancis (PO-041)',
            [
                ['account_id' => $accPersediaanBb->id, 'debit' => 7500000, 'credit' => 0, 'memo' => 'Persediaan Bahan Baku bertambah 100 yard'],
                ['account_id' => $accHutangSupplier->id, 'debit' => 0, 'credit' => 7500000, 'memo' => 'Hutang Dagang ke CV Tekstil Bali'],
            ],
            $admin->id
        );

        // -----------------------------------------------------------------
        // LANGKAH 2: Pengeluaran 100 Yard Kain ke Penjahit (SPK-202610-001)
        // -----------------------------------------------------------------
        $wo = WorkOrder::firstOrCreate(['wo_number' => 'WO-202610-001'], [
            'product_id' => $kebaya->id,
            'tailor_id' => $tailor->id,
            'target_qty' => 60,
            'finished_qty' => 60,
            'status' => 'qc_inspected',
            'start_date' => '2026-10-20',
            'finish_date' => '2026-10-22',
            'sewing_cost_per_pcs' => 45000,
            'notes' => 'Produksi 60 pcs Kebaya Bali Bordir Maroon M',
            'user_id' => $admin->id,
        ]);

        // Kartu Stok: Keluar 100 Yard dari Gudang Bahan Baku (Saldo = 0)
        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-20 08:30:00'),
            'document_number' => 'SPK-202610-001',
            'reference_type' => 'SPK',
            'reference_id' => $wo->id,
            'product_id' => $kainBrokat->id,
            'variant_id' => null,
            'warehouse_id' => $gdBb->id,
            'type' => 'OUT',
            'qty_in' => 0,
            'qty_out' => 100,
            'balance' => 0,
            'unit_cost' => 75000,
            'total_cost' => 7500000,
            'notes' => 'Pengeluaran 100 yard kain brokat ke Penjahit Wayan Artana',
            'user_id' => $admin->id,
        ]);

        // Kartu Stok: Masuk 100 Yard ke Gudang Penjahit (WIP)
        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-20 09:30:00'),
            'document_number' => 'SPK-202610-001',
            'reference_type' => 'SPK',
            'reference_id' => $wo->id,
            'product_id' => $kainBrokat->id,
            'variant_id' => null,
            'warehouse_id' => $gdWip->id,
            'type' => 'IN',
            'qty_in' => 100,
            'qty_out' => 0,
            'balance' => 100,
            'unit_cost' => 75000,
            'total_cost' => 7500000,
            'notes' => 'Penerimaan alokasi kain di Gudang Penjahit (WIP)',
            'user_id' => $admin->id,
        ]);

        // Jurnal Otomatis Langkah 2: Persediaan WIP Penjahit (D) vs Persediaan Bahan Baku (K)
        $accPersediaanWip = ChartOfAccount::where('code', '1145')->first();
        $this->createJournal(
            'JV-202610-002',
            '2026-10-20',
            'SPK',
            'SPK-202610-001',
            'Kirim 100 yard kain ke Gudang Penjahit Wayan (SPK-001)',
            [
                ['account_id' => $accPersediaanWip->id, 'debit' => 7500000, 'credit' => 0, 'memo' => 'Persediaan WIP di Penjahit bertambah'],
                ['account_id' => $accPersediaanBb->id, 'debit' => 0, 'credit' => 7500000, 'memo' => 'Persediaan Bahan Baku berkurang'],
            ],
            $admin->id
        );

        // -----------------------------------------------------------------
        // LANGKAH 3 & 4: Produksi 60 Kebaya (Standar 1,56 Yard), Konsumsi 93,60 Yd, Sisa 5,80 Yd
        // -----------------------------------------------------------------
        // Kartu Stok Kain di Penjahit: Konsumsi standar 93,60 yard + 0,60 yard waste = 94,20 yard keluar.
        // Sisa fisik aktual di Gudang Penjahit = 5,80 Yard.
        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-22 14:00:00'),
            'document_number' => 'WO-202610-001',
            'reference_type' => 'WO',
            'reference_id' => $wo->id,
            'product_id' => $kainBrokat->id,
            'variant_id' => null,
            'warehouse_id' => $gdWip->id,
            'type' => 'OUT',
            'qty_in' => 0,
            'qty_out' => 94.20,
            'balance' => 5.80, // Sisa Aktual Fisik di Gudang Penjahit sesuai Bab 4 PDF
            'unit_cost' => 75000,
            'total_cost' => 7065000,
            'notes' => 'Pemakaian jahit 60 kebaya (std 93,60 yd + waste 0,60 yd). Sisa fisik 5,80 yard',
            'user_id' => $admin->id,
        ]);

        // -----------------------------------------------------------------
        // LANGKAH 5 & 6: Masuk 60 Pcs Kebaya ke Gudang Transit QC (@ HPP Rp 185.000)
        // -----------------------------------------------------------------
        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-22 15:30:00'),
            'document_number' => 'PRD-202610-001',
            'reference_type' => 'PRD',
            'reference_id' => $wo->id,
            'product_id' => $kebaya->id,
            'variant_id' => $varM->id,
            'warehouse_id' => $gdTrn->id,
            'type' => 'IN',
            'qty_in' => 60,
            'qty_out' => 0,
            'balance' => 60,
            'unit_cost' => 185000,
            'total_cost' => 11100000,
            'notes' => 'Hasil jahit 60 pcs masuk Gudang Transit (Menunggu QC - Belum Siap Jual)',
            'user_id' => $admin->id,
        ]);

        // -----------------------------------------------------------------
        // LANGKAH 7 & 8: Inspeksi QC & Pemisahan 3 Gudang: 55 Lolos Barcode, 3 Reject, 2 Cacat
        // -----------------------------------------------------------------
        // Keluar 60 pcs dari Gudang Transit (Saldo Transit = 0)
        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-23 10:00:00'),
            'document_number' => 'QC-202610-001',
            'reference_type' => 'QC',
            'reference_id' => $wo->id,
            'product_id' => $kebaya->id,
            'variant_id' => $varM->id,
            'warehouse_id' => $gdTrn->id,
            'type' => 'OUT',
            'qty_in' => 0,
            'qty_out' => 60,
            'balance' => 0,
            'unit_cost' => 185000,
            'total_cost' => 11100000,
            'notes' => 'Inspeksi QC selesai, dialokasikan ke 3 gudang',
            'user_id' => $admin->id,
        ]);

        // Masuk 55 pcs ke Gudang Utama FG (Diterbitkan Barcode)
        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-23 10:15:00'),
            'document_number' => 'QC-202610-001',
            'reference_type' => 'QC',
            'reference_id' => $wo->id,
            'product_id' => $kebaya->id,
            'variant_id' => $varM->id,
            'warehouse_id' => $gdUtm->id,
            'type' => 'IN',
            'qty_in' => 55,
            'qty_out' => 0,
            'balance' => 55,
            'unit_cost' => 185000,
            'total_cost' => 10175000,
            'notes' => '55 pcs Lolos QC, terbit Barcode SKU masuk Gudang Utama FG',
            'user_id' => $admin->id,
        ]);

        // Masuk 3 pcs ke Gudang Reject / Service
        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-23 10:15:00'),
            'document_number' => 'QC-202610-001',
            'reference_type' => 'QC',
            'reference_id' => $wo->id,
            'product_id' => $kebaya->id,
            'variant_id' => $varM->id,
            'warehouse_id' => $gdRjc->id,
            'type' => 'IN',
            'qty_in' => 3,
            'qty_out' => 0,
            'balance' => 3,
            'unit_cost' => 185000,
            'total_cost' => 555000,
            'notes' => '3 pcs Jahitan kendor masuk Gudang Reject untuk service',
            'user_id' => $admin->id,
        ]);

        // Masuk 2 pcs ke Gudang Sale (Barang Cacat / Clearance)
        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-23 10:15:00'),
            'document_number' => 'QC-202610-001',
            'reference_type' => 'QC',
            'reference_id' => $wo->id,
            'product_id' => $kebaya->id,
            'variant_id' => $varM->id,
            'warehouse_id' => $gdSal->id,
            'type' => 'IN',
            'qty_in' => 2,
            'qty_out' => 0,
            'balance' => 2,
            'unit_cost' => 185000,
            'total_cost' => 370000,
            'notes' => '2 pcs Cacat motif masuk Gudang Sale (Jual berlabel cacat)',
            'user_id' => $admin->id,
        ]);

        // Jurnal Otomatis Langkah 7: Pengakuan Barang Jadi FG (1150), Reject (1155), Sale (1156) vs WIP & Ongkos Jahit
        $accFgUtama = ChartOfAccount::where('code', '1150')->first();
        $accReject = ChartOfAccount::where('code', '1155')->first();
        $accSale = ChartOfAccount::where('code', '1156')->first();
        $accHutangJahit = ChartOfAccount::where('code', '2120')->first();
        $this->createJournal(
            'JV-202610-003',
            '2026-10-23',
            'QC',
            'QC-202610-001',
            'Penyelesaian 60 kebaya lolos QC dan pembagian gudang',
            [
                ['account_id' => $accFgUtama->id, 'debit' => 10175000, 'credit' => 0, 'memo' => '55 pcs Lolos QC ke Gudang Utama FG'],
                ['account_id' => $accReject->id, 'debit' => 555000, 'credit' => 0, 'memo' => '3 pcs Rework ke Gudang Reject'],
                ['account_id' => $accSale->id, 'debit' => 370000, 'credit' => 0, 'memo' => '2 pcs Cacat ke Gudang Sale'],
                ['account_id' => $accPersediaanWip->id, 'debit' => 0, 'credit' => 7065000, 'memo' => 'Kredit pemakaian kain di WIP'],
                ['account_id' => $accHutangJahit->id, 'debit' => 0, 'credit' => 4035000, 'memo' => 'Hutang ongkos jahit & aksesoris batch'],
            ],
            $admin->id
        );

        // -----------------------------------------------------------------
        // LANGKAH 9: Transfer Multi-Gudang (20 pcs ke Gudang Live, 30 pcs ke Gudang Store POS)
        // -----------------------------------------------------------------
        // Transfer 1: 20 pcs ke Gudang Live
        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-23 14:00:00'),
            'document_number' => 'TRF-202610-014',
            'reference_type' => 'TRF',
            'product_id' => $kebaya->id,
            'variant_id' => $varM->id,
            'warehouse_id' => $gdUtm->id,
            'type' => 'OUT',
            'qty_in' => 0,
            'qty_out' => 20,
            'balance' => 35, // Dari 55 pcs sisa 35 pcs
            'unit_cost' => 185000,
            'total_cost' => 3700000,
            'notes' => 'Transfer 20 pcs ke Gudang Live TikTok/Shopee',
            'user_id' => $admin->id,
        ]);

        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-23 14:10:00'),
            'document_number' => 'TRF-202610-014',
            'reference_type' => 'TRF',
            'product_id' => $kebaya->id,
            'variant_id' => $varM->id,
            'warehouse_id' => $gdLiv->id,
            'type' => 'IN',
            'qty_in' => 20,
            'qty_out' => 0,
            'balance' => 20,
            'unit_cost' => 185000,
            'total_cost' => 3700000,
            'notes' => 'Diterima di Gudang Live (Stok siap live selling)',
            'user_id' => $admin->id,
        ]);

        // Transfer 2: 30 pcs ke Gudang Store POS (Outlet Toko)
        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-23 15:00:00'),
            'document_number' => 'TRF-202610-015',
            'reference_type' => 'TRF',
            'product_id' => $kebaya->id,
            'variant_id' => $varM->id,
            'warehouse_id' => $gdUtm->id,
            'type' => 'OUT',
            'qty_in' => 0,
            'qty_out' => 30,
            'balance' => 5, // Dari 35 pcs sisa 5 pcs di Gudang Utama
            'unit_cost' => 185000,
            'total_cost' => 5550000,
            'notes' => 'Transfer 30 pcs ke Gudang Store (Outlet Toko Kasir POS)',
            'user_id' => $admin->id,
        ]);

        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-23 15:15:00'),
            'document_number' => 'TRF-202610-015',
            'reference_type' => 'TRF',
            'product_id' => $kebaya->id,
            'variant_id' => $varM->id,
            'warehouse_id' => $gdStr->id,
            'type' => 'IN',
            'qty_in' => 30,
            'qty_out' => 0,
            'balance' => 30,
            'unit_cost' => 185000,
            'total_cost' => 5550000,
            'notes' => 'Diterima di Gudang Store POS (Display toko outlet)',
            'user_id' => $admin->id,
        ]);

        // -----------------------------------------------------------------
        // LANGKAH 10 & 11: Penjualan POS Kasir & Pemotongan Stok Otomatis
        // -----------------------------------------------------------------
        // Kasir POS: Penjualan 1 pcs KB-BRD-MRN-M via Scan Barcode
        StockMovement::create([
            'movement_date' => Carbon::parse('2026-10-24 16:45:00'),
            'document_number' => 'POS-202610-001',
            'reference_type' => 'POS',
            'product_id' => $kebaya->id,
            'variant_id' => $varM->id,
            'warehouse_id' => $gdStr->id,
            'type' => 'OUT',
            'qty_in' => 0,
            'qty_out' => 1,
            'balance' => 29, // Dari 30 pcs terpotong 1 pcs menjadi 29 pcs di Gudang Store POS
            'unit_cost' => 185000,
            'total_cost' => 185000,
            'notes' => 'Penjualan Kasir POS Scan Barcode Member Ni Wayan Sukmawati',
            'user_id' => $admin->id,
        ]);

        // Jurnal Otomatis Langkah 10 & 11:
        // Kas Toko (D) Rp 385.000 vs Pendapatan Penjualan POS (K) Rp 385.000
        // Beban HPP (D) Rp 185.000 vs Persediaan Gudang Store POS (K) Rp 185.000
        $accKasToko = ChartOfAccount::where('code', '1110')->first();
        $accPenjualanPos = ChartOfAccount::where('code', '4110')->first();
        $accHpp = ChartOfAccount::where('code', '5110')->first();
        $accPersediaanStore = ChartOfAccount::where('code', '1151')->first();

        $this->createJournal(
            'JV-202610-004',
            '2026-10-24',
            'POS',
            'POS-202610-001',
            'Penjualan Kasir POS Kebaya Bali Bordir Maroon M',
            [
                ['account_id' => $accKasToko->id, 'debit' => 385000, 'credit' => 0, 'memo' => 'Penerimaan Kasir Kas Tunai'],
                ['account_id' => $accPenjualanPos->id, 'debit' => 0, 'credit' => 385000, 'memo' => 'Pendapatan Penjualan Toko Fisik'],
                ['account_id' => $accHpp->id, 'debit' => 185000, 'credit' => 0, 'memo' => 'Pengakuan HPP Barang Terjual'],
                ['account_id' => $accPersediaanStore->id, 'debit' => 0, 'credit' => 185000, 'memo' => 'Pengurangan Persediaan Gudang Store POS'],
            ],
            $admin->id
        );

        // -----------------------------------------------------------------
        // SYNC SALDO AKHIR KE TABEL `stocks`
        // -----------------------------------------------------------------
        $this->syncCurrentStocks();
    }

    private function createJournal(string $number, string $date, string $refType, string $refNumber, string $desc, array $lines, int $userId): void
    {
        $totalDebit = collect($lines)->sum('debit');
        $totalCredit = collect($lines)->sum('credit');

        $entry = JournalEntry::create([
            'entry_number' => $number,
            'entry_date' => $date,
            'reference_type' => $refType,
            'reference_number' => $refNumber,
            'description' => $desc,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'is_posted' => true,
            'user_id' => $userId,
        ]);

        foreach ($lines as $l) {
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $l['account_id'],
                'debit' => $l['debit'],
                'credit' => $l['credit'],
                'memo' => $l['memo'] ?? null,
            ]);
        }
    }

    private function syncCurrentStocks(): void
    {
        // Hitung saldo akhir per product, variant, dan warehouse dari movement log
        $balances = StockMovement::select('product_id', 'variant_id', 'warehouse_id')
            ->selectRaw('MAX(id) as last_movement_id')
            ->groupBy('product_id', 'variant_id', 'warehouse_id')
            ->get();

        foreach ($balances as $b) {
            $last = StockMovement::find($b->last_movement_id);
            if ($last) {
                Stock::updateOrCreate(
                    [
                        'product_id' => $last->product_id,
                        'variant_id' => $last->variant_id,
                        'warehouse_id' => $last->warehouse_id,
                    ],
                    [
                        'qty' => $last->balance,
                        'hpp' => $last->unit_cost,
                    ]
                );
            }
        }
    }
}
