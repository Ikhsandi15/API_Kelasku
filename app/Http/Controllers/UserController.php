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

    /**
     * Membuat atau membatalkan permintaan pertemanan.
     *
     * @param  int  $target_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestFriendship($target_id)
    {
        $isExist = User::where("id", $target_id)->first();
        // dd($isExist);
        if (!$isExist) {
            return Helper::APIResponse('ID user tidak ditemukan', 200, null, null);
        }

        $currentUserId = Auth::user()->id;
        $existingRequest = Friendship::where('user_id', $currentUserId)
            ->where('friend_id', $target_id)
            ->first();

        if ($existingRequest) {
            $existingRequest->delete();
            return Helper::APIResponse('Permintaan pertemanan dibatalkan', 200, null, null);
        }

        $target_name = User::where('id', $target_id)->first();
        $data = Friendship::create([
            'id' => Str::uuid(),
            'user_id' => $currentUserId,
            'friend_id' => $target_id,
            'status' => 'pending'
        ]);

        $datas = [
            'id' => $data->id,
            'user_id' => $currentUserId,
            'friend_id' => $target_id,
            'friend_name' => $target_name->name,
            'status' => $data->status
        ];

        return Helper::APIResponse('Permintaan pertemanan dibuat', 200, null, $datas);
    }

    /**
     * Mendapatkan semua permintaan pertemanan yang diterima pengguna.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRequestFriendship()
    {
        $user = User::where("id", Auth::id())->first();
        $friendRequests = $user->friendOf('pending')->get();

        if ($friendRequests->isEmpty()) {
            return Helper::APIResponse('Tidak ada data', 200, null, null);
        }

        return Helper::APIResponse('Berhasil mendapatkan semua permintaan pertemanan', 200, null, $friendRequests);
    }

    /**
     * Menolak permintaan pertemanan.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Menolak permintaan pertemanan.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectFriendship($id)
    {
        $currentUserId = Auth::user()->id;
        $friendship = Friendship::with('user')
            ->where('friend_id', $currentUserId)
            ->where('id', $id)
            ->first();

        if (!$friendship) {
            return Helper::APIResponse('Permintaan pertemanan tidak ditemukan', 404, 'Data tidak ditemukan', null);
        }

        $datas = [
            'id' => $friendship->id,
            'req_id' => $friendship->user_id,
            'req_name' => User::where('id', $friendship->user_id)->pluck('name')->first(),
            'your_id' => $friendship->friend_id,
            'your_name' => User::where('id', $friendship->friend_id)->pluck('name')->first(),
            'status' => 'rejected'
        ];

        $friendship->delete();

        return Helper::APIResponse('Permintaan pertemanan ditolak', 200, null, $datas);
    }


    /**
     * Menerima permintaan pertemanan.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptFriendship($id)
    {
        $currentUserId = Auth::user()->id;
        $friendship = Friendship::with('user')->where('friend_id', $currentUserId)
            ->where('id', $id)
            ->first();

        if (!$friendship) {
            return Helper::APIResponse('Gagal menerima pertemanan, permintaan tidak ditemukan', 400, 'Bad Request', null);
        }

        $friendship->status = 'accept';
        $friendship->save();

        $datas = [
            'id' => $friendship->id,
            'req_id' => $friendship->user_id,
            'req_name' => User::where('id', $friendship->user_id)->pluck('name'),
            'your_id' => $friendship->friend_id,
            'your_name' => User::where('id', $friendship->friend_id)->pluck('name'),
            'status' => $friendship->status
        ];

        return Helper::APIResponse('Permintaan pertemanan diterima', 200, null, $datas);
    }

    /**
     * Mendapatkan semua teman yang sudah diterima.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllMyFriends()
    {
        $user = User::where("id", Auth::id())->first();
        $friendsOfMine = $user->friendsOfMine('accept')->get();
        $friendsOf = $user->friendOf('accept')->get();

        $allFriends = $friendsOfMine->merge($friendsOf);

        foreach ($allFriends as &$friend) {
            $friend['sameSchool'] = $friend['school_id'] == $user->school_id;
        }

        return Helper::APIResponse('Berhasil mendapatkan semua teman', 200, null, $allFriends);
    }

    public function friendDetail($friend_id)
    {
        $user = User::where('id', $friend_id)->first();
        if ($user) {
            return Helper::APIResponse('Get detail', 200, null, $user);
        }
        return Helper::APIResponse('Failed detail', 404, 'data not found', null);
    }

    public function colek(Request $req)
    {
        FCM::android([$req->query('regId')])->send([
            'title' => 'Hello, ' . $req->query('name'),
            'message' => $req->query('msg')
        ]);
    }
}
