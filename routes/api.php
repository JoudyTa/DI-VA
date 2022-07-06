<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserFollowController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Broadcast;


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

/* Route Sing (in / up) */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::post('mtn/code', [AuthController::class, 'mtn']);

Route::prefix('myprofile')->group(
    function () {
        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::post('/makefollowpage', [AuthController::class, 'makefollowpage']);
            Route::post('/photo', [AuthController::class, 'photo']);
            Route::post('uploadImage', [AuthController::class, 'uploadImage']);
            Route::post('/promotion', [AuthController::class, 'promotion']);
            Route::put('updateinfo', [AuthController::class, 'update']);
            Route::put('/changepassword', [AuthController::class, 'changePassword']);
            Route::get('/{id}', [AuthController::class, 'myprofile']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::delete('/delete', [AuthController::class, 'destroy']);
        });
    }
);
Broadcast::routes(['middleware' => ['auth:api']]);

Route::group(
    ['middleware' => ['auth:sanctum']],
    function () {
        Route::post('/follow/{id}', [UserFollowController::class, 'follow']);
        Route::get('/followers/{id}', [UserFollowController::class, 'follower']);
        Route::get('/following/{id}', [UserFollowController::class, 'following']);
    }
);

Route::prefix('story')->group(
    function () {
        Route::group(['middleware' => ['auth:sanctum']], function () {

            //For stories
            Route::post('/create', [StoryController::class, 'createStory']);
            Route::delete('/delete/{id}', [StoryController::class, 'delete']);
        });
    }
);


Route::prefix('post')->group(
    function () {
        Route::group(['middleware' => ['auth:sanctum']], function () {
            //For Posts
            Route::post('/creat', [PostController::class, 'store']);
            Route::get('/show/{id}', [PostController::class, 'show']);
            Route::post('/updatephoto', [PostController::class, 'updatephoto']);
            Route::put('/{id}', [PostController::class, 'update']);
            Route::get('/{id}', [PostController::class, 'show']);
            Route::get('/home', [PostController::class, 'home']);
            Route::get('/explore', [PostController::class, 'explore']);
            Route::delete('/{id}', [PostController::class, 'destroy']);
        });


        //For Comments
        Route::prefix("/{postid}")->group(function () {
            Route::post('/comment', [CommentController::class, 'createComment']);
            Route::delete('/comment/{id}/delete', [CommentController::class, 'delete']);
            Route::post('/comment/{id}/update', [CommentController::class, 'update']);
        });

        /*

            //For Like
            Route::prefix("/{product}/likes")->group(function () {
                Route::post('/', [LikeController::class, 'store']);
            });



            //For Categories
            Route::prefix("sorting")->group(function () {
                Route::get('/{id}', [ProductController::class, 'sorting']);
            });


        */
    }
);