<?php

namespace Tests;

trait SortableTestTrait
{
    protected $item2Attributes = [];

    /** @test */
    public function move_item_before()
    {
        $item = $this->createItem($this->itemAttributes);
        $item2 = $this->createItem($this->item2Attributes);
        $this->actingAs($this->user)
            ->postJson($this->uri . '/' . ($this->pivot ? $item2->{$this->pivotAttribute} : $item2->id)
                . '/move-before/' . ($this->pivot ? $item->{$this->pivotAttribute} : $item->id))
            ->assertSuccessful()
            ->assertJsonPath($this->pivot ? 'data.pivot.position' : 'data.position', $item->position);
    }

    /** @test */
    public function move_item_after()
    {
        $item = $this->createItem($this->itemAttributes);
        $item2 = $this->createItem($this->item2Attributes);
        $this->actingAs($this->user)
            ->postJson($this->uri . '/' . ($this->pivot ? $item->{$this->pivotAttribute} : $item->id)
                . '/move-after/' . ($this->pivot ? $item2->{$this->pivotAttribute} : $item2->id))
            ->assertSuccessful()
            ->assertJsonPath($this->pivot ? 'data.pivot.position' : 'data.position', $item2->position);
    }
}
