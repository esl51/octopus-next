<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\ItemController;
use App\Http\Resources\Access\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends ItemController
{
    protected $class = Role::class;
    protected $resourceClass = RoleResource::class;
    protected $fillable = [
        'name',
        'guard_name',
    ];
    protected $fillableTranslations = [
        'title',
    ];
    protected $with = [
        'permissions',
    ];
    protected $withCount = [
        'users',
    ];

    /**
     * @inheritDoc
     */
    public function getValidationRules(Request $request, $id = null)
    {
        return [
            'name' => 'required|string|max:255',
            '%title%' => 'required|string|max:255',
            'guard_name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer',
        ];
    }

    /**
     * @inheritDoc
     */
    public function newItemsQuery(Request $request)
    {
        $items = parent::newItemsQuery($request);

        $search = htmlspecialchars($request->search);
        if ($search && !is_numeric($search)) {
            $items->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('guard_name', 'like', '%' . $search . '%')
                    ->orWhereTranslationLike('title', '%' . $search . '%');
            });
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function afterStore(Request $request, $item)
    {
        $item->syncPermissions($request->permissions);
    }

    /**
     * @inheritDoc
     */
    public function afterUpdate(Request $request, $item)
    {
        $item->syncPermissions($request->permissions);
    }

    /**
     * @inheritDoc
     */
    public function sortByTranslations()
    {
        return [
            'title' => 'role_translations.title',
        ];
    }
}
