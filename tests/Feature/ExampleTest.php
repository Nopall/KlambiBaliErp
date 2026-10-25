<?php

namespace Tests\Feature;

use Database\Seeders\KlambiBaliErpSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = KlambiBaliErpSeeder::class;

    /**
     * Test root route redirects to home dashboard.
     */
    public function test_the_root_redirects_to_home(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/home');
    }

    /**
     * Test home dashboard returns 200.
     */
    public function test_the_home_dashboard_returns_success(): void
    {
        $response = $this->get('/home');

        $response->assertStatus(200);
        $response->assertSee('KlambiBali ERP');
    }

    /**
     * Test POS page returns 200.
     */
    public function test_the_pos_page_returns_success(): void
    {
        $response = $this->get('/pos');

        $response->assertStatus(200);
        $response->assertSee('Point of Sale (POS) Kasir');
    }

    /**
     * Test dummy module list and form returns 200.
     */
    public function test_the_dynamic_module_pages_return_success(): void
    {
        $modules = [
            '/master/produk',
            '/master/coa',
            '/master/penjahit',
            '/purchasing/po',
            '/inventory/stok',
            '/inventory/movement',
            '/inventory/opname',
            '/inventory/minimum',
            '/produksi/work-orders',
            '/produksi/pengeluaran-bahan',
            '/produksi/bom',
            '/produksi/pemakaian-bahan',
            '/produksi/hasil-produksi',
            '/produksi/hpp',
            '/qc/inspeksi',
            '/penjualan/transaksi',
            '/marketplace/pesanan',
            '/retur/paket',
            '/finance/arus-kas',
            '/finance/utang-piutang',
            '/finance/piutang',
            '/finance/rekonsiliasi',
            '/finance/laba-rugi',
            '/accounting/coa',
            '/accounting/jurnal',
            '/accounting/buku-besar',
            '/accounting/neraca-saldo',
            '/accounting/laba-rugi',
            '/accounting/neraca',
            '/accounting/arus-kas',
            '/laporan/transaksi',
            '/laporan/keuangan',
        ];

        foreach ($modules as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
        }
    }
}
