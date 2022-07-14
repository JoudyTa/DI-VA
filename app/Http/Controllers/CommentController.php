<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\CommentEvent;
use Str;

class CommentController extends Controller
{
    public function createComment(Request $request, $postid)
    {


        $user = auth()->id();
        $arrcomments = Post::find($postid)->comments;
        if ($arrcomments === null)
            $arrcomments = [];
        $comment_id = \random_int(1, 1000000000);

        array_push($arrcomments, [
            "id" => $comment_id,
            "content" => $request->input('comment'),
            "user_id" => $user,
            "created_at" => now()->format('Y-m-d H:i:s')
        ]);

        $post =  Post::find($postid)->update([
            'comments' => $arrcomments
        ]);
        $comments = Post::find($postid)->comments;
        for ($i = 0; $i < count($comments); $i++) {
            if ($comments[$i]['id'] === $comment_id) {
                $comment = $comments[$i];
            }
        }

        event(new CommentEvent($comment, $postid));
        return  response()->json("Done", 200);
    }


    public function delete($postid, $id)
    {
        $comments = Post::find($postid)->comments;
        for ($i = 0; $i < count($comments); $i++) {
            if ($comments[$i]['id'] == $id) {
                unset($comments[$i]);
                $comments = array_merge($comments);
            }
        }

        Post::find($postid)->update([
            'comments' => $comments
        ]);

        return $comments;
    }


    public function update(Request $request, $postid, $id)
    {

        $newcomment = $request->input('comment');
        $comment = Post::find($postid)->comments;

        for ($i = 0; $i < count($comment); $i++) {
            if ($comment[$i]['id'] == $id) {
                $comment[$i] = [
                    "id" => $id,
                    "content" => $newcomment,
                    "user_id" => Auth::id(),
                    "updated_at" => now()->format('Y-m-d H:i:s')

                ];
            }
        }

        Post::find($postid)->update([
            'comments' => $comment
        ]);

        return $comment;
    }
}