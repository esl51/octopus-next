<?php

namespace App\Http\Controllers\Files;

use App\Http\Controllers\ItemController;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends ItemController
{
    public function download(Request $request): StreamedResponse
    {
        $item = $this->findItem((int) $request->route('id'));

        return $item->download();
    }

    public function view(Request $request): StreamedResponse
    {
        $item = $this->findItem((int) $request->route('id'));

        return $item->response();
    }
}
