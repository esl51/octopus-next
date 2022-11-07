<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $root = User::factory()->create([
            'name' => 'Root',
            'password' => Hash::make('root'),
            'email' => 'root@root.org',
            'email_verified_at' => null,
        ]);
        $root->assignRole('root');
    }
}
