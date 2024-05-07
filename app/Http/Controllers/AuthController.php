<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $req)
    {
        $validateMsg = [
            'password.regex' => 'The :attribute must contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character'
        ];

        $validation = Validator::make($req->all(), [
            'name' => 'required',
            'phone_number' => 'required|unique:users|min:12',
            'school_id' => 'required',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ],
            'password_confirmation' => 'required|same:password'
        ], $validateMsg);

        if ($validation->fails()) {
            return Helper::APIResponse('error validation', 422, null, $validation->errors());
        }

        $password = Hash::make($req->password);

        User::create([
            'id' => Str::uuid(),
            'name' => $req->name,
            'phone_number' => $req->phone_number,
            'school_id' => $req->school_id,
            'password' => $password
        ]);
        return Helper::APIResponse('Register Success', 200, null, null);
    }

    public function login(Request $req)
    {
        $validation = Validator::make($req->all(), [
            'phone_number' => 'required|min:12',
            'password' => 'required|min:8'
        ]);

        if ($validation->fails()) {
            return Helper::APIResponse('error validation', 422, $validation->errors(), null);
        }

        $user = User::where('phone_number', $req->phone_number)->first();

        if (!$user) {
            return Helper::APIResponse('Phone Number or Password not match', 404, 'User not found', null);
        }

        if (!Hash::check($req->password, $user->password)) {
            return Helper::APIResponse('password not match', 422, 'error password validate', null);
        }

        $user->save();

        $user['token'] = $user->createToken('user_token')->plainTextToken;

        return Helper::APIResponse('Login Success', 200, null, $user);
    }

    // send the regId to db if login is success
    public function successLogin(Request $req)
    {
        $user = User::where('id', Auth::id())->first();

        $regId = $req->regId;

        $user->regId = $regId;
        $user->update();
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();

        $user = User::where('id', Auth::user()->id)->first();
        $user->regId = null;
        $user->save();

        return Helper::APIResponse('Logout Success', 200, null, null);
    }

    public function passwordUpdate(Request $req)
    {
        $validateMsg = [
            'new_password.regex' => 'The :attribute must contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character'
        ];
        $validation = Validator::make($req->all(), [
            'old_password' => 'required',
            'new_password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ],
            'new_password_confirmation' => 'required|same:new_password'
        ], $validateMsg);

        if ($validation->fails()) {
            return Helper::APIResponse('error validation', 422, $validation->errors(), null);
        }

        $user = User::where('id', Auth::user()->id)->first();

        if (!Hash::check($req->old_password, $user->password)) {
            return Helper::APIResponse('password not match', 422, 'error password validate', null);
        }

        $user->password = Hash::make($req->new_password);
        $user->update();
        return  Helper::APIResponse('success update password', 200, null, $user);
    }
}
