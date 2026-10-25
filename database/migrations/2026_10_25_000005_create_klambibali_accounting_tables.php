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
        // 1. Chart of Accounts (COA Berstandar SAK ETAP / Indonesian GAAP)
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // 1110, 1120, 1140, 2110, 3100, 4110, 5110, 6110
            $table->string('name', 150);
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->string('category', 50); // asset, liability, equity, revenue, cogs, expense
            $table->string('normal_balance', 10); // debit, credit
            $table->boolean('is_cash_bank')->default(false);
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('category');
        });

        // 2. Journal Entries (Jurnal Umum & Jurnal Otomatis Hulu)
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number', 50)->unique();
            $table->date('entry_date');
            $table->string('reference_type', 50)->nullable(); // PO, GRN, SPK, WO, SALES, POS, RETUR, SETTLEMENT, EXPENSE, ADJUSTMENT
            $table->string('reference_number', 50)->nullable();
            $table->text('description');
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            $table->boolean('is_posted')->default(true);
            $table->boolean('is_reversed')->default(false);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('entry_date');
            $table->index('reference_type');
        });

        // 3. Journal Entry Lines (Detail Baris Debit & Kredit)
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('chart_of_accounts');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('memo')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'journal_entry_id']);
        });

        // 4. Operating Expenses (Biaya Operasional Sesuai Bab 8 PRD)
        Schema::create('operating_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number', 50)->unique();
            $table->foreignId('account_id')->constrained('chart_of_accounts'); // Akun beban (6xxx)
            $table->foreignId('payment_account_id')->constrained('chart_of_accounts'); // Akun kas/bank (1110/1120)
            $table->date('expense_date');
            $table->decimal('amount', 15, 2);
            $table->string('channel_or_outlet', 50)->default('All'); // Outlet Sanur, Shopee, TikTok, Pusat
            $table->string('payee', 100)->nullable(); // Penerima pembayaran
            $table->text('description');
            $table->string('receipt_file')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 5. Bank Reconciliations (Pencocokan Rekening Koran vs Sistem)
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->string('reconcile_number', 50)->unique();
            $table->foreignId('bank_account_id')->constrained('chart_of_accounts');
            $table->date('statement_date');
            $table->decimal('statement_balance', 15, 2); // Saldo rekening koran BCA
            $table->decimal('book_balance', 15, 2); // Saldo buku kas sistem
            $table->decimal('difference', 15, 2)->default(0);
            $table->string('status', 30)->default('matched'); // matched, discrepancy
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliations');
        Schema::dropIfExists('operating_expenses');
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
    }
};
