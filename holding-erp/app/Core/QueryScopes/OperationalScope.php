<?php

namespace App\Core\QueryScopes;

use App\Core\Support\Scoping\ScopeContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OperationalScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $context = app(ScopeContext::class);

        foreach ($model->operationalScopeColumns() as $column) {
            if ($context->has($column)) {
                $builder->where($model->qualifyColumn($column), $context->get($column));
            }
        }
    }
}
