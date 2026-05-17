<?php

namespace Modules\Audit\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends BaseModel
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
