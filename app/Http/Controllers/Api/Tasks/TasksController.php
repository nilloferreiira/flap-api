<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\MoveTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Services\Tasks\TasksService;
use Illuminate\Http\Request;

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

    public function moveTask(MoveTaskRequest $request, $id)
    {
        $position = $request->input('position');
        $listId = $request->input('listId');
        return $this->tasksService->moveTask($request->user(), $id, $listId, $position);
    }

    public function destroy(Request $request, $id)
    {
        return $this->tasksService->delete($request->user(), $id);
    }
}
