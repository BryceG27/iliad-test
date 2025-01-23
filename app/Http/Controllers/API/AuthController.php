<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->api(null, 'Invalid credentials');
        }

        return response()->json([
            'token' => Auth::user()->createToken('authToken')->plainTextToken,
        ]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->api([
            'message' => 'Logged out',
        ]);
    }

    public function get() {
        $user = Auth::user();

        $response = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        return response()->api(
            $response
        );
    }
    
    public function update(Request $request) {
        $validation = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);
        
        Auth::user()->update([
            'name' => $validation['name'],
            'email' => $validation['email'],
        ]);
        
        return response()->api([
            'message' => 'User updated',
        ]);
    }

    public function store(Request $request) {
        $validation = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        $user = User::create([
            'name' => $validation['name'],
            'email' => $validation['email'],
            'password' => Hash::make($validation['password']),
        ]);
        
        return response()->api([
            'message' => 'User created',
            'token' => $user->createToken('authToken')->plainTextToken,
        ]);
    }
    
    public function destroy() {
        dd(Auth::user());
    }
}
