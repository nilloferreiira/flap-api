<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Clients\ClientsController;
use App\Http\Controllers\Api\Dashboard\DashboardController;
use App\Http\Controllers\Api\Lists\ListsController;
use App\Http\Controllers\Api\Roles\RolesController;
use App\Http\Controllers\Api\Tasks\TasksController;
use App\Http\Controllers\Api\Users\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication
Route::post('/auth/sign-up', [AuthController::class, 'signUp']);
Route::post('/auth/sign-in', [AuthController::class, 'signIn']);
Route::get('/auth/sign-out', [AuthController::class, 'signOut']);
Route::get('/auth/refresh', [AuthController::class, 'refresh']);
Route::get('/auth/me', [AuthController::class, 'me']);


// Private routes
Route::middleware('auth:api')->group(function () {
    // Dashboard
    Route::get('/dashboard/tasks-count-per-user', [DashboardController::class, 'tasksCountPerUser']);
    Route::get('/dashboard/tasks-count-per-user-by-list', [DashboardController::class, 'tasksCountPerUserByList']);

    // Users
    Route::resource('/users', UsersController::class);
    Route::get('/all-users', [UsersController::class, 'getAllUsers']);
    // Permissions
    Route::get('/roles/permissions', [RolesController::class, 'getAllPermissions']);
    // Roles
    Route::resource('/roles', RolesController::class);
    // Clients
    Route::resource('clients', ClientsController::class);
    // Lists
    Route::resource('lists', ListsController::class);
    // Tasks 
    Route::resource('tasks', TasksController::class);
    Route::put('tasks/{id}/move', [TasksController::class, 'moveTask']);
    // Task elements
    Route::prefix('tasks/{taskId}')->group(function () {
        // comments
        Route::post('comments', [TasksController::class, 'createComment']);
        Route::put('comments/{id}', [TasksController::class, 'updateComment']);
        Route::delete('comments/{id}', [TasksController::class, 'deleteComment']);

        // checklists 
        Route::post('checklists', [TasksController::class, 'createChecklist']);
        Route::post('checklists/{id}/item', [TasksController::class, 'createChecklistItem']);
        Route::put('checklists/{id}', [TasksController::class, 'updateChecklist']);
        Route::patch('checklists/{id}/item/{itemId}', [TasksController::class, 'updateChecklistItem']);
        Route::delete('checklists/{id}/item/{itemId}', [TasksController::class, 'deleteChecklistItem']);
        Route::patch('checklists/{id}/item/{itemId}/toggle', [TasksController::class, 'markChecklistItem']);
        Route::delete('checklists/{id}', [TasksController::class, 'deleteChecklist']);

        // links
        Route::post('links', [TasksController::class, 'createLink']);
        Route::put('links/{id}', [TasksController::class, 'updateLink']);
        Route::delete('links/{id}', [TasksController::class, 'deleteLink']);

        // members
        Route::post('members', [TasksController::class, 'createMember']);
        Route::delete('members/{id}', [TasksController::class, 'deleteMember']);
        Route::get('available-members/', [TasksController::class, 'getAvailableMembers']);
    });
});
