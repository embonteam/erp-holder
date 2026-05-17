<?php

namespace Modules\Purchasing\Repositories;

use App\Core\Repositories\EloquentRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Purchasing\Models\Supplier;

class SupplierRepository extends EloquentRepository
{
    protected function model(): Model
    {
        return new Supplier();
    }
}
