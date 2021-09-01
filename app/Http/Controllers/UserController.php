<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|unique:users,name|max:50',
            'email'     => 'required|email|unique:users,email',
            'password'  => [
                'required',
                'confirmed',
                Password::min(6)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
            ]
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        $token = $user->createToken('web');
        return response()->json([
            'user'          => $user,
            'access_token'  => $token->plainTextToken
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!isset($user) || !Hash::check($request->password, $user->password)) {
            throw new ValidationException([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        $token = $user->createToken('web');
        return ['access_token' => $token->plainTextToken];
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => $user
        ]);
    }
}
