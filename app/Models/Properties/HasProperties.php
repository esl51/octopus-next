<?php

namespace App\Models\Properties;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;

trait HasProperties
{
    use HasRelationships;

    public function getValidatorsAttribute()
    {
        $validators = [];
        foreach ($this->properties as $property) {
            $validators = array_merge($validators, $property->pivot->validators);
        }
        return $validators;
    }

    public function getAttributeNamesAttribute()
    {
        $names = [];
        foreach ($this->properties as $property) {
            $names = array_merge($names, $property->pivot->attributeNames);
        }
        return $names;
    }

    public function getPropertiesFillableAttribute()
    {
        $fillable = [];
        foreach ($this->properties as $property) {
            $fillable[] = $property->alias;
            if ($property->has_note) {
                $fillable[] = $property->note_attribute_name;
            }
        }
        return $fillable;
    }

    public function properties()
    {
        return $this->morphToMany(Property::class, 'entity', 'entity_property')
            ->using(EntityProperty::class)
            ->withPivot('id')
            ->with([
                'pivot.values.entityProperty.property',
                'pivot.values.propertyValue.property',
                'pivot.values.translations',
            ])
            ->withTimestamps()
            ->withTranslation();
    }
}
