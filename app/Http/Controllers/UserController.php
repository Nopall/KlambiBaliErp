<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users and the RBAC authorization matrix.
     */
    public function index(): View
    {
        $users = User::orderBy('id')->get();
        $roleLabels = User::roleLabels();
        $roleBadges = User::roleBadges();

        $modules = [
            '1. Dashboard Utama' => [
                'owner' => ['status' => 'full', 'label' => 'Full Akses Real-time'],
                'purchasing' => ['status' => 'view', 'label' => 'Ringkasan Pengadaan'],
                'produksi' => ['status' => 'view', 'label' => 'Ringkasan WIP/Produksi'],
                'gudang' => ['status' => 'view', 'label' => 'Ringkasan 7 Gudang'],
                'sales' => ['status' => 'view', 'label' => 'Ringkasan Penjualan'],
                'kasir' => ['status' => 'none', 'label' => 'Dibatasi (Langsung POS)'],
                'finance' => ['status' => 'view', 'label' => 'Ringkasan Keuangan'],
            ],
            '2. Master Data (Produk, Kategori, Bahan)' => [
                'owner' => ['status' => 'full', 'label' => 'Full Akses Master'],
                'purchasing' => ['status' => 'limited', 'label' => 'Supplier & Bahan'],
                'produksi' => ['status' => 'limited', 'label' => 'Penjahit & BOM'],
                'gudang' => ['status' => 'limited', 'label' => 'SKU & Bahan Baku'],
                'sales' => ['status' => 'limited', 'label' => 'Pelanggan & Member'],
                'kasir' => ['status' => 'none', 'label' => 'Dibatasi'],
                'finance' => ['status' => 'limited', 'label' => 'COA Akuntansi'],
            ],
            '3. Purchasing & Penerimaan (GRN)' => [
                'owner' => ['status' => 'full', 'label' => 'Full Akses & Approval'],
                'purchasing' => ['status' => 'full', 'label' => 'Input PO & GRN'],
                'produksi' => ['status' => 'none', 'label' => 'Dibatasi'],
                'gudang' => ['status' => 'limited', 'label' => 'Verifikasi GRN Masuk'],
                'sales' => ['status' => 'none', 'label' => 'Dibatasi'],
                'kasir' => ['status' => 'none', 'label' => 'Dibatasi'],
                'finance' => ['status' => 'view', 'label' => 'Hutang Dagang PO'],
            ],
            '4. Multi-Warehouse 7 Gudang & Kartu Stok' => [
                'owner' => ['status' => 'full', 'label' => 'Full Akses & Otorisasi Opname'],
                'purchasing' => ['status' => 'view', 'label' => 'Cek Stok Bahan'],
                'produksi' => ['status' => 'limited', 'label' => 'Stok WIP & Transit'],
                'gudang' => ['status' => 'full', 'label' => 'Full Transfer, Kartu Stok, Opname'],
                'sales' => ['status' => 'view', 'label' => 'Cek Stok Siap Jual'],
                'kasir' => ['status' => 'limited', 'label' => 'Stok Toko Fisik (GD-TKO-01)'],
                'finance' => ['status' => 'view', 'label' => 'Valuasi HPP Persediaan'],
            ],
            '5. Produksi, SPK, Resep BOM & HPP' => [
                'owner' => ['status' => 'full', 'label' => 'Full Akses & Approval SPK'],
                'purchasing' => ['status' => 'none', 'label' => 'Dibatasi'],
                'produksi' => ['status' => 'full', 'label' => 'WO, SPK, BOM, Sisa 1.56 yd'],
                'gudang' => ['status' => 'none', 'label' => 'Dibatasi'],
                'sales' => ['status' => 'none', 'label' => 'Dibatasi'],
                'kasir' => ['status' => 'none', 'label' => 'Dibatasi'],
                'finance' => ['status' => 'view', 'label' => 'Simulasi & Validasi HPP'],
            ],
            '6. Quality Control (QC) & Barcode' => [
                'owner' => ['status' => 'full', 'label' => 'Full Akses'],
                'purchasing' => ['status' => 'none', 'label' => 'Dibatasi'],
                'produksi' => ['status' => 'full', 'label' => 'Inspeksi Transit, Reject, Rework'],
                'gudang' => ['status' => 'limited', 'label' => 'Cetak Barcode Thermal'],
                'sales' => ['status' => 'none', 'label' => 'Dibatasi'],
                'kasir' => ['status' => 'none', 'label' => 'Dibatasi'],
                'finance' => ['status' => 'none', 'label' => 'Dibatasi'],
            ],
            '7. Penjualan & Marketplace (Shopee/TikTok)' => [
                'owner' => ['status' => 'full', 'label' => 'Full Akses'],
                'purchasing' => ['status' => 'none', 'label' => 'Dibatasi'],
                'produksi' => ['status' => 'none', 'label' => 'Dibatasi'],
                'gudang' => ['status' => 'none', 'label' => 'Dibatasi'],
                'sales' => ['status' => 'full', 'label' => 'Order, Resi 4 Digit, Marketplace'],
                'kasir' => ['status' => 'none', 'label' => 'Dibatasi'],
                'finance' => ['status' => 'view', 'label' => 'Piutang & Pembayaran'],
            ],
            '8. Point of Sale (POS) Kasir Toko Fisik' => [
                'owner' => ['status' => 'full', 'label' => 'Full Akses & Otorisasi Void'],
                'purchasing' => ['status' => 'none', 'label' => 'Dibatasi'],
                'produksi' => ['status' => 'none', 'label' => 'Dibatasi'],
                'gudang' => ['status' => 'none', 'label' => 'Dibatasi'],
                'sales' => ['status' => 'limited', 'label' => 'Bantu Kasir'],
                'kasir' => ['status' => 'full', 'label' => 'Kasir POS, Multi-Pay, Tutup Shift'],
                'finance' => ['status' => 'view', 'label' => 'Rekonsiliasi Kas Toko'],
            ],
            '9. Return Paket (Reverse Logistics 4 Digit Resi)' => [
                'owner' => ['status' => 'full', 'label' => 'Full Akses'],
                'purchasing' => ['status' => 'none', 'label' => 'Dibatasi'],
                'produksi' => ['status' => 'none', 'label' => 'Dibatasi'],
                'gudang' => ['status' => 'limited', 'label' => 'Terima Fisik GD-TRN-03'],
                'sales' => ['status' => 'full', 'label' => 'Input Resi 4 Digit & Verifikasi Retur'],
                'kasir' => ['status' => 'none', 'label' => 'Dibatasi'],
                'finance' => ['status' => 'view', 'label' => 'Klaim / Refund Pelanggan'],
            ],
            '10. Finance & Kas/Bank' => [
                'owner' => ['status' => 'full', 'label' => 'Full Akses & Approval Pengeluaran'],
                'purchasing' => ['status' => 'none', 'label' => 'Dibatasi'],
                'produksi' => ['status' => 'none', 'label' => 'Dibatasi'],
                'gudang' => ['status' => 'none', 'label' => 'Dibatasi'],
                'sales' => ['status' => 'none', 'label' => 'Dibatasi'],
                'kasir' => ['status' => 'none', 'label' => 'Dibatasi'],
                'finance' => ['status' => 'full', 'label' => 'Kas/Bank, Hutang, Piutang, Biaya'],
            ],
            '11. Akuntansi Standar SAK ETAP' => [
                'owner' => ['status' => 'full', 'label' => 'Full Akses & Tutup Buku'],
                'purchasing' => ['status' => 'none', 'label' => 'Dibatasi'],
                'produksi' => ['status' => 'none', 'label' => 'Dibatasi'],
                'gudang' => ['status' => 'none', 'label' => 'Dibatasi'],
                'sales' => ['status' => 'none', 'label' => 'Dibatasi'],
                'kasir' => ['status' => 'none', 'label' => 'Dibatasi'],
                'finance' => ['status' => 'full', 'label' => 'COA, Jurnal, Buku Besar, Neraca, Laba Rugi, Arus Kas'],
            ],
            '12. Laporan & Ekspor Excel' => [
                'owner' => ['status' => 'full', 'label' => 'Semua Laporan & Ekspor'],
                'purchasing' => ['status' => 'limited', 'label' => 'Laporan Pembelian'],
                'produksi' => ['status' => 'limited', 'label' => 'Laporan Produksi'],
                'gudang' => ['status' => 'limited', 'label' => 'Laporan Stok Gudang'],
                'sales' => ['status' => 'limited', 'label' => 'Laporan Penjualan'],
                'kasir' => ['status' => 'none', 'label' => 'Dibatasi'],
                'finance' => ['status' => 'full', 'label' => 'Laporan Finansial & HPP'],
            ],
        ];

        return view('pages.master.users', compact('users', 'roleLabels', 'roleBadges', 'modules'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'role' => 'required|string|in:owner,purchasing,produksi,gudang,sales,kasir,finance',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'active';

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Pengguna baru berhasil ditambahkan dengan role hak akses!');
    }

    /**
     * Update user role and status.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => 'required|string|in:owner,purchasing,produksi,gudang,sales,kasir,finance',
            'status' => 'required|string|in:active,inactive',
        ]);

        $user->update($validated);

        return redirect()->route('users.index')->with('success', "Role dan status pengguna {$user->name} berhasil diperbarui!");
    }
}
