<?php

namespace App\Http\Controllers\Api;

use App\Libs\GoogleAuthenticator;
use App\Mail\ForgotPasswordMail;
use App\Mail\VerificationMail;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    //
    public function register(Request $request)
    {
        $input = $request->input();

        if (User::where('email', $input['email'])->count() > 0) {
            return response()->json(['success' => false, 'error' => 'Email exist']);
        }

        $user = new User;

        $user->name = $input['name'];
        $user->birthday = $input['birthday'];
        $user->email = $input['email'];
        $user->password = bcrypt($input['password']);
        $user->photo = env('APP_URL').'/storage/profile.jpg';

        $confirm_code = str_random(30);
        Mail::to($input['email'])->send(new VerificationMail($confirm_code, $user));

        $user->confirm_code = $confirm_code;
        $user->save();

        //return $this->login($request);
        return response()->json(['success' => true, 'email' => $user->email]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Failed', 'success' => false]);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'token error'], 500);
        }

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'birthday' => $user->birthday,
            'site' => $user->site,
            'address' => $user->address,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'photo' => $user->photo,
            'verified' => $user->verified,
            'token' => $token
        ]);
    }

    public function forgotPassword(Request $request) {
        $input = $request->input();

        $user = User::where([['email', $input['email']], ['verified', 1]])->first();

        if ($user == null) {
            return response()->json([
                'success' => false,
            ]);
        } else {
            $confirm_code = str_random(30);
            Mail::to($input['email'])->send(new ForgotPasswordMail($confirm_code, $user));

            $user->confirm_code = $confirm_code;
            $user->save();

            return response()->json([
                'success' => true,
            ]);
        }
    }

    public function resetPassword(Request $request) {
        $input = $request->input();

        $user = User::where('confirm_code', $input['confirmcode'])->first();

        if ($user == null) {
            return response()->json([
                'success' => false,
            ]);
        } else {

            $user->password = bcrypt($input['password']);

            $user->save();

            return response()->json([
                'success' => true,
            ]);
        }
    }

    public function sendVerifyEmail(Request $request) {

        $input = $request->input();

        $user = User::where('email', $input['email'])->first();

        if ($user == null) {
            return response()->json([
                'success' => false,
            ]);
        } else {
            $confirm_code = str_random(30);
            Mail::to($input['email'])->send(new VerificationMail($confirm_code));

            $user->confirm_code = $confirm_code;
            $user->save();

            return response()->json([
                'success' => true,
            ]);
        }
    }

    public function verifyMail($confirm_code, Request $request) {
        $count = User::where('confirm_code', $confirm_code)->count();

        if ($count == 0) {
            return 'Invalid code';
        } else {
            User::where('confirm_code', $confirm_code)->update(['verified' => 1]);

            return ("<script>location.href = '".env('CLIENT_URL')."/login'</script>");
        }
    }

    public function loadBusinessUsers(Request $request) {
        $users = User::where([['businessUser', 1], ['verified', 1]])->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}
