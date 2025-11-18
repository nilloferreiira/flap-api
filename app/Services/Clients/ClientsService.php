<?php

namespace App\Services\Clients;

use App\Constants\Permissions;
use App\Models\Client\Client;
use App\Models\User;
use App\Traits\CheckPermission;
use Illuminate\Http\JsonResponse;

class ClientsService
{
    use CheckPermission;

    /**
     * Lista todos os clientes.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function getAll(User $user)
    {
        if ($permission = $this->checkPermission($user, Permissions::VIEW_CLIENT)) return $permission;

        $clients = Client::paginate(10);
        return response()->json($clients);
    }

    /**
     * Retorna um cliente pelo ID.
     *
     * @param User $user
     * @param string $id
     * @return JsonResponse
     */
    public function getById(User $user, $id)
    {
        if ($permission = $this->checkPermission($user, Permissions::VIEW_CLIENT)) return $permission;

        $client = Client::find($id);
        if (!$client) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        return response()->json($client);
    }

    /**
     * Cria um novo cliente.
     *
     * @param User $user
     * @param array $data
     * @return JsonResponse
     */
    public function create(User $user, array $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::CREATE_CLIENT)) return $permission;

        $client = Client::create([
            'companyName' => $data['companyName'],
            'cnpj' => $data['cnpj'],
            'address' => $data['address'],
            'primaryContact' => $data['primaryContact'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'avatarUrl' => $data['avatarUrl'] ?? null,
            'agentUrl' => $data['agentUrl'] ?? null,
        ]);

        return response()->json(['message' => 'Cliente criado com sucesso', 'client' => $client], 201);
    }

    /**
     * Atualiza um cliente existente.
     *
     * @param User $user
     * @param string $id
     * @param array $data
     * @return JsonResponse
     */
    public function update(User $user, $id, array $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::EDIT_CLIENT)) return $permission;

        $client = Client::find($id);
        if (!$client) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        if (isset($data['email']) && $data['email'] !== $client->email) {
            $existingClientEmail = Client::where('email', $data['email'])->first();
            if ($existingClientEmail) {
                return response()->json(['message' => 'Este e-mail já está em uso por outro cliente.'], 422);
            }
        }

        if (isset($data['cnpj']) && $data['cnpj'] !== $client->cnpj) {
            $existingClientCnpj = Client::where('cnpj', $data['cnpj'])->first();
            if ($existingClientCnpj) {
                return response()->json(['message' => 'Este CNPJ já está em uso por outro cliente.'], 422);
            }
        }

        $client->update([
            'companyName' => $data['companyName'] ?? $client->companyName,
            'cnpj' => $data['cnpj'] ?? $client->cnpj,
            'address' => $data['address'] ?? $client->address,
            'primaryContact' => $data['primaryContact'] ?? $client->primaryContact,
            'phone' => $data['phone'] ?? $client->phone,
            'email' => $data['email'] ?? $client->email,
            'avatarUrl' => $data['avatarUrl'] ?? $client->avatarUrl,
            'agentUrl' => $data['agentUrl'] ?? $client->agentUrl,
        ]);

        return response()->json(['message' => 'Cliente atualizado com sucesso', 'client' => $client], 200);
    }

    /**
     * Exclui (soft delete) um cliente.
     *
     * @param User $user
     * @param string $id
     * @return JsonResponse
     */
    public function delete(User $user, $id)
    {
        if ($permission = $this->checkPermission($user, Permissions::DELETE_CLIENT)) return $permission;

        $client = Client::find($id);
        if (!$client) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        $client->delete();

        return response()->json(['message' => 'Cliente excluído com sucesso'], 200);
    }
}
