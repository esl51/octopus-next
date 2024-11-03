<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CollectionBelongsToMany extends Relation
{
    protected Collection $relatedItems;
    protected Collection $pivotItems;
    protected $foreignKey;
    protected $relatedKey;
    protected $pivotTable;
    protected bool $withPivot = false;

    public function __construct($parent, Collection $relatedItems, $pivotTable, $foreignKey, $relatedKey)
    {
        $this->relatedItems = $relatedItems;
        $this->pivotItems = collect();
        $this->pivotTable = $pivotTable;
        $this->foreignKey = $foreignKey;
        $this->relatedKey = $relatedKey;
        parent::__construct($parent->newQuery(), $parent);
    }

    public function addConstraints()
    {
        //
    }

    public function addEagerConstraints(array $models)
    {
        $keys = collect($models)->pluck($models[0]->getKeyName())->unique()->all();

        $this->pivotItems = DB::table($this->pivotTable)
            ->whereIn($this->foreignKey, $keys)
            ->get();

        $relatedKeys = $this->pivotItems->pluck($this->relatedKey)->unique()->all();
        $this->relatedItems = $this->relatedItems->whereIn('id', $relatedKeys);
    }

    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->getRelatedFor($model));
        }

        return $models;
    }

    public function match(array $models, Collection $results, $relation)
    {
        $groupedPivot = $this->pivotItems->groupBy($this->foreignKey);
        foreach ($models as $model) {
            $key = $model->getKey();
            $relatedItems = collect();

            if ($groupedPivot->has($key)) {
                foreach ($groupedPivot[$key] as $pivot) {
                    $relatedItem = $this->relatedItems->whereIn('id', $pivot->{$this->relatedKey})->first();
                    if ($this->withPivot) {
                        $relatedItem->pivot = $pivot;
                    }
                    $relatedItems->push($relatedItem);
                }
            }

            $model->setRelation($relation, $relatedItems);
        }

        return $models;
    }

    public function getResults()
    {
        return $this->relatedItems;
    }

    protected function getRelatedFor($model)
    {
        $relatedIds = $this->pivotItems->where(
            $this->foreignKey,
            $model->getKey()
        )->pluck($this->relatedKey);
        return $this->relatedItems->whereIn('id', $relatedIds);
    }

    public function sync(array $ids)
    {
        $existingIds = $this->pivotItems->pluck($this->relatedKey)->all();

        $toAttach = array_diff($ids, $existingIds);
        $toDetach = array_diff($existingIds, $ids);

        foreach ($toAttach as $id) {
            DB::table($this->pivotTable)->insert([
                $this->foreignKey => $this->parent->getKey(),
                $this->relatedKey => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($toDetach as $id) {
            DB::table($this->pivotTable)
                ->where($this->foreignKey, $this->parent->getKey())
                ->where($this->relatedKey, $id)
                ->delete();
        }

        $this->addEagerConstraints([$this->parent]);
    }

    public function withPivot()
    {
        $this->withPivot = true;
        return $this;
    }
}
