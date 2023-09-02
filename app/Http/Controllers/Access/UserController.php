<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\ItemController;
use App\Http\Resources\Access\UserResource;
use App\Models\Access\Role;
use App\Models\Access\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends ItemController
{
    protected string $class = User::class;
    protected string $resourceClass = UserResource::class;
    protected array $fillable = [
        'name',
        'email',
    ];
    protected array $with = [
        'roles',
        'avatar',
    ];

    public function getValidationRules(Request $request, ?int $id = null): array
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email' . ($id ? ",$id" : ''),
            'avatar' => 'nullable|mimes:jpeg,png',
            'password' => ($id ? 'nullable' : 'required') . '|min:8',
            'roles' => 'nullable|array',
            'roles.*' => 'nullable|integer',
        ];
    }

    public function getCustomAttributes(): array
    {
        return [
            'name' => trans('validation.attributes.person_name'),
        ];
    }

    protected function newItemsQuery(Request $request): Builder
    {
        $items = parent::newItemsQuery($request);

        $search = htmlspecialchars($request->search);
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

        $role = htmlspecialchars($request->role);
        if ($role) {
            $items->join('roles', 'roles.id', '=', 'model_has_roles.role_id');
            $items->where('roles.name', $role);
        }

        $items->orderByRaw('count(model_has_roles.role_id) desc, name asc');

        return $items;
    }

    public function beforeStore(Request $request, array $data): array
    {
        $data['password'] = Hash::make($request->password);
        $data['email_verified_at'] = now();

        return $data;
    }

    public function afterStore(Request $request, Model $item): void
    {
        parent::afterStore($request, $item);
        $item->handleFiles($request);
        $roles = $request->roles;
        $rootRole = Role::where('name', 'root')->first();
        // if current user is not root - unset root role from new roles
        if (!$request->user()->hasRole('root')) {
            if (($key = array_search($rootRole->id, $roles)) !== false) {
                unset($roles[$key]);
            }
        }
        $item->syncRoles($roles);
    }

    public function beforeUpdate(Request $request, array $data): array
    {
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        return $data;
    }

    public function afterUpdate(Request $request, Model $item): void
    {
        parent::afterUpdate($request, $item);
        $item->handleFiles($request);
        $roles = $request->roles;
        $rootRole = Role::where('name', 'root')->first();
        // if user is current user and current user is root and root was unchecked - set only root role
        if (
            $item->id == $request->user()->id &&
            $request->user()->hasRole('root') &&
            !in_array($rootRole->id, $roles)
        ) {
            $roles = [$rootRole->id];
        }
        // if current user is not root and user is not root - unset root role from new roles
        if (!$request->user()->hasRole('root') && !$item->hasRole('root')) {
            if (($key = array_search($rootRole->id, $roles)) !== false) {
                unset($roles[$key]);
            }
        }
        $item->syncRoles($roles);
    }
}
