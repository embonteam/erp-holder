<?php

namespace Modules\Inventory\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Holding\Models\Brand;

class Category extends BaseModel
{
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
