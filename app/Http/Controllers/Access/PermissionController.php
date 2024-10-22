<?php

namespace App\Http\Controllers\Access;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\ItemController;
use Illuminate\Database\Eloquent\Builder;

class PermissionController extends ItemController
{
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
                    ->where(fn($query) => $query->where('guard_name', $request->guard_name)),
            ],
            'guard_name' => 'required|string|max:255',
        ];
    }

    public function newItemsQuery(Request $request): Builder
    {
        $items = parent::newItemsQuery($request);

        $search = $request->query('search');
        if ($search && !is_numeric($search)) {
            $items->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('guard_name', 'like', '%' . $search . '%');
            });
        }

        return $items;
    }
}
