<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->append(['is_editable', 'is_deletable']);
        if ($this->pivot) {
            $this->pivot->append(['is_editable', 'is_deletable']);
        }

        return parent::toArray($request);
    }
}
