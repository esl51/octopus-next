<?php

namespace App\Models\Access;

use App\Traits\HasColumns;
use App\Traits\HasService;
use App\Traits\SerializesDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Roquie\LaravelPerPageResolver\PerPageResolverTrait;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    use HasColumns;
    use HasFactory;
    use HasService;
    use PerPageResolverTrait;
    use SerializesDates;

    public function getRolesCountAttribute(): int
    {
        if (! isset($this->attributes['roles_count'])) {
            return $this->roles()->count();
        }

        return $this->attributes['roles_count'];
    }

    public function getIsDeletableAttribute(): bool
    {
        if (
            in_array($this->name, [
                'manage access',
                'manage users',
            ])
        ) {
            return false;
        }
        if ($this->roles_count) {
            return false;
        }

        return true;
    }

    public function getIsEditableAttribute(): bool
    {
        if (
            in_array($this->name, [
                'manage access',
                'manage users',
            ])
        ) {
            return false;
        }

        return true;
    }
}
