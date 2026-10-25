<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChartOfAccount extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_cash_bank' => 'boolean',
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    /**
     * Get current running balance for this account based on normal balance.
     */
    public function getCurrentBalance(): float
    {
        $totalDebit = (float) $this->journalLines()->sum('debit');
        $totalCredit = (float) $this->journalLines()->sum('credit');

        if ($this->normal_balance === 'debit') {
            return (float) $this->opening_balance + ($totalDebit - $totalCredit);
        }

        return (float) $this->opening_balance + ($totalCredit - $totalDebit);
    }
}
