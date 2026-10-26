<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Tailor;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoPipelineSeeder extends Seeder
{
    /**
     * Seed active pipeline records across all ERP stages so dropdowns and forms
     * have live data ready for testing (Purchasing GRN, Produksi SPK, Rekonsiliasi, and QC).
     */
    public function run(): void
    {
        $admin = User::first();
        $gdBb = Warehouse::where('code', 'GD-BB-01')->first();
        $gdWip = Warehouse::where('code', 'GD-WIP-02')->first();
        $gdTransit = Warehouse::where('code', 'GD-TRN-03')->first();

        $kainBrokat = Product::where('sku', 'BB-KN-BRKT-01')->first();
        $kainEndek = Product::where('sku', 'BB-KN-EDK-02')->first();
        $kebaya = Product::where('sku', 'KB-BRD-MRN')->first();
        $kamen = Product::where('sku', 'KM-EDK-GLD')->first();
        $safari = Product::where('sku', 'KMJ-SFR-WHT')->first();

        $tailorWayan = Tailor::find(1) ?? Tailor::first();
        $tailorSidemen = Tailor::find(2) ?? Tailor::first();
        $supplierTekstil = Supplier::find(1) ?? Supplier::first();
        $supplierSidemen = Supplier::find(2) ?? Supplier::first();

        // 1. TAMBAH STOK BAHAN BAKU DI GUDANG BAHAN BAKU (GD-BB-01)
        if ($kainBrokat && $gdBb) {
            $stockBrokat = Stock::firstOrCreate(
                ['product_id' => $kainBrokat->id, 'warehouse_id' => $gdBb->id],
                ['qty' => 0]
            );
            $stockBrokat->update(['qty' => 350.00]);
        }

        if ($kainEndek && $gdBb) {
            $stockEndek = Stock::firstOrCreate(
                ['product_id' => $kainEndek->id, 'warehouse_id' => $gdBb->id],
                ['qty' => 0]
            );
            $stockEndek->update(['qty' => 250.00]);
        }

        // 2. WORK ORDERS AKTIF (UNTUK SPK BAHAN & REKONSILIASI)
        // WO-202610-002: Status DRAFT (Siap diterbitkan SPK Pengeluaran Bahan)
        if ($kamen && $tailorSidemen) {
            WorkOrder::firstOrCreate(['wo_number' => 'WO-202610-002'], [
                'product_id' => $kamen->id,
                'tailor_id' => $tailorSidemen->id,
                'target_qty' => 50,
                'finished_qty' => 0,
                'status' => 'draft',
                'start_date' => Carbon::now()->toDateString(),
                'due_date' => Carbon::now()->addDays(10)->toDateString(),
                'sewing_cost_per_pcs' => 35000,
                'notes' => 'Batch 50 pcs Kamen Endek Sutra Motif Wajik Emas',
                'user_id' => $admin?->id,
            ]);
        }

        // WO-202610-003: Status in_production (Bahan sudah keluar, sedang jahit, siap Rekonsiliasi)
        if ($safari && $tailorWayan) {
            WorkOrder::firstOrCreate(['wo_number' => 'WO-202610-003'], [
                'product_id' => $safari->id,
                'tailor_id' => $tailorWayan->id,
                'target_qty' => 40,
                'finished_qty' => 0,
                'status' => 'in_production',
                'start_date' => Carbon::now()->subDays(3)->toDateString(),
                'due_date' => Carbon::now()->addDays(7)->toDateString(),
                'sewing_cost_per_pcs' => 50000,
                'notes' => 'Batch 40 pcs Kemeja Safari Putih Katun Jepang',
                'user_id' => $admin?->id,
            ]);
        }

        // 3. PENDING PURCHASE ORDER (UNTUK PENERIMAAN BARANG / GRN)
        if ($supplierTekstil && $kainBrokat) {
            $unit = Unit::first();
            $pendingPo = PurchaseOrder::firstOrCreate(['po_number' => 'PO-202610-042'], [
                'supplier_id' => $supplierTekstil->id,
                'warehouse_id' => $gdBb?->id,
                'po_date' => Carbon::now()->toDateString(),
                'due_date' => Carbon::now()->addDays(5)->toDateString(),
                'status' => 'pending',
                'subtotal' => 15000000,
                'grand_total' => 15000000,
                'notes' => 'Pengadaan 200 Yard Tambahan Kain Brokat Semi Prancis',
                'user_id' => $admin?->id,
            ]);

            PurchaseOrderItem::firstOrCreate([
                'purchase_order_id' => $pendingPo->id,
                'product_id' => $kainBrokat->id,
            ], [
                'unit_id' => $unit?->id ?? 1,
                'qty_ordered' => 200.00,
                'qty_received' => 0.00,
                'unit_price' => 75000,
                'total_price' => 15000000,
            ]);
        }

        // 4. STOK TRANSIT QC (UNTUK FORMASI INSPEKSI QC)
        if ($kebaya && $gdTransit) {
            $stockTransit = Stock::firstOrCreate(
                ['product_id' => $kebaya->id, 'warehouse_id' => $gdTransit->id],
                ['qty' => 0]
            );
            $stockTransit->update(['qty' => 25.00]);
        }
    }
}
