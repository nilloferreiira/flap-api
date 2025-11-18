<?php

namespace App\Http\Controllers\Api\Boards;

use App\Http\Controllers\Controller;
use App\Services\Boards\BoardsService;
use Illuminate\Http\Request;

class BoardsController extends Controller
{
    protected $boardsService;

    public function __construct(BoardsService $boardsService)
    {
        $this->boardsService = $boardsService;
    }

    public function index(Request $request)
    {
        return response()->json($this->boardsService->getAll($request->user()));
    }

    public function store(Request $request)
    {
        return $this->boardsService->create($request->user(), $request->all());
    }

    public function show(Request $request, $id)
    {
        return $this->boardsService->getById($request->user(), $id);
    }

    public function update(Request $request, $id)
    {
        return $this->boardsService->update($request->user(), $id, $request->all());
    }

    public function destroy(Request $request, $id)
    {
        return $this->boardsService->delete($request->user(), $id);
    }
}
