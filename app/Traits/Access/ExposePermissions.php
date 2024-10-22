<?php

namespace App\Traits\Access;

use App\Models\Access\Permission;
use Illuminate\Support\Collection;

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
            if ($this->can($permission->name)) {
                $permissions[$permission->name] = true;
            } else {
                $permissions[$permission->name] = false;
            }
        }
        return $permissions;
    }
}
