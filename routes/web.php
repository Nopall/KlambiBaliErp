<?php

use App\Http\Controllers\HomeController;
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

// Dynamic ERP modules route (List, Search, Filter & Forms)
Route::get('/{module}/{submodule?}', [ModuleController::class, 'show'])
    ->where('module', 'master|purchasing|inventory|produksi|qc|penjualan|marketplace|finance|laporan')
    ->name('module.show');
