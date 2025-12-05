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
            // Permissões de listas
            ['slug' => 'create_list',     'label' => 'Criar listas'],
            ['slug' => 'edit_list',       'label' => 'Editar listas'],
            ['slug' => 'delete_list',     'label' => 'Excluir listas'],

            // Permissões gerais de jobs
            ['slug' => 'view_job',        'label' => 'Visualizar tarefas'],
            ['slug' => 'create_job',      'label' => 'Criar tarefas'],
            ['slug' => 'edit_job',        'label' => 'Editar tarefas'],
            ['slug' => 'delete_job',      'label' => 'Excluir tarefas'],
            ['slug' => 'move_job',        'label' => 'Mover as tarefas'],
            ['slug' => 'archive_job',     'label' => 'Arquivar tarefas'],
            ['slug' => 'edit_members',    'label' => 'Editar membros das tarefas'],
            ['slug' => 'comment_on_job',  'label' => 'Comentar em tarefas'],

            // Permissões de usuários
            ['slug' => 'view_user',       'label' => 'Visualizar usuários'],
            ['slug' => 'create_user',     'label' => 'Criar usuários'],
            ['slug' => 'edit_user',       'label' => 'Editar usuários'],
            ['slug' => 'archive_user',    'label' => 'Arquivar usuários'],
            ['slug' => 'delete_user',     'label' => 'Excluir usuários'],

            // Permissões de clientes
            ['slug' => 'view_client',     'label' => 'Visualizar clientes'],
            ['slug' => 'create_client',   'label' => 'Criar clientes'],
            ['slug' => 'edit_client',     'label' => 'Editar clientes'],
            ['slug' => 'archive_client',  'label' => 'Arquivar clientes'],
            ['slug' => 'delete_client',   'label' => 'Excluir clientes'],

            // Permissões de cargos
            ['slug' => 'view_role',       'label' => 'Visualizar cargos'],
            ['slug' => 'create_role',     'label' => 'Criar cargos'],
            ['slug' => 'edit_role',       'label' => 'Editar cargos'],
            ['slug' => 'archive_role',    'label' => 'Arquivar cargos'],
            ['slug' => 'delete_role',     'label' => 'Excluir cargos'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }
    }
}
