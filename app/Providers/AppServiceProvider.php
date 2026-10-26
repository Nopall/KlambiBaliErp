<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Superadmin override: Owner has access to everything
        Gate::before(function (User $user) {
            if ($user->isOwner()) {
                return true;
            }

            return null;
        });

        // 1. Master Data & Users
        Gate::define('access-master', fn (User $user) => $user->hasRole(['owner', 'gudang', 'purchasing', 'produksi']));
        Gate::define('access-users', fn (User $user) => $user->isOwner());

        // 2. Purchasing
        Gate::define('access-purchasing', fn (User $user) => $user->hasRole(['owner', 'purchasing', 'finance']));

        // 3. Multi-Warehouse & Inventory
        Gate::define('access-inventory', fn (User $user) => $user->hasRole(['owner', 'gudang', 'purchasing', 'produksi', 'finance']));

        // 4. Production & BOM
        Gate::define('access-produksi', fn (User $user) => $user->hasRole(['owner', 'produksi']));

        // 5. Quality Control & Barcode
        Gate::define('access-qc', fn (User $user) => $user->hasRole(['owner', 'produksi', 'gudang']));

        // 6. Penjualan
        Gate::define('access-penjualan', fn (User $user) => $user->hasRole(['owner', 'sales', 'finance']));

        // 7. Marketplace (Shopee & TikTok)
        Gate::define('access-marketplace', fn (User $user) => $user->hasRole(['owner', 'sales']));

        // 8. Point of Sale (POS)
        Gate::define('access-pos', fn (User $user) => $user->hasRole(['owner', 'kasir', 'sales']));

        // 9. Return Paket (Reverse Logistics)
        Gate::define('access-retur', fn (User $user) => $user->hasRole(['owner', 'sales', 'gudang']));

        // 10. Finance & Kas
        Gate::define('access-finance', fn (User $user) => $user->hasRole(['owner', 'finance']));

        // 11. Accounting & SAK ETAP
        Gate::define('access-accounting', fn (User $user) => $user->hasRole(['owner', 'finance']));

        // 12. Laporan & Ekspor
        Gate::define('access-laporan', fn (User $user) => $user->hasRole(['owner', 'finance', 'purchasing', 'gudang', 'produksi', 'sales']));
    }
}
