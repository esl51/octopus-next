<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->append(['is_editable', 'is_deletable']);
        if ($this->pivot) {
            $this->pivot->append(['is_editable', 'is_deletable']);
        }
        return parent::toArray($request);
    }
}
