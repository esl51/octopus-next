<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Image;

trait HasFiles
{
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
     * @param string|null $directory
     * @return string
     */
    public function getStoragePath($directory = null)
    {
        return $this->getTypeName() . '/' . $this->id . ($directory ? '/' . $directory : '');
    }

    /**
     * Get file path.
     *
     * @param string $fileName
     * @param string|null $directory
     * @return string
     */
    public function getPath($fileName, $directory = null)
    {
        return $this->getStoragePath($directory) . '/' . $fileName;
    }

    /**
     * Get file download url.
     *
     * @param string $fileName
     * @param string|null $directory
     * @param string|null $routePrefix
     * @return string
     */
    public function getFileUrl($fileName, $directory = null, $routePrefix = null)
    {
        return route(($routePrefix ?: $this->getTypeName()) . '.file', [
            $this->getTypeName() => $this->id,
            'fileName' => $fileName,
            'directory' => $directory,
        ]);
    }

    /**
     * Store files.
     *
     * @param \Illuminate\Http\UploadedFile[]|\Illuminate\Http\UploadedFile $files
     * @param string|null $directory
     * @param integer $cropSize
     * @return void
     */
    public function storeFiles($files, $directory = null, $cropSize = 1024)
    {
        if (!is_array($files)) {
            $files = [$files];
        }
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                if (in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
                    $img = Image::make($file);
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
                    Storage::put($this->getPath($file->hashName(), $directory), $img);
                } else {
                    $file->store($this->getStoragePath($directory));
                }
            }
        }
    }

    /**
     * Delete file from storage
     *
     * @param string $fileName
     * @param string|null $directory
     * @return void
     */
    public function deleteFile($fileName, $directory = null)
    {
        Storage::delete($this->getPath($fileName, $directory));
    }

    /**
     * Get file from storage
     *
     * @param string $fileName
     * @param string|null $directory
     * @return \Illuminate\Http\Response
     */
    public function getFile($fileName, $directory = null)
    {
        return Storage::response($this->getPath($fileName, $directory));
    }

    /**
     * Download file from storage
     *
     * @param string $fileName
     * @param string|null $directory
     * @return \Illuminate\Http\Response
     */
    public function downloadFile($fileName, $directory = null)
    {
        return Storage::download($this->getPath($fileName, $directory));
    }

    /**
     * Download entire directory from storage
     *
     * @param string|null $directory
     * @return void
     */
    public function deleteDirectory($directory = null)
    {
        Storage::deleteDirectory($this->getStoragePath($directory));
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
