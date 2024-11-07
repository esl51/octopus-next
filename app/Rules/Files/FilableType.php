<?php

namespace App\Rules\Files;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FilableType implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $className = 'App\\Models\\'.$value;
        if (! class_exists($className) || ! method_exists($className, 'service')) {
            $fail(trans('filable_type', [
                'attribute' => $attribute,
            ]));
        }
    }
}
