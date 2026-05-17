<?php

namespace Modules\Holding\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoldingCityPosition extends BaseModel
{
    public function holding(): BelongsTo
    {
        return $this->belongsTo(Holding::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
