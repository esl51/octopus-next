<?php

namespace App\Models;

abstract class Type
{
    public $id;
    public $name;

    abstract protected static function items(): array;

    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Find one type by id.
     *
     * @param mixed $id Type id
     * @return static Type
     */
    public static function find($id)
    {
        return static::collect()->first(function ($value) use ($id) {
            return $value->id == $id;
        });
    }

    /**
     * Get all types as collection.
     *
     * @return \Illuminate\Support\Collection Collection of types
     */
    public static function collect()
    {
        return collect(static::items())->map(function ($item) {
            return new static($item);
        });
    }
}
