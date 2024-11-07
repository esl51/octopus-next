<?php

namespace App\Services\Files;

use App\Services\ItemService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FileService extends ItemService
{
    public function addConditions(Builder $query): Builder
    {
        return $query->viewable(! auth()->user()->can('manage all files'));
    }

    public function newItemsQuery(array $params): Builder
    {
        $items = parent::newItemsQuery($params);

        $search = $params['search'] ?? null;
        if ($search && ! is_numeric($search)) {
            $items->where(function ($query) use ($search) {
                $query->orWhere('original_name', 'like', '%'.$search.'%')
                    ->orWhereTranslationLike('title', '%'.$search.'%');
            });
        }

        $filableType = $params['filable_type'] ?? null;
        if ($filableType) {
            $items->where('filable_type', 'App\\Models\\'.$filableType);
        }
        $filableId = $params['filable_id'] ?? null;
        if ($filableId) {
            $items->where('filable_id', $filableId);
        }
        $type = $params['type'] ?? null;
        if ($type) {
            $items->where('type', $type);
        }

        return $items;
    }

    public function store(array $data, ?array $files = null): Model
    {
        /** @var ItemService $filableService */
        $filableService = call_user_func(['App\\Models\\'.$data['filable_type'], 'service']);
        $filable = $filableService->get($data['filable_id']);

        $keyedFiles = [
            $data['type'] => $files['file'],
        ];

        $handledFiles = $filable->handleFiles($keyedFiles);

        return is_array($handledFiles[$data['type']])
            ? $handledFiles[$data['type']][0]
            : $handledFiles[$data['type']];
    }

    public function sortByTranslations(): array
    {
        return [
            'title' => 'file_translations.title',
        ];
    }
}
