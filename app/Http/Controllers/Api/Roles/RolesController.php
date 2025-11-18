<?php

namespace App\Http\Controllers\Api\Roles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Permission\Permission;
use App\Services\Roles\RolesService;
use Illuminate\Http\Request;

class RolesController extends Controller
{

    protected RolesService $rolesService;

    public function __construct(RolesService $rolesService)
    {
        $this->rolesService = $rolesService;
    }

    // Lista todos os papéis
    public function index(Request $request)
    {
        $user = $request->user();
        return $this->rolesService->getAll($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        return $this->rolesService->getById($user, $id);
    }

    // Cria um novo cargo
    public function store(CreateRoleRequest $request)
    {

        $user = $request->user();

        $data = $request->validated();

        return $this->rolesService->create($user, $data);
    }

    // Atualiza um papel existente
    public function update(UpdateRoleRequest $request, $id)
    {
        $user = $request->user();

        $data = $request->validated();

        return $this->rolesService->update($user, $id, $data);
    }

    // Exclui (soft delete) um papel
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        return $this->rolesService->delete($user, $id);
    }

    public function getAllPermissions()
    {
        $permissions = Permission::all();

        return response()->json($permissions);
    }
}
