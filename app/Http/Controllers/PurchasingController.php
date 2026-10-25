<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchasingController extends Controller
{
    /**
     * Tampilan Daftar Purchase Order (PO).
     */
    public function index(): View
    {
        $orders = PurchaseOrder::with(['supplier', 'items.product', 'warehouse', 'user'])->latest()->get();
        $suppliers = Supplier::orderBy('name')->get();
        $rawMaterials = Product::where('type', 'raw_material')->get();
        $units = Unit::all();

        return view('pages.purchasing.po', compact('orders', 'suppliers', 'rawMaterials', 'units'));
    }

    /**
     * Buat Dokumen Purchase Order (PO) Baru ke Supplier.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $user = Auth::user() ?? User::first();
        $gdBb = Warehouse::where('code', 'GD-BB-01')->first() ?? Warehouse::first();
        $raw = Product::findOrFail($request->product_id);

        $poNumber = 'PO-'.date('Ym').'-'.str_pad((PurchaseOrder::whereMonth('created_at', date('m'))->count() + 1), 3, '0', STR_PAD_LEFT);
        $totalPrice = (float) $request->qty * (float) $request->unit_price;

        $po = PurchaseOrder::create([
            'po_number' => $poNumber,
            'supplier_id' => $request->supplier_id,
            'warehouse_id' => $gdBb->id,
            'po_date' => Carbon::now()->toDateString(),
            'due_date' => Carbon::now()->addDays(30)->toDateString(),
            'status' => 'pending',
            'subtotal' => $totalPrice,
            'shipping_cost' => $request->shipping_cost ?? 0,
            'grand_total' => $totalPrice + ($request->shipping_cost ?? 0),
            'payment_status' => 'unpaid',
            'notes' => $request->notes ?? 'Pemesanan bahan baku kain ke supplier',
            'user_id' => $user->id,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'product_id' => $raw->id,
            'qty_ordered' => $request->qty,
            'qty_received' => 0,
            'unit_price' => $request->unit_price,
            'total_price' => $totalPrice,
        ]);

        return redirect()->route('purchasing.po')->with('success', "Purchase Order {$poNumber} berhasil diterbitkan!");
    }

    /**
     * Tampilan Penerimaan Barang (Goods Receipt / GRN) & QC Awal.
     */
    public function receipts(): View
    {
        $pendingPos = PurchaseOrder::with(['supplier', 'items.product'])->whereIn('status', ['pending', 'partially_received'])->get();
        $receipts = GoodsReceipt::with(['purchaseOrder.supplier', 'warehouse', 'receiver'])->latest()->get();

        return view('pages.purchasing.penerimaan', compact('pendingPos', 'receipts'));
    }

    /**
     * Konfirmasi Penerimaan Barang (GRN), Tambah Stok & Jurnal Hutang Dagang Otomatis.
     */
    public function storeReceipt(Request $request): RedirectResponse
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'qty_received' => 'required|numeric|min:1',
        ]);

        $user = Auth::user() ?? User::first();
        $po = PurchaseOrder::with(['items.product', 'supplier'])->findOrFail($request->purchase_order_id);
        $item = $po->items->first();
        $product = $item->product;
        $gdBb = Warehouse::where('code', 'GD-BB-01')->first() ?? Warehouse::first();

        $qtyRec = (float) $request->qty_received;
        $grnNumber = 'GRN-'.date('Ym').'-'.str_pad((GoodsReceipt::whereMonth('created_at', date('m'))->count() + 1), 3, '0', STR_PAD_LEFT);

        try {
            DB::beginTransaction();

            $grn = GoodsReceipt::create([
                'grn_number' => $grnNumber,
                'purchase_order_id' => $po->id,
                'warehouse_id' => $gdBb->id,
                'received_date' => Carbon::now()->toDateString(),
                'status' => 'received',
                'notes' => $request->notes ?? "Penerimaan {$qtyRec} yard bahan dari {$po->supplier?->name}",
                'received_by' => $user->id,
            ]);

            GoodsReceiptItem::create([
                'goods_receipt_id' => $grn->id,
                'purchase_order_item_id' => $item->id,
                'product_id' => $product->id,
                'qty_received' => $qtyRec,
                'qty_accepted' => $qtyRec,
                'qty_rejected' => 0,
            ]);

            // Tambah stok fisik di Gudang Bahan Baku
            $stockBb = Stock::where('product_id', $product->id)->where('warehouse_id', $gdBb->id)->first();
            $oldStock = $stockBb ? (float) $stockBb->qty : 0;
            $newStock = $oldStock + $qtyRec;
            if ($stockBb) {
                $stockBb->update(['qty' => $newStock]);
            } else {
                Stock::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $gdBb->id,
                    'qty' => $newStock,
                    'hpp' => $item->unit_price,
                ]);
            }

            // Catat di Kartu Stok (StockMovement)
            StockMovement::create([
                'movement_date' => Carbon::now(),
                'document_number' => $po->po_number,
                'reference_type' => 'PO',
                'reference_id' => $po->id,
                'product_id' => $product->id,
                'warehouse_id' => $gdBb->id,
                'type' => 'IN',
                'qty_in' => $qtyRec,
                'qty_out' => 0,
                'balance' => $newStock,
                'unit_cost' => $item->unit_price,
                'total_cost' => $item->unit_price * $qtyRec,
                'notes' => "Penerimaan barang dari PO {$po->po_number} via {$grnNumber}",
                'user_id' => $user->id,
            ]);

            // Update item & status PO
            $item->increment('qty_received', $qtyRec);
            if ($item->qty_received >= $item->qty_ordered) {
                $po->update(['status' => 'received']);
            } else {
                $po->update(['status' => 'partially_received']);
            }

            // Jurnal Otomatis Pembelian: Persediaan Bahan Baku (D) vs Hutang Dagang Supplier (K)
            $accPersediaanBb = ChartOfAccount::where('code', '1140')->first();
            $accHutangSupplier = ChartOfAccount::where('code', '2110')->first();
            $totalCost = $qtyRec * $item->unit_price;

            if ($accPersediaanBb && $accHutangSupplier) {
                $jvNumber = 'JV-'.date('Ym').'-'.str_pad((JournalEntry::whereMonth('created_at', date('m'))->count() + 1), 4, '0', STR_PAD_LEFT);
                $entry = JournalEntry::create([
                    'entry_number' => $jvNumber,
                    'entry_date' => Carbon::now()->toDateString(),
                    'reference_type' => 'PO',
                    'reference_number' => $po->po_number,
                    'description' => "Penerimaan {$qtyRec} yard kain dari Supplier {$po->supplier?->name} ({$po->po_number})",
                    'total_debit' => $totalCost,
                    'total_credit' => $totalCost,
                    'is_posted' => true,
                    'user_id' => $user->id,
                ]);

                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accPersediaanBb->id,
                    'debit' => $totalCost,
                    'credit' => 0,
                    'memo' => "Penerimaan persediaan bahan baku {$product->name}",
                ]);

                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accHutangSupplier->id,
                    'debit' => 0,
                    'credit' => $totalCost,
                    'memo' => "Timbul hutang usaha ke {$po->supplier?->name}",
                ]);
            }

            DB::commit();

            return redirect()->route('purchasing.penerimaan')->with('success', "Penerimaan barang {$grnNumber} berhasil disahkan! Stok Gudang Bahan Baku bertambah {$qtyRec} yard dan hutang supplier tercatat di akuntansi.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal memproses penerimaan: '.$e->getMessage());
        }
    }
}
