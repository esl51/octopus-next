<?php

namespace App\Models\Properties;

use App\Models\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Rutorika\Sortable\SortableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Property Group
 *
 * @property string $name
 *
 * @property \Illuminate\Support\Collection<Property> $properties
 */
class PropertyGroup extends Model implements TranslatableContract
{
    use HasFactory;
    use SortableTrait;
    use Translatable;

    protected $translatedAttributes = [
        'name',
    ];

    public function getPropertiesCountAttribute()
    {
        if (!isset($this->attributes['properties_count'])) {
            return $this->properties()->count();
        }
        return $this->attributes['properties_count'];
    }

    public function getIsDeletableAttribute(): bool
    {
        if ($this->properties_count > 0) {
            return false;
        }
        return true;
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
