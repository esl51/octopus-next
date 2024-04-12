<?php

namespace App\Models\Properties;

use App\Models\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Entity Property Value
 *
 * @property integer $entity_property_id
 * @property string $string_value
 * @property string $text_value
 * @property integer $int_value
 * @property float $float_value
 * @property integer $property_value_id
 *
 * @property EntityProperty $entityProperty
 * @property PropertyValue $propertyValue
 */
class EntityPropertyValue extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = [
        'entity_property_id',
        'int_value',
        'float_value',
        'property_value_id',
    ];
    protected $translatedAttributes = [
        'string_value',
        'text_value',
    ];

    public function getValueAttribute()
    {
        $propertyType = $this->entityProperty->property->propertyType;
        $column = $propertyType->column;
        if ($column == 'property_value_id') {
            return $this->propertyValue;
        } elseif ($column) {
            return $this->{$column};
        }
        return null;
    }

    public function entityProperty()
    {
        return $this->belongsTo(EntityProperty::class);
    }

    public function propertyValue()
    {
        return $this->belongsTo(PropertyValue::class);
    }
}
