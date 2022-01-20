<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Member;
use JWTAuth;

class MemberController extends Controller
{
    // public $user;
    // public function __construct()
    // {
    //     $this->user = JWTAuth::parseToken()->authenticate();
    // }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'alamat' => 'required|string',
            'jenis_kelamin' => 'required|string',
            'tlp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $member = new Member();
        $member->nama = $request->nama;
        $member->alamat = $request->alamat;
        $member->jenis_kelamin = $request->jenis_kelamin;
        $member->tlp = $request->tlp;

        $member->save();

        $data = Member::where('id_member', '=', $member->id_member)->first();

        return response()->json([
            'message' => 'Data member berhasil di input',
            'data' => $data,
        ]);
    }

    public function getAll()
    {
        // $data['count'] = Member::count();
        // $data['member'] = Member::get();
        // return response()->json(['member' => $data]);
        $data = Member::get();        
        return response()->json($data);
    }

    public function getById($id_member)
    {
        $data['member'] = Member::where('id_member', '=', $id_member)->get();
        return response()->json(['data' => $data]);
    }

    public function update(Request $request, $id_member)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'alamat' => 'required|string',
            'jenis_kelamin' => 'required|string',
            'tlp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $member = Member::where('id_member', '=', $id_member)->first();
        $member->nama = $request->nama;
        $member->alamat = $request->alamat;
        $member->jenis_kelamin = $request->jenis_kelamin;
        $member->tlp = $request->tlp;

        $member->save();

        return response()->json([
            'message' => 'Data member berhasil diupdate'
        ]);
    }

    public function delete($id_member)
    {
        $delete = Member::where('id_member', '=', $id_member)->delete();

        if ($delete) {
            return response()->json(['message' => 'Berhasil dihapus']);
        } else {
            return response()->json(['message' => 'Gagal dihapus']);
        }
    }
}
