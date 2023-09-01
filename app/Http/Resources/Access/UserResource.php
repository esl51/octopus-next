<?php

namespace App\Http\Resources\Access;

use App\Http\Resources\ItemResource;
use Illuminate\Http\Request;

class UserResource extends ItemResource
{
    public function toArray(Request $request): array
    {
        $this->makeVisible('roles');
        if ($this->avatar) {
            $this->avatar->append('is_editable', 'is_deletable');
        }
        $data = parent::toArray($request);

        return $data;
    }
}
