<?php

namespace App\Models\Access;

use App\Traits\HasColumns;
use App\Traits\HasService;
use App\Traits\SerializesDates;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Roquie\LaravelPerPageResolver\PerPageResolverTrait;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole implements TranslatableContract
{
    use HasColumns;
    use HasFactory;
    use HasService;
    use PerPageResolverTrait;
    use SerializesDates;
    use Translatable;

    public $translatedAttributes = [
        'title',
    ];

    public function getUsersCountAttribute(): int
    {
        if (! isset($this->attributes['users_count'])) {
            return $this->users()->count();
        }

        return $this->attributes['users_count'];
    }

    public function getIsDeletableAttribute(): bool
    {
        if ($this->name == 'root') {
            return false;
        }
        if ($this->users_count) {
            return false;
        }

        return true;
    }

    public function getIsEditableAttribute(): bool
    {
        if ($this->name == 'root') {
            return false;
        }

        return true;
    }
}
