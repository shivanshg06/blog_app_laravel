<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        // print($request);
        $fields = $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|unique:users,email',
            'password'=>'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return Response($response, 201);
        // return Response($request, 404);
    }
    public function login(Request $request){
        $fields = $request->validate([
            'email'=>'required|string',
            'password'=>'required|string'
        ]);

        // Check Email
        $user = User::where('email', $fields['email'])->first();

        // Check Password
        if (!$user) {
            return Response([
                'message' => 'User Not found',
            ],404);
        }

        if(!Hash::check($fields['password'], $user->password)){
            return Response([
                'message' => 'Bad Credentials',
            ], 401);
        }
        
        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return Response($response, 200);
    }

    public function logout(Request $request){
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged Out',
        ];
    }

    public function userId(Request $request){
        return [
            'id' => $request->user()->id,
        ];
    }
}
