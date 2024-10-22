<?php

namespace App\Http\Requests\Access;

use App\Http\Requests\ItemStoreUpdateRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class PermissionStoreUpdateRequest extends ItemStoreUpdateRequest
{
    protected array $fillable = [
        'name',
        'guard_name',
    ];

    public function rules(): array
    {
        return $this->prepareRules([
            'name' => [
                'required',
                'string',
                'max:255',
                $this->getUniqueNameRule(),
            ],
            'guard_name' => 'required|string|max:255',
        ]);
    }

    protected function getUniqueNameRule(): Unique
    {
        $uniqueRule = Rule::unique('permissions')->where('guard_name', $this->guard_name);

        if ($this->item) {
            $uniqueRule->ignore($this->item->id);
        }

        return $uniqueRule;
    }
}
