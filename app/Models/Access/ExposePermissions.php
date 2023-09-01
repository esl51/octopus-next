<?php

namespace App\Models\Access;

use Auth;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

trait ExposePermissions
{
    /**
     * Get all user permissions.
     */
    public function getAllPermissionsAttribute(): Collection
    {
        return $this->getAllPermissions();
    }

    /**
     * Get all user permissions in a flat array.
     */
    public function getCanAttribute(): array
    {
        $permissions = [];
        foreach (Permission::all() as $permission) {
            if (Auth::user()->can($permission->name)) {
                $permissions[$permission->name] = true;
            } else {
                $permissions[$permission->name] = false;
            }
        }
        return $permissions;
    }
}
