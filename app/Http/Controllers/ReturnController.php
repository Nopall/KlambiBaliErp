<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Product;
use App\Models\ReturnOrder;
use App\Models\SalesOrder;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReturnController extends Controller
{
    /**
     * Tampilan Manajemen Return Paket (Reverse Logistics - Bab 14 PRD).
     */
    public function index(Request $request): View
    {
        $searchResi = $request->query('search_resi');
        $matchedOrders = collect();

        if ($searchResi) {
            // Pencarian 4 digit terakhir resi atau resi lengkap
            $matchedOrders = SalesOrder::with(['items', 'customer'])
                ->where(function ($q) use ($searchResi) {
                    $q->where('order_number', 'like', "%{$searchResi}%")
                        ->orWhere('tracking_number', 'like', "%{$searchResi}%");
                })->get();
        }

        $returns = ReturnOrder::with(['salesOrder.customer', 'product', 'user'])->latest()->get();
        $warehouses = Warehouse::orderBy('code')->get();

        return view('pages.retur.paket', compact('returns', 'searchResi', 'matchedOrders', 'warehouses'));
    }

    /**
     * Cari Transaksi Penjualan Asal via 4 Digit Terakhir Resi atau Nomor Pesanan (AJAX).
     */
    public function searchOrder(Request $request): JsonResponse
    {
        $keyword = trim($request->query('keyword', ''));

        if (strlen($keyword) < 3) {
            return response()->json(['success' => false, 'message' => 'Masukkan minimal 3 karakter untuk pencarian resi.']);
        }

        $orders = SalesOrder::with(['items', 'customer'])
            ->where('order_number', 'like', "%{$keyword}%")
            ->orWhere('tracking_number', 'like', "%{$keyword}%")
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    /**
     * Simpan Penerimaan Paket Return & Eksekusi 3 Jalur Disposisi Fisik (Bab 14 PRD).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'product_id' => 'required|exists:products,id',
            'qty_returned' => 'required|numeric|min:1',
            'condition_disposition' => 'required|in:good_ready_sale,repair_reject,damaged_sale',
        ]);

        $user = Auth::user() ?? User::first();
        $order = SalesOrder::with('items')->findOrFail($request->sales_order_id);
        $product = Product::findOrFail($request->product_id);
        $var = $product->variants->first();

        // 1. Validasi Duplicate Return Lock (Bab 14 PRD)
        $existingReturn = ReturnOrder::where('sales_order_id', $order->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingReturn) {
            return redirect()->back()->with('error', "Gagal: Paket untuk pesanan {$order->order_number} sudah pernah diproses return sebelumnya!");
        }

        $qty = (float) $request->qty_returned;
        $returnNumber = 'RET-'.date('Ym').'-'.str_pad((ReturnOrder::whereMonth('created_at', date('m'))->count() + 1), 3, '0', STR_PAD_LEFT);

        // Tentukan gudang tujuan sesuai 3 Jalur Disposisi
        $disposition = $request->condition_disposition;
        if ($disposition === 'good_ready_sale') {
            $destWarehouse = Warehouse::where('code', 'GD-UTM-06')->first() ?? Warehouse::first();
            $statusLabel = 'Kondisi Baik &rarr; Masuk Gudang Utama FG (Siap Jual)';
        } elseif ($disposition === 'repair_reject') {
            $destWarehouse = Warehouse::where('code', 'GD-RJC-04')->first() ?? Warehouse::first();
            $statusLabel = 'Rusak Ringan &rarr; Masuk Gudang Reject/Service';
        } else {
            $destWarehouse = Warehouse::where('code', 'GD-SAL-05')->first() ?? Warehouse::first();
            $statusLabel = 'Cacat Permanen &rarr; Masuk Gudang Sale Cacat';
        }

        try {
            DB::beginTransaction();

            $retur = ReturnOrder::create([
                'return_number' => $returnNumber,
                'sales_order_id' => $order->id,
                'channel' => $order->channel,
                'tracking_number' => $order->tracking_number ?? $request->input('tracking_number', 'RESI-'.$returnNumber),
                'product_id' => $product->id,
                'variant_id' => $var?->id,
                'qty_returned' => $qty,
                'reason' => $request->reason ?? 'Ukuran tidak pas / barang retur pembeli',
                'inspection_status' => 'inspected',
                'disposition' => $disposition,
                'destination_warehouse_id' => $destWarehouse->id,
                'notes' => $request->notes ?? "Disposisi retur ke {$destWarehouse->name}",
                'user_id' => $user->id,
            ]);

            // Tambah stok ke gudang disposisi
            $stockDest = Stock::where('product_id', $product->id)->where('warehouse_id', $destWarehouse->id)->first();
            $oldStock = $stockDest ? (float) $stockDest->qty : 0;
            $newStock = $oldStock + $qty;
            if ($stockDest) {
                $stockDest->update(['qty' => $newStock]);
            } else {
                Stock::create([
                    'product_id' => $product->id,
                    'variant_id' => $var?->id,
                    'warehouse_id' => $destWarehouse->id,
                    'qty' => $newStock,
                    'hpp' => $product->hpp,
                ]);
            }

            // Catat di Kartu Stok (StockMovement)
            StockMovement::create([
                'movement_date' => Carbon::now(),
                'document_number' => $returnNumber,
                'reference_type' => 'RETUR',
                'reference_id' => $retur->id,
                'product_id' => $product->id,
                'variant_id' => $var?->id,
                'warehouse_id' => $destWarehouse->id,
                'type' => 'IN',
                'qty_in' => $qty,
                'qty_out' => 0,
                'balance' => $newStock,
                'unit_cost' => $product->hpp,
                'total_cost' => $product->hpp * $qty,
                'notes' => "Penerimaan paket retur dari {$order->order_number} ({$statusLabel})",
                'user_id' => $user->id,
            ]);

            // Update status pesanan asal menjadi returned
            $order->update(['status' => 'returned']);

            // Jurnal Otomatis Penyesuaian Retur Penjualan & Persediaan
            $accReturPenjualan = ChartOfAccount::where('code', '4110')->first();
            $accKasBank = ChartOfAccount::where('code', '1110')->first();
            $accHpp = ChartOfAccount::where('code', '5110')->first();
            $accPersediaan = ChartOfAccount::where('code', '1150')->first();

            $refundValue = $qty * (float) $product->selling_price;
            $hppValue = $qty * (float) $product->hpp;

            if ($accReturPenjualan && $accKasBank) {
                $jvNumber = 'JV-'.date('Ym').'-'.str_pad((JournalEntry::whereMonth('created_at', date('m'))->count() + 1), 4, '0', STR_PAD_LEFT);
                $entry = JournalEntry::create([
                    'entry_number' => $jvNumber,
                    'entry_date' => Carbon::now()->toDateString(),
                    'reference_type' => 'RETUR',
                    'reference_number' => $returnNumber,
                    'description' => "Penjurnalan Retur Penjualan {$returnNumber} ({$order->order_number})",
                    'total_debit' => $refundValue + $hppValue,
                    'total_credit' => $refundValue + $hppValue,
                    'is_posted' => true,
                    'user_id' => $user->id,
                ]);

                // Debit Pendapatan / Retur Penjualan
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accReturPenjualan->id,
                    'debit' => $refundValue,
                    'credit' => 0,
                    'memo' => "Retur Penjualan {$order->order_number}",
                ]);

                // Kredit Kas / Piutang Refund
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accKasBank->id,
                    'debit' => 0,
                    'credit' => $refundValue,
                    'memo' => "Pengembalian Dana Pembeli {$order->order_number}",
                ]);

                // Debit Persediaan (Barang kembali ke aset)
                if ($accPersediaan && $accHpp && $hppValue > 0) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $accPersediaan->id,
                        'debit' => $hppValue,
                        'credit' => 0,
                        'memo' => "Persediaan bertambah dari retur {$returnNumber}",
                    ]);

                    // Kredit HPP (Membalikkan beban HPP)
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $accHpp->id,
                        'debit' => 0,
                        'credit' => $hppValue,
                        'memo' => 'Pembalikan HPP atas barang retur',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('retur.paket')->with('success', "Paket return {$returnNumber} berhasil diverifikasi! Stok dialokasikan ke {$destWarehouse->name} dan jurnal akuntansi telah disesuaikan.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal memproses return: '.$e->getMessage());
        }
    }
}
