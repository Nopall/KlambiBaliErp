<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\PurchasingController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root to home / login
Route::get('/', function () {
    return redirect()->route('home');
});

// Authentication Routes (Login, Logout, Register if enabled)
Auth::routes();

// Protected ERP Application Routes (Requires Authentication)
Route::middleware(['auth'])->group(function () {

    // Dashboard Utama (Eksekutif & Multi-Role)
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // ==========================================
    // 1. MASTER DATA: USER MANAGEMENT & RBAC
    // ==========================================
    Route::get('/master/users', [UserController::class, 'index'])->name('users.index')->middleware('role:owner');
    Route::post('/master/users', [UserController::class, 'store'])->name('users.store')->middleware('role:owner');
    Route::put('/master/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role')->middleware('role:owner');

    // ==========================================
    // POINT OF SALE (POS) KASIR TOKO FISIK (BAB 11 PRD)
    // ==========================================
    Route::middleware(['role:owner,kasir,sales'])->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('/pos/riwayat', [PosController::class, 'history'])->name('pos.history');
        Route::post('/pos/close-shift', [PosController::class, 'closeShift'])->name('pos.close-shift');
    });

    // ==========================================
    // KARTU STOK & INVENTORI 7 GUDANG
    // ==========================================
    Route::middleware(['role:owner,gudang,purchasing,produksi,finance'])->group(function () {
        Route::get('/inventory/movement', [InventoryController::class, 'stockCard'])->name('inventory.stock-card');
        Route::get('/inventory/stok', [InventoryController::class, 'stockOverview'])->name('inventory.stock-overview');
    });

    // ==========================================
    // STANDAR AKUNTANSI (SAK ETAP) & LAPORAN
    // ==========================================
    Route::middleware(['role:owner,finance'])->group(function () {
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
    });

    // ==========================================
    // PURCHASING & PEMBELIAN (BAB 2 PRD)
    // ==========================================
    Route::middleware(['role:owner,purchasing,finance'])->group(function () {
        Route::get('/purchasing/po', [PurchasingController::class, 'index'])->name('purchasing.po');
        Route::post('/purchasing/po', [PurchasingController::class, 'store'])->name('purchasing.po.store');
        Route::get('/purchasing/penerimaan', [PurchasingController::class, 'receipts'])->name('purchasing.penerimaan');
        Route::post('/purchasing/penerimaan', [PurchasingController::class, 'storeReceipt'])->name('purchasing.penerimaan.store');
    });

    // ==========================================
    // PRODUKSI & QC (BAB 3, 4 & 5 PRD)
    // ==========================================
    Route::middleware(['role:owner,produksi'])->group(function () {
        Route::get('/produksi/work-orders', [ProductionController::class, 'workOrders'])->name('produksi.work-orders');
        Route::post('/produksi/work-orders', [ProductionController::class, 'storeWorkOrder'])->name('produksi.work-orders.store');
        Route::get('/produksi/pengeluaran-bahan', [ProductionController::class, 'materialIssues'])->name('produksi.material-issues');
        Route::post('/produksi/pengeluaran-bahan', [ProductionController::class, 'storeMaterialIssue'])->name('produksi.material-issues.store');
        Route::get('/produksi/pemakaian-bahan', [ProductionController::class, 'reconciliation'])->name('produksi.reconciliation');
        Route::post('/produksi/pemakaian-bahan', [ProductionController::class, 'storeReconciliation'])->name('produksi.reconciliation.store');
    });

    Route::middleware(['role:owner,produksi,gudang'])->group(function () {
        Route::get('/qc/inspeksi', [ProductionController::class, 'qcInspection'])->name('qc.inspeksi');
        Route::post('/qc/inspeksi', [ProductionController::class, 'storeQcInspection'])->name('qc.inspeksi.store');
    });

    // ==========================================
    // RETURN PAKET REVERSE LOGISTICS (BAB 14 PRD)
    // ==========================================
    Route::middleware(['role:owner,sales,gudang'])->group(function () {
        Route::get('/retur/paket', [ReturnController::class, 'index'])->name('retur.paket');
        Route::get('/retur/search-order', [ReturnController::class, 'searchOrder'])->name('retur.search-order');
        Route::post('/retur/paket', [ReturnController::class, 'store'])->name('retur.paket.store');
    });

    // ==========================================
    // EKSPOR NYATA KE EXCEL (.CSV / .XLSX) (BAB 13 PRD)
    // ==========================================
    Route::middleware(['role:owner,finance,purchasing,gudang,produksi,sales'])->group(function () {
        Route::get('/export/{type}', [ExportController::class, 'export'])->name('export.data');
    });

    // Dynamic ERP modules route (List, Search, Filter & Forms for other modules)
    Route::get('/{module}/{submodule?}', [ModuleController::class, 'show'])
        ->where('module', 'master|purchasing|inventory|produksi|qc|penjualan|marketplace|finance|accounting|retur|return|laporan')
        ->name('module.show');
});
