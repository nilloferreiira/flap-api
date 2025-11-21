<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CreateTaskRequest;
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
        return response()->json($this->tasksService->getAll($request->user()));
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
}
