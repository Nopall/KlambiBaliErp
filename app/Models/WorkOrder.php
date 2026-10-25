<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'finish_date' => 'date',
        'sewing_cost_per_pcs' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function tailor(): BelongsTo
    {
        return $this->belongsTo(Tailor::class);
    }

    public function materialIssues(): HasMany
    {
        return $this->hasMany(MaterialIssue::class);
    }

    public function warehouseWip(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_wip_id');
    }

    public function reconciliation(): HasOne
    {
        return $this->hasOne(ProductionReconciliation::class);
    }

    public function reconciliations(): HasMany
    {
        return $this->hasMany(ProductionReconciliation::class);
    }

    public function qcInspections(): HasMany
    {
        return $this->hasMany(QcInspection::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
