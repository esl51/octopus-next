<?php

namespace App\Services\Access;

use App\Services\ItemService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RoleService extends ItemService
{
    protected array $with = [
        'permissions',
    ];

    protected array $withCount = [
        'users',
    ];

    public function sortByTranslations(): array
    {
        return [
            'title' => 'role_translations.title',
        ];
    }

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

    public function store(array $data, ?array $files = null): Model
    {
        $item = parent::store($data, $files);
        $item->syncPermissions(array_map(fn ($p) => (int) $p, $data['permissions'] ?? []));

        return $item;
    }

    public function update(int $id, array $data, ?array $files = null): Model
    {
        $item = parent::update($id, $data, $files);
        $item->syncPermissions(array_map(fn ($p) => (int) $p, $data['permissions'] ?? []));

        return $item;
    }
}
