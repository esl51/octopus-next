<?php

namespace Database\Seeders;

use App\Models\Access\Permission;
use App\Models\Access\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'root' => [
                'ru' => ['title' => 'Суперадмин'],
                'en' => ['title' => 'Root'],
            ],
            'admin' => [
                'ru' => ['title' => 'Администратор'],
                'en' => ['title' => 'Admin'],
            ],
        ];
        foreach ($roles as $roleName => $roleData) {
            Role::factory()->create(array_merge([
                'name' => $roleName,
            ], $roleData));
        }

        $permissions = [
            'manage access' => [],
            'manage users' => ['admin'],
            'manage files' => ['admin'],
        ];
        foreach ($permissions as $item => $roles) {
            Permission::factory()->create([
                'name' => $item,
            ]);
            foreach ($roles as $roleItem) {
                $role = Role::where(['name' => $roleItem])->first();
                $role->givePermissionTo($item);
            }
        }
    }
}
