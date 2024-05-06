<?php

namespace App\Http\Controllers;

use App\Helpers\FCM;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\Friendship;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $user = User::with('school')->get();
        if (count($user) == 0) {
            return Helper::APIResponse('Data empty', 200, null, null);
        }
        return Helper::APIResponse('Success get all users', 200, null, $user);
    }

    public function profile()
    {
        $user = User::with('school')->where('id', Auth::id())->first();
        return Helper::APIResponse('Success get my detail', 200, null, $user);
    }

    public function update(Request $req)
    {
        $validation = Validator::make($req->all(), [
            'name' => 'nullable|string',
            'school_id' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'profile' => 'mimes:jpg,png|max:2084'
        ]);
        // name, phone_number, school_id
        if ($validation->fails()) {
            return Helper::APIResponse('Error Validation', 422, $validation->errors(), null);
        }

        $user = Auth::user();
        $data = User::where('id', $user->id)->first();

        $name = $data->name;
        $school_id = $data->school_id;
        $phone_number = $data->phone_number;
        if ($req->name != null) {
            $name = $req->name;
        }
        if ($req->school_id != null) {
            $school_id = $req->school_id;
        }
        if ($req->phone_number != null) {
            $phone_number = $req->phone_number;
        }

        if ($data) {
            if ($req->hasFile('profile')) {
                // cek misal user akan update atau ubah image maka image sebelumnya akan dihapus dari storage
                if ($data->profile != null) {
                    Storage::delete('public/profiles/' . $data->profile);
                }
                $image = $req->file('profile');
                $newImageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/profiles/' . $newImageName);

                $input['name'] = $name;
                $input['school_id'] = $school_id;
                $input['phone_number'] = $phone_number;
                $input['profile'] = $newImageName;

                $data->update($input);
                return Helper::APIResponse('Success update data with image', 200, null, $data);
            }
        }

        $input['name'] = $name;
        $input['school_id'] = $school_id;
        $input['phone_number'] = $phone_number;

        $data->update($input);

        return Helper::APIResponse('Success update data', 200, null, $data);
    }

    // friends
    public function requestFriendship($target_id)
    {
        $isCreateReq = Friendship::where('user_id', Auth::user()->id)->where('friend_id', $target_id)->first();
        if ($isCreateReq) {
            $isCreateReq->delete();
            return Helper::APIResponse('Success cancel request', 200, null, null);
        }

        $data = Friendship::create([
            'id' => Str::uuid(),
            'user_id' => Auth::user()->id,
            'friend_id' => $target_id,
            'status' => 'pending'
        ]);

        return Helper::APIResponse('Success create request', 200, null, $data);
    }

    public function getAllRequestFriendship()
    {
        $user = User::where('id', Auth::id())->first();

        $friendsOfMine = $user->friendsOfMine('pending')->get();
        $friendOf = $user->friendOf('pending')->get();

        $combinedFriends = $friendsOfMine->merge($friendOf);
        // $combinedFriendsArray = $combinedFriends->toArray();

        $friends = $combinedFriends;

        if (count($friends) == 0) {
            return Helper::APIResponse('Data empty', 200, null, null);
        }

        return Helper::APIResponse('Success get all friendship', 200, null, $friends);
    }

    public function rejectFriendship($id)
    {
        $data = Friendship::where('id', $id)->delete();
        if (!$data) {
            return Helper::APIResponse('Failed rejected friendship', 404, "data not found", null);
        }
        return Helper::APIResponse('Success rejected friendship', 200, null, $data);
    }

    public function acceptFriendship($id)
    {
        $data = Friendship::where('friend_id', Auth::user()->id)->where('id', $id)->first();
        if (!$data) {
            return Helper::APIResponse('Failed accepted friendship, user not have relationship request', 400, 'Bad Request', null);
        }
        $data->status = 'accept';
        $data->save();

        return Helper::APIResponse('Success accepted friendship', 200, null, $data);
    }

    public function getAllMyFriends()
    {
        $user = User::where('id', Auth::id())->first();

        $friendsOfMine = $user->friendsOfMine('accept')->get();
        $friendOf = $user->friendOf('accept')->get();

        $combinedFriends = $friendsOfMine->merge($friendOf);
        // $combinedFriendsArray = $combinedFriends->toArray();

        $friends = $combinedFriends;

        foreach ($friends as &$friend) {
            $friend['sameSchool'] = $friend['school_id'] == $user->school_id;
        }

        return Helper::APIResponse('Success get all friends', 200, null, $friends);
    }

    public function friendDetail($friend_id)
    {
        $user = User::where('id', $friend_id)->first();
        if ($user) {
            return Helper::APIResponse('Get detail friend', 200, null, $user);
        }
        return Helper::APIResponse('Failed detail friend', 404, 'data not found', null);
    }

    public function colek(Request $req)
    {
        FCM::android([$req->query('regId')])->send([
            'title' => 'Hello, ' . $req->query('name'),
            'message' => $req->query('msg')
        ]);
    }
}
