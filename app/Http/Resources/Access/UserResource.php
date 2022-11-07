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

        if (auth()->user()->can('manage users')) {
            $data['avatar_files'] = $this->getAvatarFiles();
        } else {
            $data['avatar_files'] = $this->getAvatarFiles('profile');
        }

        return $data;
    }
}
