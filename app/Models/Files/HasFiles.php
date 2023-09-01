<?php

namespace App\Models\Files;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Image;
use Storage;
use Str;

trait HasFiles
{
    public static function viewableFilesScope(Builder $query): void
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
        return $this->getTypeName() . '/' . $this->id . ($type ? '/' . $type : '');
    }

    /**
     * Get file path.
     */
    public function getPath(string $fileName, ?string $type = null): string
    {
        return $this->getStoragePath($type) . '/' . $fileName;
    }

    /**
     * Store files.
     */
    public function storeFiles(array|UploadedFile $files, ?string $type = null, int $cropSize = 1024): void
    {
        if (!is_array($files)) {
            $files = [$files];
        }
        foreach ($files as $uploadedFile) {
            if ($uploadedFile instanceof UploadedFile) {
                if (in_array($uploadedFile->getMimeType(), ['image/jpeg', 'image/png'])) {
                    $img = Image::make($uploadedFile);
                    if ($img->width() < $img->height()) {
                        $img->widen($cropSize, function ($constraint) {
                            $constraint->upsize();
                        });
                    } else {
                        $img->heighten($cropSize, function ($constraint) {
                            $constraint->upsize();
                        });
                    }
                    $img->stream();
                    Storage::put($this->getPath($uploadedFile->hashName(), $type), $img);
                } else {
                    $uploadedFile->store($this->getStoragePath($type));
                }
                $file = $this->getPath($uploadedFile->hashName(), $type);
                $this->files()->create([
                    'type' => $type,
                    'file_name' => $uploadedFile->hashName(),
                    'original_name' => $uploadedFile->getClientOriginalName(),
                    'mime_type' => Storage::mimeType($file),
                    'extension' => $uploadedFile->getExtension(),
                    'size' => Storage::size($file),
                    'title:' . config('translatable.fallback_locale') => $uploadedFile->getClientOriginalName(),
                ]);
            }
        }
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

    protected static function bootHasFiles(): void
    {
        static::deleting(function ($model) {
            // remove storable files
            $model->deleteDirectory();
        });
    }
}
