<?php

namespace App\Http\Resources\Access;

use App\Http\Resources\Files\FileResource;
use App\Http\Resources\ItemResource;
use Illuminate\Http\Request;

class UserResource extends ItemResource
{
    public function toArray(Request $request): array
    {
        $this->makeVisible('roles');
        $this->append('is_disablable', 'is_enablable');
        $data = parent::toArray($request);
        $data['avatar'] = FileResource::make($this->whenLoaded('avatar'));
        return $data;
    }
}
