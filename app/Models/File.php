<?php

namespace App\Models;

use Rutorika\Sortable\SortableTrait;
use Astrotomic\Translatable\Translatable;
use Storage;

/**
 * File
 *
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
class File extends Model
{
    use SortableTrait;
    use Translatable;

    private $url;

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
        'path',
    ];

    public function filable()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute()
    {
        return route('files.view', [
            'file' => $this->id,
        ]);
    }

    public function getPathAttribute()
    {
        return $this->filable->getPath($this->file_name, $this->type);
    }

    public function getIsDeletableAttribute(): bool
    {
        return $this->filable->is_deletable;
    }

    public function getIsEditableAttribute(): bool
    {
        return $this->filable->is_editable;
    }

    public function download()
    {
        return Storage::download($this->filable->getPath($this->file_name, $this->type), $this->original_name, [
            'Content-Type' => $this->mime_type,
        ]);
    }

    public function response()
    {
        return Storage::response($this->filable->getPath($this->file_name, $this->type), $this->original_name, [
            'Content-Type' => $this->mime_type,
        ]);
    }

    public function scopeViewable($query)
    {
        $filableTypes = self::select('filable_type')
            ->groupBy('filable_type')
            ->get()
            ->pluck('filable_type');

        foreach ($filableTypes as $filableType) {
            $query->where(function ($query) use ($filableType) {
                $query->where('filable_type', $filableType);
                $filableType::viewableFilesScope($query);
            })->orWhere('filable_type', '!=', $filableType);
        }
    }

    /**
     * @inheritDoc
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            // remove storable file
            Storage::delete($model->filable->getPath($model->file_name, $model->type));
        });
    }
}
