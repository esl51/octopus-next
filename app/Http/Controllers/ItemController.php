<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseDestroyRequest;
use App\Services\BaseService;
use App\Services\ItemService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class ItemController extends Controller
{
    protected ?string $class = null;
    protected ?string $resourceClass = null;
    protected ?string $storeRequestClass = null;
    protected ?string $updateRequestClass = null;
    protected ?string $destroyRequestClass = null;
    protected ?string $serviceClass = null;
    protected ?ItemService $service = null;

    public function __construct()
    {
        $this->class = $this->class ?: self::guessClass();
        $this->resourceClass = $this->resourceClass ?: self::guessResourceClass();
        $this->storeRequestClass = $this->storeRequestClass ?: self::guessStoreRequestClass();
        $this->updateRequestClass = $this->updateRequestClass ?: self::guessUpdateRequestClass();
        $this->destroyRequestClass = $this->destroyRequestClass ?: self::guessDestroyRequestClass();

        $this->serviceClass = $this->serviceClass ?: self::guessServiceClass();
        $this->service = app($this->serviceClass, [
            'modelClass' => $this->class,
        ]);
    }

    private static function guessClass(): string
    {
        return preg_replace('/(.+)\\\\Http\\\\Controllers\\\\(.+)Controller$/m', '$1\Models\\\$2', static::class);
    }

    private static function guessResourceClass(): string
    {
        return preg_replace('/(.+)\\\\Controllers\\\\(.+)Controller$/m', '$1\Resources\\\$2Resource', static::class);
    }

    private static function guessRequestClass(string $type): string
    {
        return preg_replace(
            '/(.+)\\\\Controllers\\\\(.+)Controller$/m',
            '$1\Requests\\\$2' . ucfirst($type) . 'Request',
            static::class
        );
    }

    private static function guessStoreRequestClass(): string
    {
        $requestClass = self::guessRequestClass('Store');
        if (class_exists($requestClass)) {
            return $requestClass;
        }
        return self::guessRequestClass('StoreUpdate');
    }

    private static function guessUpdateRequestClass(): string
    {
        $requestClass = self::guessRequestClass('Update');
        if (class_exists($requestClass)) {
            return $requestClass;
        }
        return self::guessRequestClass('StoreUpdate');
    }

    private static function guessDestroyRequestClass(): string
    {
        $requestClass = self::guessRequestClass('Destroy');
        if (class_exists($requestClass)) {
            return $requestClass;
        }
        return BaseDestroyRequest::class;
    }

    private static function guessServiceClass(): string
    {
        $class = preg_replace(
            '/(.+)\\\\Http\\\\Controllers\\\\(.+)Controller$/m',
            '$1\Services\\\$2Service',
            static::class
        );
        if (class_exists($class)) {
            return $class;
        }
        return BaseService::class;
    }

    public function findItem(int $id, $loadRelations = false)
    {
        $item = $this->service->get($id, $loadRelations);
        if (!$item) {
            abort(404, trans('item.not_found'));
        }
        return $item;
    }

    public function index(Request $request): JsonResponse
    {
        return call_user_func(
            [$this->resourceClass, 'collection'],
            $this->service->newItemsQuery($request->query())->paginate()
        )->response($request);
    }

    public function show(Request $request): JsonResponse
    {
        $item = $this->findItem($request->route('id'), true);
        return (new $this->resourceClass($item))->response($request);
    }

    public function store(Request $request): JsonResponse
    {
        $data = app($this->storeRequestClass)->validated();
        $item = $this->service->store($data, $request->allFiles());
        $request->route()->setParameter('id', $item->id);
        return $this->show($request);
    }

    public function update(Request $request): JsonResponse
    {
        $item = $this->findItem($request->route('id'));
        $data = app($this->updateRequestClass, [
            'item' => $item,
        ])->validated();
        $this->service->update($item->id, $data, $request->allFiles());
        return $this->show($request);
    }

    public function destroy(Request $request): JsonResponse
    {
        $item = $this->findItem($request->route('id'));
        app($this->destroyRequestClass, [
            'item' => $item,
        ])->validated();
        try {
            $this->service->destroy($item->id);
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

    public function moveBefore(Request $request): JsonResponse
    {
        $item = $this->findItem($request->route('id'));
        if (!$item) {
            abort(404, trans('item.not_found'));
        }
        $itemBefore = $this->findItem($request->route('before'));
        $this->service->moveBefore($item->id, $itemBefore->id);
        return $this->show($request);
    }

    public function moveAfter(Request $request): JsonResponse
    {
        $item = $this->findItem($request->route('id'));
        if (!$item) {
            abort(404, trans('item.not_found'));
        }
        $itemAfter = $this->findItem($request->route('after'));
        $this->service->moveAfter($item->id, $itemAfter->id);
        return $this->show($request);
    }
}
