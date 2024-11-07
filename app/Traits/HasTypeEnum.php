<?php

namespace App\Traits;

use Illuminate\Support\Collection;
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
        $namespaceParts = array_splice($className, 0, -1);
        $prefix = end($namespaceParts);
        if ($prefix == 'Models') {
            $prefix = 'App';
        }
        $prefix = Str::snake($prefix);
        $typeName = Str::snake(Str::pluralStudly(class_basename(self::class)));

        return trans($prefix.'.'.$typeName.'.'.str_replace('type_', '', $this->alias()));
    }

    public function asObject(): stdClass
    {
        return (object) $this->asArray();
    }

    public function asArray(): array
    {
        $methods = array_filter(
            get_class_methods($this),
            fn ($item) => ! in_array($item, ['from', 'tryFrom', 'cases', 'asArray', 'asObject', 'collect'])
        );

        $array = [
            'id' => $this->value,
        ];

        foreach ($methods as $method) {
            $array[Str::snake($method)] = $this->$method();
        }

        return $array;
    }

    public static function collect(): Collection
    {
        return collect(self::cases())->map(fn ($item) => $item->asObject());
    }
}
