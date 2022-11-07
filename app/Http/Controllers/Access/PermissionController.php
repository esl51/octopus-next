<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\ItemController;
use App\Http\Resources\Access\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends ItemController
{
    protected $class = Permission::class;
    protected $resourceClass = PermissionResource::class;
    protected $fillable = [
        'name',
        'guard_name',
    ];

    /**
     * @inheritDoc
     */
    public function getValidationRules(Request $request, $id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'guard_name' => 'required|string|max:255',
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
                    ->orWhere('guard_name', 'like', '%' . $search . '%');
            });
        }

        return $items;
    }
}
