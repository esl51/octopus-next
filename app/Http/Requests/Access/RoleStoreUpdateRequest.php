<?php

namespace App\Http\Requests\Access;

use App\Http\Requests\ItemStoreUpdateRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class RoleStoreUpdateRequest extends ItemStoreUpdateRequest
{
    protected array $fillable = [
        'name',
        'guard_name',
    ];

    protected array $fillableTranslations = [
        'title',
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
            '%title%' => 'required_if_fallback:nullable|string|max:255',
            'guard_name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer',
        ]);
    }

    protected function getUniqueNameRule(): Unique
    {
        $uniqueRule = Rule::unique('roles')->where('guard_name', $this->guard_name);

        if ($this->item) {
            $uniqueRule->ignore($this->item->id);
        }

        return $uniqueRule;
    }
}
