<?php

namespace App\Services\Boards;

use App\Constants\Permissions;
use App\Models\Board\Board;
use App\Models\User;
use App\Traits\CheckPermission;
use Illuminate\Support\Str;

class BoardsService
{
    use CheckPermission;

    /**
     * Lista todos os boards.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(User $user)
    {
        if ($permission = $this->checkPermission($user, Permissions::VIEW_JOB)) return $permission;

        $boards = Board::paginate(20);
        return response()->json($boards);
    }

    /**
     * Retorna um board pelo ID.
     *
     * @param User $user
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById(User $user, $id)
    {
        if ($permission = $this->checkPermission($user, Permissions::VIEW_JOB)) return $permission;

        $board = Board::find($id);

        if (!$board) {
            return response()->json(['message' => 'Board não encontrado'], 404);
        }

        return response()->json($board);
    }

    /**
     * Cria um novo board.
     *
     * @param User $user
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(User $user, $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::CREATE_JOB)) return $permission;

        $board = Board::create([
            'name' => $data['name']
        ]);

        return response()->json(['message' => 'Board criado com sucesso', 'board' => $board], 201);
    }

    /**
     * Atualiza um board existente.
     *
     * @param User $user
     * @param int $id
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(User $user, $id, $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::EDIT_JOB)) return $permission;

        $board = Board::findOrFail($id);

        $board->update([
            'name' => $data['name'],
        ]);

        return response()->json(['message' => 'Board atualizado com sucesso', 'board' => $board], 200);
    }

    /**
     * Exclui um board.
     *
     * @param User $user
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(User $user, $id)
    {
        if ($permission = $this->checkPermission($user, Permissions::DELETE_JOB)) return $permission;

        $board = Board::findOrFail($id);
        $board->delete();

        return response()->json(['message' => 'Board excluído com sucesso'], 204);
    }
}
