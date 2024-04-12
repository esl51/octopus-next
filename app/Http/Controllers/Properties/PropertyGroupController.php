<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\ItemController;
use App\Models\Properties\PropertyGroup;
use Illuminate\Http\Request;
use App\Http\Resources\Properties\PropertyGroupResource;
use Illuminate\Database\Eloquent\Builder;

class PropertyGroupController extends ItemController
{
    protected string $class = PropertyGroup::class;
    protected string $resourceClass = PropertyGroupResource::class;
    protected array $fillableTranslations = [
        'name',
    ];
    protected array $withCount = [
        'properties',
    ];

    public function getValidationRules(Request $request, ?int $id = null): array
    {
        return [
            '%name%' => 'required_if_fallback:nullable|string|max:255',
        ];
    }

    public function newItemsQuery(Request $request): Builder
    {
        $items = parent::newItemsQuery($request);

        $search = $request->query('search');
        if ($search && !is_numeric($search)) {
            $items->whereTranslationLike('name', '%' . $search . '%');
        }

        return $items;
    }

    public function sortByTranslations(): array
    {
        return [
            'name' => 'property_group_translations.name',
        ];
    }
}
