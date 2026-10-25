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
        // 1. POS Sessions (Tutup Kasir / Shift Closing)
        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('opening_time');
            $table->dateTime('closing_time')->nullable();
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('expected_cash', 15, 2)->default(0);
            $table->decimal('actual_cash', 15, 2)->default(0);
            $table->decimal('difference', 15, 2)->default(0);
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->string('status', 30)->default('open'); // open, closed
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Sales Orders (Omnichannel: Shopee, TikTok Shop, Reguler, Outlet POS)
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->string('channel', 30); // shopee, tiktok, reguler, pos
            $table->string('resi_number', 60)->nullable();
            $table->string('resi_last_4_digits', 10)->nullable(); // Pencarian cepat 4 digit resi
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('pos_session_id')->nullable()->constrained('pos_sessions')->nullOnDelete();
            $table->dateTime('order_date');
            $table->string('status', 30)->default('baru'); // baru, diproses, dikemas, dikirim, selesai, batal, retur
            $table->string('payment_status', 30)->default('paid'); // unpaid, paid, refunded
            $table->string('payment_method', 50)->default('cash'); // cash, debit, qris, split, marketplace_escrow
            $table->decimal('split_cash_amount', 15, 2)->default(0);
            $table->decimal('split_non_cash_amount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('marketplace_fee', 15, 2)->default(0); // Biaya komisi/admin Shopee & TikTok
            $table->decimal('net_payout', 15, 2)->default(0); // Nilai pencairan bersih setelah fee
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('total_hpp', 15, 2)->default(0); // Nilai HPP terakumulasi untuk laba kotor
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('resi_last_4_digits');
            $table->index('channel');
            $table->index('order_date');
        });

        // 3. Sales Order Items
        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses'); // Gudang asal pemotongan stok (misal Gudang Store POS atau Gudang Utama/Live)
            $table->decimal('qty', 12, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('unit_hpp', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('subtotal_hpp', 15, 2);
            $table->timestamps();
        });

        // 4. Return Orders (Reverse Logistics Sesuai Bab 14 PRD)
        Schema::create('return_orders', function (Blueprint $table) {
            $table->id();
            $table->string('return_number', 50)->unique();
            $table->foreignId('sales_order_id')->constrained('sales_orders');
            $table->string('channel', 30);
            $table->string('resi_return', 60)->nullable();
            $table->dateTime('return_date');
            $table->string('status', 30)->default('transit_received'); // transit_received, inspected, completed
            $table->string('verification_condition', 50)->default('menunggu_pemeriksaan'); // baik, rusak_bisa_service, rusak_cacat_sale
            $table->foreignId('destination_warehouse_id')->nullable()->constrained('warehouses'); // Gd Utama / Gd Reject / Gd Sale
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->text('reason')->nullable();
            $table->foreignId('inspected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_orders');
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('pos_sessions');
    }
};
