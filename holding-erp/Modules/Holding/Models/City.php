<?php

namespace Modules\Holding\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class City extends BaseModel
{
    public function holding(): BelongsTo
    {
        return $this->belongsTo(Holding::class);
    }

    public function holdingCityPosition(): HasOne
    {
        return $this->hasOne(HoldingCityPosition::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}
