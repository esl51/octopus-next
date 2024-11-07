<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class ItemService
{
    protected ?string $class = null;

    protected array $with = [];

    protected array $withCount = [];

    protected bool $search = true;

    public function __construct(string $modelClass)
    {
        $this->class = $modelClass ?: self::guessClass();
    }

    private static function guessClass(): string
    {
        return preg_replace('/(.+)\\\\Services\\\\(.+)Service$/m', '$1\Models\\\$2', static::class);
    }

    protected function sortByReplacements(): array
    {
        return [];
    }

    protected function sortByTranslations(): array
    {
        return [];
    }

    protected function initQuery(): Builder
    {
        return call_user_func([$this->class, 'query']);
    }

    protected function addConditions(Builder $query): Builder
    {
        return $query;
    }

    protected function handleOrder(
        array $params,
        Builder $items,
        string $table = '',
        bool $sortable = false,
        array $replacements = [],
        array $translations = []
    ): Builder {
        $sortBy = $params['sort_by'] ?? null;
        if ($sortBy) {
            $table = (app($this->class))->getTable();
            $columns = array_map(
                fn ($item) => str_replace($table.'.', '', $item),
                $this->class::getColumns()
            );
            $sortDirection = ! empty($params['sort_desc']) ? 'desc' : 'asc';
            if (! empty($replacements[$sortBy])) {
                $items->orderBy($replacements[$sortBy], $sortDirection);
            } elseif (! empty($translations[$sortBy])) {
                $items->orderByTranslation($sortBy, $sortDirection);
                $items->groupBy($translations[$sortBy]);
            } elseif (in_array($sortBy, $columns)) {
                $items->orderBy(($table ? $table.'.' : '').$sortBy, $sortDirection);
            }
        }
        if ($sortable) {
            $items->sorted();
        }

        return $items;
    }

    public function newItemsQuery(array $params): Builder
    {
        $table = (new $this->class)->getTable();
        $columns = $this->class::getColumns();

        $items = $this->initQuery();
        $items->select($columns)->groupBy($columns);

        $items = $this->addConditions($items);
        $with = $this->with;
        if (method_exists($this->class, 'translations')) {
            $with[] = 'translations';
            //$items = call_user_func([$items, 'withTranslation']);
        }
        if (! empty($with)) {
            $items->with($with);
        }

        if (! empty($this->withCount)) {
            $items->withCount($this->withCount);
        }

        $id = $params['id'] ?? null;
        $search = $params['search'] ?? null;
        $filteredById = false;
        if ($id && ! $search) {
            $items->where($table.'.id', (int) $id);
            $filteredById = true;
        }

        if ($this->search && ! $filteredById) {
            if (is_numeric($search)) {
                $items->where($table.'.id', $search);
            }
        }

        $items = $this->handleOrder(
            $params,
            $items,
            $table,
            method_exists($this->class, 'scopeSorted'),
            $this->sortByReplacements(),
            $this->sortByTranslations(),
        );

        return $items;
    }

    public function get(int $id, bool $loadRelations = false): Model
    {
        $item = $this->initQuery();
        $item = $this->addConditions($item);
        if ($loadRelations) {
            $columns = $this->class::getColumns();
            $item->select($columns)->groupBy($columns);
            $with = $this->with;
            // load all translations for modifying purposes
            if (method_exists($this->class, 'translations')) {
                $with[] = 'translations';
            }
            if (! empty($with)) {
                $item = $item->with($with);
            }
        }

        return $item->find($id);
    }

    public function store(array $data, ?array $files = null): Model
    {
        $item = $this->initQuery()->create($data);

        if (method_exists($item, 'handleFiles')) {
            $item->handleFiles($files);
        }

        return $item;
    }

    public function update(int $id, array $data, ?array $files = null): Model
    {
        $item = $this->get($id);
        $item->update($data);

        if (method_exists($item, 'handleFiles')) {
            $item->handleFiles($files);
        }

        return $item;
    }

    public function destroy(int $id): void
    {
        $item = $this->get($id);
        $item->delete();
    }

    public function moveBefore(int $id, int $beforeId): Model
    {
        $item = $this->get($id);
        $itemBefore = $this->get($beforeId);
        $item->moveBefore($itemBefore);

        return $item;
    }

    public function moveAfter(int $id, int $afterId): Model
    {
        $item = $this->get($id);
        $itemAfter = $this->get($afterId);
        $item->moveAfter($itemAfter);

        return $item;
    }
}
