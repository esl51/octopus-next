<?php

namespace App\Http\Requests\Files;

use App\Http\Requests\ItemStoreUpdateRequest;
use App\Rules\Files\Filable;
use App\Rules\Files\FilableType;
use App\Rules\Files\FileType;

class FileStoreUpdateRequest extends ItemStoreUpdateRequest
{
    protected array $fillable = [
        'original_name',
    ];

    protected array $fillableTranslations = [
        'title',
    ];

    public function authorize(): bool
    {
        if ($this->isMethod('post') && empty($this->item)) {
            return true;
        } elseif (!empty($this->item)) {
            return $this->item->is_editable;
        }
        return false;
    }

    public function rules(): array
    {
        if ($this->item) {
            return $this->prepareRules([
                'original_name' => 'required|string|max:255',
                '%title%' => 'nullable|string|max:255',
            ]);
        } else {
            return $this->prepareRules([
                'filable_type' => ['required', 'string', new FilableType()],
                'filable_id' => ['required', 'integer', new Filable()],
                'type' => ['required', 'string', new FileType()],
            ]);
        }
    }
}
