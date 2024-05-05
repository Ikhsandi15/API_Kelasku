<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SchoolController extends Controller
{
    public function index()
    {
        $data = School::all();
        if (count($data) == 0) {
            return Helper::APIResponse('Data empty', 200, null, null);
        }
        return Helper::APIResponse('Success get all schools', 200, null, $data);
    }

    public function create(Request $req)
    {
        $validation = Validator::make($req->all(), [
            'school_name' => 'required'
        ]);

        if ($validation->fails()) {
            return Helper::APIResponse('Error validation', 422, $validation->errors(), null);
        }

        $data = School::create([
            'id' => Str::uuid(),
            'school_name' => $req->school_name
        ]);

        return Helper::APIResponse('success create data', 200, null, $data);
    }

    public function update(Request $req, $id)
    {
        $validation = Validator::make($req->all(), [
            'school_name' => 'required'
        ]);

        if ($validation->fails()) {
            return Helper::APIResponse('Error validation', 422, $validation->errors(), null);
        }

        $data = School::where('id', $id)->first();
        $data->update($req->all());

        return Helper::APIResponse('success update data', 200, null, $data);
    }

    public function show($id)
    {
        $data = School::where('id', $id)->first();
        if (!$data) {
            return Helper::APIResponse('data not found', 404, null, null);
        }
        return Helper::APIResponse('Success get school', 200, null, $data);
    }

    public function delete($id)
    {
        $data = School::where('id', $id)->first();
        if (!$data) {
            return Helper::APIResponse('data not found', 404, null, null);
        }
        $data->delete();
        return Helper::APIResponse('Success delete school', 200, null, $data);
    }
}
