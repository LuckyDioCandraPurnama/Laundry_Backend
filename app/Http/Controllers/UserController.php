<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid username and password']);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Generate Token Failed']);
        }

        $user = JWTAuth::user();

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user
        ]);
        // $data = [
        // 	'token' => $token,
        // 	'user'  => JWTAuth::user()
        // ];
        // return response()->json(['message' => 'Authentication success', 'data' => $data]);
    }

    public function getUser()
    {
        $user = JWTAuth::user();
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'username' => 'required',
            'password' => 'required|string|min:6',
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response->errorResponse($validator->errors());
        }

        $user = new User();
        $user->nama     = $request->nama;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->role     = $request->role;

        $user->save();

        $token = JWTAuth::fromUser($user);

        $data = User::where('username', '=', $request->username)->first();

        return response()->json(['message' => 'Berhasil menambah user baru', 'data' => $data]);
    }

    public function loginCheck()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->response->errorResponse('Invalid token!');
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->response->errorResponse('Token expired!');
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->response->errorResponse('Invalid token!');
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->response->errorResponse('Token absent!');
        }

        return $this->response->successResponseData('Authentication success!', $user);
    }

    public function logout(Request $request)
    {
        if (JWTAuth::invalidate(JWTAuth::getToken())) {
            return $this->response->successResponse('You are logged out');
        } else {
            return $this->response->errorResponse('Logged out failed');
        }
    }
}
