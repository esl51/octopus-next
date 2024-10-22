<?php

namespace App\Models;

use App\Classes\EloquentBuilder;
use App\Traits\HasColumns;
use App\Traits\SerializesDates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Roquie\LaravelPerPageResolver\PerPageResolverTrait;

/**
 * Model
 *
 * @mixin \Eloquent
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
abstract class Model extends BaseModel
{
    use PerPageResolverTrait;
    use HasColumns;
    use SerializesDates;

    public function getIsEditableAttribute(): bool
    {
        return true;
    }

    public function getIsDeletableAttribute(): bool
    {
        return true;
    }

    public function newEloquentBuilder($query): Builder
    {
        return new EloquentBuilder($query);
    }
}
