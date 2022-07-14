<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

$id = auth()->id();

Broadcast::channel('App.Models.User' . $id . 'Post', function ($user) {
    return in_array(auth()->id(), $user->followers_id);
});

Broadcast::channel('Post.{id}', function ($user) {
});