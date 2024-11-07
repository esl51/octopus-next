<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\ItemController;
use App\Http\Requests\Access\UserDisableRequest;
use App\Http\Requests\Access\UserEnableRequest;
use Illuminate\Http\JsonResponse;

class UserController extends ItemController
{
    public function disable(UserDisableRequest $request): JsonResponse
    {
        $item = $this->findItem((int) $request->route('id'));
        $this->service->disable($item->id);

        return $this->show($request);
    }

    public function enable(UserEnableRequest $request): JsonResponse
    {
        $item = $this->findItem((int) $request->route('id'));
        $this->service->enable($item->id);

        return $this->show($request);
    }
}
