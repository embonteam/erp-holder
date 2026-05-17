<?php

namespace Modules\Inventory\Models;

use App\Core\Models\ScopedModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Holding\Models\Brand;

class Product extends ScopedModel
{
    public function operationalScopeColumns(): array
    {
        return ['brand_id'];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }
}
