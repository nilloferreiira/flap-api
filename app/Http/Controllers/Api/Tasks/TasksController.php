<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\Elements\CreateCheckListRequest;
use App\Http\Requests\Task\Elements\UpdateCheckListRequest;
use App\Http\Requests\Task\MoveTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Services\Tasks\TasksService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TasksController extends Controller
{
    protected $tasksService;

    public function __construct(TasksService $tasksService)
    {
        $this->tasksService = $tasksService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        return $this->tasksService->getAll($user);
    }

    public function store(CreateTaskRequest $request)
    {
        return $this->tasksService->create($request->user(), $request->all());
    }

    public function show(Request $request, $id)
    {
        return $this->tasksService->getById($request->user(), $id);
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        return $this->tasksService->update($request->user(), $id, $request->all());
    }

    public function destroy(Request $request, $id)
    {
        return $this->tasksService->delete($request->user(), $id);
    }

    public function moveTask(MoveTaskRequest $request, $id)
    {
        $position = $request->input('position');
        $listId = $request->input('listId');
        return $this->tasksService->moveTask($request->user(), $id, $listId, $position);
    }

    // --- Task elements: comments
    public function createComment(Request $request, $taskId)
    {
        return $this->tasksService->createComment($request->user(), $taskId, $request->all());
    }

    public function updateComment(Request $request, $taskId, $id)
    {
        return $this->tasksService->updateComment($request->user(), $taskId, $id, $request->all());
    }

    public function deleteComment(Request $request, $taskId, $id)
    {
        return $this->tasksService->deleteComment($request->user(), $taskId, $id);
    }

    // --- Task elements: checklists
    public function createChecklist(CreateCheckListRequest $request, $taskId)
    {
        return $this->tasksService->createChecklist($request->user(), $taskId, $request->validated());
    }

    public function createChecklistItem(Request $request, $taskId, $checklistId)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        return $this->tasksService->createChecklistItem($request->user(), $taskId, $checklistId, $data);
    }



    public function updateChecklist(UpdateCheckListRequest $request, $taskId, $id)
    {
        return $this->tasksService->updateChecklist($request->user(), $taskId, $id, $request->validated());
    }

    public function deleteChecklist(Request $request, $taskId, $id)
    {
        return $this->tasksService->deleteChecklist($request->user(), $taskId, $id);
    }

    public function deleteChecklistItem(Request $request, $taskId, $checklistId, $itemId)
    {
        return $this->tasksService->deleteChecklistItem($request->user(), $taskId, $checklistId, $itemId);
    }

    public function updateCheckListItem(Request $request, $taskId, $checklistId, $itemId)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $description = $data['description'];

        return $this->tasksService->updateCheckListItem($request->user(), $taskId, $checklistId, $itemId, $description);
    }

    public function markChecklistItem(Request $request, $taskId, $checklistId, $itemId)
    {
        $validator = Validator::make($request->all(), [
            'is_completed' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $isCompleted = $data['is_completed'];
        return $this->tasksService->markCheckListItemCompleted($request->user(), $taskId, $checklistId, $itemId, $isCompleted);
    }

    // --- Task elements: links
    public function createLink(Request $request, $taskId)
    {
        return $this->tasksService->createLink($request->user(), $taskId, $request->all());
    }

    public function updateLink(Request $request, $taskId, $id)
    {
        return $this->tasksService->updateLink($request->user(), $taskId, $id, $request->all());
    }

    public function deleteLink(Request $request, $taskId, $id)
    {
        return $this->tasksService->deleteLink($request->user(), $taskId, $id);
    }

    // --- Task elements: members
    public function getAvailableMembers(Request $request, $taskId)
    {
        return $this->tasksService->getAvailableMembers($request->user(), $taskId);
    }

    public function createMember(Request $request, $taskId)
    {
        return $this->tasksService->createMember($request->user(), $taskId, $request->all());
    }

    public function deleteMember(Request $request, $taskId, $id)
    {
        return $this->tasksService->deleteMember($request->user(), $taskId, $id);
    }
}
