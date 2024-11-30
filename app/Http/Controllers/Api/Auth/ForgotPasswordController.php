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


    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422); 
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? $this->successResponse(['message' => __($status)], 'Success', 200)
            : $this->errorResponse(__($status), 400);
    }
}
