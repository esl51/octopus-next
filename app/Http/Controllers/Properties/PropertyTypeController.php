<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Http\Resources\TypeResource;
use App\Models\Properties\PropertyType;

class PropertyTypeController extends Controller
{
    public function __invoke()
    {
        return TypeResource::collection(PropertyType::cases())->response();
    }
}
