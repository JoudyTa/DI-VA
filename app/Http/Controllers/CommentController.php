<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

class CommentController extends Controller
{
    public function createComment(Request $request, $id)
    {
        $arrcomments = Post::find($id)->comments;
        if ($arrcomments == null)
            $arrcomments = [];


        array_push($arrcomments, [
            "id" => \random_int(100, 999),
            "content" => $request->input('comment'),
            "uesr_id" => Auth::id()
        ]);

        $post =  Post::find($id)->update([
            'comments' => $arrcomments
        ]);
        return response()->json("done", 200);
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
                    "user_id" => Auth::id()

                ];
            }
        }

        Post::find($postid)->update([
            'comments' => $comment
        ]);

        return $comment;
    }
}