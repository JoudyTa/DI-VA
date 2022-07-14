<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\UserFollowController;
use App\Http\Controllers\VotesController;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* Route Sing (in / up) */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/checkemail', [AuthController::class, 'checkemail']);
Route::post('/chkcode', [AuthController::class, 'chkcode']);
Route::post('/resetpassword', [AuthController::class, 'resetpassword']);

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
        Route::post('/block/{id}', [UserFollowController::class, 'block']);
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

            //For Comments
            Route::prefix("/{postid}")->group(function () {
                Route::post('/comment', [CommentController::class, 'createComment']);
                Route::delete('/comment/{id}/delete', [CommentController::class, 'delete']);
                Route::post('/comment/{id}/update', [CommentController::class, 'update']);
            });

            //For Votes
            Route::prefix("/{post}")->group(function () {
                Route::post('/upvote', [VotesController::class, 'upvote']);
                Route::post('/downvote', [VotesController::class, 'downvote']);
            });
        });
    }
);