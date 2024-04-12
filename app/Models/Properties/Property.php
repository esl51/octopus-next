<?php

namespace App\Models\Properties;

use App\Models\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rutorika\Sortable\BelongsToSortedManyTrait;
use stdClass;

/**
 * Property
 *
 * @property string $name
 * @property string $alias
 * @property integer $property_group_id
 * @property PropertyType $property_type_id
 *
 * @property PropertyGroup $propertyGroup
 * @property \Illuminate\Support\Collection<PropertyValue> $values
 */
class Property extends Model implements TranslatableContract
{
    use HasFactory;
    use BelongsToSortedManyTrait;
    use Translatable;

    protected $fillable = [
        'alias',
        'property_group_id',
        'property_type_id',
    ];
    protected $translatedAttributes = [
        'name'
    ];
    protected $casts = [
        'property_type_id' => PropertyType::class,
    ];
    protected $appends = [
        'property_type',
    ];

    public function getPropertyTypeAttribute(): stdClass
    {
        return $this->property_type_id->asObject();
    }

    public function getIsDeletableAttribute(): bool
    {
        if ($this->entity_property_values_count > 0) {
            return false;
        }
        return true;
    }

    public function propertyGroup(): BelongsTo
    {
        return $this->belongsTo(PropertyGroup::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(PropertyValue::class);
    }
}
