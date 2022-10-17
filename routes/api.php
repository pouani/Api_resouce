<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;

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

//authentification route
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware(['auth:api'])->group(function () {
    //utilisateurs routes
    Route::get('user', [UserController::class, 'user']);
    Route::post('users/info', [UserController::class, 'updateInfo']);
    Route::post('users/password', [UserController::class, 'updatePassword']);
    Route::post('upload', [ImageController::class, 'upload']);

    //export order routes
    Route::get('export', [App\Http\Controllers\OrderController::class, 'export']);

    Route::apiResource('users', App\Http\Controllers\UserController::class);

    //roles routes
    Route::apiResource('roles', App\Http\Controllers\RoleController::class);

    //products routes
    Route::apiResource('products', App\Http\Controllers\ProductController::class);

    Route::apiResource('orders', OrderController::class)->only('index', 'show');

    Route::apiResource('permissions', PermissionController::class)->only('index');
});
