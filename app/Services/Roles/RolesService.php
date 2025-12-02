<?php

namespace App\Services\Roles;

use App\Constants\Permissions;
use App\Models\Role\Role;
use App\Models\User;
use App\Traits\CheckPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class RolesService
{
    use CheckPermission;

    /**
     * Lista todos os cargos.
     *
     * @param  User $user
     * @return JsonResponse
     */
    public function getAll(User $user)
    {
        if ($permission = $this->checkPermission($user, Permissions::VIEW_ROLE)) return $permission;

        $roles = Role::paginate(10);
        return response()->json($roles);
    }

    /**
     * Retorna um cargo pelo ID.
     *
     * @param  User $user
     * @param  int $id
     * @return Role|null
     */
    public function getById(User $user, $id)
    {
        if ($permission = $this->checkPermission($user, Permissions::VIEW_ROLE)) return $permission;

        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Cargo não encontrado'], 404);
        }

        return response()->json($role);
    }

    /**
     * Cria um novo cargo.
     *
     * @param User $user
     * @param  array $data
     * @return Role
     */
    public function create(User $user, $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::CREATE_ROLE)) return $permission;

        $data['slug'] = Str::slug($data['name']);

        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        $role = Role::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'is_system_role' => false,
            'description' => $data['description'] ?? null,
        ]);

        $role->permissions()->sync($permissions);

        return response()->json(['message' => 'Cargo criado com sucesso', 'role' => $role], 201);
    }

    /**
     * Atualiza um cargo existente.
     *
     * @param User $user
     * @param  int   $id
     * @param  array $data
     * @return Role
     */
    public function update(User $user, $id, $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::EDIT_ROLE)) return $permission;

        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Cargo não encontrado'], 404);
        }

        if ($role->is_system_role) {
            return response()->json(['message' => 'Cargos de sistema não podem ser editados'], 403);
        }

        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $newSlug = Str::slug($data['name']);

        $role->update([
            'name' => $data['name'],
            'slug' => $newSlug,
            'description' => $data['description'] ?? null,
        ]);

        $role->permissions()->sync($permissions);

        return response()->json(['message' => 'Cargo atualizado com sucesso', 'role' => $role], 200);
    }

    /**
     * Exclui um cargo.
     *
     * @param User $user
     * @param  int $id
     * @return bool
     */
    public function delete(User $user, $id)
    {
        if ($permission = $this->checkPermission($user, Permissions::DELETE_ROLE)) return $permission;

        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Cargo não encontrado'], 404);
        }

        if ($role->is_system_role) {
            return response()->json(['message' => 'Cargos de sistema não podem ser excluídos'], 403);
        }
        $role->delete();

        return response()->json(['message' => 'Cargo excluído com sucesso'], 200);
    }
}
