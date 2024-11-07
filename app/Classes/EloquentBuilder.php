<?php

namespace App\Classes;

use Closure;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Roquie\LaravelPerPageResolver\Builder;

class EloquentBuilder extends Builder
{
    protected function eagerLoadRelation(array $models, $name, Closure $constraints): array
    {
        try {
            return parent::eagerLoadRelation($models, $name, $constraints);
        } catch (RelationNotFoundException $e) {
            // The eager loading request is not a relation - is it a pivot?
            if (head($models)->{$name} instanceof Pivot) {
                $relations = array_filter(array_keys($this->eagerLoad), function ($relation) use ($name) {
                    return $relation != $name && Str::startsWith($relation, $name);
                });

                $pivots = $this->getModel()->newCollection(
                    Arr::pluck($models, $name)
                );

                $pivots->load(array_map(function ($relation) use ($name) {
                    return substr($relation, strlen($name) + 1);
                }, $relations));

                return $models;
            }

            throw $e;
        }
    }
}
