<?php

namespace App\Http\Controllers;

use App\Models\TranslatableRuleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class ItemController extends Controller
{
    /**
     * Item class name
     */
    protected string $class;
    /**
     * Resource class name
     */
    protected string $resourceClass;
    /**
     * Fillable properties of class
     */
    protected array $fillable = [];
    /**
     * Fillable translatable properties of class
     */
    protected array $fillableTranslations = [];
    /**
     * Relations for loading
     */
    protected array $with = [];
    /**
     * Counts for loading
     */
    protected array $withCount = [];
    /**
     * Has search
     */
    protected bool $search = true;

    /**
     * Get validation rules for new or existing resource.
     */
    abstract protected function getValidationRules(Request $request, ?int $id = null): array;

    /**
     * Get messages.
     */
    public function getMessages(): array
    {
        return [];
    }

    /**
     * Get custom attributes.
     *
     * @return array
     */
    public function getCustomAttributes(): array
    {
        return [];
    }

    /**
     * Get fillable translations.
     */
    public function getFillableTranslations(): array
    {
        $translations = [];
        foreach ($this->fillableTranslations as $key) {
            foreach (config('translatable.locales') as $locale) {
                $translations[] = $key . ':' . $locale;
            }
        }

        return $translations;
    }

    /**
     * Add conditions hook.
     */
    public function addConditions(Request $request, Builder $query): Builder
    {
        return $query;
    }

    /**
     * "sort_by" replacements
     */
    protected function sortByReplacements(): array
    {
        return [];
    }

    /**
     * "sort_by" translations
     */
    protected function sortByTranslations(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function validate(
        Request $request,
        array $rules,
        array $messages = [],
        array $attributes = []
    ): array {
        $allMessages = $this->getMessages();
        $allAttributes = $this->getCustomAttributes();
        if (count($this->fillableTranslations)) {
            $rules = TranslatableRuleFactory::make($rules);

            foreach ($this->fillableTranslations as $attribute) {
                foreach (config('translatable.locales') as $locale) {
                    $allAttributes[$attribute . ':' . $locale] =
                        trans('validation.attributes.' . $attribute) . ' (' . $locale . ')';
                }
            }
        }
        return parent::validate(
            $request,
            $rules,
            array_merge($messages, $allMessages),
            array_merge($attributes, $allAttributes)
        );
    }

    /**
     * Init query.
     */
    protected function initQuery(Request $request): Builder
    {
        return call_user_func([$this->class, 'query']);
    }

    /**
     * Get items query.
     */
    protected function newItemsQuery(Request $request): Builder
    {
        $table = (new $this->class())->getTable();
        $columns = $this->class::getColumns();

        $items = $this->initQuery($request);
        $items->select($columns)->groupBy($columns);

        $items = $this->addConditions($request, $items);
        $with = $this->with;
        if (method_exists($this->class, 'translations')) {
            $with[] = 'translations';
            //$items = call_user_func([$items, 'withTranslation']);
        }
        if (!empty($with)) {
            $items->with($with);
        }

        if (!empty($this->withCount)) {
            $items->withCount($this->withCount);
        }

        $id = $request->integer('id');
        if ($id && !$request->query('search')) {
            $items->where($table . '.id', $id);
        }

        if ($this->search) {
            $search = $request->query('search');
            if (is_numeric($search)) {
                $items->where($table . '.id', $search);
            }
        }

        $items = $this->handleOrder(
            $request,
            $items,
            $table,
            method_exists($this->class, 'scopeSorted'),
            $this->sortByReplacements(),
            $this->sortByTranslations(),
        );

        return $items;
    }

    /**
     * Handle ordering.
     */
    public function handleOrder(
        Request $request,
        Builder $items,
        string $table = '',
        bool $sortable = false,
        array $replacements = [],
        array $translations = []
    ): Builder {
        $sortBy = $request->query('sort_by');
        if ($sortBy) {
            $table = (new $this->class())->getTable();
            $columns = array_map(
                fn ($item) => str_replace($table . '.', '', $item),
                $this->class::getColumns()
            );
            $sortDirection = $request->boolean('sort_desc') ? 'desc' : 'asc';
            if (!empty($replacements[$sortBy])) {
                $items->orderBy($replacements[$sortBy], $sortDirection);
            } elseif (!empty($translations[$sortBy])) {
                $items->orderByTranslation($sortBy, $sortDirection);
                $items->groupBy($translations[$sortBy]);
            } elseif (in_array($sortBy, $columns)) {
                $items->orderBy(($table ? $table . '.' : '') . $sortBy, $sortDirection);
            }
        }
        if ($sortable) {
            $items->sorted();
        }
        return $items;
    }

    /**
     * Get item.
     */
    public function getItem(Request $request, int $id, bool $withRelations = false): Model
    {
        $columns = $this->class::getColumns();

        $item = $this->initQuery($request);
        $item->select($columns)->groupBy($columns);

        $item = $this->addConditions($request, $item);
        if ($withRelations) {
            $with = $this->with;
            // load all translations for modifying purposes
            if (method_exists($this->class, 'translations')) {
                $with[] = 'translations';
            }
            if (!empty($with)) {
                $item = $item->with($with);
            }
        }

        return $item->find($id);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        return call_user_func(
            [$this->resourceClass, 'collection'],
            $this->newItemsQuery($request)->paginate()
        )->response($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $rules = $this->getValidationRules($request);
        $this->validate($request, $rules);
        $data = $request->only($this->fillable);
        if (count($this->fillableTranslations)) {
            $data = array_merge($data, $request->only($this->getFillableTranslations()));
        }
        $data = $this->beforeStore($request, $data);
        $item = $this->initQuery($request)->create($data);
        $this->afterStore($request, $item);
        $request->merge([
            'id' => $item->id,
        ]);
        return $this->show($request, $item->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request): JsonResponse
    {
        $item = $this->getItem($request, (int) $request->id, true);
        if (!$item) {
            abort(404, trans('item.not_found'));
        }
        return (new $this->resourceClass($item))->response($request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): JsonResponse
    {
        $rules = $this->getValidationRules($request, (int) $request->id);
        $this->validate($request, $rules);
        $item = $this->getItem($request, (int) $request->id);
        if (!$item) {
            abort(404, trans('item.not_found'));
        }
        if (!$item->is_editable) {
            return response()->json([
                'status' => trans('item.not_editable'),
            ], 400);
        }
        $data = $request->only($this->fillable);
        if (count($this->fillableTranslations)) {
            $data = array_merge($data, $request->only($this->getFillableTranslations()));
            $item->deleteTranslations();
        }
        $data = $this->beforeUpdate($request, $data);
        $item->update($data);
        $this->afterUpdate($request, $item);
        return $this->show($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): JsonResponse
    {
        $item = $this->getItem($request, (int) $request->id);
        if (!$item) {
            abort(404, trans('item.not_found'));
        }
        if (!$item->is_deletable) {
            return response()->json([
                'status' => trans('item.not_deletable'),
            ], 400);
        }
        try {
            $item->delete();
        } catch (QueryException $e) {
            if ($e->errorInfo[0] == 23000) {
                return response()->json([
                    'status' => trans('item.not_deletable'),
                ], 400);
            } else {
                throw $e;
            }
        }
        return response()->json(null, 204);
    }

    /**
     * Before store callback.
     */
    public function beforeStore(Request $request, array $data): array
    {
        return $data;
    }

    /**
     * Before update callback.
     */
    public function beforeUpdate(Request $request, array $data): array
    {
        return $data;
    }

    /**
     * After store callback.
     */
    public function afterStore(Request $request, Model $item): void
    {
        //
    }

    /**
     * After update callback.
     */
    public function afterUpdate(Request $request, Model $item): void
    {
        //
    }

    /**
     * Move the specified resource before another.
     */
    public function moveBefore(Request $request): JsonResponse
    {
        $item = $this->getItem($request, (int) $request->id);
        if (!$item) {
            abort(404, trans('item.not_found'));
        }
        $itemBefore = $this->getItem($request, (int) $request->before);
        $item->moveBefore($itemBefore);
        return $this->show($request, $item->id);
    }

    /**
     * Move the specified resource after another.
     */
    public function moveAfter(Request $request): JsonResponse
    {
        $item = $this->getItem($request, (int) $request->id);
        if (!$item) {
            abort(404, trans('item.not_found'));
        }
        $itemAfter = $this->getItem($request, (int) $request->after);
        if (!$itemAfter) {
            abort(404, trans('item.not_found'));
        }
        $item->moveAfter($itemAfter);
        return $this->show($request, $item->id);
    }
}
