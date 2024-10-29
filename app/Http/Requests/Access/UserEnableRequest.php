<?php

namespace App\Http\Requests\Access;

use App\Http\Requests\ItemRequest;
use App\Models\Access\User;

class UserEnableRequest extends ItemRequest
{
    protected $item = null;

    public function __construct($item = null)
    {
        $this->item = $item;
    }

    public function authorize(): bool
    {
        if (!$this->item) {
            $this->item = User::find((int) $this->route('id'));
        }
        return $this->item->is_enablable;
    }
}
