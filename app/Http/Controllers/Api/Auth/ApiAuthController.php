<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ApiAuthController extends BaseController
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $this->successResponse([
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }

    public function user(Request $request)
    {
        return $this->successResponse($request->user());
    }

    public function logout(Request $request)
    {
        if (!$request->user()) {
            return $this->errorResponse('Unauthorized', 401);
        }

        Log::info('Tokens before logout: ', $request->user()->tokens->toArray());

        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        Log::info('Tokens after logout: ', $request->user()->tokens->toArray());

        return $this->successResponse(['message' => 'Logged out successfully.']);
    }
}
