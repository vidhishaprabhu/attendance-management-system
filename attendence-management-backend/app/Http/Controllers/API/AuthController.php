<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email'=>'required|string|email|max:255|unique:users',
            'password'=>'required|string|min:8'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password'=>bcrypt($request->password)
        ]);
        return response()->json([
            'message'=>'User registered successfully'
            
        ],201);
    }
    public function login(Request $request){
        $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);
        if(!Auth::attempt($request->only('email','password'))){
            return response()->json([
                'message'=>'Invalid credentials'
            ],401);
        }
        $user = Auth::user();
        return response()->json([
            'Login successfull',
            'user'=>$user,
            'token'=>$user->createToken('auth_token')->plainTextToken
        ],200);
    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'message'=>'User Logged out successfully'
        ],200);
    }
}
