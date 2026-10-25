<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionReconciliation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'allocated_fabric_qty' => 'decimal:2',
        'standard_consumption_per_pcs' => 'decimal:2',
        'total_standard_consumption' => 'decimal:2',
        'theoretical_leftover' => 'decimal:2',
        'actual_leftover' => 'decimal:2',
        'waste_qty' => 'decimal:2',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'leftover_warehouse_id');
    }
}
