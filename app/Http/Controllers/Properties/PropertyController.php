<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\ItemController;
use App\Models\Properties\Property;
use Illuminate\Http\Request;
use App\Http\Resources\Properties\PropertyResource;
use App\Rules\Alias;
use Illuminate\Database\Eloquent\Builder;

class PropertyController extends ItemController
{
    protected string $class = Property::class;
    protected string $resourceClass = PropertyResource::class;
    protected array $fillable = [
        'alias',
        'property_group_id',
        'property_type_id',
    ];
    protected array $fillableTranslations = [
        'name',
    ];
    protected array $with = [
        'propertyGroup',
        'values',
    ];
    protected array $valueFillable = [];
    protected array $valueTranslations = [
        'name',
    ];

    public function getValidationRules(Request $request, ?int $id = null): array
    {
        return [
            '%name%' => 'required_if_fallback:nullable|string|max:255',
            'alias' => [
                'required',
                'string',
                'max:255',
                new Alias(),
                'unique:properties,alias' . ($id ? ",$id" : ''),
            ],
            'property_group_id' => 'nullable|integer|exists:property_groups,id',
            'property_type_id' => 'required|integer',
        ];
    }

    public function beforeUpdate(Request $request, array $data): array
    {
        // disable updating property_type_id
        unset($data['property_type_id']);
        return $data;
    }

    public function newItemsQuery(Request $request): Builder
    {
        $items = parent::newItemsQuery($request);

        $search = $request->query('search');
        if ($search && !is_numeric($search)) {
            $items->where(function ($query) use ($search) {
                $query->whereTranslationLike('name', '%' . $search . '%')
                    ->orWhere('alias', 'like', '%' . $search . '%');
            });
        }

        return $items;
    }

    public function sortByTranslations(): array
    {
        return [
            'name' => 'property_translations.name',
        ];
    }
}
