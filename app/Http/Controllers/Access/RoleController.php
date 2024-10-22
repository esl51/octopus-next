<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\ItemController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends ItemController
{
    protected array $fillable = [
        'name',
        'guard_name',
    ];
    protected array $fillableTranslations = [
        'title',
    ];
    protected array $with = [
        'permissions',
    ];
    protected array $withCount = [
        'users',
    ];

    public function getValidationRules(Request $request, ?int $id = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')
                    ->ignore($id)
                    ->where(fn($query) => $query->where('guard_name', $request->guard_name)),
            ],
            '%title%' => 'required_if_fallback:nullable|string|max:255',
            'guard_name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer',
        ];
    }

    public function newItemsQuery(Request $request): Builder
    {
        $items = parent::newItemsQuery($request);

        $search = $request->query('search');
        if ($search && !is_numeric($search)) {
            $items->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('guard_name', 'like', '%' . $search . '%')
                    ->orWhereTranslationLike('title', '%' . $search . '%');
            });
        }

        return $items;
    }

    public function afterStore(Request $request, Model $item): void
    {
        $item->syncPermissions($request->permissions);
    }

    public function afterUpdate(Request $request, Model $item): void
    {
        $item->syncPermissions($request->permissions);
    }

    public function sortByTranslations(): array
    {
        return [
            'title' => 'role_translations.title',
        ];
    }
}
