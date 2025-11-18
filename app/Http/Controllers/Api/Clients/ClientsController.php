<?php

namespace App\Http\Controllers\Api\Clients;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clients\CreateClientRequest;
use App\Http\Requests\Clients\UpdateClientRequest;
use App\Services\Clients\ClientsService;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    protected ClientsService $clientsService;

    public function __construct(ClientsService $clientsService)
    {
        $this->clientsService = $clientsService;
    }

    // Lista todos os clientes
    public function index(Request $request)
    {
        $user = $request->user();
        return $this->clientsService->getAll($user);
    }

    /**
     * Exibe um cliente específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        return $this->clientsService->getById($user, $id);
    }

    // Cria um novo cliente
    public function store(CreateClientRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        return $this->clientsService->create($user, $data);
    }

    // Atualiza um cliente existente
    public function update(UpdateClientRequest $request, $id)
    {
        $user = $request->user();
        $data = $request->validated();
        return $this->clientsService->update($user, $id, $data);
    }

    // Exclui (soft delete) um cliente
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        return $this->clientsService->delete($user, $id);
    }
}
