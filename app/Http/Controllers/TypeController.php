<?php

namespace App\Http\Controllers;

use App\Http\Resources\TypeResource;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    /**
     * Display a listing of property types.
     *
     * @return \Illuminate\Http\Response
     */
    public function propertyTypes(Request $request)
    {
        return TypeResource::collection(PropertyType::cases());
    }
}
