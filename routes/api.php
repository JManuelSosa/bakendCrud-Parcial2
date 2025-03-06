<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//* Para los Usuarios
Route::post('registro', [AuthController::class, 'RegisterUser']);
Route::post('login', [AuthController::class, 'Login']);

Route::middleware(['auth:sanctum'])->group(function(){

    //* Cerrar sesión
    Route::get('logout', [AuthController::class, 'Logout']);

    //* Para Usuarios
    Route::get('usuarios', [UserController::class, 'UserIndex']);
    Route::get('usuarios/{id}', [UserController::class, 'ShowUser']);
    Route::post('usuarios', [UserController::class, 'StoreUser']);
    Route::put('usuarios/{id}', [UserController::class, 'UpdateUser']);
    Route::delete('usuarios/{id}', [UserController::class, 'DestroyUser']);


    //* Para productos
    Route::get('productos', [ProductController::class, 'IndexProduct']);
    Route::get('productos/{id}', [ProductController::class, 'ShowProduct']);
    Route::post('productos', [ProductController::class, 'StoreProduct']);
    Route::put('productos/{id}', [ProductController::class, 'UpdateProduct']);
    Route::delete('productos/{id}', [ProductController::class, 'DestroyProduct']);

});

