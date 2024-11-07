<?php

namespace App\Services\Access;

use App\Services\ItemService;
use Illuminate\Database\Eloquent\Builder;

class PermissionService extends ItemService
{
    public function newItemsQuery(array $params): Builder
    {
        $items = parent::newItemsQuery($params);
        $search = $params['search'] ?? null;
        if ($search && ! is_numeric($search)) {
            $items->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', '%'.$search.'%')
                    ->orWhere('guard_name', 'like', '%'.$search.'%');
            });
        }

        return $items;
    }
}
