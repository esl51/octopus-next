<?php

namespace App\Http\Controllers;

use App\Models\TranslatableRuleFactory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

abstract class ItemController extends Controller
{
    /**
     * Item class name
     * @var string
     */
    protected $class;
    /**
     * Resource class name
     * @var string
     */
    protected $resourceClass;
    /**
     * Fillable properties of class
     * @var array
     */
    protected $fillable = [];
    /**
     * Fillable translatable properties of class
     * @var array
     */
    protected $fillableTranslations = [];
    /**
     * Relations for loading
     * @var array
     */
    protected $with = [];
    /**
     * Counts for loading
     * @var array
     */
    protected $withCount = [];
    /**
     * Return sortable listing
     * @var boolean
     */
    protected $sortable = false;
    /**
     * Has search
     * @var boolean
     */
    protected $search = true;

    /**
     * Get validation rules for new or existing resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  integer|null  $id
     * @return array
     */
    abstract protected function getValidationRules(Request $request, $id = null);

    /**
     * Get messages.
     *
     * @return array
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
     *
     * @return array Prepared fillable translations
     */
    public function getFillableTranslations()
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
     * Check permissions hook.
     *
     * @param \App\EloquentBuilder $query
     * @return \App\EloquentBuilder
     */
    public function checkPermissions($query)
    {
        return $query;
    }

    /**
     * "sort_by" replacements
     *
     * @return array
     */
    protected function sortByReplacements()
    {
        return [];
    }

    /**
     * "sort_by" translations
     *
     * @return array
     */
    protected function sortByTranslations()
    {
        return [];
    }


    /**
     * @inheritDoc
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $allMessages = $this->getMessages();
        $allCustomAttributes = $this->getCustomAttributes();
        if (count($this->fillableTranslations)) {
            $rules = TranslatableRuleFactory::make($rules);

            foreach ($this->fillableTranslations as $attribute) {
                foreach (config('translatable.locales') as $locale) {
                    $allCustomAttributes[$attribute . ':' . $locale] =
                        trans('validation.attributes.' . $attribute) . ' (' . $locale . ')';
                }
            }
        }
        return parent::validate(
            $request,
            $rules,
            array_merge($messages, $allMessages),
            array_merge($customAttributes, $allCustomAttributes)
        );
    }

    /**
     * Get items query.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\EloquentBuilder
     */
    protected function newItemsQuery(Request $request)
    {
        $table = (new $this->class())->getTable();
        $columns = $this->class::getColumns();

        $items = call_user_func([$this->class, 'query']);
        $items->select($columns)->groupBy($columns);

        $items = $this->checkPermissions($items);
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

        $id = intval($request->id);
        if ($id && !$request->search) {
            $items->where($table . '.id', $id);
        }

        if ($this->search) {
            $search = htmlspecialchars($request->search);
            if (is_numeric($search)) {
                $items->where($table . '.id', $search);
            }
        }

        $sortBy = htmlspecialchars($request->sort_by);
        $sortDesc = filter_var($request->sort_desc, FILTER_VALIDATE_BOOLEAN);
        if ($sortBy) {
            $replacements = $this->sortByReplacements();
            $translations = $this->sortByTranslations();
            if (!empty($replacements[$sortBy])) {
                $items->orderBy($replacements[$sortBy], $sortDesc ? 'desc' : 'asc');
            } elseif (!empty($translations[$sortBy])) {
                $items->orderByTranslation($sortBy, $sortDesc ? 'desc' : 'asc');
                $items->groupBy($translations[$sortBy]);
            } else {
                $items->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
            }
        }
        if (!empty($this->sortable)) {
            $items->sorted();
        }

        return $items;
    }

    /**
     * Get item.
     *
     * @param  integer  $id
     * @param  boolean  $withRelations
     * @return mixed
     */
    public function getItem($id, $withRelations = false)
    {
        $columns = $this->class::getColumns();

        $item = call_user_func([$this->class, 'query']);
        $item->select($columns)->groupBy($columns);

        $item = $this->checkPermissions($item);
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
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return call_user_func([$this->resourceClass, 'collection'], $this->newItemsQuery($request)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = $this->getValidationRules($request);
        $this->validate($request, $rules);
        $data = $request->only($this->fillable);
        if (count($this->fillableTranslations)) {
            $data = array_merge($data, $request->only($this->getFillableTranslations()));
        }
        $data = $this->beforeStore($request, $data);
        $item = call_user_func([$this->class, 'create'], $data);
        $this->afterStore($request, $item);
        return $this->show($item->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = $this->getItem($id, true);
        if (!$item) {
            abort(404, trans('item.not_found'));
        }
        return new $this->resourceClass($item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = $this->getValidationRules($request, intval($id));
        $this->validate($request, $rules);
        $item = $this->getItem($id);
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
        return $this->show($item->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = call_user_func([$this->class, 'find'], $id);
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
     *
     * @param  \Illuminate\Http\Request $request
     * @param  array $data
     * @return array
     */
    public function beforeStore(Request $request, array $data)
    {
        return $data;
    }

    /**
     * Before update callback.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  array $data
     * @return array
     */
    public function beforeUpdate(Request $request, array $data)
    {
        return $data;
    }

    /**
     * After store callback.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $item
     */
    public function afterStore(Request $request, $item)
    {
        //
    }

    /**
     * After update callback.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $item
     */
    public function afterUpdate(Request $request, $item)
    {
        //
    }

    /**
     * Move the specified resource before another.
     *
     * @param  integer  $id
     * @param  integer  $before
     * @return  \Illuminate\Http\Response
     */
    public function moveBefore($id, $before)
    {
        $item = $this->getItem($id);
        if (!$item) {
            abort(404, trans('item.not_found'));
        }
        $itemBefore = $this->getItem($before);
        $item->moveBefore($itemBefore);
        return $this->show($item->id);
    }

    /**
     * Move the specified resource after another.
     *
     * @param  integer  $id
     * @param  integer  $after
     * @return  \Illuminate\Http\Response
     */
    public function moveAfter($id, $after)
    {
        $item = $this->getItem($id);
        if (!$item) {
            abort(404, trans('item.not_found'));
        }
        $itemAfter = $this->getItem($after);
        if (!$itemAfter) {
            abort(404, trans('item.not_found'));
        }
        $item->moveAfter($itemAfter);
        return $this->show($item->id);
    }
}
