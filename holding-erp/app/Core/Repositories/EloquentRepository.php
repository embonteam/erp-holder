<?php

namespace App\Core\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentRepository implements RepositoryInterface
{
    abstract protected function model(): Model;

    public function find(int|string $id): ?Model
    {
        return $this->model()->newQuery()->find($id);
    }

    public function all(): Collection
    {
        return $this->model()->newQuery()->get();
    }

    public function create(array $attributes): Model
    {
        return $this->model()->newQuery()->create($attributes);
    }

    public function update(Model $model, array $attributes): Model
    {
        $model->fill($attributes)->save();

        return $model->refresh();
    }

    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }
}
