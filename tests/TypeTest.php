<?php

namespace Tests;

use App\Models\Access\User;
use PHPUnit\Framework\Attributes\Test;

abstract class TypeTest extends TestCase
{
    protected $user;

    protected $validStructure;

    protected $uri;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->afterCreating(function ($model) {
            $model->assignRole('root');
        })->create();
    }

    #[Test]
    public function list_items()
    {
        $this->actingAs($this->user)
            ->getJson($this->uri)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['*' => $this->validStructure['data']]]);
    }
}
