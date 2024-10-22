<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class ItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
