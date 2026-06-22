<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserAuthController extends Controller 
{
    public function register(RegisterUserRequest $request)
    {
        $registerUserData = $request->validated();
        $user = User::create([
            'name' => $registerUserData['name'],
            'email' => $registerUserData['email'],
            'password' => Hash::make($registerUserData['password']),
        ]);

        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
        // event(new Registered($user));
        Log::info('User registered: ' . $user->email);

        return response()->json([
            'message' => 'User Created Successfully!',
            'user' => new UserResource($user),
            'access_token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $loginUserData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:8'
        ]);

        $user = User::where('email', $loginUserData['email'])->first();
        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
        return response()->json([
            'user' => new UserResource($user),
            'access_token' => $token,
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            "message" => "logged out successfully!"
        ]);
    }

    public function user()
    {
        if (!auth()->user()) {
            return response()->json([
                "message" => "User not found"
            ]);
        }
        return response()->json([
            "user" => new UserResource(auth()->user()),
        ]);
    }

    public function refreshToken()
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();
        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
        return response()->json([
            'access_token' => $token,
        ]);
    }
}
