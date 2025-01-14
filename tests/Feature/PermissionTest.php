<?php

namespace Tests\Feature;

use App\Models\Access\Permission;
use Tests\ItemTest;

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

    protected function setUp(): void
    {
        parent::setUp();
        $this->dummyData = [
            'name' => 'Test Permission',
            'guard_name' => 'api',
        ];
    }
}
