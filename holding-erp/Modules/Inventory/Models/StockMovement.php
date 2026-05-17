<?php

namespace Modules\Inventory\Models;

use App\Core\Models\ScopedModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Holding\Models\Warehouse;

class StockMovement extends ScopedModel
{
    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
