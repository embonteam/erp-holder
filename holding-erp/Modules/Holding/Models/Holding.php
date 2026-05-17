<?php

namespace Modules\Holding\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Holding extends BaseModel
{
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }
}
