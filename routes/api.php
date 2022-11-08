<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PHPUnit\TextUI\XmlConfiguration\Group;

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


Route::group(
    ['prefix' => '/v1'],
    function () {
        //Public routes group
        Route::group(
            ['prefix' => 'public'],
            function(){
                //Public users routes
                Route::group(
                    ['prefix' => 'users'],
                    function(){
                        Route::post('/create', [UserController::class, 'register']);
                        Route::post('/login', [UserController::class, 'login']);
                        Route::get('/profile/{id}', [UserController::class, 'profile']);
                    }
                );
            }
            
        );
        //Private routes group
        Route::group(
            ['prefix' => 'private', 'middleware' => 'jwt.auth'],
            function(){
                Route::group(
                    ['prefix' => 'users'],
                    function(){
                        Route::get('/profile/{id}', [UserController::class, 'profile']);
                        Route::put('/profile/{id}/update', [UserController::class, 'updateProfile']);
                    }
                );
            }
        );
    }
);
