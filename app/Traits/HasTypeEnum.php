<?php

namespace App\Traits;

use stdClass;
use Str;

trait HasTypeEnum
{
    public function alias(): string
    {
        return strtolower($this->name);
    }

    public function name(): string
    {
        $className = explode('\\', self::class);
        $typeName = Str::snake(preg_replace('/Type$/', '', end($className)));
        return trans('type.' . $typeName . '.' . $this->alias());
    }

    public function asObject(): stdClass
    {
        return (object) $this->asArray();
    }

    public function asArray(): array
    {
        $methods = array_filter(
            get_class_methods($this),
            fn($item) => !in_array($item, ['from', 'tryFrom', 'cases', 'asArray', 'asObject'])
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
