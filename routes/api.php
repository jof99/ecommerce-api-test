<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
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

Route::middleware('auth:api')->post('/cart/add', [ProductController::class, 'addToCart']);
Route::post('/register',[AuthenticationController::class,'register']);
Route::post('/login',[AuthenticationController::class,'login']);
Route::middleware('auth:api')->get('/cart', [ProductController::class, 'getCart']);
Route::middleware('auth:api')->post('/checkout', [OrderController::class, 'checkout']);




Route::middleware('auth:api')->group(function(){
    Route::post('/logout',[AuthenticationController::class,'logout']);
    Route::resource('products',ProductController::class);
 
});

