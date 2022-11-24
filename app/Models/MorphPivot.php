<?php

namespace App\Models;

use App\Models\SerializesDates;
use Illuminate\Database\Eloquent\Relations\MorphPivot as BaseMorphPivot;

/**
 * Morph Pivot
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
abstract class MorphPivot extends BaseMorphPivot
{
    use SerializesDates;

    // for potential using with laravel-translatable
    public $incrementing = true;

    public function getIsDeletableAttribute()
    {
        return true;
    }

    public function getIsEditableAttribute()
    {
        return true;
    }
}
