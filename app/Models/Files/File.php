<?php

namespace App\Models\Files;

use App\Models\Model;
use App\Traits\HasService;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Rutorika\Sortable\SortableTrait;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * File
 *
 * @property string $title
 * @property int $position
 * @property int $filable_id
 * @property string $filable_type
 * @property string $type
 * @property string $file_name
 * @property string $original_name
 * @property string $mime_type
 * @property string $extension
 * @property int $size
 * @property string $url
 */
class File extends Model implements TranslatableContract
{
    use HasFactory;
    use HasService;
    use SortableTrait;
    use Translatable;

    protected $fillable = [
        'filable_id',
        'filable_type',
        'type',
        'file_name',
        'original_name',
        'mime_type',
        'extension',
        'size',
    ];

    public $translatedAttributes = [
        'title',
    ];

    public $appends = [
        'url',
    ];

    public $hidden = [
        'path',
        'filable_id',
        'filable_type',
        'file_name',
    ];

    public function filable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        return route('files.view', [
            'id' => $this->id,
        ]);
    }

    public function getPathAttribute()
    {
        return $this->filable->getPath($this->file_name, $this->type);
    }

    public function getIsDeletableAttribute(): bool
    {
        return $this->filable->is_editable;
    }

    public function getIsEditableAttribute(): bool
    {
        return $this->filable->is_editable;
    }

    public function download(): StreamedResponse
    {
        return Storage::download($this->filable->getPath($this->file_name, $this->type), $this->original_name, [
            'Content-Type' => $this->mime_type,
        ]);
    }

    public function response(): StreamedResponse
    {
        return Storage::response($this->filable->getPath($this->file_name, $this->type), $this->original_name, [
            'Content-Type' => $this->mime_type,
        ]);
    }

    public function scopeViewable(Builder $query, bool $owned = false): void
    {
        $filableTypes = self::select('filable_type')
            ->groupBy('filable_type')
            ->get()
            ->pluck('filable_type');

        $query->where(function (Builder $query) use ($filableTypes, $owned) {
            foreach ($filableTypes as $filableType) {
                $query->orWhere(function ($query) use ($filableType, $owned) {
                    $filableType::viewableFilesScope($query);
                    if ($owned) {
                        $filableType::ownedFilesScope($query);
                    }
                });
            }
        });
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($model) {
            // remove storable file
            Storage::delete($model->filable->getPath($model->file_name, $model->type));
        });
    }
}
