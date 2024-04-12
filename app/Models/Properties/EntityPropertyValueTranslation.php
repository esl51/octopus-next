<?php

namespace App\Models\Properties;

use App\Models\Model;

class EntityPropertyValueTranslation extends Model
{
    protected $fillable = [
        'string_value',
        'text_value',
    ];
}
