<?php

namespace Database\Seeders;

use App\Models\Permission\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $permissions = [
            // Permissões gerais de jobs
            ['slug' => 'view_job',        'label' => 'Visualizar jobs'],
            ['slug' => 'create_job',      'label' => 'Criar jobs'],
            ['slug' => 'edit_job',        'label' => 'Editar jobs'],
            ['slug' => 'delete_job',      'label' => 'Excluir jobs'],
            ['slug' => 'move_job',        'label' => 'Mover jobs entre colunas'],
            ['slug' => 'archive_job',     'label' => 'Arquivar jobs'],

            // Permissões de usuários
            ['slug' => 'view_user',       'label' => 'Visualizar usuários'],
            ['slug' => 'create_user',     'label' => 'Criar usuários'],
            ['slug' => 'edit_user',       'label' => 'Editar usuários'],
            ['slug' => 'delete_user',     'label' => 'Excluir usuários'],

            // Permissões de clientes
            ['slug' => 'view_client',     'label' => 'Visualizar clientes'],
            ['slug' => 'create_client',   'label' => 'Criar clientes'],
            ['slug' => 'edit_client',     'label' => 'Editar clientes'],
            ['slug' => 'delete_client',   'label' => 'Excluir clientes'],

            // Permissões de cargos
            ['slug' => 'view_role',       'label' => 'Visualizar papéis'],
            ['slug' => 'create_role',     'label' => 'Criar papéis'],
            ['slug' => 'edit_role',       'label' => 'Editar papéis'],
            ['slug' => 'delete_role',     'label' => 'Excluir papéis'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }
    }
}
