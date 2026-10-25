<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    /**
     * Menampilkan Kartu Stok (Stock Card Ledger) per Produk, Varian, Gudang, dan Rentang Tanggal.
     */
    public function stockCard(Request $request): View
    {
        $products = Product::with('variants', 'unit', 'category')->orderBy('name')->get();
        $warehouses = Warehouse::orderBy('code')->get();

        // Default to first product or request
        $selectedProductId = $request->query('product_id', $products->first()?->id);
        $selectedProduct = Product::with('variants', 'unit', 'category')->find($selectedProductId);

        $variants = $selectedProduct ? $selectedProduct->variants : collect();
        $selectedVariantId = $request->query('variant_id');
        $selectedWarehouseId = $request->query('warehouse_id');

        $dateFrom = $request->query('date_from', Carbon::now()->subMonths(1)->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->query('date_to', Carbon::now()->format('Y-m-d'));

        // Query Stock Movements
        $query = StockMovement::with(['product', 'variant', 'warehouse', 'user'])
            ->whereBetween('movement_date', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ]);

        if ($selectedProductId) {
            $query->where('product_id', $selectedProductId);
        }

        if ($selectedVariantId) {
            $query->where('variant_id', $selectedVariantId);
        }

        if ($selectedWarehouseId) {
            $query->where('warehouse_id', $selectedWarehouseId);
        }

        $movements = $query->orderBy('movement_date', 'asc')->orderBy('id', 'asc')->get();

        // Hitung Running Balance Kronologis
        $runningBalance = 0;
        $totalIn = 0;
        $totalOut = 0;
        $lastUnitCost = $selectedProduct?->hpp ?? 0;

        foreach ($movements as $m) {
            $totalIn += (float) $m->qty_in;
            $totalOut += (float) $m->qty_out;
            $runningBalance += ((float) $m->qty_in - (float) $m->qty_out);
            $m->calculated_balance = $runningBalance;
            if ((float) $m->unit_cost > 0) {
                $lastUnitCost = (float) $m->unit_cost;
            }
        }

        $currentStockTotal = Stock::query()
            ->when($selectedProductId, fn ($q) => $q->where('product_id', $selectedProductId))
            ->when($selectedVariantId, fn ($q) => $q->where('variant_id', $selectedVariantId))
            ->when($selectedWarehouseId, fn ($q) => $q->where('warehouse_id', $selectedWarehouseId))
            ->sum('qty');

        $selectedVariant = $selectedVariantId ? ProductVariant::find($selectedVariantId) : null;
        $selectedWarehouse = $selectedWarehouseId ? Warehouse::find($selectedWarehouseId) : null;

        // Breakdown stok fisik per 7 gudang untuk produk terpilih
        $stockPerWarehouse = [];
        foreach ($warehouses as $wh) {
            $qtyWh = Stock::where('warehouse_id', $wh->id)
                ->when($selectedProductId, fn ($q) => $q->where('product_id', $selectedProductId))
                ->when($selectedVariantId, fn ($q) => $q->where('variant_id', $selectedVariantId))
                ->sum('qty');

            $stockPerWarehouse[] = [
                'warehouse' => $wh,
                'qty' => $qtyWh,
                'value' => $qtyWh * $lastUnitCost,
            ];
        }

        return view('pages.inventory.stock-card', compact(
            'products',
            'warehouses',
            'variants',
            'selectedProduct',
            'selectedVariant',
            'selectedWarehouse',
            'selectedProductId',
            'selectedVariantId',
            'selectedWarehouseId',
            'dateFrom',
            'dateTo',
            'movements',
            'totalIn',
            'totalOut',
            'runningBalance',
            'lastUnitCost',
            'currentStockTotal',
            'stockPerWarehouse'
        ));
    }

    /**
     * Tampilan matriks stok multi-gudang (Bab 3 PRD).
     */
    public function stockOverview(Request $request): View
    {
        $warehouses = Warehouse::orderBy('code')->get();
        $products = Product::with(['variants', 'unit', 'category', 'stocks.warehouse'])->orderBy('name')->get();

        $totalInventoryValue = 0;
        foreach ($products as $p) {
            $totalQty = $p->stocks->sum('qty');
            $totalInventoryValue += ($totalQty * (float) $p->hpp);
        }

        return view('pages.inventory.stock-overview', compact('warehouses', 'products', 'totalInventoryValue'));
    }
}
