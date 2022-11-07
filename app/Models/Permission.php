<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as BasePermission;
use Roquie\LaravelPerPageResolver\PerPageResolverTrait;

class Permission extends BasePermission
{
    use PerPageResolverTrait;
    use HasFactory;
    use HasColumns;

    public function getIsDeletableAttribute(): bool
    {
        return true;
    }

    public function getIsEditableAttribute(): bool
    {
        return true;
    }
}
