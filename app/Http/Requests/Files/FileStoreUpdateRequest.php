<?php

namespace App\Http\Requests\Files;

use App\Http\Requests\ItemStoreUpdateRequest;

class FileStoreUpdateRequest extends ItemStoreUpdateRequest
{
    protected array $fillable = [
        'original_name',
    ];

    protected array $fillableTranslations = [
        'title',
    ];

    public function rules(): array
    {
        return $this->prepareRules([
            'original_name' => 'required|string|max:255',
            '%title%' => 'nullable|string|max:255',
        ]);
    }
}
