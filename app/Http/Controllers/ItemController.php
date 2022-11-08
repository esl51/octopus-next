<?php

namespace App\Http\Controllers;

use App\Models\Model;
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
     * Return translated items
     * @var boolean
     */
    protected $translatable = false;
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
     * Unset empty translations.
     *
     * @param array $translations Translations
     * @return array Prepared translations
     */
    public static function prepareTranslations($translations)
    {
        foreach ($translations as $key => $value) {
            if (is_array($value)) {
                $translations[$key] = self::prepareTranslations($translations[$key]);
            }

            if (empty($translations[$key])) {
                unset($translations[$key]);
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
        if ($this->translatable) {
            $rules = TranslatableRuleFactory::make($rules);
        }
        return parent::validate($request, $rules, $messages, $customAttributes);
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
        if ($this->translatable) {
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
            $items->ordered();
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
            if ($this->translatable) {
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
        if ($this->translatable) {
            $data = array_merge($data, self::prepareTranslations($request->translations));
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
        if (!$item->is_editable) {
            return response()->json([
                'status' => trans('item.not_editable'),
            ], 400);
        }
        $data = $request->only($this->fillable);
        if ($this->translatable) {
            $data = array_merge($data, self::prepareTranslations($request->translations));
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
        $itemAfter = $this->getItem($after);
        $item->moveAfter($itemAfter);
        return $this->show($item->id);
    }

    /**
     * Remove the specified file from storage.
     *
     * @param  integer  $id
     * @param  string|null  $directory
     * @param  string|null  $fileName
     * @return \Illuminate\Http\Response
     */
    public function destroyFile($id, $directory = null, $fileName = null)
    {
        $item = $this->getItem($id);
        $item->deleteFile($fileName, $directory);

        return response()->json(null, 204);
    }

    /**
     * Get the specified file from storage.
     *
     * @param  integer  $id
     * @param  string|null  $directory
     * @param  string|null  $fileName
     * @return \Illuminate\Http\Response
     */
    public function getFile($id, $directory = null, $fileName = null)
    {
        $item = $this->getItem($id);
        return $item->getFile($fileName, $directory);
    }
}
