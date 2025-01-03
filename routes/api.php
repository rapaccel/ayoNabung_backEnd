<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TabunganController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/tabungan/store',[TabunganController::class,'store']);
Route::get('/tabungan/index/{id}',[TabunganController::class,'index']);
Route::get('/tabungan/detail/{id}',[TabunganController::class,'getDetail']);
Route::put('/tabungan/update/{id}',[TabunganController::class,'update']);
Route::delete('/tabungan/delete/{id}',[TabunganController::class,'delete']);

Route::post('/users/register',[UsersController::class,'register']);
Route::post('/users/login',[UsersController::class,'authenticate']);
Route::post('/users/logout',[UsersController::class,'logout']);
Route::get('/users/index',[UsersController::class,'index']);
Route::get('/users/detailUser/{id}',[UsersController::class,'detailUser']);
Route::post('/sendWa',[UsersController::class,'sendWa']);
Route::post('midtrans/notify',[OrdersController::class,'notify']);
Route::get('/product/index',[ProductController::class,'index']);
Route::post('/product/store',[ProductController::class,'store']);
Route::get('/product/show/{id}',[ProductController::class,'show']);
Route::put('/product/update/{id}',[ProductController::class,'update']);
Route::delete('/product/delete/{id}',[ProductController::class,'delete']);

Route::post('/orders/store',[OrdersController::class,'store']);
Route::get('/orders/users/{id}',[OrdersController::class,'ordersByUsers']);