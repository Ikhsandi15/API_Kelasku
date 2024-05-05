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
        $user = User::all();
        if (count($user) == 0) {
            return Helper::APIResponse('Data empty', 200, null, null);
        }
        return Helper::APIResponse('Success get all users', 200, null, $user);
    }

    public function profile()
    {
        $user = Auth::user();
        return Helper::APIResponse('Success get my detail', 200, null, $user);
    }

    public function update(Request $req)
    {
        $validation = Validator::make($req->all(), [
            'name' => 'required',
            'school_id' => 'required',
            'profile' => 'mimes:jpg,png|max:2084'
        ]);

        if ($validation->fails()) {
            return Helper::APIResponse('Error Validation', 402, $validation->errors(), null);
        }

        $user = Auth::user();
        $data = User::where('id', $user->id)->first();

        if ($data) {
            if ($req->hasFile('profile')) {
                // cek misal user akan update atau ubah image maka image sebelumnya akan dihapus dari storage
                if ($data->profile != null) {
                    Storage::delete('public/profiles/' . $data->profile);
                }
                $image = $req->file('profile');
                $newImageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/profiles/' . $newImageName);

                $input = $req->all();
                $input['profile'] = $newImageName;

                $data->update($input);
                return Helper::APIResponse('Success update data with image', 200, null, $data);
            }
        }

        $input = $req->all();

        $data->update($input);

        return Helper::APIResponse('Success update data', 200, null, $data);
    }

    public function requestFriendship($target_id)
    {
        $isCreateReq = Friendship::where('request_friendship', Auth::user()->id)->where('accept_friendship', $target_id)->first();
        if ($isCreateReq) {
            $isCreateReq->delete();
            return Helper::APIResponse('Success cancel request', 200, null, null);
        }

        $data = Friendship::create([
            'id' => Str::uuid(),
            'request_friendship' => Auth::user()->id,
            'accept_friendship' => $target_id,
            'status' => 'pending'
        ]);

        return Helper::APIResponse('Success create request', 200, null, $data);
    }

    public function getAllRequestFriendship()
    {
        $data = Friendship::where('accept_friendship', Auth::user()->id)->where('status', 'pending')->get();
        if (count($data) == 0) {
            return Helper::APIResponse('Data empty', 200, null, null);
        }
        return Helper::APIResponse('Success get all friendship', 200, null, $data);
    }

    public function rejectFriendship($id)
    {
        $data = Friendship::where('id', $id)->delete();
        return Helper::APIResponse('Success rejected friendship', 200, null, $data);
    }

    public function acceptFriendship($id)
    {
        $data = Friendship::where('accept_friendship', Auth::user()->id)->where('id', $id)->first();
        if (!$data) {
            return Helper::APIResponse('Failed accepted friendship, user not have relationship request', 400, 'Bad Request', null);
        }
        $data->status = 'accept';
        $data->save();

        return Helper::APIResponse('Success accepted friendship', 200, null, $data);
    }

    public function getAllMyFriends()
    {
        // dd(User::with('friendship')->where('id', Auth::user()->id)->first()); // [][]
        // Ambil semua pertemanan (di mana pengguna saat ini adalah penerima atau pengirim undangan yang diterima)
        $friendships = Friendship::where('status', 'accept')
            ->where(function ($query) {
                $query->where('accept_friendship', Auth::user()->id)
                    ->orWhere('request_friendship', Auth::user()->id);
            })
            ->get();

        // Kumpulkan semua ID pengguna dari pertemanan
        $relatedUserIds = $friendships->pluck('accept_friendship')->merge($friendships->pluck('request_friendship'))->unique();

        // Ambil semua data pengguna yang berelasi
        $relatedUsers = User::whereIn('id', $relatedUserIds)->get();

        // Tambahkan atribut 'sameSchool' untuk menandai pengguna yang memiliki school_id yang sama dengan pengguna yang sedang login
        $relatedUsers->each(function ($user) {
            $user->sameSchool = $user->school_id == Auth::user()->school_id;
        });

        // Hapus pengguna yang sedang login dari hasil
        $relatedUsers = $relatedUsers->reject(function ($user) {
            return $user->id == Auth::user()->id;
        });

        if (count($relatedUsers) == 0) {
            return Helper::APIResponse('Data empty', 200, null, null);
        }

        // Reset kunci array menjadi numerik
        $relatedUsers = $relatedUsers->values();


        return Helper::APIResponse('Success get all friendship', 200, null, $relatedUsers);
    }

    public function friendDetail($friend_id)
    {
        $user = User::where('id', $friend_id)->first();
        if ($user) {
            return Helper::APIResponse('Get detail friend', 200, null, $user);
        }
        return Helper::APIResponse('Failed detail friend', 400, 'bad request', null);
    }

    public function colek(Request $req)
    {
        FCM::android([$req->query('regId')])->send([
            'title' => 'Hello, ' . $req->query('name'),
            'message' => $req->query('msg')
        ]);
    }
}
