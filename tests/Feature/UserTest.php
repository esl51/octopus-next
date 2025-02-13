<?php

namespace Tests\Feature;

use App\Models\Access\Role;
use App\Models\Access\User;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\ItemTest;

class UserTest extends ItemTest
{
    protected $dummyRole;

    protected $validStructure = [
        'data' => [
            'id',
            'name',
            'email',
            'roles',
        ],
    ];

    protected $uri = '/api/access/users';

    protected $class = User::class;

    protected $admin;

    protected $itemAttributes = [
        'name' => 'Fake Test User',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->dummyRole = Role::factory()->create([
            'name' => 'test_role',
        ]);
        $this->dummyData = [
            'name' => 'Test User',
            'email' => 'test@test.app',
        ];
        $this->dummyAdditionalData = [
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 1024, 768),
            'roles' => [$this->dummyRole->id],
            'password' => 'secret00',
            'password_confirmation' => 'secret00',
        ];
        $this->admin = User::factory()->afterCreating(function ($model) {
            $model->assignRole('admin');
        })->create();
    }

    protected function createItem($attributes = [])
    {
        $item = parent::createItem($attributes);
        $item->syncRoles([$this->dummyRole->id]);

        return $item;
    }

    #[Test]
    public function create_item()
    {
        $response = parent::create_item();
        $response->assertJsonCount(1, 'data.roles');
        $data = $response->getData()->data;
        $item = ($this->class)::find($data->id);
        $this->storage->assertExists($item->avatar->path);
    }

    #[Test]
    public function update_item()
    {
        $response = parent::update_item();
        $response->assertJsonCount(1, 'data.roles');
        $data = $response->getData()->data;
        $item = ($this->class)::find($data->id);
        $this->storage->assertExists($item->avatar->path);
    }

    #[Test]
    public function disable_item()
    {
        $item = $this->createItem();
        $response = $this->actingAs($this->user)
            ->postJson($this->uri.'/'.$item->id.'/disable')
            ->assertSuccessful()
            ->assertJsonStructure($this->pivot
                ? ['data' => ['pivot' => $this->validStructure['data']]]
                : $this->validStructure)
            ->assertJsonPath('data.disabled_at', fn ($v) => $v != null);
    }

    #[Test]
    public function enable_item()
    {
        $item = $this->createItem([
            'disabled_at' => now(),
        ]);
        $response = $this->actingAs($this->user)
            ->postJson($this->uri.'/'.$item->id.'/enable')
            ->assertSuccessful()
            ->assertJsonStructure($this->pivot
                ? ['data' => ['pivot' => $this->validStructure['data']]]
                : $this->validStructure)
            ->assertJsonPath('data.disabled_at', null);
    }

    #[Test]
    public function list_items_by_role_name()
    {
        $this->createItem($this->itemAttributes);
        $this->actingAs($this->user)
            ->getJson($this->uri.'/?role='.$this->dummyRole->name)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['*' => $this->validStructure['data']]])
            ->assertJsonCount(1, 'data');
    }

    #[Test]
    public function admin_has_can_attribute()
    {
        $this->createItem($this->itemAttributes);
        $this->actingAs($this->admin)
            ->getJson('/api/auth/user')
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['can']]);
    }

    #[Test]
    public function admin_can_not_update_root()
    {
        $this->actingAs($this->admin)
            ->putJson('/api/access/users/'.$this->user->id, [
                'name' => 'Root User',
                'email' => 'new.email@root.org',
            ])
            ->assertStatus(403);
    }

    #[Test]
    public function admin_can_not_delete_root()
    {
        $this->actingAs($this->admin)
            ->deleteJson('/api/access/users/'.$this->user->id)
            ->assertStatus(403);
    }
}
