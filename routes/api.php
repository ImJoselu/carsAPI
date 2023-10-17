<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CocheController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\UsuarioController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('prueba' , function(){
    return response("funciona" , 200);
});

// COCHES
Route::get('/coches' , [CocheController::class , 'index']);
Route::post('/coches' , [CocheController::class , 'store']);
Route::get('/coches/{coche}' , [CocheController::class , 'show']);
Route::delete('/coches/{coche}' , [CocheController::class , 'destroy']);
Route::put('/coches/edit/{coche}',  [CocheController::class, 'update']);
Route::post('/coches/{coche}/comprar', [CocheController::class, 'comprar']);
Route::post('/coches/{coche}/vender', [CocheController::class, 'vender']);
Route::get('/filtrarEstado', [CocheController::class, 'filtrarEstado']);
Route::get('/filtroCompleto', [CocheController::class, 'filtroCompleto']);

// LOGIN
Route::post('/login' , [LoginController::class , 'login']);
Route::get('/logout', [LoginController::class, 'logout']);

// USER
Route::get('/usuarios' , [UsuarioController::class , 'index']);
Route::post('/usuarios' , [UsuarioController::class , 'store']);
Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show']);
Route::put('/usuarios/edit/{usuario}',  [UsuarioController::class, 'update']);
Route::delete('/usuarios/{usuario}' , [UsuarioController::class , 'destroy']);
Route::get('/usuariosEliminados' , [UsuarioController::class , 'eliminados']);
Route::post('/usuariosEliminados/{id}/restaurar', [UsuarioController::class, 'restaurarUsuario']);

// MARCAS
Route::get('/marcas' , [MarcaController::class , 'index']);
Route::post('/marcas' , [MarcaController::class , 'store']);
Route::get('/filtrar', [MarcaController::class, 'filtrar']);
Route::delete('/marcas/{marca}' , [MarcaController::class , 'destroy']);

// BLOG
Route::get('/blog' , [BlogController::class , 'index']);
Route::post('/blog' , [BlogController::class , 'store']);






