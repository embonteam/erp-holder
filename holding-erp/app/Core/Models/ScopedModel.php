<?php

namespace App\Core\Models;

use App\Core\Traits\HasOperationalScope;

abstract class ScopedModel extends BaseModel
{
    use HasOperationalScope;

    /**
     * @return array<int, string>
     */
    public function operationalScopeColumns(): array
    {
        return ['brand_id', 'city_id', 'branch_id', 'warehouse_id'];
    }
}
