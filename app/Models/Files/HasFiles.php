<?php

namespace App\Models\Files;

use Illuminate\Http\UploadedFile;
use Image;
use Storage;
use Str;

trait HasFiles
{
    public static function viewableFilesScope($query)
    {
        //
    }

    /**
     * Get type name.
     *
     * @return string
     */
    public function getTypeName()
    {
        $class = get_class($this);
        $classParts = explode('\\', $class);
        $name = end($classParts);
        return Str::snake($name);
    }

    /**
     * Get storage path.
     *
     * @param string|null $type
     * @return string
     */
    public function getStoragePath($type = null)
    {
        return $this->getTypeName() . '/' . $this->id . ($type ? '/' . $type : '');
    }

    /**
     * Get file path.
     *
     * @param string $fileName
     * @param string|null $type
     * @return string
     */
    public function getPath($fileName, $type = null)
    {
        return $this->getStoragePath($type) . '/' . $fileName;
    }

    /**
     * Store files.
     *
     * @param \Illuminate\Http\UploadedFile[]|\Illuminate\Http\UploadedFile $files
     * @param string|null $type
     * @param integer $cropSize
     * @return void
     */
    public function storeFiles($files, $type = null, $cropSize = 1024)
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
     * Delete file from storage
     *
     * @param string $fileName
     * @param string|null $type
     * @return void
     */
    public function deleteFile($fileName, $type = null)
    {
        $files = $this->files()->where(['file_name' => $fileName]);
        if ($type) {
            $files->where(['type' => $type]);
        }
        $files->delete();
    }

    /**
     * Download entire type from storage
     *
     * @param string|null $type
     * @return void
     */
    public function deleteDirectory($type = null)
    {
        $files = $this->files();
        if ($type) {
            $files->where(['type' => $type]);
        }
        $files->delete();
        Storage::deleteDirectory($this->getStoragePath($type));
    }

    public function files()
    {
        return $this->morphMany(File::class, 'filable');
    }

    /**
     * @inheritDoc
     */
    protected static function bootHasFiles()
    {
        static::deleting(function ($model) {
            // remove storable files
            $model->deleteDirectory();
        });
    }
}
