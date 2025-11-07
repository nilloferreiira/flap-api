<?php

namespace App\Http\Controllers\Api\Roles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Role\Role;

class RolesController extends Controller
{
    // Lista todos os papéis
    public function index()
    {
        $roles = Role::paginate(10);
        return response()->json($roles);
    }

    // Cria um novo papel
    public function store(CreateRoleRequest $request)
    {
        $data = $request->validated();
        $role = Role::create($data);
        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }

    // Atualiza um papel existente
    public function update(UpdateRoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);

        $data = $request->validated();

        $role->update($data);
        return response()->json(['message' => 'Role updated successfully', 'role' => $role], 200);
    }

    // Exclui (soft delete) um papel
    public function destroy($id)
    {
        //TODO autorizacao
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully'], 200);
    }
}
