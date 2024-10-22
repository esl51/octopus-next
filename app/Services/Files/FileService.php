<?php

namespace App\Services\Files;

use App\Services\ItemService;
use Illuminate\Database\Eloquent\Builder;

class FileService extends ItemService
{
    public function addConditions(Builder $query): Builder
    {
        return $query->viewable();
    }

    public function newItemsQuery(array $params): Builder
    {
        $items = parent::newItemsQuery($params);

        $search = $params['search'] ?? null;
        if ($search && !is_numeric($search)) {
            $items->where(function ($query) use ($search) {
                $query->orWhere('original_name', 'like', '%' . $search . '%')
                    ->orWhereTranslationLike('title', '%' . $search . '%');
            });
        }

        return $items;
    }

    public function sortByTranslations(): array
    {
        return [
            'title' => 'file_translations.title',
        ];
    }
}
