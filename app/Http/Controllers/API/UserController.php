<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            // TODO: Validate Request
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required'
            ]);

            // TODO: Get Request Email and Password
            $credentials = request(['email', 'password']);

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
                'access_token' => $tokenResult,
                'token_type'   => 'Bearer',
                'data'         => $user
            ], 'Login Success');
        } catch (Exception $error) {
            // TODO: Error Response
            return ResponseFormatter::error('Authentication Failed');
        }
    }

    public function register(Request $request)
    {
        try {
            // TODO: Validate Request
            $request->validate([
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', new Password],
            ]);

            // TODO: Create User
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // TODO: Generate Token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // TODO: Return Success Response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type'   => 'Bearer',
                'user'         => $user
            ], 'Register Successfully');
        } catch (Exception $error) {
            // TODO: Return Failed Response
            return ResponseFormatter::error($error->getMessage());
        }
    }

    public function logout(Request $request)
    {
        // TODO: Revoke Token
        $token = $request->user()->currentAccessToken()->delete();

        // TODO: Return Response
        return ResponseFormatter::success($token, 'Logout Success');
    }

    public function fetch(Request $request)
    {
        // TODO: GET USER
        $user = $request->user();

        // TODO: Return Response
        return ResponseFormatter::success($user, 'Fetch Success');
    }
}
