<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'nullable|string',
            'name'      => 'nullable|string',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6',
        ]);

        $name = $validated['name'] ?? $validated['full_name'] ?? null;
        if (!$name) {
            return response()->json([
                'status' => 'error',
                'message' => 'The name field is required.',
            ], 422);
        }

        $user = User::create([
            'name'      => $name,
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Registration successful',
            'data'    => array_merge($user->toArray(), ['token' => $token]),
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Username or password incorrect',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful',
            'data'    => array_merge($user->toArray(), ['token' => $token]),
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout successful',
        ]);
    }
}
