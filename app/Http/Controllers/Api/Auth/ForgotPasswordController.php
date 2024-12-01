<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends BaseController
{

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(
            ['email' => 'required|email'],
            ['email.required' => 'The email field is required.', 'email.email' => 'The email must be a valid email address.']
        );

        $status = Password::sendResetLink($request->only('email'));

        return $status == Password::RESET_LINK_SENT
            ? $this->successResponse(['message' => __($status)], 'Success', 200)
            : $this->errorResponse(__($status), 400);
    }


    public function resetForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');
    
        if (!$token || !$email) {
            return response()->json(['error' => 'Token and email are required'], 400);
        }
    
        return response()->json([
            'message' => 'Token and email retrieved successfully',
            'token' => $token,
            'email' => $email
        ]);
    }
    
   

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );
    
        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully'], 200);
        }
    
        return response()->json(['error' => __($status)], 400);
    }
    
}
