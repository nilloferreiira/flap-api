<?php

namespace Database\Seeders;

use App\Models\Permission\Permission;
use App\Models\Role\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'slug' => 'admin',
                'name' => 'Admin',
                'description' => 'Administrador do sistema, acesso total.',
                'is_system_role' => true
            ],
            [
                'slug' => 'gerente_de_projetos',
                'name' => 'Gerente de Projetos',
                'description' => 'Gerencia projetos e clientes, acesso intermediário.',
                'is_system_role' => false
            ],
            [
                'slug' => 'usuario',
                'name' => 'Usuário',
                'description' => 'Usuário padrão, acesso restrito.',
                'is_system_role' => false
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);

            switch ($role->slug) {
                case 'admin':
                    $permissionIds = Permission::pluck('id')->toArray();
                    break;
                case 'gerente_de_projetos':
                    $permissionIds = Permission::whereIn('slug', [
                        'view_job',
                        'create_job',
                        'edit_job',
                        'delete_job',
                        'move_job',
                        'archive_job',
                        'view_client',
                        'create_client',
                        'edit_client',
                        'delete_client'
                    ])->pluck('id')->toArray();
                    break;
                case 'usuario':
                    $permissionIds = Permission::whereIn('slug', [
                        'view_job',
                        'view_client'
                    ])->pluck('id')->toArray();
                    break;
                default:
                    $permissionIds = [];
            }

            $role->permissions()->sync($permissionIds);
        }
    }
}
