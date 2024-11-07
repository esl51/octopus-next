<?php

namespace App\Rules\Files;

use App\Services\ItemService;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class Filable implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $filableTypeRule = new FilableType;
        $filableTypeRule->validate('filable_type', $this->data['filable_type'], $fail);
        $service = call_user_func(['App\\Models\\'.$this->data['filable_type'], 'service']);
        if (! $service instanceof ItemService || ! $item = $service->get($this->data['filable_id'])) {
            $fail(trans('filable', [
                'attribute' => $attribute,
            ]));
        }
    }
}
