<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class File
{
    public $name;
    public $type;
    public $path;
    public $url;

    /**
     * Collect File models.
     *
     * @param \App\Models\Model $model
     * @param string|null $directory
     * @param string|null $routePrefix
     * @return \Illuminate\Support\Collection
     */
    public static function collect($model, $directory = null, $routePrefix = null)
    {
        $files = Storage::files($model->getStoragePath($directory));
        $items = [];
        foreach ($files as $file) {
            $items[] = self::make($model, pathinfo($file, PATHINFO_BASENAME), $directory, $routePrefix);
        }
        return collect($items);
    }

    /**
     * Make single File model.
     *
     * @param \App\Models\Model $model
     * @param string $fileName
     * @param string|null $directory
     * @param string|null $routePrefix
     * @return \App\File
     */
    public static function make($model, $fileName, $directory = null, $routePrefix = null)
    {
        $value = new self();
        $value->name = $fileName;
        $value->path = $model->getPath($fileName, $directory);
        $value->url = $model->getFileUrl($fileName, $directory, $routePrefix);
        $value->type = Storage::mimeType($model->getPath($fileName, $directory));

        return $value;
    }
}
