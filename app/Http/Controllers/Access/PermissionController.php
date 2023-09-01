<?php

namespace App\Http\Controllers\Access;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\ItemController;
use App\Http\Resources\Access\PermissionResource;
use Illuminate\Database\Eloquent\Builder;

class PermissionController extends ItemController
{
    protected string $class = Permission::class;
    protected string $resourceClass = PermissionResource::class;
    protected array $fillable = [
        'name',
        'guard_name',
    ];

    public function getValidationRules(Request $request, ?int $id = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions')
                    ->ignore($id)
                    ->where(fn ($query) => $query->where('guard_name', $request->input('guard_name'))),
            ],
            'guard_name' => 'required|string|max:255',
        ];
    }

    public function newItemsQuery(Request $request): Builder
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
