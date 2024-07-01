<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PermissionUserController;

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

Route::get('/', fn () => response()->json([ 'message' => 'ok' ]));

Route::post('/auth', [AuthController::class, 'auth'])->name('user.auth');

Route::middleware([ 'auth:sanctum', 'acl'])->group(function () {

    // Set Permissions
    Route::post('/users/{user}/permissions-sync', [
        PermissionUserController::class, 'syncUserPermissions'
    ])->name('user.permissions.sync');
    Route::get('/users/{user}/permissions-sync', [
        PermissionUserController::class, 'getUserPermissions'
    ])->name('user.permissions.get');

    Route::get('/logout', [AuthController::class, 'logout'])->name('user.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('user.me');

    //CRUD Permissions
    Route::apiResource('/permissions', PermissionController::class);

    //Users
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
});



