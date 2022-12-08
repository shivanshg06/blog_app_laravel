<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // print($request);
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'security-question' => 'NA',
            'security-answer' => 'NA',
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return Response($response, 201);
    }

    public function setSecurity(Request $request)
    {
        $user = User::find($request->user()->id);
        $fields = $request->validate(
            [
                'security-question' => 'required|string',
                'security-answer' => 'required|string',
            ]
        );
        $user->update($fields);
        return Response($user, 200);
    }

    public function getUser(Request $request)
    {
        $user = $request->user;
        return [
            'user' => $user,
        ];
    }

    public function userId(Request $request)
    {
        return [
            'id' => $request->user()->id,
        ];
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check Email
        $user = User::where('email', $fields['email'])->first();

        // Check Password
        if (!$user) {
            return Response([
                'message' => 'User Not found',
            ], 404);
        }

        if (!Hash::check($fields['password'], $user->password)) {
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

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged Out',
        ];
    }

    public function resetPassword(Request $request)
    {
        $user = $request->user();
        $fields = $request->validate([
                'password' => 'required|string|confirmed',
                'old-password' => 'required|string',
            ]);
        $update = [
            'password'=>bcrypt($fields['password']),
        ];
        if (!Hash::check($fields['old-password'], $user->password)) {
            return Response([
                'message' => 'Bad Credentials',
            ], 401,);
        }
        
        $user->update($update);

        return Response([
                'message' => 'Password Reset Successful',
            ], 
            200,
        );
    }
}