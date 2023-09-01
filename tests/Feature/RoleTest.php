<?php

namespace Tests\Feature;

use App\Models\Access\Permission;
use Tests\ItemTest;
use App\Models\Access\Role;

class RoleTest extends ItemTest
{
    protected $dummyPermission;
    protected $validStructure = [
        'data' => [
            'id',
            'name',
            'title',
            'guard_name',
            'permissions',
        ],
    ];
    protected $uri = '/api/access/roles';
    protected $class = Role::class;

    public function setUp(): void
    {
        parent::setUp();
        $this->dummyPermission = Permission::factory()->create([
            'name' => 'Test Permission',
        ]);
        $this->dummyData = [
            'name' => 'Test Role',
            'guard_name' => 'web',
        ];
        $this->dummyAdditionalData = [
            'permissions' => [$this->dummyPermission->id],
        ];
        $this->dummyTranslatableData = [
            'title:' . config('translatable.fallback_locale') => 'Test Role',
        ];
    }

    protected function createItem($attributes = [])
    {
        $item = parent::createItem($attributes);
        $item->syncPermissions([$this->dummyPermission->id]);
        return $item;
    }

    /** @test */
    public function create_item()
    {
        $response = parent::create_item();
        $response->assertJsonCount(1, 'data.permissions');
    }

    /** @test */
    public function update_item()
    {
        $response = parent::update_item();
        $response->assertJsonCount(1, 'data.permissions');
    }

    /** @test */
    public function list_items_sorted_by_title()
    {
        $this->createItem($this->itemAttributes);
        $this->actingAs($this->user)
            ->getJson($this->uri . '?sort_by=title&sort_desc=0')
            ->assertSuccessful()
            ->assertJsonStructure($this->pivot
                ? ['data' => ['*' => ['pivot' => $this->validStructure['data']]]]
                : ['data' => ['*' => $this->validStructure['data']]]);
    }
}
