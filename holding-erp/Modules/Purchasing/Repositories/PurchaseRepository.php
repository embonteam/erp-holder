<?php

namespace Modules\Purchasing\Repositories;

use App\Core\Repositories\EloquentRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Purchasing\Models\Purchase;

class PurchaseRepository extends EloquentRepository
{
    protected function model(): Model
    {
        return new Purchase();
    }
}
