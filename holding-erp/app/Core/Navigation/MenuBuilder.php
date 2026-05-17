<?php

namespace App\Core\Navigation;

use App\Models\User;
use Illuminate\Support\Facades\Route;

class MenuBuilder
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function forUser(?User $user): array
    {
        if ($user === null) {
            return [];
        }

        return collect(config('navigation.items', []))
            ->filter(fn (array $item): bool => Route::has($item['route']))
            ->filter(fn (array $item): bool => ! isset($item['permission']) || $user->hasPermission($item['permission']))
            ->values()
            ->all();
    }
}
