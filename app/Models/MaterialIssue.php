<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialIssue extends Model
{
    protected $guarded = [];

    protected $casts = [
        'issue_date' => 'datetime',
        'qty_allocated' => 'decimal:2',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function tailor(): BelongsTo
    {
        return $this->belongsTo(Tailor::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'raw_material_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
