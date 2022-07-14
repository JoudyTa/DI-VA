<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Traits\TraitPhoto;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\Post;
use App\Models\UserInterestId;
use App\Models\ResetPassword;
use App\Models\Story;
use App\Mail\Reset;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $fields = $request->validate([

            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'gender' => 'required',
            'birthday' => 'required'
        ]);
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'gender' => $fields['gender'],
            'birthday' => $fields['birthday'],
        ]);
        if ($request->has('number_of_posts'))
            $user->create([
                'number_of_posts' => $request->number_of_posts,
            ]);
        $token = $user->createToken('Joudy-H-Taleb')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function mtn(Request $request)
    {
        $user = User::find($request->id);
        $user->code = $request->code;
        $user->save();
        return response()->json('Thanks for your trust.'); //for MTN
    }

    public function checkcode(Request $request)
    {
        $user = User::find(auth()->id());
        if ($user->code == $request->code)
            return true;
    }

    public function promotion(Request $request)
    {
        if ($this->checkcode($request)) {
            $user = User::find(auth()->id());
            $user->is_promtion = 1;
            $user->number_of_posts = $request->number_of_posts;
            $user->update();
            return response()->json('You are now a promoter!, Welcome'); //for Customer
        } else
            return response()->json('Your code is invalid , Please check your code');
    }

    use TraitPhoto;
    public function photo(Request $request)
    {
        $file_name = $this->saveImage($request->photo, 'images/UsersPhoto');

        $user = User::find(auth()->id());
        $user->photo = $file_name;
        $user->save();
        return response()->json('Your photo has been added');
    }

    public function uploadImage(Request $request)
    {

        $file_name = $this->saveImage($request->file('photo'), 'images/UsersPhoto');
        $user = User::find(auth()->id());
        $user->photo = $file_name;
        $user->update();
        return response()->json('Your photo has been uploaded ');
    }

    public function changePassword(Request $request)
    {

        $user = User::find(auth()->id());


        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json('Your passowrd is not match.');
        } else {

            $user->password = bcrypt($request->new_password);
            $user->save();
            return response()->json('Your passowrd has been changed.');
        }
    }

    public function update(Request $request)
    {

        $user = User::find(auth()->id());
        $user->name = $request->name;
        $user->birthday = $request->birthday;
        $user->gender = $request->gender;
        $user->bio = $request->bio;
        return response()->json('Your Info has been updated.');
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Your Email or Password is incorrect'
            ], 401);
        }

        $token = $user->createToken('Joudy-H-Taleb')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function makefollowpage(Request $request)
    {
        $user = UserFollow::create([
            'user_id' => auth()->id(),
            'following_id' => null,
            'followers_id' => null

        ]);

        for ($i = 0; $i < count($request->interest_id); $i++) {
            if ($request->interest_id[$i] === "{" || $request->interest_id[$i] === "}" || $request->interest_id[$i] === ",")
                continue;
            UserInterestId::create([
                'interest_id' => $request->interest_id[$i],
                'user_id' => Auth::id()
            ]);
        }

        return response()->json('Welcome in Di-Va');
    }

    public  function myprofile($id)
    {

        $following_id = (new UserFollowController)->following($id);
        $followers_id = (new UserFollowController)->follower($id);
        $story = Story::Where('user_id', auth()->id());
        $data['My Info']['Personal'] = User::find($id);
        if ($followers_id === null)
            $data['My Info']['Followers'] = "No followers yet!";
        else
            $data['My Info']['Followers'] = count($followers_id) - 1;

        if ($following_id === null)
            $data['My Info']['Following'] = "No follwing yet!";
        else
            $data['My Info']['Following'] = count($following_id) - 1;
        $data['My Posts'] = $this->myposts($id);
        $data['My Info']['Stores'] = $story;
        return response()->json($data);
    }

    public function myposts($id)
    {
        $posts = Post::orderBy('is_prometed', 'created_at')->get();
        $myposts[] = null;
        $j = 0;
        for ($i = 0; $i < count($posts); $i++) {
            if ($posts[$i]['user_id'] == $id) {
                $myposts[$j] = $posts[$i];
                $j++;
            }
        }
        if ($myposts == null)
            return response()->json('You dont have any posts to show it ');
        else
            return response()->json($myposts);
    }

    public function logout()
    {
        return [
            'message' => 'Logged out'
        ];
    }

    public function destroy(Request $request)
    {
        $user = User::find(auth()->id());
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json('Your passowrd is not match.');
        } else {
            $user->delete();
            return response()->json('Di-Va is sorry to lose you , hopes you enjoyed ');
        }
    }

    public function checkemail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $em = ResetPassword::Where('email', $request->email)->first();
        if (!$em) {
            $email = User::where('email', $request->email)->first();

            if ($email) {
                $code = \random_int(10000, 99999);
                Mail::to($request->email)->send(new Reset($code));

                $reset = ResetPassword::create([
                    'email' => $email->email,
                    'code' => $code
                ]);

                return "We send code to your Email, check it !";
            } else {
                return "Your email is uncorrect";
            }
        } else {
            $code = \random_int(10000, 99999);
            Mail::to($request->email)->send(new Reset($code));
            $em->code = $code;
            $em->save();
            return "Code has been sent back to your email , check it back";
        }
    }

    public function chkcode(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $code = $request->input('code');
        $email = $request->input('email');

        $user = ResetPassword::where('code', $code)->first();

        if ($user->email === $email) {
            ResetPassword::destroy($user->id);
            return response()->json('Code is correct , you can change password');
        } else {
            return response()->json('Code is uncorrect');
        }
    }

    public function resetpassword(Request $request)
    {

        $request->validate([
            'newpassword' => 'required|string|confirmed'
        ]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        $user->update([
            'password' => bcrypt($request->input('newpassword'))
        ]);

        return response()->json('Your password has been changed');
    }
}