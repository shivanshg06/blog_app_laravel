<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\AuthController;

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

// Public Routes
Route::prefix('blogs')->group(function () {
    Route::get('', [BlogController::class, 'index']);
    Route::get('{id}', [BlogController::class, 'show']);
    Route::get('search/{name}', [BlogController::class, 'search']);
});

Route::prefix('user')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('blogs/{user_id}', [BlogController::class, 'showByUser']);
});

// Private Routes
Route::group(['middleware'=>['auth:sanctum']],function(){
    Route::post('/logout',[AuthController::class,'logout']);
    Route::prefix('user')->group(function () {
        Route::get('userId/',[AuthController::class, 'userId']);
    });
    Route::prefix('blogs')->group(function(){
        Route::post('', [BlogController::class, 'store']);
        Route::put('{id}', [BlogController::class, 'update']);
        Route::delete('{id}', [BlogController::class, 'destroy']);

    });
});