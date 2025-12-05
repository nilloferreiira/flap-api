<?php

namespace App\Services\Users;

use App\Constants\Permissions;
use App\Models\User;
use App\Traits\CheckPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UsersService
{
    use CheckPermission;

    /**
     * Lista todos os usuários.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function getAll(User $user)
    {
        if ($permission = $this->checkPermission($user, Permissions::VIEW_USER)) return $permission;

        $users = User::paginate(10);
        return response()->json($users);
    }

    /**
     * Retorna um usuário pelo ID.
     *
     * @param User $user
     * @param int $id
     * @return JsonResponse
     */
    public function getById(User $user, $id)
    {
        if ($permission = $this->checkPermission($user, Permissions::VIEW_USER)) return $permission;

        $foundUser = User::find($id);
        if (!$foundUser) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        return response()->json($foundUser);
    }

    /**
     * Atualiza um usuário.
     *
     * @param User $user
     * @param int $id
     * @param array $data
     * @return JsonResponse
     */
    public function update(User $user, $id, array $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::EDIT_USER)) return $permission;

        $foundUser = User::find($id);
        if (!$foundUser) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        if (isset($data['email']) && $data['email'] !== $foundUser->email) {
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser) {
                return response()->json(['message' => 'Este e-mail já está em uso por outro usuário.'], 422);
            }
        }

        // Remove campos vazios para não sobrescrever com null
        $filteredData = array_filter($data, fn($v) => $v !== null && $v !== '');

        $foundUser->update($filteredData);

        return response()->json(['message' => 'Usuário atualizado com sucesso', 'user' => $foundUser], 200);
    }

    /**
     * Exclui (soft delete) um usuário.
     *
     * @param User $user
     * @param int $id
     * @return JsonResponse
     */
    public function delete(User $user, $id)
    {
        if ($permission = $this->checkPermission($user, Permissions::DELETE_USER)) return $permission;

        $foundUser = User::find($id);
        if (!$foundUser) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        $foundUser->delete();

        return response()->json(['message' => 'Usuário excluído com sucesso'], 204);
    }

    public function updateProfile(User $user, array $data)
    {
        if (!isset($data['current_password']) || !Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Senha atual inválida'], 400);
        }

        unset($data['current_password']);
        $user->update(['password' => $data['password']]);

        return response()->json(['message' => 'Senha atualizada com sucesso', 'user' => $user], 200);
    }
}
