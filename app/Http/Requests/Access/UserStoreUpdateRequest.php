<?php

namespace App\Http\Requests\Access;

use App\Http\Requests\ItemStoreUpdateRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UserStoreUpdateRequest extends ItemStoreUpdateRequest
{
    public function rules(): array
    {
        return $this->prepareRules([
            'name' => 'required|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                $this->getUniqueEmailRule(),
            ],
            'avatar' => 'nullable|mimes:jpeg,png',
            'password' => ($this->item ? 'nullable' : 'required').'|min:8',
            'roles' => 'nullable|array',
            'roles.*' => 'integer',
        ]);
    }

    protected function getUniqueEmailRule(): Unique
    {
        $uniqueRule = Rule::unique('users', 'email');

        if ($this->item) {
            $uniqueRule->ignore($this->item->id);
        }

        return $uniqueRule;
    }

    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'name' => trans('validation.attributes.person_name'),
        ]);
    }
}
