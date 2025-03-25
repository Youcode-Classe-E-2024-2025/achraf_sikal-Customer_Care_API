<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            "name"=> "required|max:255",
            "email"=> "required|email|unique:users",
            "password" => "required|confirmed",
        ]);
        $user = User::create($fields);
        $token = $user->createToken($request->name)->plainTextToken;
        return [
            "user" => $user,
            "token"=> $token
        ];
    }
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email|exists:users",
            "password" => "required",
        ]);
        $user = User::where("email", $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(["message"=> "The prevoided credintials are incorrect"],401);
        }
        $token = $user->createToken($user->name)->plainTextToken;
        return [
            "user" => $user,
            "token" => $token
        ];
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(["message"=> "You are logged out"],200);
    }
}
