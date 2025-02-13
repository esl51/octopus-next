<?php

namespace App\Traits\Files;

use App\Models\Files\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Image;
use Storage;
use Str;

trait HasFiles
{
    /**
     * Default file type options.
     */
    private array $defaultOptions = [
        'multiple' => true,
        'replace' => false,
        'cropSize' => 1024,
        'viewable' => true,
        'extensions' => 'jpg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx',
    ];

    /**
     * Only viewable files scope.
     */
    public static function viewableFilesScope(Builder $query): void
    {
        $types = array_keys(array_filter((new self)->getFileTypes(), fn ($item) => ! empty($item['viewable'])));
        if (! empty($types)) {
            $query->where('filable_type', self::class);
            $query->whereIn('type', $types);
        }
    }

    /**
     * Only owned files scope.
     */
    public static function ownedFilesScope(Builder $query): void
    {
        //
    }

    /**
     * Get type name.
     */
    public function getTypeName(): string
    {
        $class = get_class($this);
        $classParts = explode('\\', $class);
        $name = end($classParts);

        return Str::snake($name);
    }

    /**
     * Get storage path.
     */
    public function getStoragePath(?string $type = null): string
    {
        return $this->getTypeName().'/'.$this->id.($type ? '/'.$type : '');
    }

    /**
     * Get file path.
     */
    public function getPath(string $fileName, ?string $type = null): string
    {
        return $this->getStoragePath($type).'/'.$fileName;
    }

    /**
     * Store files.
     */
    public function storeFiles(array|UploadedFile $files, ?string $type = null, int $cropSize = 1024): array
    {
        $storedFiles = [];
        if (! is_array($files)) {
            $files = [$files];
        }
        foreach ($files as $uploadedFile) {
            if ($uploadedFile instanceof UploadedFile) {
                if (in_array($uploadedFile->getMimeType(), ['image/jpeg', 'image/png'])) {
                    $img = Image::read($uploadedFile);
                    $img->orient();
                    if ($img->width() < $img->height()) {
                        $img->scaleDown(width: $cropSize);
                    } else {
                        $img->scaleDown(height: $cropSize);
                    }
                    if ($uploadedFile->getMimeType() == 'image/jpeg') {
                        $img = $img->toJpeg()->toFilePointer();
                    } elseif ($uploadedFile->getMimeType() == 'image/png') {
                        $img = $img->toPng()->toFilePointer();
                    }
                    Storage::put($this->getPath($uploadedFile->hashName(), $type), $img);
                } else {
                    $uploadedFile->store($this->getStoragePath($type));
                }
                $file = $this->getPath($uploadedFile->hashName(), $type);
                $storedFiles[] = $this->files()->create([
                    'type' => $type,
                    'file_name' => $uploadedFile->hashName(),
                    'original_name' => $uploadedFile->getClientOriginalName(),
                    'mime_type' => Storage::mimeType($file),
                    'extension' => $uploadedFile->getClientOriginalExtension(),
                    'size' => Storage::size($file),
                    'title:'.config('translatable.fallback_locale') => pathinfo(
                        $uploadedFile->getClientOriginalName(),
                        PATHINFO_FILENAME
                    ),
                ]);
            }
        }

        return $storedFiles;
    }

    /**
     * Delete file from storage.
     */
    public function deleteFile(string $fileName, ?string $type = null): void
    {
        $files = $this->files()->where(['file_name' => $fileName]);
        if ($type) {
            $files->where(['type' => $type]);
        }
        $files->delete();
    }

    /**
     * Download entire type from storage.
     */
    public function deleteDirectory(?string $type = null): void
    {
        $files = $this->files();
        if ($type) {
            $files->where(['type' => $type]);
        }
        $files->delete();
        Storage::deleteDirectory($this->getStoragePath($type));
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'filable');
    }

    public function handleFiles(?array $files = null, string|array|null $types = null): array
    {
        if (empty($files)) {
            return [];
        }
        $handledFiles = [];
        $fileTypes = $this->getFileTypes();
        if (is_string($types)) {
            $fileTypes = array_filter(
                $fileTypes,
                fn ($item) => $item == $types,
                ARRAY_FILTER_USE_KEY
            );
        } elseif (is_array($types)) {
            $fileTypes = array_filter(
                $fileTypes,
                fn ($item) => in_array($item, $types),
                ARRAY_FILTER_USE_KEY
            );
        }
        foreach ($fileTypes as $type => $options) {
            if (data_get($files, $type)) {
                if ($options['replace']) {
                    $this->deleteDirectory($type);
                }
                $storedFiles = $this->storeFiles(data_get($files, $type), $type, $options['cropSize']);
                if ($options['multiple']) {
                    $handledFiles[$type] = $storedFiles;
                } else {
                    $handledFiles[$type] = count($storedFiles) ? $storedFiles[0] : [];
                }
            }
        }

        return $handledFiles;
    }

    /**
     * Get file types from `fileTypes` property.
     */
    public function getFileTypes(): array
    {
        if (! isset($this->fileTypes)) {
            return [];
        }
        $types = [];
        foreach ($this->fileTypes as $type => $options) {
            if (is_string($options)) {
                $types[$options] = $this->defaultOptions;
            } elseif (is_string($type) && is_array($options)) {
                $types[$type] = array_merge($this->defaultOptions, $options);
            }
        }

        return $types;
    }

    public function getFilableTitleAttribute(): string
    {
        return $this->getTypeName().' '.$this->id;
    }

    protected static function bootHasFiles(): void
    {
        foreach ((new static)->getFileTypes() as $type => $options) {
            static::resolveRelationUsing($type, function ($model) use ($type, $options) {
                if ($options['multiple']) {
                    return $model->morphMany(File::class, 'filable')->where('type', $type)->sorted();
                } else {
                    return $model->morphOne(File::class, 'filable')->where('type', $type)->sorted();
                }
            });
        }

        static::deleting(function ($model) {
            // remove storable files
            $model->deleteDirectory();
        });
    }
}
