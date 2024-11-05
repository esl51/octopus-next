<?php

namespace App\Traits;

use Str;

trait HasService
{
    public static function resolveServiceName(string $modelName)
    {
        $modelName = Str::after($modelName, 'App\\Models\\');
        return 'App\\Services\\' . $modelName . 'Service';
    }

    public static function service()
    {
        $service = static::resolveServiceName(static::class);
        return new $service(static::class);
    }
}
