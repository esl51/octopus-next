<?php

namespace Tests;

use Tests\TestCase;
use App\Models\Access\User;

abstract class TypeTest extends TestCase
{
    protected $user;
    protected $dummyData = [];
    protected $validStructure;
    protected $uri;
    protected $class;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->afterCreating(function ($model) {
            $model->assignRole('root');
        })->create();
    }

    /** @test */
    public function list_items()
    {
        $this->actingAs($this->user)
            ->getJson($this->uri)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['*' => $this->validStructure['data']]]);
    }
}
