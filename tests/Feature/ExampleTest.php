<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
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
            '/purchasing/po',
            '/inventory/stok',
            '/produksi/work-orders',
            '/qc/inspeksi',
            '/penjualan/transaksi',
            '/marketplace/pesanan',
            '/finance/arus-kas',
            '/laporan/transaksi',
        ];

        foreach ($modules as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
            $response->assertSee('Tambah Data Baru');
        }
    }
}
