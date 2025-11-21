<?php

namespace App\Services\Tasks;

use App\Constants\Permissions;
use App\Models\Task\Task;
use App\Models\User;
use App\Traits\CheckPermission;

class TasksService
{
    use CheckPermission;

    public function getAll(User $user)
    {
        if ($permission = $this->checkPermission($user, Permissions::VIEW_JOB)) return $permission;

        $tasks = Task::paginate(20);
        return response()->json($tasks);
    }

    public function getById(User $user, $id)
    {
        if ($permission = $this->checkPermission($user, Permissions::VIEW_JOB)) return $permission;

        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Tarefa não encontrada'], 404);
        }

        return response()->json($task);
    }

    public function create(User $user, $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::CREATE_JOB)) return $permission;

        $task = Task::create([
            'list_id' => $data['list_id'],
            'client_id' => $data['client_id'],
            'title' => $data['title'],
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'description' => $data['description'] ?? null,
            'position' => $data['position'],
        ]);

        return response()->json(['message' => 'Tarefa criada com sucesso', 'tarefa' => $task], 201);
    }

    public function update(User $user, $id, $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::EDIT_JOB)) return $permission;

        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Tarefa não encontrada'], 404);
        }

        $task->update([
            'list_id' => $data['list_id'] ?? $task->list_id,
            'client_id' => $data['client_id'] ?? $task->client_id,
            'title' => $data['title'] ?? $task->title,
            'start_date' => $data['start_date'] ?? $task->start_date,
            'end_date' => $data['end_date'] ?? $task->end_date,
            'description' => $data['description'] ?? $task->description,
            'position' => $data['position'] ?? $task->position,
        ]);

        return response()->json(['message' => 'Tarefa atualizada com sucesso', 'tarefa' => $task], 200);
    }

    public function delete(User $user, $id)
    {
        if ($permission = $this->checkPermission($user, Permissions::DELETE_JOB)) return $permission;

        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Tarefa não encontrada'], 404);
        }
        $task->delete();

        return response()->json(['message' => 'Tarefa excluída com sucesso'], 204);
    }
}
