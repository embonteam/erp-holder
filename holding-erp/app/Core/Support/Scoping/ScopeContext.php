<?php

namespace App\Core\Support\Scoping;

class ScopeContext
{
    /**
     * @var array<string, int|null>
     */
    private array $scope = [
        'holding_id' => null,
        'brand_id' => null,
        'city_id' => null,
        'branch_id' => null,
        'warehouse_id' => null,
    ];

    /**
     * @param array<string, int|null> $scope
     */
    public function hydrate(array $scope): void
    {
        $this->scope = array_merge($this->scope, $scope);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->scope) && $this->scope[$key] !== null;
    }

    public function get(string $key): ?int
    {
        return $this->scope[$key] ?? null;
    }

    /**
     * @return array<string, int|null>
     */
    public function all(): array
    {
        return $this->scope;
    }
}
