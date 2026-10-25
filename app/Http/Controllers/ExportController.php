<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\PurchaseOrder;
use App\Models\ReturnOrder;
use App\Models\SalesOrder;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Ekspor Data Bersih ke Microsoft Excel (.csv / .xlsx compatible) sesuai Mandat Bab 13 PRD.
     */
    public function export(Request $request, string $type): StreamedResponse
    {
        $filename = 'klambibali_'.str_replace('-', '_', $type).'_'.date('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($type) {
            $handle = fopen('php://output', 'w');

            // Tambahkan UTF-8 BOM agar Excel di Windows membaca karakter rupiah & simbol dengan benar
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            switch ($type) {
                // 1. KARTU STOK EXPORT
                case 'stock-card':
                case 'movement':
                    fputcsv($handle, ['No', 'Tanggal', 'No Dokumen', 'Tipe Ref', 'Kode SKU', 'Nama Produk / Varian', 'Gudang', 'Tipe Mutasi', 'Masuk (Qty)', 'Keluar (Qty)', 'Saldo Berjalan', 'HPP Satuan (Rp)', 'Total Nilai (Rp)', 'Keterangan', 'Otorisator']);

                    $movements = StockMovement::with(['product', 'variant', 'warehouse', 'user'])->orderBy('movement_date', 'asc')->get();
                    $balance = 0;
                    foreach ($movements as $idx => $m) {
                        $balance += ((float) $m->qty_in - (float) $m->qty_out);
                        fputcsv($handle, [
                            $idx + 1,
                            $m->movement_date ? $m->movement_date->format('Y-m-d H:i') : '',
                            $m->document_number,
                            $m->reference_type,
                            $m->variant?->sku ?? $m->product?->sku,
                            $m->variant?->variant_name ?? $m->product?->name,
                            $m->warehouse?->name,
                            $m->type,
                            $m->qty_in,
                            $m->qty_out,
                            $balance,
                            $m->unit_cost,
                            $m->total_cost,
                            $m->notes,
                            $m->user?->name ?? 'Admin',
                        ]);
                    }
                    break;

                    // 2. INVENTORI MULTI GUDANG EXPORT
                case 'inventory':
                case 'stok':
                    fputcsv($handle, ['No', 'Kode SKU', 'Nama Produk', 'Kategori', 'Satuan', 'HPP (Rp)', 'Gudang', 'Jumlah Stok Fisik', 'Total Nilai Persediaan (Rp)']);

                    $stocks = Stock::with(['product.category', 'product.unit', 'warehouse'])->get();
                    foreach ($stocks as $idx => $s) {
                        $val = (float) $s->qty * (float) $s->hpp;
                        fputcsv($handle, [
                            $idx + 1,
                            $s->product?->sku,
                            $s->product?->name,
                            $s->product?->category?->name,
                            $s->product?->unit?->symbol ?? 'pcs',
                            $s->hpp,
                            $s->warehouse?->name,
                            $s->qty,
                            $val,
                        ]);
                    }
                    break;

                    // 3. SALES / PENJUALAN EXPORT
                case 'sales':
                case 'penjualan':
                    fputcsv($handle, ['No', 'No Order', 'Channel', 'Tanggal', 'No Resi', 'Nama Pelanggan', 'Metode Bayar', 'Status', 'Subtotal (Rp)', 'Diskon (Rp)', 'Grand Total (Rp)']);

                    $orders = SalesOrder::with(['customer'])->latest()->get();
                    foreach ($orders as $idx => $o) {
                        fputcsv($handle, [
                            $idx + 1,
                            $o->order_number,
                            strtoupper($o->channel),
                            $o->order_date,
                            $o->tracking_number,
                            $o->customer?->name ?? 'Pelanggan Umum',
                            strtoupper($o->payment_method),
                            $o->status,
                            $o->subtotal,
                            $o->discount_amount,
                            $o->grand_total,
                        ]);
                    }
                    break;

                    // 4. PURCHASING / PO EXPORT
                case 'purchasing':
                case 'po':
                    fputcsv($handle, ['No', 'No PO', 'Supplier', 'Tanggal PO', 'Jatuh Tempo', 'Status Barang', 'Status Bayar', 'Grand Total (Rp)']);

                    $pos = PurchaseOrder::with('supplier')->latest()->get();
                    foreach ($pos as $idx => $p) {
                        fputcsv($handle, [
                            $idx + 1,
                            $p->po_number,
                            $p->supplier?->name,
                            $p->po_date,
                            $p->due_date,
                            $p->status,
                            $p->payment_status,
                            $p->grand_total,
                        ]);
                    }
                    break;

                    // 5. PRODUKSI / WORK ORDER EXPORT
                case 'production':
                case 'produksi':
                    fputcsv($handle, ['No', 'No WO', 'Model Kebaya', 'Vendor Penjahit', 'Alokasi Kain (Yard)', 'Standar BOM (Yd/pcs)', 'Target Hasil (Pcs)', 'Status']);

                    $wos = WorkOrder::with(['product', 'tailor'])->latest()->get();
                    foreach ($wos as $idx => $w) {
                        fputcsv($handle, [
                            $idx + 1,
                            $w->wo_number,
                            $w->product?->name,
                            $w->tailor?->name,
                            $w->allocated_raw_material_qty,
                            $w->std_kain_consumption,
                            $w->target_qty,
                            $w->status,
                        ]);
                    }
                    break;

                    // 6. JURNAL UMUM EXPORT
                case 'journal':
                case 'jurnal':
                    fputcsv($handle, ['No', 'No Jurnal', 'Tanggal', 'Tipe Ref', 'No Dokumen', 'Deskripsi Transaksi', 'Kode Akun', 'Nama Akun', 'Debit (Rp)', 'Kredit (Rp)']);

                    $journals = JournalEntry::with(['lines.account'])->latest()->get();
                    $rowNo = 1;
                    foreach ($journals as $j) {
                        foreach ($j->lines as $line) {
                            fputcsv($handle, [
                                $rowNo++,
                                $j->entry_number,
                                $j->entry_date ? $j->entry_date->format('Y-m-d') : '',
                                $j->reference_type,
                                $j->reference_number,
                                $j->description,
                                $line->account?->code,
                                $line->account?->name,
                                $line->debit,
                                $line->credit,
                            ]);
                        }
                    }
                    break;

                    // 7. CHART OF ACCOUNTS EXPORT
                case 'coa':
                    fputcsv($handle, ['Kode Akun', 'Nama Akun COA', 'Kategori Akun', 'Posisi Normal', 'Tipe Kas/Bank', 'Saldo Awal (Rp)', 'Saldo Berjalan (Rp)']);

                    $accounts = ChartOfAccount::orderBy('code')->get();
                    foreach ($accounts as $acc) {
                        fputcsv($handle, [
                            $acc->code,
                            $acc->name,
                            strtoupper($acc->category),
                            strtoupper($acc->normal_balance),
                            $acc->is_cash_bank ? 'Ya' : 'Bukan',
                            $acc->opening_balance,
                            $acc->getCurrentBalance(),
                        ]);
                    }
                    break;

                    // 8. RETURN PAKET EXPORT
                case 'return':
                case 'retur':
                    fputcsv($handle, ['No', 'No Return', 'No Resi', 'Channel', 'Produk', 'Qty Retur (Pcs)', 'Disposisi Fisik', 'Alasan Retur', 'Tanggal']);

                    $returs = ReturnOrder::with(['product'])->latest()->get();
                    foreach ($returs as $idx => $r) {
                        fputcsv($handle, [
                            $idx + 1,
                            $r->return_number,
                            $r->tracking_number,
                            strtoupper($r->channel),
                            $r->product?->name,
                            $r->qty_returned,
                            $r->disposition,
                            $r->reason,
                            $r->created_at ? $r->created_at->format('Y-m-d H:i') : '',
                        ]);
                    }
                    break;

                default:
                    fputcsv($handle, ['Info', 'Tipe ekspor tidak dikenali']);
                    break;
            }

            fclose($handle);
        }, 200, $headers);
    }
}
