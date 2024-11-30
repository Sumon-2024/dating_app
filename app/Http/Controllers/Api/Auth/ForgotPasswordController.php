<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends BaseController
{
    /**
     * Sends a password reset link to the provided email address.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validate the email with custom messages
        $request->validate(
            ['email' => 'required|email'],
            ['email.required' => 'The email field is required.', 'email.email' => 'The email must be a valid email address.']
        );

        // Send the reset link
        $status = Password::sendResetLink($request->only('email'));

        // Return response
        return $status == Password::RESET_LINK_SENT
            ? $this->successResponse(['message' => __($status)], 'Success', 200)
            : $this->errorResponse(__($status), 400);
    }

    /**
     * Resets the user's password using the provided token and new password.
     */
    public function reset(Request $request)
    {
        // Validate the reset request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422); // Updated to 422 for validation errors
        }

        // Attempt to reset the password
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
