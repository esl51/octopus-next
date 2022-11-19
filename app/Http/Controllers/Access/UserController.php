<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\ItemController;
use App\Http\Resources\Access\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends ItemController
{
    protected $class = User::class;
    protected $resourceClass = UserResource::class;
    protected $fillable = [
        'name',
        'email',
    ];
    protected $with = [
        'roles',
    ];

    /**
     * @inheritDoc
     */
    public function getValidationRules(Request $request, $id = null)
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

    /**
     * @inheritDoc
     */
    protected function newItemsQuery(Request $request)
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

    /**
     * @inheritDoc
     */
    public function beforeStore(Request $request, array $data)
    {
        $data['password'] = Hash::make($request->password);
        $data['email_verified_at'] = now();

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function afterStore(Request $request, $item)
    {
        if ($request->avatar) {
            $item->storeAvatar($request->avatar);
        }

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

    /**
     * @inheritDoc
     */
    public function beforeUpdate(Request $request, array $data)
    {
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function afterUpdate(Request $request, $item)
    {
        if ($request->avatar) {
            $item->storeAvatar($request->avatar);
        }

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
