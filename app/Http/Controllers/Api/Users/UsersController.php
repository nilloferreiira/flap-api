<?php

namespace App\Http\Controllers\Api\Users;

use App\Constants\Permissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use App\Services\Users\UsersService;
use Illuminate\Http\Request;

class UsersController extends Controller
{

    protected UsersService $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $authenticatedUser = $request->user();

        if (!$authenticatedUser->hasPermission(Permissions::VIEW_USER)) {
            return response()->json(['message' => 'Você não tem permissão para visualizar usuários'], 403);
        }

        return $this->usersService->getAll($authenticatedUser);
    }

    public function getAll(Request $request)
    {
        $authenticatedUser = $request->user();

        if (!$authenticatedUser->hasPermission(Permissions::VIEW_USER)) {
            return response()->json(['message' => 'Você não tem permissão para visualizar usuários'], 403);
        }

        return User::all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $authenticatedUser = $request->user();

        if (!$authenticatedUser->hasPermission(Permissions::VIEW_USER)) {
            return response()->json(['message' => 'Você não tem permissão para visualizar usuários'], 403);
        }

        return $this->usersService->getById($authenticatedUser, $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Users\UpdateUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $authenticatedUser = $request->user();

        if (!$authenticatedUser->hasPermission(Permissions::EDIT_USER)) {
            return response()->json(['message' => 'Você não tem permissão para atualizar usuários'], 403);
        }

        $data = $request->only(['name', 'email', 'password', 'role_id']);
        return $this->usersService->update($authenticatedUser, $id, $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $authenticatedUser = $request->user();

        if (!$authenticatedUser->hasPermission(Permissions::DELETE_USER)) {
            return response()->json(['message' => 'Você não tem permissão para excluir usuários'], 403);
        }

        return $this->usersService->delete($authenticatedUser, $id);
    }
}
