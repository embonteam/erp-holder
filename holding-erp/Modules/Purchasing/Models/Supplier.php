<?php

namespace Modules\Purchasing\Models;

use App\Core\Models\ScopedModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Holding\Models\Holding;

class Supplier extends ScopedModel
{
    /**
     * @return array<int, string>
     */
    public function operationalScopeColumns(): array
    {
        return ['holding_id'];
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function holding(): BelongsTo
    {
        return $this->belongsTo(Holding::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
