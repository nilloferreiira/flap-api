<?php

namespace App\Http\Controllers\Api\Roles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Permission\Permission;
use App\Models\Role\Role;
use Illuminate\Support\Str;

class RolesController extends Controller
{
    // Lista todos os papéis
    public function index()
    {
        $roles = Role::paginate(10);
        return response()->json($roles);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Role::findOrFail($id));
    }

    // Cria um novo papel
    public function store(CreateRoleRequest $request)
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['name']);

        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        $role = Role::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'is_system_role' => false,
            'description' => $data['description'] ?? null,
        ]);

        //todo relacionar as permissões

        $role->permissions()->sync($permissions);

        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }

    // Atualiza um papel existente
    public function update(UpdateRoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($role->is_system_role) {
            return response()->json(['message' => 'System roles cannot be updated'], 403);
        }

        $data = $request->validated();

        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $role->update($data);

        $role->permissions()->sync($permissions);

        return response()->json(['message' => 'Role updated successfully', 'role' => $role], 200);
    }

    // Exclui (soft delete) um papel
    public function destroy($id)
    {
        //TODO autorizacao
        $role = Role::findOrFail($id);

        if ($role->is_system_role) {
            return response()->json(['message' => 'System roles cannot be deleted'], 403);
        }
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully'], 200);
    }

    public function getAllPermissions()
    {
        $permissions = Permission::all();

        return response()->json($permissions);
    }
}
