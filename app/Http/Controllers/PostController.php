<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Story;
use App\Models\UserInterestId;
use App\Traits\TraitPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
//use Illuminate\Support\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use App\Events\PostEvent;
use Carbon\Carbon;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function explore()
    {
        return Post::orderBy('is_prometed', 'created_at')->get();
    }

    public function updatephoto(Request $request)
    {
        $user = User::find(auth()->id());
        $file_name = $this->saveImage($request->photo, 'images/PostsPhoto');
        $user->photo = $file_name;
        $user->save();
        return $user->photo;
    }

    public function show($id)
    {
        $post = Post::find($id);

        return $post;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    use TraitPhoto;
    public function store(Request $request)
    {

        $file_name = $this->saveImage($request->photo, 'images/PostsPhoto');
        $user = auth()->id();
        $us = User::find($user);
        if ($request->is_prometed != 0) {

            if ($us->number_of_posts > 0)
                $us->update([
                    'number_of_posts' => ($us->number_of_posts - 1)
                ]);
            else
                return response()->json('You no longer have permission to promote, your number of posts has expired .');
        }
        $post = Post::create([

            'photo' => $file_name,
            'user_id' => $user,
            'interest_id' => $request->interest_id,
        ]);
        if ($request->has("content")) {
            $post->content = $request->content;
            $post->save();
        } else {
            $post->content = null;
            $post->save();
        }
        if ($request->has("is_prometed")) {
            $post->is_prometed = $request->is_prometed;
            $post->save();
        }
        event(new PostEvent($post));
        return response()->json('Post added successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */

    public function home()
    {
        //for checking story
        $stories = Story::all();
        $currentdate = Carbon::now();

        foreach ($stories as $story) {
            $created_at = Carbon::parse($story->created_at);
            // $created_at = $created_at->toDateTimeString();
            $ended_at = $created_at->addHours(24);
            if ($currentdate === $ended_at) {
                Story::destroy($story->id);
            }
        }

        $user = auth()->id();
        $allinterested = UserInterestId::all()->where('user_id', $user); //gives you all user's interested
        $following_id = (new UserFollowController)->following($user);

        $post = Post::orderBy('is_prometed', 'created_at')->get();

        $getallpost[] = null;
        $getpost1[] = null; //interst
        $getpost2[] = null; //following
        $getinterest[] = null;
        for ($i = 0; $i < count($post); $i++) {


            $tw = $post[$i]['interest_id'];
            $r = 0;
            for ($l = 0; $l < Str::length($tw); $l++) {

                if ($tw[$l] === "[" || $tw[$l] === "]" || $tw[$l] === "," || $tw[$l] === null)
                    continue;
                else {
                    $getinterest[$r] = $tw[$l];
                    for ($j = 0; $j < count($allinterested); $j++) {

                        if ($getinterest[$r] == $allinterested[$j]->interest_id) {

                            $getpost1[$i] = $post[$i];
                        }
                    }
                }
            }
            for ($k = 0; $k < count($following_id); $k++) {
                if ($post[$i]['user_id'] == $following_id[$k]) {
                }
            }
        }
        for ($k = 1; $k < count($following_id); $k++) {
            $getpost2[$k] = (new AuthController)->myposts($following_id[$k]);
        }

        return $getpost2;
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if (($post->is_prometed == true) && ($request->is_prometed == false)) {
            return response()->json('You can\'t update this');
        }
        if (Auth::id() != $post->user_id) {
            return response()->json('Not allowed to update post');
        }
        if ($request->has('content')) {
            $post->content = $request->content;
        }
        if ($request->has('interest_id')) {
            $post->interest_id = $request->interest_id;
        }
        $post->update();
        return response()->json('Post updated successfully');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $arrsearch = [];
        $posts = Post::all();
        foreach ($posts as $post) {
            if ($post->interest_id == $search) {
                array_push($arrsearch, $post);
            }
        }
        return $arrsearch;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = (Post::find($id));

        if (auth()->id() === $post->user_id) { {
                Post::find($id)->delete();
                return response()->json('Post deleted successfully');
            }
        } else
            return response()->json('You can\'t delete this post');
    }
}