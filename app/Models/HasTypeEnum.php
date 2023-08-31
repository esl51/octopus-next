<?php

namespace App\Models;

use Str;

trait HasTypeEnum
{
    public function alias()
    {
        return strtolower($this->name);
    }

    public function name()
    {
        $className = explode('\\', self::class);
        $typeName = Str::snake(preg_replace('/Type$/', '', end($className)));
        return trans('type.' . $typeName . '.' . $this->alias());
    }

    public function asObject()
    {
        return (object) $this->asArray();
    }

    public function asArray()
    {
        $methods = array_filter(
            get_class_methods($this),
            fn ($item) => !in_array($item, ['from', 'tryFrom', 'cases', 'asArray', 'asObject'])
        );

        $array = [
            'id' => $this->value,
        ];

        foreach ($methods as $method) {
            $array[Str::snake($method)] = $this->$method();
        }

        return $array;
    }
}
