<?php

namespace App\Services\Tasks;

use App\Constants\Permissions;
use App\Models\List\ListModel;
use App\Models\Task\Task;
use App\Models\User;
use App\Traits\CheckPermission;
use Illuminate\Support\Facades\DB;

class TasksService
{
    use CheckPermission;

    public function getAll(User $user)
    {
        if ($permission = $this->checkPermission($user, Permissions::VIEW_JOB)) return $permission;

        $tasks = Task::orderBy('position')->get();
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

        $lastPosition = Task::max('position') ?? 0;
        $data['position'] = $lastPosition + 1;

        $task = Task::create($data);
        $task->refresh();
        return response()->json(['message' => 'Tarefa criada com sucesso', 'task' => $task], 201);
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
        $task->refresh();
        return response()->json(['message' => 'Tarefa atualizada com sucesso', 'task' => $task], 200);
    }

    public function moveTask(User $user, $id, $listId, $position)
    {
        //TODO permissao de mover task
        if ($permission = $this->checkPermission($user, Permissions::EDIT_JOB)) return $permission;

        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Tarefa não encontrada'], 404);
        }

        $list = ListModel::find($listId);
        if (!$list) {
            return response()->json(['message' => 'Lista não encontrada'], 404);
        }

        DB::transaction(function () use ($task, $listId, $position) {
            // Update positions of other tasks in the target list
            //TODO lidar com max position

            if ($position > 0) $position = 0;

            $maxPosition = Task::where('list_id', $listId)->max('position') ?? 0;
            if ($position > $maxPosition + 1) {
                $position = $maxPosition + 1;
            }

            if ($task->list_id == $listId) {
                $this->updatePositionsWithinList($task, $position);
                $task->position = $position;
                $task->save();
            } else {
                $this->updatePositionsBetweenLists($task, $listId, $position);
                $task->list_id = $listId;
                $task->position = $position;
                $task->save();
            }
        });

        return response()->json(['message' => 'Tarefa atualizada com sucesso', 'task' => $task], 200);
    }

    public function updatePositionsBetweenLists(Task $task, $newListId, $newPosition)
    {

        // Update positions in the old list
        $this->updatePositionsWithinList($task, $newPosition);

        // Update positions in the new list
        if ($newPosition == $task->position) return response()->json(['message' => 'Ja esta na msm lista'], 200);

        if ($newPosition < $task->position) {
            Task::query()
                ->where('list_id', $newListId)
                ->whereBetween('position', [$newPosition, $task->position - 1])
                ->increment('position');
        } else {
            Task::query()
                ->where('list_id', $newListId)
                ->whereBetween('position', [$task->position + 1, $newPosition])
                ->decrement('position');
        }
    }

    public function updatePositionsWithinList(Task $task, $newPosition)
    {

        if ($newPosition == $task->position) return;

        if ($newPosition < $task->position) {
            Task::query()
                ->where('list_id', $task->list_id)
                ->whereBetween('position', [$newPosition, $task->position - 1])
                ->increment('position');
        } else {
            Task::query()
                ->where('list_id', $task->list_id)
                ->whereBetween('position', [$task->position + 1, $newPosition])
                ->decrement('position');
        }
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
