<?php

namespace App\Http\Requests;

abstract class ItemDestroyRequest extends ItemRequest
{
    protected $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function authorize(): bool
    {
        return $this->item->is_deletable;
    }
}
