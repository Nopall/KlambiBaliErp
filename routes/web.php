<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root to home dashboard
Route::get('/', function () {
    return redirect()->route('home');
});

// Authentication Routes
Auth::routes();

// Dashboard Route
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Quick POS Route
Route::get('/pos', function () {
    return view('pages.pos');
})->name('pos.index');

// ==========================================
// KARTU STOK & INVENTORI GUDANG
// ==========================================
Route::get('/inventory/movement', [InventoryController::class, 'stockCard'])->name('inventory.stock-card');
Route::get('/inventory/stok', [InventoryController::class, 'stockOverview'])->name('inventory.stock-overview');

// ==========================================
// STANDAR AKUNTANSI (SAK ETAP) & LAPORAN
// ==========================================
Route::get('/accounting/coa', [AccountingController::class, 'coa'])->name('accounting.coa');
Route::get('/master/coa', [AccountingController::class, 'coa']);
Route::get('/accounting/jurnal', [AccountingController::class, 'journal'])->name('accounting.journal');
Route::get('/accounting/buku-besar', [AccountingController::class, 'generalLedger'])->name('accounting.general-ledger');
Route::get('/accounting/neraca-saldo', [AccountingController::class, 'trialBalance'])->name('accounting.trial-balance');
Route::get('/accounting/laba-rugi', [AccountingController::class, 'incomeStatement'])->name('accounting.laba-rugi');
Route::get('/finance/laba-rugi', [AccountingController::class, 'incomeStatement'])->name('accounting.income-statement');
Route::get('/accounting/neraca', [AccountingController::class, 'balanceSheet'])->name('accounting.neraca');
Route::get('/accounting/balance-sheet', [AccountingController::class, 'balanceSheet'])->name('accounting.balance-sheet');
Route::get('/accounting/arus-kas', [AccountingController::class, 'cashFlow'])->name('accounting.arus-kas');
Route::get('/finance/arus-kas', [AccountingController::class, 'cashFlow'])->name('accounting.cash-flow');

// Dynamic ERP modules route (List, Search, Filter & Forms for other modules)
Route::get('/{module}/{submodule?}', [ModuleController::class, 'show'])
    ->where('module', 'master|purchasing|inventory|produksi|qc|penjualan|marketplace|finance|accounting|retur|return|laporan')
    ->name('module.show');
