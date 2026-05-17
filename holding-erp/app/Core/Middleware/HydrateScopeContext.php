<?php

namespace App\Core\Middleware;

use App\Core\Support\Scoping\ScopeContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HydrateScopeContext
{
    public function __construct(private readonly ScopeContext $scopeContext)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null) {
            $this->scopeContext->hydrate([
                'holding_id' => $user->holding_id,
                'brand_id' => $user->brand_id,
                'city_id' => $user->city_id,
                'branch_id' => $user->branch_id,
                'warehouse_id' => $user->warehouse_id,
            ]);
        }

        return $next($request);
    }
}
