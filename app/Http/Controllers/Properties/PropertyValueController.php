<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\ItemController;
use App\Models\Properties\PropertyValue;
use Illuminate\Http\Request;
use App\Http\Resources\Properties\PropertyValueResource;
use App\Models\Properties\Property;
use Illuminate\Database\Eloquent\Builder;

class PropertyValueController extends ItemController
{
    protected string $class = PropertyValue::class;
    protected string $resourceClass = PropertyValueResource::class;
    protected array $fillableTranslations = [
        'name',
    ];
    protected array $with = [
        'property',
    ];
    protected array $withCount = [
        'entityPropertyValues',
    ];

    public function getValidationRules(Request $request, ?int $id = null): array
    {
        return [
            '%name%' => 'required_if_fallback:nullable|string|max:255',
        ];
    }

    public function initQuery(Request $request): Builder
    {
        $property = Property::find($request->property);
        return $property->values()->getQuery();
    }

    public function beforeStore(Request $request, array $data): array
    {
        $data['property_id'] = (int) $request->property;
        return $data;
    }

    public function newItemsQuery(Request $request): Builder
    {
        $items = parent::newItemsQuery($request);

        $search = $request->query('search');
        if ($search && !is_numeric($search)) {
            $items->where(function ($query) use ($search) {
                $query->whereTranslationLike('name', '%' . $search . '%');
            });
        }

        return $items;
    }

    public function sortByTranslations(): array
    {
        return [
            'name' => 'property_value_translations.name',
        ];
    }
}
