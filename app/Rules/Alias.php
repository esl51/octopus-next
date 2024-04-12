<?php

namespace App\Rules;

use Arr;
use Illuminate\Contracts\Validation\ValidationRule;

class Alias implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (
            Arr::has(config('app.locales'), $value) ||
            preg_match("/^[a-z0-9]{1}[a-z0-9_]*$/", $value) !== 1
        ) {
            $fail('validation.alias')->translate();
        }
    }
}
