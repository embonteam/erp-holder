<?php

namespace Modules\Inventory\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUnit extends BaseModel
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
