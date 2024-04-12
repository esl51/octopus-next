<?php

namespace App\Models\Properties;

use App\Models\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Query\JoinClause;
use Rutorika\Sortable\SortableTrait;

/**
 * Property Value
 *
 * @property integer $property_id
 * @property string $name
 *
 * @property Property $property
 * @property EntityPropertyValue[] $entityPropertyValues
 */
class PropertyValue extends Model implements TranslatableContract
{
    use HasFactory;
    use SortableTrait;
    use Translatable;

    protected $fillable = [
        'property_id',
    ];
    protected $translatedAttributes = [
        'name',
    ];

    public function getEntityPropertyValuesCountAttribute()
    {
        if (!isset($this->attributes['entity_property_values_count'])) {
            return $this->entityPropertyValues()->count();
        }
        return $this->attributes['entity_property_values_count'];
    }

    public function getIsDeletableAttribute(): bool
    {
        if ($this->entity_property_values_count > 0) {
            return false;
        }
        return true;
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function entityPropertyValues()
    {
        return $this->hasMany(EntityPropertyValue::class);
    }
}
