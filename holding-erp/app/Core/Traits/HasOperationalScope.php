<?php

namespace App\Core\Traits;

use App\Core\QueryScopes\OperationalScope;
use Illuminate\Database\Eloquent\Model;

trait HasOperationalScope
{
    protected static function bootHasOperationalScope(): void
    {
        static::addGlobalScope(new OperationalScope());

        static::creating(function (Model $model): void {
            $context = app(\App\Core\Support\Scoping\ScopeContext::class);

            foreach ($model->operationalScopeColumns() as $column) {
                if ($model->getAttribute($column) === null && $context->has($column)) {
                    $model->setAttribute($column, $context->get($column));
                }
            }
        });
    }
}
