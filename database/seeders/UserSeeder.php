<?php

namespace Database\Seeders;

use App\Models\Role\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::where('slug', 'admin')->first();

        User::firstOrCreate(
            ['email' => env('ADMIN_SEEDER_EMAIL')],
            [
                'name' => 'Administrador',
                'password' => env('ADMIN_SEEDER_PWD'),
                'role_id' => $adminRole ? $adminRole->id : null,
            ]
        );
    }
}
