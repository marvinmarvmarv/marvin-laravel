<?php

use App\Http\Controllers\Project\GetProjectByTaskIdController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Task\GetAllExpiredTasksController;
use App\Http\Controllers\Task\GetAllTasksByProjectIdController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\User\GetAllTasksByUserIdController;
use App\Http\Controllers\User\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['Die Zugangsdaten sind ungültig.'],
        ]);
    }

    return response()->json([
        'token' => $user->createToken('API Token')->plainTextToken,
    ]);
});

Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('admin')
    ->group(function () {
        Route::apiResource('users', UserController::class);

        Route::get('tasks', [TaskController::class, 'index']);
        Route::get('tasks/{task}', [TaskController::class, 'show']);
        Route::delete('tasks/{task}', [TaskController::class, 'destroy']);

        Route::get('tasks/all/expired', GetAllExpiredTasksController::class);

        Route::apiResource('projects', ProjectController::class);

        Route::get('projects/{project}/tasks', GetAllTasksByProjectIdController::class);
        Route::get('projects/task/{task}', GetProjectByTaskIdController::class);
    });

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('users', UserController::class)->only(['show', 'update', 'destroy']);

    Route::get('users/{user}/tasks', GetAllTasksByUserIdController::class);

    Route::post('tasks', [TaskController::class, 'store']);

    Route::middleware('own.task')->group(function () {
        Route::get('tasks/{task}', [TaskController::class, 'show']);
        Route::put('tasks/{task}', [TaskController::class, 'update']);
        Route::delete('tasks/{task}', [TaskController::class, 'destroy']);
    });

    Route::middleware('expired.task')->group(function () {
        Route::put('tasks/expired/{task}', [TaskController::class, 'update']);
        Route::delete('tasks/expired/{task}', [TaskController::class, 'destroy']);
    });
});
