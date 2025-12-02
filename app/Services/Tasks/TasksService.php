<?php

namespace App\Services\Tasks;

use App\Constants\Permissions;
use App\Models\List\ListModel;
use App\Models\Task\Task;
use App\Models\User;
use App\Models\Task\Elements\Comment;
use App\Models\Task\Elements\Checklist;
use App\Models\Task\Elements\Link;
use App\Models\Task\Elements\TaskMember;
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
            'priority' => $data['priority'] ?? $task->priority,
            'status' => $data['status'] ?? $task->status,
        ]);
        $task->refresh();
        return response()->json(['message' => 'Tarefa atualizada com sucesso', 'task' => $task], 200);
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

    public function moveTask(User $user, $id, $listId, $position)
    {
        //TODO permissao de mover task
        if ($permission = $this->checkPermission($user, Permissions::MOVE_JOB)) return $permission;

        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Tarefa não encontrada'], 404);
        }

        $list = ListModel::find($listId);
        if (!$list) {
            return response()->json(['message' => 'Lista não encontrada'], 404);
        }

        if ($position < 0) $position = 0;

        $maxPosition = Task::where('list_id', $listId)->max('position') ?? 0;
        // Garante que o position nunca ultrapasse por mais de "1" o maximo da lista, assim a sequencia se mantem correta
        if ($position > $maxPosition + 1) {
            $position = $maxPosition + 1;
        }

        // dd($task, $listId, $position);

        DB::transaction(function () use ($task, $listId, $position, $user) {
            // Update positions of other tasks in the target list
            if ($task->list_id == $listId) {
                $this->updatePositionsWithinList($task, $position);
                $task->position = $position;
                $task->save();
            } else {
                if ($permission = $this->checkPermission($user, Permissions::MOVE_JOBS_BETWEEN_LISTS)) return $permission;

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

        $this->compactPositionsAfterRemove($task);

        // Update positions in the new list
        Task::query()
            ->where('list_id', $newListId)
            ->where('position', '>=', $newPosition)
            ->increment('position');
    }

    public function updatePositionsWithinList(Task $task, $newPosition)
    {
        if ($task->position > $newPosition) {
            Task::query()
                ->where('list_id', $task->list_id)
                ->whereBetween('position', [$newPosition, $task->position - 1])
                ->increment('position');
        } else if ($task->position < $newPosition) {
            Task::query()
                ->where('list_id', $task->list_id)
                ->whereBetween('position', [$task->position + 1, $newPosition])
                ->decrement('position');
        } else {
            Task::query()
                ->where('list_id', $task->list_id)
                ->where('position', '>=', $newPosition)
                ->increment('position');
        }
    }

    public function compactPositionsAfterRemove(Task $task)
    {
        $tasks = Task::query()->where('list_id', $task->list_id)
            ->where('id', '!=', $task->id)
            ->where('position', '>', $task->position)
            ->get();

        $tasks->each->decrement('position');
        // dd($tasks);
    }

    // ---------- Task elements

    // Comments
    public function createComment(User $user, $taskId, $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::COMMENT_ON_JOB)) return $permission;

        $task = Task::find($taskId);
        if (!$task) return response()->json(['message' => 'Tarefa não encontrada'], 404);

        $content = $data['content'] ?? null;
        if (! $content) return response()->json(['message' => 'Conteúdo do comentário é obrigatório'], 422);

        $comment = Comment::createFor(
            $task,
            $user,
            $content
        );

        return response()->json(['message' => 'Comentário criado com sucesso', 'comment' => $comment], 201);
    }

    public function updateComment(User $user, $taskId, $id, $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::COMMENT_ON_JOB)) return $permission;

        $comment = Comment::where('id', $id)->where('task_id', $taskId)->first();
        if (! $comment) return response()->json(['message' => 'Comentário não encontrado'], 404);

        $comment->content = $data['content'] ?? $comment->content;
        $comment->save();

        return response()->json(['message' => 'Comentário atualizado com sucesso', 'comment' => $comment], 200);
    }

    public function deleteComment(User $user, $taskId, $id)
    {
        // dd($taskId, $id);
        if ($permission = $this->checkPermission($user, Permissions::COMMENT_ON_JOB)) return $permission;

        $comment = Comment::where('id', $id)->where('task_id', $taskId)->first();
        if (! $comment) return response()->json(['message' => 'Comentário não encontrado'], 404);

        if ($comment->user_id != $user->id) {
            return response()->json(['message' => 'Você não tem permissão para excluir este comentário'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'Comentário excluído com sucesso'], 204);
    }

    // ---------- Task elements: Checklists
    public function createChecklist(User $user, $taskId, $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::EDIT_JOB)) return $permission;

        $task = Task::find($taskId);
        if (!$task) return response()->json(['message' => 'Tarefa não encontrada'], 404);

        $title = $data['title'] ?? null;
        if (! $title) return response()->json(['message' => 'Título é obrigatório'], 422);

        $checklist = Checklist::create([
            'task_id' => $task->id,
            'title' => $title,
        ]);

        return response()->json(['message' => 'Checklist criado com sucesso', 'checklist' => $checklist], 201);
    }

    public function updateChecklist(User $user, $taskId, $id, $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::EDIT_JOB)) return $permission;

        $checklist = Checklist::where('id', $id)->where('task_id', $taskId)->first();
        if (! $checklist) return response()->json(['message' => 'Checklist não encontrado'], 404);

        $checklist->title = $data['title'] ?? $checklist->title;
        $checklist->save();

        return response()->json(['message' => 'Checklist atualizado com sucesso', 'checklist' => $checklist], 200);
    }

    public function deleteChecklist(User $user, $taskId, $id)
    {
        if ($permission = $this->checkPermission($user, Permissions::EDIT_JOB)) return $permission;

        $checklist = Checklist::where('id', $id)->where('task_id', $taskId)->first();
        if (! $checklist) return response()->json(['message' => 'Checklist não encontrado'], 404);

        $checklist->delete();
        return response()->json(['message' => 'Checklist excluído com sucesso'], 204);
    }

    // ---------- Task elements: Links
    public function createLink(User $user, $taskId, $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::EDIT_JOB)) return $permission;

        $task = Task::find($taskId);
        if (!$task) return response()->json(['message' => 'Tarefa não encontrada'], 404);

        $url = trim($data['url'] ?? '');
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['message' => 'URL inválida'], 422);
        }

        $link = Link::create([
            'task_id' => $task->id,
            'title' => $data['title'] ?? null,
            'url' => $url,
        ]);

        return response()->json(['message' => 'Link criado com sucesso', 'link' => $link], 201);
    }

    public function updateLink(User $user, $taskId, $id, $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::EDIT_JOB)) return $permission;

        $link = Link::where('id', $id)->where('task_id', $taskId)->first();
        if (! $link) return response()->json(['message' => 'Link não encontrado'], 404);

        $url = trim($data['url'] ?? $link->url);
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['message' => 'URL inválida'], 422);
        }

        $link->update([
            'title' => $data['title'] ?? $link->title,
            'url' => $url,
        ]);

        return response()->json(['message' => 'Link atualizado com sucesso', 'link' => $link], 200);
    }

    public function deleteLink(User $user, $taskId, $id)
    {
        if ($permission = $this->checkPermission($user, Permissions::EDIT_JOB)) return $permission;

        $link = Link::where('id', $id)->where('task_id', $taskId)->first();
        if (! $link) return response()->json(['message' => 'Link não encontrado'], 404);

        $link->delete();
        return response()->json(['message' => 'Link excluído com sucesso'], 204);
    }

    // ---------- Task elements: Members
    public function createMember(User $user, $taskId, $data)
    {
        if ($permission = $this->checkPermission($user, Permissions::EDIT_MEMBERS)) return $permission;

        $task = Task::find($taskId);
        if (!$task) return response()->json(['message' => 'Tarefa não encontrada'], 404);

        $userId = $data['user_id'] ?? null;
        if (! $userId) return response()->json(['message' => 'user_id é obrigatório'], 422);

        $memberUser = User::find($userId);
        if (! $memberUser) return response()->json(['message' => 'Usuário não encontrado'], 404);

        $membership = TaskMember::addMember($task, $memberUser);

        return response()->json(['message' => 'Membro adicionado com sucesso', 'member' => $membership], 201);
    }

    public function deleteMember(User $user, $taskId, $id)
    {
        if ($permission = $this->checkPermission($user, Permissions::EDIT_MEMBERS)) return $permission;

        $membership = TaskMember::where('task_id', $taskId)->where('user_id', $id)->first();

        if (!$membership || $membership->task_id != $taskId) return response()->json(['message' => 'Membro não encontrado'], 404);

        $membership->delete();
        return response()->json(['message' => 'Membro removido com sucesso'], 204);
    }
}
