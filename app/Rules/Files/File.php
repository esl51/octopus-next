<?php

namespace App\Rules\Files;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Validator;

class File implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $fileTypeRule = new FileType;
        $fileTypeRule->setData($this->data);
        $fileTypeRule->validate('type', $this->data['type'], $fail);
        $className = 'App\\Models\\'.$this->data['filable_type'];
        $item = new $className;
        $extensions = $item->getFileTypes()[$this->data['type']]['extensions'];
        $fileValidator = Validator::make($this->data, [
            $attribute => 'extensions:'.$extensions,
        ]);
        $fileValidator->validate();
    }
}
