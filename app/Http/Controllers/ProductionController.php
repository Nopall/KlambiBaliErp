<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\MaterialIssue;
use App\Models\Product;
use App\Models\ProductionReconciliation;
use App\Models\QcInspection;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Tailor;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductionController extends Controller
{
    /**
     * Tampilan Daftar Work Order Produksi (WO).
     */
    public function workOrders(): View
    {
        $workOrders = WorkOrder::with(['product', 'tailor', 'warehouseWip', 'reconciliations'])->orderBy('created_at', 'desc')->get();
        $products = Product::where('type', 'finished_goods')->get();
        $tailors = Tailor::where('status', 'aktif')->get();

        return view('pages.produksi.work-orders', compact('workOrders', 'products', 'tailors'));
    }

    /**
     * Simpan Perintah Produksi (Work Order) Baru.
     */
    public function storeWorkOrder(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'tailor_id' => 'required|exists:tailors,id',
            'target_qty' => 'required|numeric|min:1',
            'allocated_raw_material_qty' => 'required|numeric|min:1',
            'std_kain_consumption' => 'required|numeric|min:0.1',
        ]);

        $user = Auth::user() ?? User::first();
        $gdaWip = Warehouse::where('code', 'GD-WIP-02')->first() ?? Warehouse::first();

        $woNumber = 'WO-'.date('Ym').'-'.str_pad((WorkOrder::whereMonth('created_at', date('m'))->count() + 1), 3, '0', STR_PAD_LEFT);

        WorkOrder::create([
            'wo_number' => $woNumber,
            'product_id' => $request->product_id,
            'tailor_id' => $request->tailor_id,
            'start_date' => Carbon::now()->toDateString(),
            'due_date' => Carbon::now()->addDays(14)->toDateString(),
            'target_qty' => $request->target_qty,
            'status' => 'draft',
            'notes' => $request->notes,
            'user_id' => $user->id,
        ]);

        return redirect()->route('produksi.work-orders')->with('success', "Work Order {$woNumber} berhasil dibuat!");
    }

    /**
     * Tampilan Surat Pengeluaran Bahan (SPK) ke Penjahit.
     */
    public function materialIssues(): View
    {
        $issues = MaterialIssue::with(['workOrder.product', 'tailor', 'rawMaterial', 'user'])->latest()->get();
        $workOrders = WorkOrder::with(['product', 'tailor'])->whereNotIn('status', ['cancelled'])->orderBy('id', 'desc')->get();
        $rawMaterials = Product::where('type', 'raw_material')->get();
        $gdBb = Warehouse::where('code', 'GD-BB-01')->first();
        $gdWip = Warehouse::where('code', 'GD-WIP-02')->first();

        return view('pages.produksi.material-issues', compact('issues', 'workOrders', 'rawMaterials', 'gdBb', 'gdWip'));
    }

    /**
     * Simpan SPK & Pindahkan Kain dari Gudang Bahan Baku ke Gudang Penjahit WIP.
     */
    public function storeMaterialIssue(Request $request): RedirectResponse
    {
        $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'raw_material_id' => 'required|exists:products,id',
            'qty_issued' => 'required|numeric|min:1',
        ]);

        $user = Auth::user() ?? User::first();
        $wo = WorkOrder::with('tailor')->findOrFail($request->work_order_id);
        $raw = Product::findOrFail($request->raw_material_id);
        $gdBb = Warehouse::where('code', 'GD-BB-01')->first() ?? Warehouse::first();
        $gdWip = Warehouse::where('code', 'GD-WIP-02')->first() ?? Warehouse::first();

        $spkNumber = 'SPK-'.date('Ym').'-'.str_pad((MaterialIssue::whereMonth('created_at', date('m'))->count() + 1), 3, '0', STR_PAD_LEFT);

        try {
            DB::beginTransaction();

            $issue = MaterialIssue::create([
                'spk_number' => $spkNumber,
                'work_order_id' => $wo->id,
                'tailor_id' => $wo->tailor_id,
                'raw_material_id' => $raw->id,
                'issue_date' => Carbon::now(),
                'qty_allocated' => $request->qty_issued,
                'receipt_status' => 'Diterima Penjahit',
                'notes' => $request->notes ?? "Alokasi {$request->qty_issued} yard kain ke Penjahit {$wo->tailor?->name}",
                'issued_by' => $user->id,
            ]);

            // Update status WO
            $wo->update(['status' => 'in_production']);

            // 1. Kurangi stok di Gudang Bahan Baku
            $stockBb = Stock::where('product_id', $raw->id)->where('warehouse_id', $gdBb->id)->first();
            $bbOld = $stockBb ? (float) $stockBb->qty : 0;
            $bbNew = max(0, $bbOld - (float) $request->qty_issued);
            if ($stockBb) {
                $stockBb->update(['qty' => $bbNew]);
            }

            StockMovement::create([
                'movement_date' => Carbon::now(),
                'document_number' => $spkNumber,
                'reference_type' => 'SPK',
                'reference_id' => $issue->id,
                'product_id' => $raw->id,
                'warehouse_id' => $gdBb->id,
                'type' => 'OUT',
                'qty_in' => 0,
                'qty_out' => $request->qty_issued,
                'balance' => $bbNew,
                'unit_cost' => $raw->purchase_price,
                'total_cost' => $raw->purchase_price * $request->qty_issued,
                'notes' => "Pengeluaran bahan kain ke Penjahit via {$spkNumber}",
                'user_id' => $user->id,
            ]);

            // 2. Tambah stok di Gudang Penjahit (WIP)
            $stockWip = Stock::where('product_id', $raw->id)->where('warehouse_id', $gdWip->id)->first();
            $wipOld = $stockWip ? (float) $stockWip->qty : 0;
            $wipNew = $wipOld + (float) $request->qty_issued;
            if ($stockWip) {
                $stockWip->update(['qty' => $wipNew]);
            } else {
                Stock::create([
                    'product_id' => $raw->id,
                    'warehouse_id' => $gdWip->id,
                    'qty' => $wipNew,
                    'hpp' => $raw->purchase_price,
                ]);
            }

            StockMovement::create([
                'movement_date' => Carbon::now(),
                'document_number' => $spkNumber,
                'reference_type' => 'SPK',
                'reference_id' => $issue->id,
                'product_id' => $raw->id,
                'warehouse_id' => $gdWip->id,
                'type' => 'IN',
                'qty_in' => $request->qty_issued,
                'qty_out' => 0,
                'balance' => $wipNew,
                'unit_cost' => $raw->purchase_price,
                'total_cost' => $raw->purchase_price * $request->qty_issued,
                'notes' => "Penerimaan kain di Gudang Penjahit (WIP) via {$spkNumber}",
                'user_id' => $user->id,
            ]);

            // 3. Jurnal Akuntansi Otomatis: WIP Penjahit (D) vs Persediaan Bahan Baku (K)
            $accPersediaanWip = ChartOfAccount::where('code', '1145')->first();
            $accPersediaanBb = ChartOfAccount::where('code', '1140')->first();
            $totalCost = $raw->purchase_price * (float) $request->qty_issued;

            if ($accPersediaanWip && $accPersediaanBb) {
                $jvNumber = 'JV-'.date('Ym').'-'.str_pad((JournalEntry::whereMonth('created_at', date('m'))->count() + 1), 4, '0', STR_PAD_LEFT);
                $entry = JournalEntry::create([
                    'entry_number' => $jvNumber,
                    'entry_date' => Carbon::now()->toDateString(),
                    'reference_type' => 'SPK',
                    'reference_number' => $spkNumber,
                    'description' => "Pengeluaran {$request->qty_issued} yard kain ke Penjahit ({$spkNumber})",
                    'total_debit' => $totalCost,
                    'total_credit' => $totalCost,
                    'is_posted' => true,
                    'user_id' => $user->id,
                ]);

                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accPersediaanWip->id,
                    'debit' => $totalCost,
                    'credit' => 0,
                    'memo' => 'Persediaan WIP di Penjahit bertambah',
                ]);

                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accPersediaanBb->id,
                    'debit' => 0,
                    'credit' => $totalCost,
                    'memo' => 'Persediaan Bahan Baku berkurang',
                ]);
            }

            DB::commit();

            return redirect()->route('produksi.material-issues')->with('success', "Surat Pengeluaran Bahan {$spkNumber} disahkan! Stok kain berhasil dialokasikan ke Gudang Penjahit.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal memproses SPK: '.$e->getMessage());
        }
    }

    /**
     * Tampilan Rekonsiliasi Pemakaian Kain & Sisa 1,56 Yard (Bab 4.2 PRD).
     */
    public function reconciliation(): View
    {
        $workOrders = WorkOrder::with(['product.variants', 'tailor', 'reconciliations'])->whereNotIn('status', ['cancelled'])->orderBy('id', 'desc')->get();
        $history = ProductionReconciliation::with(['workOrder.product', 'user'])->latest()->get();

        return view('pages.produksi.reconciliation', compact('workOrders', 'history'));
    }

    /**
     * Simpan Hasil Jahit Kebaya, Rekonsiliasi Sisa Kain & Masuk Gudang Transit QC.
     */
    public function storeReconciliation(Request $request): RedirectResponse
    {
        $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'finished_qty' => 'required|numeric|min:1',
            'actual_leftover_fabric' => 'required|numeric|min:0',
        ]);

        $user = Auth::user() ?? User::first();
        $wo = WorkOrder::with(['product', 'tailor'])->findOrFail($request->work_order_id);
        $kain = Product::where('type', 'raw_material')->first();
        $gdWip = Warehouse::where('code', 'GD-WIP-02')->first();
        $gdTransit = Warehouse::where('code', 'GD-TRN-03')->first();

        $stdConsumption = (float) $wo->std_kain_consumption; // 1.56 yd
        $finishedQty = (float) $request->finished_qty; // 60 kebaya
        $totalFabricAllocated = (float) $wo->allocated_raw_material_qty; // 100 yd

        // Perhitungan PRD Bab 4.2:
        $stdFabricUsed = $finishedQty * $stdConsumption; // 60 * 1.56 = 93.60 yd
        $theoreticalLeftover = max(0, $totalFabricAllocated - $stdFabricUsed); // 100 - 93.60 = 6.40 yd
        $actualLeftover = (float) $request->actual_leftover_fabric; // misal 5.80 yd
        $wasteFabric = max(0, $theoreticalLeftover - $actualLeftover); // 0.60 yd

        $docNumber = 'PRD-'.date('Ym').'-'.str_pad((ProductionReconciliation::whereMonth('created_at', date('m'))->count() + 1), 3, '0', STR_PAD_LEFT);

        try {
            DB::beginTransaction();

            ProductionReconciliation::create([
                'work_order_id' => $wo->id,
                'batch_number' => $docNumber,
                'fabric_allocated' => $totalFabricAllocated,
                'std_consumption_rate' => $stdConsumption,
                'finished_qty' => $finishedQty,
                'std_fabric_used' => $stdFabricUsed,
                'theoretical_leftover' => $theoreticalLeftover,
                'actual_leftover' => $actualLeftover,
                'waste_fabric' => $wasteFabric,
                'leftover_action' => $request->leftover_action ?? 'keep_at_tailor',
                'leftover_warehouse_id' => $gdWip->id,
                'notes' => $request->notes ?? "Penyelesaian {$finishedQty} pcs kebaya, sisa kain aktual {$actualLeftover} yd (waste {$wasteFabric} yd)",
                'user_id' => $user->id,
            ]);

            // 1. Kurangi pemakaian kain dari Gudang Penjahit (WIP)
            $totalFabricOut = $totalFabricAllocated - $actualLeftover; // 94.20 yd keluar
            $stockWip = Stock::where('product_id', $kain->id)->where('warehouse_id', $gdWip->id)->first();
            if ($stockWip) {
                $stockWip->update(['qty' => $actualLeftover]);
            }

            StockMovement::create([
                'movement_date' => Carbon::now(),
                'document_number' => $docNumber,
                'reference_type' => 'PRD',
                'reference_id' => $wo->id,
                'product_id' => $kain->id,
                'warehouse_id' => $gdWip->id,
                'type' => 'OUT',
                'qty_in' => 0,
                'qty_out' => $totalFabricOut,
                'balance' => $actualLeftover,
                'unit_cost' => $kain->purchase_price,
                'total_cost' => $kain->purchase_price * $totalFabricOut,
                'notes' => "Pemakaian kain jahit {$finishedQty} kebaya (sisa fisik {$actualLeftover} yd)",
                'user_id' => $user->id,
            ]);

            // 2. Masukkan hasil kebaya jadi ke Gudang Transit (QC)
            $varM = $wo->product->variants->first();
            $stockTrn = Stock::where('product_id', $wo->product_id)->where('warehouse_id', $gdTransit->id)->first();
            $trnOld = $stockTrn ? (float) $stockTrn->qty : 0;
            $trnNew = $trnOld + $finishedQty;
            if ($stockTrn) {
                $stockTrn->update(['qty' => $trnNew]);
            } else {
                Stock::create([
                    'product_id' => $wo->product_id,
                    'variant_id' => $varM?->id,
                    'warehouse_id' => $gdTransit->id,
                    'qty' => $trnNew,
                    'hpp' => $wo->product->hpp,
                ]);
            }

            StockMovement::create([
                'movement_date' => Carbon::now(),
                'document_number' => $docNumber,
                'reference_type' => 'PRD',
                'reference_id' => $wo->id,
                'product_id' => $wo->product_id,
                'variant_id' => $varM?->id,
                'warehouse_id' => $gdTransit->id,
                'type' => 'IN',
                'qty_in' => $finishedQty,
                'qty_out' => 0,
                'balance' => $trnNew,
                'unit_cost' => $wo->product->hpp,
                'total_cost' => $wo->product->hpp * $finishedQty,
                'notes' => "Hasil jahit {$finishedQty} pcs masuk Gudang Transit QC (Belum Siap Jual)",
                'user_id' => $user->id,
            ]);

            $wo->update(['status' => 'completed']);

            DB::commit();

            return redirect()->route('produksi.reconciliation')->with('success', "Penyelesaian {$finishedQty} kebaya berhasil dicatat! {$finishedQty} pcs telah masuk ke Gudang Transit untuk inspeksi QC.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal memproses rekonsiliasi: '.$e->getMessage());
        }
    }

    /**
     * Tampilan Inspeksi QC & Pemisahan ke 3 Gudang (Bab 3 & 5 PRD).
     */
    public function qcInspection(): View
    {
        $gdTransit = Warehouse::where('code', 'GD-TRN-03')->first();
        $transitStocks = Stock::with(['product.variants', 'warehouse'])->where('warehouse_id', $gdTransit?->id)->where('qty', '>', 0)->get();
        $inspections = QcInspection::with(['product', 'inspector'])->latest()->get();

        return view('pages.qc.inspection', compact('transitStocks', 'inspections'));
    }

    /**
     * Eksekusi Pemisahan QC 3 Gudang (Lolos ke FG Utama, Service ke Reject, Cacat ke Sale).
     */
    public function storeQcInspection(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty_passed' => 'required|numeric|min:0',
            'qty_reject' => 'required|numeric|min:0',
            'qty_defect_sale' => 'required|numeric|min:0',
        ]);

        $user = Auth::user() ?? User::first();
        $product = Product::with('variants')->findOrFail($request->product_id);
        $varM = $product->variants->first();

        $gdTransit = Warehouse::where('code', 'GD-TRN-03')->first();
        $gdUtama = Warehouse::where('code', 'GD-UTM-06')->first();
        $gdReject = Warehouse::where('code', 'GD-RJC-04')->first();
        $gdSale = Warehouse::where('code', 'GD-SAL-05')->first();

        $passed = (float) $request->qty_passed;
        $reject = (float) $request->qty_reject;
        $sale = (float) $request->qty_defect_sale;
        $totalInspected = $passed + $reject + $sale;

        $docNumber = 'QC-'.date('Ym').'-'.str_pad((QcInspection::whereMonth('created_at', date('m'))->count() + 1), 3, '0', STR_PAD_LEFT);

        try {
            DB::beginTransaction();

            QcInspection::create([
                'inspection_number' => $docNumber,
                'source_type' => 'production',
                'product_id' => $product->id,
                'variant_id' => $varM?->id,
                'qty_inspected' => $totalInspected,
                'qty_passed' => $passed,
                'qty_reject_service' => $reject,
                'qty_defect_sale' => $sale,
                'reject_reason' => $request->reject_reason,
                'status' => 'completed',
                'notes' => $request->notes ?? "Pemeriksaan QC: {$passed} lolos, {$reject} reject service, {$sale} cacat",
                'inspector_id' => $user->id,
            ]);

            // 1. Kurangi dari Gudang Transit
            $stockTrn = Stock::where('product_id', $product->id)->where('warehouse_id', $gdTransit->id)->first();
            $trnOld = $stockTrn ? (float) $stockTrn->qty : 0;
            $trnNew = max(0, $trnOld - $totalInspected);
            if ($stockTrn) {
                $stockTrn->update(['qty' => $trnNew]);
            }

            StockMovement::create([
                'movement_date' => Carbon::now(),
                'document_number' => $docNumber,
                'reference_type' => 'QC',
                'product_id' => $product->id,
                'variant_id' => $varM?->id,
                'warehouse_id' => $gdTransit->id,
                'type' => 'OUT',
                'qty_in' => 0,
                'qty_out' => $totalInspected,
                'balance' => $trnNew,
                'unit_cost' => $product->hpp,
                'total_cost' => $product->hpp * $totalInspected,
                'notes' => "Hasil inspeksi QC dialokasikan ke 3 gudang ({$docNumber})",
                'user_id' => $user->id,
            ]);

            // 2. Masuk Gudang Utama FG (Lolos Barcode)
            if ($passed > 0) {
                $stockUtm = Stock::where('product_id', $product->id)->where('warehouse_id', $gdUtama->id)->first();
                $utmOld = $stockUtm ? (float) $stockUtm->qty : 0;
                $utmNew = $utmOld + $passed;
                if ($stockUtm) {
                    $stockUtm->update(['qty' => $utmNew]);
                } else {
                    Stock::create(['product_id' => $product->id, 'variant_id' => $varM?->id, 'warehouse_id' => $gdUtama->id, 'qty' => $utmNew, 'hpp' => $product->hpp]);
                }

                StockMovement::create([
                    'movement_date' => Carbon::now(),
                    'document_number' => $docNumber,
                    'reference_type' => 'QC',
                    'product_id' => $product->id,
                    'variant_id' => $varM?->id,
                    'warehouse_id' => $gdUtama->id,
                    'type' => 'IN',
                    'qty_in' => $passed,
                    'qty_out' => 0,
                    'balance' => $utmNew,
                    'unit_cost' => $product->hpp,
                    'total_cost' => $product->hpp * $passed,
                    'notes' => "{$passed} pcs lolos QC masuk Gudang Utama FG (Terbit Barcode SKU)",
                    'user_id' => $user->id,
                ]);
            }

            // 3. Masuk Gudang Reject / Service
            if ($reject > 0) {
                $stockRjc = Stock::where('product_id', $product->id)->where('warehouse_id', $gdReject->id)->first();
                $rjcOld = $stockRjc ? (float) $stockRjc->qty : 0;
                $rjcNew = $rjcOld + $reject;
                if ($stockRjc) {
                    $stockRjc->update(['qty' => $rjcNew]);
                } else {
                    Stock::create(['product_id' => $product->id, 'variant_id' => $varM?->id, 'warehouse_id' => $gdReject->id, 'qty' => $rjcNew, 'hpp' => $product->hpp]);
                }

                StockMovement::create([
                    'movement_date' => Carbon::now(),
                    'document_number' => $docNumber,
                    'reference_type' => 'QC',
                    'product_id' => $product->id,
                    'variant_id' => $varM?->id,
                    'warehouse_id' => $gdReject->id,
                    'type' => 'IN',
                    'qty_in' => $reject,
                    'qty_out' => 0,
                    'balance' => $rjcNew,
                    'unit_cost' => $product->hpp,
                    'total_cost' => $product->hpp * $reject,
                    'notes' => "{$reject} pcs gagal QC masuk Gudang Reject untuk perbaikan",
                    'user_id' => $user->id,
                ]);
            }

            // 4. Masuk Gudang Sale Cacat
            if ($sale > 0) {
                $stockSal = Stock::where('product_id', $product->id)->where('warehouse_id', $gdSale->id)->first();
                $salOld = $stockSal ? (float) $stockSal->qty : 0;
                $salNew = $salOld + $sale;
                if ($stockSal) {
                    $stockSal->update(['qty' => $salNew]);
                } else {
                    Stock::create(['product_id' => $product->id, 'variant_id' => $varM?->id, 'warehouse_id' => $gdSale->id, 'qty' => $salNew, 'hpp' => $product->hpp]);
                }

                StockMovement::create([
                    'movement_date' => Carbon::now(),
                    'document_number' => $docNumber,
                    'reference_type' => 'QC',
                    'product_id' => $product->id,
                    'variant_id' => $varM?->id,
                    'warehouse_id' => $gdSale->id,
                    'type' => 'IN',
                    'qty_in' => $sale,
                    'qty_out' => 0,
                    'balance' => $salNew,
                    'unit_cost' => $product->hpp,
                    'total_cost' => $product->hpp * $sale,
                    'notes' => "{$sale} pcs cacat masuk Gudang Sale (Clearance berlabel cacat)",
                    'user_id' => $user->id,
                ]);
            }

            // Jurnal Otomatis QC Selesai
            $accFgUtama = ChartOfAccount::where('code', '1150')->first();
            $accReject = ChartOfAccount::where('code', '1155')->first();
            $accSale = ChartOfAccount::where('code', '1156')->first();
            $accPersediaanWip = ChartOfAccount::where('code', '1145')->first();
            $accHutangJahit = ChartOfAccount::where('code', '2120')->first();

            if ($accFgUtama && $accPersediaanWip) {
                $jvNumber = 'JV-'.date('Ym').'-'.str_pad((JournalEntry::whereMonth('created_at', date('m'))->count() + 1), 4, '0', STR_PAD_LEFT);
                $valPassed = $passed * $product->hpp;
                $valReject = $reject * $product->hpp;
                $valSale = $sale * $product->hpp;
                $totalVal = $valPassed + $valReject + $valSale;

                $entry = JournalEntry::create([
                    'entry_number' => $jvNumber,
                    'entry_date' => Carbon::now()->toDateString(),
                    'reference_type' => 'QC',
                    'reference_number' => $docNumber,
                    'description' => "Pengesahan QC {$docNumber} & alokasi 3 gudang",
                    'total_debit' => $totalVal,
                    'total_credit' => $totalVal,
                    'is_posted' => true,
                    'user_id' => $user->id,
                ]);

                if ($passed > 0) {
                    JournalEntryLine::create(['journal_entry_id' => $entry->id, 'account_id' => $accFgUtama->id, 'debit' => $valPassed, 'credit' => 0, 'memo' => "{$passed} pcs lolos ke Gudang Utama FG"]);
                }
                if ($reject > 0 && $accReject) {
                    JournalEntryLine::create(['journal_entry_id' => $entry->id, 'account_id' => $accReject->id, 'debit' => $valReject, 'credit' => 0, 'memo' => "{$reject} pcs rework ke Gudang Reject"]);
                }
                if ($sale > 0 && $accSale) {
                    JournalEntryLine::create(['journal_entry_id' => $entry->id, 'account_id' => $accSale->id, 'debit' => $valSale, 'credit' => 0, 'memo' => "{$sale} pcs cacat ke Gudang Sale"]);
                }

                // Kredit WIP & Hutang Jahit
                $kainCost = $totalVal * 0.65;
                $jahitCost = $totalVal - $kainCost;
                JournalEntryLine::create(['journal_entry_id' => $entry->id, 'account_id' => $accPersediaanWip->id, 'debit' => 0, 'credit' => $kainCost, 'memo' => 'Kredit pemakaian kain di WIP']);
                if ($accHutangJahit) {
                    JournalEntryLine::create(['journal_entry_id' => $entry->id, 'account_id' => $accHutangJahit->id, 'debit' => 0, 'credit' => $jahitCost, 'memo' => 'Hutang ongkos jahit maklon']);
                }
            }

            DB::commit();

            return redirect()->route('qc.inspeksi')->with('success', "Inspeksi QC {$docNumber} berhasil disahkan! {$passed} pcs lolos ke Gudang Utama, {$reject} pcs ke Reject/Service, {$sale} pcs ke Gudang Sale.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal memproses inspeksi QC: '.$e->getMessage());
        }
    }
}
