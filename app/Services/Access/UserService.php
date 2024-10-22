<?php

namespace App\Services\Access;

use App\Models\Access\Role;
use App\Models\Access\User;
use App\Services\ItemService;
use Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserService extends ItemService
{
    protected array $with = [
        'roles',
        'avatar',
    ];

    public function newItemsQuery(array $params): Builder
    {
        $items = parent::newItemsQuery($params);

        $search = $params['search'] ?? null;
        if ($search && !is_numeric($search)) {
            $items->where(function ($query) use ($search) {
                $query->orWhere('users.name', 'like', '%' . $search . '%')
                    ->orWhere('users.email', 'like', '%' . $search . '%');
            });
        }

        $items->leftJoin('model_has_roles', function ($join) {
            $join->on('model_has_roles.model_id', '=', 'users.id')
                ->where('model_has_roles.model_type', '=', User::class);
        });

        $role = $params['role'] ?? null;
        if ($role) {
            $items->join('roles', 'roles.id', '=', 'model_has_roles.role_id');
            $items->where('roles.name', $role);
        }

        $items->orderByRaw('count(model_has_roles.role_id) desc, name asc');

        return $items;
    }

    public function store(array $data, ?array $files = null): Model
    {
        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = now();

        $item = parent::store($data, $files);

        $roles = array_map(fn($r) => (int) $r, $data['roles'] ?? []);
        $rootRole = Role::where('name', 'root')->first();
        // if current user is not root - unset root role from new roles
        if (!auth()->user()->hasRole('root')) {
            if (($key = array_search($rootRole->id, $roles)) !== false) {
                unset($roles[$key]);
            }
        }
        $item->syncRoles($roles);

        return $item;
    }

    public function update(int $id, array $data, ?array $files = null): Model
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $item = parent::update($id, $data, $files);

        $roles = array_map(fn($r) => (int) $r, $data['roles'] ?? []);
        $rootRole = Role::where('name', 'root')->first();
        // if user is current user and current user is root and root was unchecked - set only root role
        if (
            $item->id == auth()->id() &&
            auth()->user()->hasRole('root') &&
            !in_array($rootRole->id, $roles)
        ) {
            $roles = [$rootRole->id];
        }
        // if current user is not root and user is not root - unset root role from new roles
        if (!auth()->user()->hasRole('root') && !$item->hasRole('root')) {
            if (($key = array_search($rootRole->id, $roles)) !== false) {
                unset($roles[$key]);
            }
        }
        $item->syncRoles($roles);

        return $item;
    }
}
