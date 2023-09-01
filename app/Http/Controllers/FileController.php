<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ItemController;
use App\Http\Resources\FileResource;
use App\Models\Files\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends ItemController
{
    protected string $class = File::class;
    protected string $resourceClass = FileResource::class;
    protected array $fillable = [
        'original_name',
    ];
    protected array $fillableTranslations = [
        'title',
    ];

    public function addConditions(Request $request, Builder $query): Builder
    {
        return $query->viewable();
    }

    public function getValidationRules(Request $request, ?int $id = null): array
    {
        return [
            'original_name' => 'required|string|max:255',
            '%title%' => 'nullable|string|max:255',
        ];
    }

    public function newItemsQuery(Request $request): Builder
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

    public function sortByTranslations(): array
    {
        return [
            'title' => 'file_translations.title',
        ];
    }

    public function download(Request $request): Response
    {
        $item = $this->getItem($request, intval($request->id));
        return $item->download();
    }

    public function view(Request $request): StreamedResponse
    {
        $item = $this->getItem($request, intval($request->id));
        return $item->response();
    }
}
