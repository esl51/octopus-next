<?php

namespace Tests\Feature;

use Tests\ItemTest;
use App\Models\Permission;

class PermissionTest extends ItemTest
{
    protected $validStructure = [
        'data' => [
            'id',
            'name',
            'guard_name',
        ],
    ];
    protected $uri = '/api/access/permissions';
    protected $class = Permission::class;

    public function setUp(): void
    {
        parent::setUp();
        $this->dummyData = [
            'name' => 'Test Permission',
            'guard_name' => 'api',
        ];
    }
}
