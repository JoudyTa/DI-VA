<?php

namespace App\Http\Controllers;

use App\Models\UserFollow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserFollowController extends Controller
{

    public function getuser($id)
    {
        $users = UserFollow::all();

        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]['user_id'] == $id) {

                return  $users[$i];
            }
        }
    }
    public function follower($id)
    {

        $user = $this->getUser($id);

        return $user->followers_id;
    }


    public function following($id)
    {

        $user = $this->getUser($id);

        return $user->following_id;
    }


    public function follow($id)
    {
        $user = auth()->id();
        $me = $this->getUser($user);
        $you = $this->getUser($id);

        $following_id = $me->following_id;
        $followers_id = $you->followers_id;

        if ($following_id === null)
            $following_id = [0];
        if ($followers_id === null)
            $followers_id = [0];

        if (in_array($user, $followers_id)) {

            $indexme = array_search($user, $followers_id);
            $indexyou = array_search($id, $following_id);

            unset($followers_id[$indexme]);
            unset($following_id[$indexyou]);

            $following_id = array_merge($following_id);
            $followers_id = array_merge($followers_id);

            $me->following_id = $following_id;
            $you->followers_id = $followers_id;

            $me->update();
            $you->update();
        } else {

            array_push($followers_id, $user);
            array_push($following_id, $id);

            $following_id = array_merge($following_id);
            $followers_id = array_merge($followers_id);

            $me->following_id = $following_id;
            $you->followers_id = $followers_id;

            $me->update();
            $you->update();
        }
    }

    public function block($id)
    {
        $me_id = Auth::id();
        $me = $this->getuser($me_id);
        $blocking_id = $me->blocking_id;

        if ($blocking_id == null)
            $blocking_id = [];

        if (in_array($id, $blocking_id)) {
            $index = array_search($id, $blocking_id);
            unset($blocking_id[$index]);
            $blocking_id = array_merge($blocking_id);
            $me->blocking_id = $blocking_id;
            $me->update();

            return response()->json("you unblocked this user", 200);
        } else {
            array_push($blocking_id, $id);
            $me->blocking_id = $blocking_id;
            $me->update();

            return response()->json("you blocked this user", 200);
        }
    }
}