<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Constants\Permissions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Traits\CheckPermission;

class DashboardController extends Controller
{

    use CheckPermission;

    /**
     * Retorna quantas tasks cada usuário possui (total).
     * Relação: $user->tasks()
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function tasksCountPerUser(Request $request): JsonResponse
    {

        $authUser = $request->user();

        $userId = $request->query('userId');

        if ($hasPermission = $this->checkPermission($authUser, Permissions::VIEW_DASHBOARD)) return $hasPermission;

        // Usa withCount para obter contagem eficiente por relacionamento
        // Quando user_id for passado, traz apenas esse usuário; caso contrário, traz top 10.
        $usersQuery = User::withCount('tasks')->orderBy('tasks_count', 'desc');

        if ($userId) {
            // Se foi passado user_id, traz apenas desse usuário
            $usersQuery->where('id', $userId);
        } else {
            // Caso padrão, limita ao top 10
            $usersQuery->limit(10);
        }

        $users = $usersQuery->get(['id', 'name']);

        $data = $users->map(function (User $u) {
            return [
                'user_id' => $u->id,
                'user_name' => $u->name ?? null,
                'tasks_count' => (int) $u->tasks_count,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Retorna quantas tasks cada usuário tem por lista.
     * Cada task tem a relação $task->listModel().
     *
     * Saída: para cada usuário, um array de { list_id, list_name, count }
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function tasksCountPerUserByList(Request $request): JsonResponse
    {
        $authUser = $request->user();

        if ($hasPermission = $this->checkPermission($authUser, Permissions::VIEW_DASHBOARD)) return $hasPermission;
        $userId = $request->query('userId');
        // Eager load tasks e suas listas para evitar N+1
        $usersQuery = User::query()->with(['tasks.listModel']);

        if ($userId) {
            // Se foi passado user_id, traz apenas desse usuário
            $usersQuery->where('id', $userId);
        } else {
            // Caso padrão, limita ao top 10
            $usersQuery->limit(10);
        }

        $users = $usersQuery->get(['id', 'name']);

        $result = $users->map(function (User $u) {
            $tasks = $u->tasks ?? collect();

            $byList = $tasks->groupBy(function ($task) {
                $list = $task->listModel ?? null;
                return $list ? $list->id : null;
            })->map(function ($group, $listId) {
                $first = $group->first();
                $list = $first ? ($first->listModel ?? null) : null;

                // Tenta pegar um nome útil da lista (name ou title)
                $listName = null;
                if ($list) {
                    $listName = $list->name ?? ($list->title ?? null);
                }

                return [
                    'list_id' => $listId,
                    'list_name' => $listName,
                    'count' => $group->count(),
                ];
            })->values();

            return [
                'user_id' => $u->id,
                'user_name' => $u->name ?? null,
                'total_tasks' => $tasks->count(),
                'tasks_by_list' => $byList,
            ];
        });

        return response()->json(['data' => $result]);
    }
}
