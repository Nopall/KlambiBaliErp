<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Purchase Orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 50)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('warehouse_id')->constrained('warehouses'); // Gudang tujuan (biasanya Gudang Bahan Baku)
            $table->date('po_date');
            $table->date('due_date')->nullable();
            $table->string('status', 30)->default('ordered'); // draft, ordered, partial, received, cancelled
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0); // Biaya angkut yang dikapitalisasi ke persediaan
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('payment_status', 30)->default('unpaid'); // unpaid, partial, paid
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Purchase Order Items
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('qty_ordered', 12, 2);
            $table->decimal('qty_received', 12, 2)->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();
        });

        // 3. Goods Receipts (Penerimaan Barang / GRN)
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number', 50)->unique();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->date('receive_date');
            $table->string('supplier_invoice_number', 50)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Goods Receipt Items
        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->constrained('purchase_order_items');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->decimal('qty_received', 12, 2);
            $table->decimal('unit_cost', 15, 2);
            $table->timestamps();
        });

        // 5. Work Orders (Perintah Kerja Produksi)
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number', 50)->unique();
            $table->foreignId('product_id')->constrained('products'); // Model Kebaya yang diproduksi
            $table->foreignId('tailor_id')->constrained('tailors'); // Penjahit maklon
            $table->integer('target_qty');
            $table->integer('finished_qty')->default(0);
            $table->string('status', 30)->default('in_production'); // draft, material_released, in_production, finished, qc_inspected
            $table->date('start_date');
            $table->date('due_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->decimal('sewing_cost_per_pcs', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 6. Material Issues (Surat Pengeluaran Bahan / SPK ke Penjahit)
        Schema::create('material_issues', function (Blueprint $table) {
            $table->id();
            $table->string('spk_number', 50)->unique();
            $table->foreignId('work_order_id')->constrained('work_orders')->cascadeOnDelete();
            $table->foreignId('tailor_id')->constrained('tailors');
            $table->dateTime('issue_date');
            $table->foreignId('raw_material_id')->constrained('products'); // Kain brokat / kain utama
            $table->decimal('qty_allocated', 12, 2); // Jumlah yard kain
            $table->string('roll_number', 50)->nullable();
            $table->string('receipt_status', 50)->default('Diterima Penjahit');
            $table->text('notes')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 7. Production Reconciliations (Rekonsiliasi Sisa Kain, Konsumsi 1,56 Yard & Waste)
        Schema::create('production_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->cascadeOnDelete();
            $table->decimal('allocated_fabric_qty', 12, 2); // misal 100 yard
            $table->decimal('standard_consumption_per_pcs', 8, 2); // misal 1.56 yard
            $table->decimal('total_standard_consumption', 12, 2); // 60 x 1.56 = 93.60 yard
            $table->decimal('theoretical_leftover', 12, 2); // 100 - 93.60 = 6.40 yard
            $table->decimal('actual_leftover', 12, 2); // 5.80 yard fisik
            $table->decimal('waste_qty', 12, 2)->default(0); // 0.60 yard
            $table->unsignedBigInteger('leftover_warehouse_id'); // Gudang Penjahit / Gudang Bahan Baku
            $table->foreign('leftover_warehouse_id', 'fk_prod_recon_wh')->references('id')->on('warehouses');
            $table->string('waste_reason')->nullable();
            $table->timestamps();
        });

        // 8. QC Inspections (Pemeriksaan Kualitas di Gudang Transit & Pemisahan 3 Jalur)
        Schema::create('qc_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('qc_number', 50)->unique();
            $table->foreignId('work_order_id')->constrained('work_orders');
            $table->date('inspection_date');
            $table->integer('total_inspected'); // 60 pcs dari Transit
            $table->integer('qty_passed'); // 55 pcs lolos ke Gudang Utama
            $table->integer('qty_rework'); // 3 pcs perbaikan ke Gudang Reject
            $table->integer('qty_defect_sale'); // 2 pcs cacat ke Gudang Sale
            $table->decimal('rework_cost_total', 15, 2)->default(0);
            $table->string('rework_notes')->nullable();
            $table->string('defect_notes')->nullable();
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qc_inspections');
        Schema::dropIfExists('production_reconciliations');
        Schema::dropIfExists('material_issues');
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
