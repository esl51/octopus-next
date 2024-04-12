<?php

namespace App\Models\Properties;

use App\Models\Files\HasFiles;
use App\Models\MorphPivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Rutorika\Sortable\SortableTrait;

/**
 * Entity Property
 *
 * @property integer $entity_id
 * @property string $entity_type
 * @property integer $property_id
 *
 * @property Model $entity
 * @property Property $property
 * @property \Illuminate\Support\Collection<EntityPropertyValue> $values
 */
class EntityProperty extends MorphPivot
{
    use HasFactory;
    use HasFiles;
    use SortableTrait;

    protected $table = 'entity_property';

    protected $fillable = [
        'is_required',
    ];

    public $incrementing = true;

    public function updateValue($value)
    {
        $propertyType = $this->property->propertyType;
        $column = $propertyType->column;
        $this->values()->delete();
        if (empty($value)) {
            return;
        }
        if ($column) {
            $values = (array) $value;
            foreach ($values as $val) {
                $this->values()->create([
                    $column => $val,
                ]);
            }
        } elseif ($propertyType->has_files) {
            $this->storeFiles($value, $this->property->alias, 4096);
        }
    }

    public function getValueAttribute()
    {
        $propertyType = $this->property->propertyType;
        if ($propertyType->has_files) {
            return $this->files()->where('type', $this->property->alias)->get();
        } elseif ($propertyType->is_multiple) {
            $values = [];
            foreach ($this->values as $value) {
                $values[] = $value->value;
            }
            return $values;
        } elseif ($this->values->count()) {
            return $this->values->first()->value;
        }
        return null;
    }

    public function entity()
    {
        return $this->morphTo()->withTranslation();
    }

    public function property()
    {
        return $this->belongsTo(Property::class)->withTranslation();
    }

    public function values()
    {
        return $this->hasMany(EntityPropertyValue::class, 'entity_property_id')->withTranslation();
    }
}
