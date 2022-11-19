<?php

namespace App\Http\Resources\Access;

use App\Http\Resources\ItemResource;

class UserResource extends ItemResource
{
    /**
     * @inheritDoc
     */
    public function toArray($request)
    {
        $this->makeVisible('roles');
        $data = parent::toArray($request);

        return $data;
    }
}
