<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ItemController;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\Request;

class FileController extends ItemController
{
    protected $class = File::class;
    protected $resourceClass = FileResource::class;
    protected $fillable = [
        'original_name',
    ];
    protected $fillableTranslations = [
        'title',
    ];

    /**
     * @inheritDoc
     */
    public function checkPermissions($query)
    {
        return $query->viewable();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRules(Request $request, $id = null)
    {
        return [
            'original_name' => 'required|string|max:255',
            '%title%' => 'nullable|string|max:255',
        ];
    }

    /**
     * @inheritDoc
     */
    public function newItemsQuery(Request $request)
    {
        $items = parent::newItemsQuery($request);

        $search = htmlspecialchars($request->search);
        if ($search && !is_numeric($search)) {
            $items->where(function ($query) use ($search) {
                $query->orWhere('original_name', 'like', '%' . $search . '%')
                    ->orWhereTranslationLike('title', '%' . $search . '%');
            });
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function sortByTranslations()
    {
        return [
            'title' => 'file_translations.title',
        ];
    }

    public function download($id)
    {
        $item = $this->getItem($id);
        return $item->download();
    }

    public function view($id)
    {
        $item = $this->getItem($id);
        return $item->response();
    }
}
