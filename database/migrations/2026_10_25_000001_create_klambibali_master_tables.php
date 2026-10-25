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
        // 1. Warehouses (7 Kategori Gudang KlambiBali)
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->string('type', 50); // raw_material, wip_penjahit, transit_qc, reject_service, sale_defect, utama_fg, live_stream, store_pos
            $table->string('location', 150)->nullable();
            $table->string('pic_name', 100)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Units
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // YD, MTR, PCS, ROLL
            $table->string('name', 50); // Yard, Meter, Pcs, Roll
            $table->string('symbol', 10);
            $table->timestamps();
        });

        // 4. Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->integer('term_days')->default(30); // 14, 30 hari
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Tailors / Vendor Penjahit Maklon
        Schema::create('tailors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->decimal('sewing_rate_default', 15, 2)->default(0);
            $table->string('status', 30)->default('aktif');
            $table->timestamps();
        });

        // 6. Customers / Members
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('member_code', 30)->unique();
            $table->string('name', 150);
            $table->string('type', 50)->default('retail'); // retail, reseller_b2b, marketplace
            $table->string('tier', 50)->default('Silver'); // Silver, Gold, Platinum, Reseller Khusus
            $table->integer('points')->default(0);
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 7. Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('sku', 50)->unique();
            $table->string('barcode', 50)->nullable()->unique();
            $table->string('name', 150);
            $table->string('model', 100)->nullable();
            $table->string('type', 50)->default('finished_goods'); // raw_material, finished_goods, accessories
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('std_kain_consumption', 8, 2)->default(1.56); // default 1.56 yard
            $table->decimal('sewing_cost', 15, 2)->default(0);
            $table->decimal('accessories_cost', 15, 2)->default(0);
            $table->decimal('overhead_cost', 15, 2)->default(0);
            $table->decimal('hpp', 15, 2)->default(0);
            $table->integer('min_stock')->default(10);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 8. Product Variants (Color, Size)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku', 60)->unique();
            $table->string('barcode', 60)->nullable()->unique();
            $table->string('variant_name', 100); // e.g. Maroon - M, Emas - All Size
            $table->string('color', 50)->nullable();
            $table->string('size', 30)->nullable();
            $table->decimal('additional_price', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 9. Bill of Materials (BOM)
        Schema::create('bill_of_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('standard_quantity', 10, 4);
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_of_materials');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('tailors');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('units');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('warehouses');
    }
};
