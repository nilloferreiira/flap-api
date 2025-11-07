<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Roles\RolesController;
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
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::post('/auth/me', [AuthController::class, 'me']);


// Private routes
Route::middleware('auth:api')->group(function () {
    // Users
    Route::resource('/users', UsersController::class);
    // Permissions
    Route::get('/roles/permissions', [RolesController::class, 'getAllPermissions']);
    // Roles
    Route::resource('/roles', RolesController::class);
});
