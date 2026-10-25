<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    /**
     * Tampilan Antarmuka Kasir Toko Fisik (POS).
     */
    public function index(): View
    {
        $gdStore = Warehouse::where('code', 'GD-STR-08')->first() ?? Warehouse::first();

        // Ambil produk dan varian yang memiliki info harga & stok di Gudang Store
        $products = Product::with(['variants', 'unit', 'category', 'stocks' => function ($q) use ($gdStore) {
            $q->where('warehouse_id', $gdStore?->id);
        }])->where('type', 'finished_goods')->get();

        // Flatten katalog item untuk kasir
        $catalog = [];
        foreach ($products as $p) {
            if ($p->variants->isNotEmpty()) {
                foreach ($p->variants as $v) {
                    $storeStock = Stock::where('product_id', $p->id)
                        ->where('variant_id', $v->id)
                        ->where('warehouse_id', $gdStore?->id)
                        ->value('qty') ?? 0;

                    $catalog[] = [
                        'product_id' => $p->id,
                        'variant_id' => $v->id,
                        'name' => $p->name.' - '.$v->variant_name,
                        'sku' => $v->sku,
                        'barcode' => $v->barcode ?? $p->barcode,
                        'price' => (float) $p->selling_price,
                        'hpp' => (float) $p->hpp,
                        'stock' => (float) $storeStock,
                        'category' => $p->category?->name ?? 'Kebaya Bali',
                        'unit' => $p->unit?->symbol ?? 'pcs',
                    ];
                }
            } else {
                $storeStock = Stock::where('product_id', $p->id)
                    ->where('warehouse_id', $gdStore?->id)
                    ->value('qty') ?? 0;

                $catalog[] = [
                    'product_id' => $p->id,
                    'variant_id' => null,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'barcode' => $p->barcode,
                    'price' => (float) $p->selling_price,
                    'hpp' => (float) $p->hpp,
                    'stock' => (float) $storeStock,
                    'category' => $p->category?->name ?? 'Kebaya Bali',
                    'unit' => $p->unit?->symbol ?? 'pcs',
                ];
            }
        }

        $customers = Customer::orderBy('name')->get();
        $user = Auth::user() ?? User::first();

        // Sesi POS aktif
        $activeSession = PosSession::where('status', 'open')->latest()->first();

        return view('pages.pos', compact('catalog', 'customers', 'gdStore', 'user', 'activeSession'));
    }

    /**
     * Memproses Transaksi Checkout POS (Simpan Order, Potong Stok Gudang Store, Jurnal Otomatis).
     */
    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'payment_method' => 'required|string',
            'paid_amount' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
        ]);

        $user = Auth::user() ?? User::first();
        $gdStore = Warehouse::where('code', 'GD-STR-08')->first() ?? Warehouse::first();

        try {
            DB::beginTransaction();

            $orderNumber = 'POS-'.date('Ym').'-'.str_pad((SalesOrder::whereMonth('created_at', date('m'))->count() + 1), 4, '0', STR_PAD_LEFT);

            $order = SalesOrder::create([
                'order_number' => $orderNumber,
                'channel' => 'store_pos',
                'customer_id' => $request->customer_id ?: null,
                'warehouse_id' => $gdStore->id,
                'order_date' => Carbon::now()->toDateString(),
                'status' => 'delivered',
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method,
                'subtotal' => $request->subtotal ?? $request->grand_total,
                'discount_amount' => $request->discount_amount ?? 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'grand_total' => $request->grand_total,
                'shipping_cost' => 0,
                'notes' => 'Transaksi POS Kasir Toko. Split: '.$request->input('split_info', '-'),
                'user_id' => $user->id,
            ]);

            $totalHpp = 0;

            foreach ($request->items as $item) {
                $qty = (float) $item['qty'];
                $price = (float) $item['price'];
                $subtotal = $qty * $price;
                $hpp = (float) ($item['hpp'] ?? 0);
                $totalHpp += ($hpp * $qty);

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?: null,
                    'sku' => $item['sku'],
                    'item_name' => $item['name'],
                    'qty' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal,
                    'hpp' => $hpp,
                ]);

                // Potong stok di Gudang Store POS
                $stockRecord = Stock::where('product_id', $item['product_id'])
                    ->where('warehouse_id', $gdStore->id)
                    ->when($item['variant_id'], fn ($q) => $q->where('variant_id', $item['variant_id']))
                    ->first();

                $oldBalance = $stockRecord ? (float) $stockRecord->qty : 0;
                $newBalance = max(0, $oldBalance - $qty);

                if ($stockRecord) {
                    $stockRecord->update(['qty' => $newBalance]);
                } else {
                    Stock::create([
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'] ?: null,
                        'warehouse_id' => $gdStore->id,
                        'qty' => 0,
                        'hpp' => $hpp,
                    ]);
                }

                // Catat di Kartu Stok (StockMovement)
                StockMovement::create([
                    'movement_date' => Carbon::now(),
                    'document_number' => $orderNumber,
                    'reference_type' => 'POS',
                    'reference_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?: null,
                    'warehouse_id' => $gdStore->id,
                    'type' => 'OUT',
                    'qty_in' => 0,
                    'qty_out' => $qty,
                    'balance' => $newBalance,
                    'unit_cost' => $hpp,
                    'total_cost' => $hpp * $qty,
                    'notes' => 'Penjualan Kasir POS Scan Barcode ('.$orderNumber.')',
                    'user_id' => $user->id,
                ]);
            }

            // Jurnal Otomatis Penjualan POS & HPP
            $accKasToko = ChartOfAccount::where('code', '1110')->first();
            $accPenjualanPos = ChartOfAccount::where('code', '4110')->first();
            $accHpp = ChartOfAccount::where('code', '5110')->first();
            $accPersediaanStore = ChartOfAccount::where('code', '1151')->first();

            if ($accKasToko && $accPenjualanPos) {
                $jvNumber = 'JV-'.date('Ym').'-'.str_pad((JournalEntry::whereMonth('created_at', date('m'))->count() + 1), 4, '0', STR_PAD_LEFT);
                $entry = JournalEntry::create([
                    'entry_number' => $jvNumber,
                    'entry_date' => Carbon::now()->toDateString(),
                    'reference_type' => 'POS',
                    'reference_number' => $orderNumber,
                    'description' => 'Penjualan Kasir Toko POS '.$orderNumber,
                    'total_debit' => (float) $request->grand_total + $totalHpp,
                    'total_credit' => (float) $request->grand_total + $totalHpp,
                    'is_posted' => true,
                    'user_id' => $user->id,
                ]);

                // Kas / Bank bertambah (Debit)
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accKasToko->id,
                    'debit' => $request->grand_total,
                    'credit' => 0,
                    'memo' => 'Penerimaan Kasir POS '.$request->payment_method,
                ]);

                // Pendapatan bertambah (Kredit)
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accPenjualanPos->id,
                    'debit' => 0,
                    'credit' => $request->grand_total,
                    'memo' => 'Pendapatan Penjualan Toko Fisik',
                ]);

                // Pengakuan HPP (Debit)
                if ($accHpp && $accPersediaanStore && $totalHpp > 0) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $accHpp->id,
                        'debit' => $totalHpp,
                        'credit' => 0,
                        'memo' => 'HPP Barang Terjual POS',
                    ]);

                    // Persediaan Gudang Store berkurang (Kredit)
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $accPersediaanStore->id,
                        'debit' => 0,
                        'credit' => $totalHpp,
                        'memo' => 'Pengurangan Persediaan Gudang Store POS',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi POS berhasil disimpan dan stok terpotong.',
                'order' => $order->load('items'),
                'order_number' => $orderNumber,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses transaksi: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Riwayat Transaksi Kasir POS.
     */
    public function history(Request $request): View
    {
        $orders = SalesOrder::with(['items', 'customer', 'user'])
            ->where('channel', 'store_pos')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pages.pos.history', compact('orders'));
    }

    /**
     * Fitur Tutup Kasir / Shift Closing & Rekonsiliasi Uang Fisik (Bab 11.3 PRD).
     */
    public function closeShift(Request $request): JsonResponse
    {
        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
        ]);

        $today = Carbon::today();
        $totalSystemCash = SalesOrder::where('channel', 'store_pos')
            ->whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->sum('grand_total');

        $difference = (float) $request->actual_cash - (float) $totalSystemCash;

        $session = PosSession::where('status', 'open')->latest()->first();
        if ($session) {
            $session->update([
                'status' => 'closed',
                'closed_at' => Carbon::now(),
                'closing_cash' => $request->actual_cash,
                'total_sales' => $totalSystemCash,
                'difference' => $difference,
                'notes' => $request->notes ?? 'Tutup Shift Harian',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Shift kasir berhasil ditutup.',
            'system_cash' => $totalSystemCash,
            'actual_cash' => (float) $request->actual_cash,
            'difference' => $difference,
            'status' => abs($difference) < 1 ? 'Seimbang (Match)' : 'Selisih Rp '.number_format($difference, 0, ',', '.'),
        ]);
    }
}
