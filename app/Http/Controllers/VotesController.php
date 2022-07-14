<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class VotesController extends Controller
{
    public function upvote($post)
    {

        $id = auth()->id();

        $upvotes = Post::find($post)->upvotes_user_id;

        if ($upvotes == null)
            $upvotes = [];

        if (in_array($id, $upvotes)) {
            $index = array_search($id, $upvotes);
            unset($upvotes[$index]);
            $upvotes = array_merge($upvotes);

            $post  = Post::find($post)->update([
                'upvotes_user_id' => $upvotes
            ]);
        } else {
            array_push($upvotes, $id);

            $post  = Post::find($post)->update([
                'upvotes_user_id' => $upvotes
            ]);
        }
    }

    public function downvote($post)
    {

        $id = auth()->id();

        $downvotes = Post::find($post)->downvotes_user_id;

        if ($downvotes == null)
            $downvotes = [];

        if (in_array($id, $downvotes)) {
            $index = array_search($id, $downvotes);
            unset($downvotes[$index]);
            $downvotes = array_merge($downvotes);

            $post  = Post::find($post)->update([
                'downvotes_user_id' => $downvotes
            ]);
        } else {
            array_push($downvotes, $id);

            $post  = Post::find($post)->update([
                'downvotes_user_id' => $downvotes
            ]);
        }
    }
}
