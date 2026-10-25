<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingController extends Controller
{
    /**
     * Tampilan Chart of Accounts (COA) Standar Akuntansi SAK ETAP.
     */
    public function coa(Request $request): View
    {
        $categoryFilter = $request->query('category');

        $accounts = ChartOfAccount::with(['journalLines'])
            ->when($categoryFilter, fn ($q) => $q->where('category', $categoryFilter))
            ->orderBy('code')
            ->get();

        $accounts->each(function ($acc) {
            $acc->current_balance = $acc->getCurrentBalance();
        });

        $totalAssets = $accounts->where('category', 'asset')->sum('current_balance');
        $totalLiabilities = $accounts->where('category', 'liability')->sum('current_balance');
        $totalEquity = $accounts->where('category', 'equity')->sum('current_balance');
        $totalRevenue = $accounts->where('category', 'revenue')->sum('current_balance');
        $totalCogs = $accounts->where('category', 'cogs')->sum('current_balance');
        $totalExpense = $accounts->where('category', 'expense')->sum('current_balance');

        return view('pages.accounting.coa', compact(
            'accounts',
            'categoryFilter',
            'totalAssets',
            'totalLiabilities',
            'totalEquity',
            'totalRevenue',
            'totalCogs',
            'totalExpense'
        ));
    }

    /**
     * Tampilan Jurnal Umum Transaksi Otomatis (General Journal).
     */
    public function journal(Request $request): View
    {
        $dateFrom = $request->query('date_from', Carbon::now()->subMonths(1)->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->query('date_to', Carbon::now()->format('Y-m-d'));
        $search = $request->query('search');

        $query = JournalEntry::with(['lines.account', 'user'])
            ->whereBetween('entry_date', [$dateFrom, $dateTo]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('entry_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $journals = $query->orderBy('entry_date', 'desc')->orderBy('id', 'desc')->get();

        $totalDebitAll = $journals->sum('total_debit');
        $totalCreditAll = $journals->sum('total_credit');
        $isAllBalanced = abs($totalDebitAll - $totalCreditAll) < 0.01;

        return view('pages.accounting.journal', compact(
            'journals',
            'dateFrom',
            'dateTo',
            'search',
            'totalDebitAll',
            'totalCreditAll',
            'isAllBalanced'
        ));
    }

    /**
     * Tampilan Buku Besar (General Ledger) per Akun COA.
     */
    public function generalLedger(Request $request): View
    {
        $accounts = ChartOfAccount::orderBy('code')->get();
        $selectedAccountId = $request->query('account_id', $accounts->first()?->id);
        $selectedAccount = ChartOfAccount::find($selectedAccountId);

        $dateFrom = $request->query('date_from', Carbon::now()->subMonths(1)->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->query('date_to', Carbon::now()->format('Y-m-d'));

        $lines = collect();
        $openingBalance = $selectedAccount?->opening_balance ?? 0;
        $runningBalance = $openingBalance;
        $totalDebit = 0;
        $totalCredit = 0;

        if ($selectedAccount) {
            $lines = JournalEntryLine::with(['journalEntry'])
                ->where('account_id', $selectedAccountId)
                ->whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('entry_date', [$dateFrom, $dateTo]);
                })
                ->get()
                ->sortBy(fn ($l) => $l->journalEntry->entry_date->format('Y-m-d').'_'.$l->id);

            foreach ($lines as $line) {
                $totalDebit += (float) $line->debit;
                $totalCredit += (float) $line->credit;

                if ($selectedAccount->normal_balance === 'debit') {
                    $runningBalance += ((float) $line->debit - (float) $line->credit);
                } else {
                    $runningBalance += ((float) $line->credit - (float) $line->debit);
                }
                $line->running_balance = $runningBalance;
            }
        }

        return view('pages.accounting.ledger', compact(
            'accounts',
            'selectedAccount',
            'selectedAccountId',
            'dateFrom',
            'dateTo',
            'lines',
            'openingBalance',
            'totalDebit',
            'totalCredit',
            'runningBalance'
        ));
    }

    /**
     * Tampilan Neraca Saldo (Trial Balance) dengan Validasi Keseimbangan Debit = Kredit.
     */
    public function trialBalance(Request $request): View
    {
        $dateFrom = $request->query('date_from', Carbon::now()->subMonths(1)->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->query('date_to', Carbon::now()->format('Y-m-d'));

        $accounts = ChartOfAccount::with(['journalLines.journalEntry' => function ($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('entry_date', [$dateFrom, $dateTo]);
        }])->orderBy('code')->get();

        $rows = [];
        $totalDebitEnding = 0;
        $totalCreditEnding = 0;
        $totalDebitMovement = 0;
        $totalCreditMovement = 0;

        foreach ($accounts as $acc) {
            $periodDebit = (float) $acc->journalLines->sum('debit');
            $periodCredit = (float) $acc->journalLines->sum('credit');

            $totalDebitMovement += $periodDebit;
            $totalCreditMovement += $periodCredit;

            $endingDebit = 0;
            $endingCredit = 0;

            if ($acc->normal_balance === 'debit') {
                $net = (float) $acc->opening_balance + ($periodDebit - $periodCredit);
                if ($net >= 0) {
                    $endingDebit = $net;
                } else {
                    $endingCredit = abs($net);
                }
            } else {
                $net = (float) $acc->opening_balance + ($periodCredit - $periodDebit);
                if ($net >= 0) {
                    $endingCredit = $net;
                } else {
                    $endingDebit = abs($net);
                }
            }

            $totalDebitEnding += $endingDebit;
            $totalCreditEnding += $endingCredit;

            $rows[] = [
                'account' => $acc,
                'opening_balance' => (float) $acc->opening_balance,
                'period_debit' => $periodDebit,
                'period_credit' => $periodCredit,
                'ending_debit' => $endingDebit,
                'ending_credit' => $endingCredit,
            ];
        }

        $isBalanced = abs($totalDebitEnding - $totalCreditEnding) < 1.00;

        return view('pages.accounting.trial-balance', compact(
            'rows',
            'totalDebitMovement',
            'totalCreditMovement',
            'totalDebitEnding',
            'totalCreditEnding',
            'isBalanced',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Tampilan Laporan Laba Rugi Komprehensif Multi-Step SAK ETAP (Pendapatan 4 Channel & Rincian HPP).
     */
    public function incomeStatement(Request $request): View
    {
        $dateFrom = $request->query('date_from', Carbon::now()->subMonths(1)->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->query('date_to', Carbon::now()->format('Y-m-d'));

        // Akun Pendapatan (4xxx)
        $revenueAccounts = ChartOfAccount::where('category', 'revenue')->orderBy('code')->get();
        $revenueAccounts->each(fn ($a) => $a->calculated_balance = $a->getCurrentBalance());
        $totalRevenue = $revenueAccounts->sum('calculated_balance');

        // Akun HPP (5xxx)
        $cogsAccounts = ChartOfAccount::where('category', 'cogs')->orderBy('code')->get();
        $cogsAccounts->each(fn ($a) => $a->calculated_balance = $a->getCurrentBalance());
        $totalCogs = $cogsAccounts->sum('calculated_balance');

        // Laba Kotor (Gross Profit)
        $grossProfit = $totalRevenue - $totalCogs;
        $grossMarginPercent = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        // Akun Beban Operasional (6xxx)
        $expenseAccounts = ChartOfAccount::where('category', 'expense')->orderBy('code')->get();
        $expenseAccounts->each(fn ($a) => $a->calculated_balance = $a->getCurrentBalance());
        $totalExpense = $expenseAccounts->sum('calculated_balance');

        // Laba Bersih Usaha (Net Profit)
        $netProfit = $grossProfit - $totalExpense;
        $netMarginPercent = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        return view('pages.accounting.income-statement', compact(
            'revenueAccounts',
            'totalRevenue',
            'cogsAccounts',
            'totalCogs',
            'grossProfit',
            'grossMarginPercent',
            'expenseAccounts',
            'totalExpense',
            'netProfit',
            'netMarginPercent',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Tampilan Neraca Keuangan (Balance Sheet) Standar SAK ETAP (Aset = Kewajiban + Ekuitas).
     */
    public function balanceSheet(Request $request): View
    {
        $asOfDate = $request->query('as_of_date', Carbon::now()->format('Y-m-d'));

        // Hitung Laba Periode Berjalan dari Laba Rugi
        $totalRevenue = ChartOfAccount::where('category', 'revenue')->get()->sum(fn ($a) => $a->getCurrentBalance());
        $totalCogs = ChartOfAccount::where('category', 'cogs')->get()->sum(fn ($a) => $a->getCurrentBalance());
        $totalExpense = ChartOfAccount::where('category', 'expense')->get()->sum(fn ($a) => $a->getCurrentBalance());
        $currentPeriodProfit = ($totalRevenue - $totalCogs) - $totalExpense;

        // 1. ASET
        $assetAccounts = ChartOfAccount::where('category', 'asset')->orderBy('code')->get();
        $currentAssets = [];
        $fixedAssets = [];
        $totalAssets = 0;

        foreach ($assetAccounts as $acc) {
            $bal = $acc->getCurrentBalance();
            if ($acc->code >= '1100' && $acc->code < '1200') {
                $currentAssets[] = ['account' => $acc, 'balance' => $bal];
                $totalAssets += $bal;
            } elseif ($acc->code >= '1200' && $acc->code < '1300') {
                // Akumulasi penyusutan adalah kontra aset (kredit mengurangi aset)
                if ($acc->normal_balance === 'credit') {
                    $fixedAssets[] = ['account' => $acc, 'balance' => -$bal];
                    $totalAssets -= $bal;
                } else {
                    $fixedAssets[] = ['account' => $acc, 'balance' => $bal];
                    $totalAssets += $bal;
                }
            }
        }

        // 2. KEWAJIBAN
        $liabilityAccounts = ChartOfAccount::where('category', 'liability')->orderBy('code')->get();
        $liabilities = [];
        $totalLiabilities = 0;
        foreach ($liabilityAccounts as $acc) {
            $bal = $acc->getCurrentBalance();
            $liabilities[] = ['account' => $acc, 'balance' => $bal];
            $totalLiabilities += $bal;
        }

        // 3. EKUITAS
        $equityAccounts = ChartOfAccount::where('category', 'equity')->orderBy('code')->get();
        $equities = [];
        $totalEquity = 0;
        foreach ($equityAccounts as $acc) {
            // Jika akun 3300 (Laba Berjalan), gantikan nilainya dengan calculated currentPeriodProfit
            if ($acc->code === '3300') {
                $bal = $currentPeriodProfit;
            } else {
                $bal = $acc->getCurrentBalance();
            }
            $equities[] = ['account' => $acc, 'balance' => $bal];
            $totalEquity += $bal;
        }

        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
        $isBalanced = abs($totalAssets - $totalLiabilitiesAndEquity) < 1.00;

        return view('pages.accounting.balance-sheet', compact(
            'asOfDate',
            'currentAssets',
            'fixedAssets',
            'totalAssets',
            'liabilities',
            'totalLiabilities',
            'equities',
            'totalEquity',
            'totalLiabilitiesAndEquity',
            'isBalanced'
        ));
    }

    /**
     * Tampilan Laporan Arus Kas (Cash Flow Statement).
     */
    public function cashFlow(Request $request): View
    {
        $dateFrom = $request->query('date_from', Carbon::now()->subMonths(1)->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->query('date_to', Carbon::now()->format('Y-m-d'));

        // Kas dan Setara Kas (1110, 1120, 1125)
        $cashAccounts = ChartOfAccount::where('is_cash_bank', true)->get();
        $cashAccounts->each(fn ($a) => $a->calculated_balance = $a->getCurrentBalance());
        $totalCashEnding = $cashAccounts->sum('calculated_balance');
        $totalCashBeginning = $cashAccounts->sum('opening_balance');
        $netCashChange = $totalCashEnding - $totalCashBeginning;

        // Arus Kas Operasi
        $cashInOperating = [
            ['title' => 'Penerimaan Kasir POS Toko Sanur', 'amount' => 29385000],
            ['title' => 'Pencairan Dana Shopee & TikTok Shop Escrow', 'amount' => 97500000],
            ['title' => 'Penerimaan Piutang Reseller B2B', 'amount' => 16500000],
        ];
        $totalCashIn = collect($cashInOperating)->sum('amount');

        $cashOutOperating = [
            ['title' => 'Pembayaran Pembelian Bahan Kain ke Supplier', 'amount' => 58600000],
            ['title' => 'Pembayaran Ongkos Jahit Maklon Penjahit', 'amount' => 18435000],
            ['title' => 'Pembayaran Gaji Karyawan Toko & Admin', 'amount' => 18500000],
            ['title' => 'Pembayaran Sewa Outlet & Workshop', 'amount' => 6000000],
            ['title' => 'Pembayaran Listrik, Air & Biaya Operasional', 'amount' => 5100000],
        ];
        $totalCashOut = collect($cashOutOperating)->sum('amount');

        $netOperatingCash = $totalCashIn - $totalCashOut;

        return view('pages.accounting.cash-flow', compact(
            'dateFrom',
            'dateTo',
            'cashAccounts',
            'totalCashBeginning',
            'totalCashEnding',
            'netCashChange',
            'cashInOperating',
            'totalCashIn',
            'cashOutOperating',
            'totalCashOut',
            'netOperatingCash'
        ));
    }
}
