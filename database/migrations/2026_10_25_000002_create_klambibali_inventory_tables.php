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
        // 1. Current Stock (Saldo Fisik per Produk, Varian & Gudang)
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->decimal('qty', 12, 2)->default(0);
            $table->decimal('hpp', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'variant_id', 'warehouse_id'], 'stock_prod_var_wh_unique');
        });

        // 2. Stock Movements (KARTU STOK RIIL & AUDIT TRAIL LOG PERSISTEN)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->dateTime('movement_date');
            $table->string('document_number', 50); // PO-041, WO-001, TRF-011, INV-POS, etc.
            $table->string('reference_type', 50); // PO, SPK, WO, PRD, QC, TRF, POS, REGULER, SHOPEE, TIKTOK, RETUR, OPNAME
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->string('type', 20); // IN, OUT, ADJUSTMENT
            $table->decimal('qty_in', 12, 2)->default(0);
            $table->decimal('qty_out', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0); // Saldo Berjalan (Running Balance)
            $table->decimal('unit_cost', 15, 2)->default(0); // Nilai HPP per unit
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->string('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['product_id', 'variant_id', 'warehouse_id'], 'idx_movement_prod_var_wh');
            $table->index('document_number');
            $table->index('movement_date');
        });

        // 3. Stock Transfers (Transfer Antar-Gudang)
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number', 50)->unique();
            $table->foreignId('from_warehouse_id')->constrained('warehouses');
            $table->foreignId('to_warehouse_id')->constrained('warehouses');
            $table->dateTime('transfer_date');
            $table->dateTime('received_date')->nullable();
            $table->string('status', 30)->default('in_transit'); // draft, in_transit, received, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Pengirim
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete(); // Penerima
            $table->timestamps();
        });

        // 4. Stock Transfer Items
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained('stock_transfers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->decimal('qty', 12, 2);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->timestamps();
        });

        // 5. Stock Opnames
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number', 50)->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->dateTime('opname_date');
            $table->string('status', 30)->default('draft'); // draft, approved, rejected
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 6. Stock Opname Items
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->decimal('system_qty', 12, 2);
            $table->decimal('physical_qty', 12, 2);
            $table->decimal('difference_qty', 12, 2);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stocks');
    }
};
