<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Mockery\Expectation;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            // TODO: Validate Request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|'
            ]);

            // TODO: Get Request Email and Password
            $credentials = request(['email'], ['password']);

            // TODO: If login failed
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error('Unauthorized', 401);
            }

            // TODO: Get Email User
            $user = User::where('email', $request->email)->first();

            // TODO: If Password Wrong
            if (!Hash::check($request->password, $user->password)) {
                throw new Exception('Invalid Password');
            }

            // TODO: Create Token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // TODO: Response Success Login
            return ResponseFormatter::success([
                'access_token' =>  $tokenResult,
                'token_type' => 'Bearer',
                'data' => $user
            ], 'Login Success');
        } catch (Expectation $e) {
            return ResponseFormatter::error('Authentication Failed');
        }
    }
}
