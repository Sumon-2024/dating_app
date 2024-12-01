<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class ForgotPasswordController extends BaseController
{

    /**
     * @method
     * Sends a password reset link to the user's email address.
     * 
     * @description Sends a password reset link to the provided email address. Returns an error if the email is not valid or an exception occurs during the process.
     * 
     * @bodyParam email string required The email address of the user requesting the password reset. Example: admin@gmail.com
     * 
     * @response scenario="success" {
     *   "status": "success",
     *   "message": "Success",
     *   "data": {
     *       "message": "We have emailed your password reset link."
     *   }
     * }
     * 
     * @response 400 scenario="error" {
     *   "status": "error",
     *   "message": "Please wait before retrying."
     * }
     * 
     * @response 500 scenario="error" {
     *   "status": "error",
     *   "message": "An error occurred during password reset. Please try again."
     * }
     */
    public function sendResetLinkEmail(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $request->validate(
                ['email' => 'required|email'],
                ['email.required' => 'The email field is required.', 'email.email' => 'The email must be a valid email address.']
            );

            $status = Password::sendResetLink($request->only('email'));

            if ($status == Password::RESET_LINK_SENT) {
                DB::commit();
                return $this->successResponse(['message' => __($status)], 'Success', 200);
            }

            DB::rollBack();
            return $this->errorResponse(__($status), 400);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Password reset link send failed for email: ' . $request->email . ', Error: ' . $e->getMessage());
            return $this->errorResponse('An error occurred during password reset. Please try again.', 500);
        }
    }


        
    /**
     * @method
     * Retrieves the token and email for password reset.
     * 
     * @description Retrieves the password reset token and email from the request query parameters. Returns an error if the token or email is not provided or if any exception occurs.
     * 
     * @queryParam token string required The password reset token. Example: 0254315374cd2a6a6d7868d9cd9b9a9742dc5f9d1034c06b0af14af81c16b5d5
     * @queryParam email string required The email address for which the reset is requested. Example: admin@gmail.com
     * 
     * @response scenario="success" {
     *   "status": "success",
     *   "message": "Success",
     *   "data": {
     *       "message": "Token and email retrieved successfully",
     *       "token": "0accac5db371a2bf4ca0f4049f0a51fd49220e87e67ba76b3c525e342c429692",
     *       "email": "admin@gmail.com"
     *   }
     * }
     * 
     * @response 400 scenario="error" {
     *   "status": "error",
     *   "message": "Token and email are required"
     * }
     * 
     * @response 500 scenario="error" {
     *   "status": "error",
     *   "message": "An error occurred while retrieving token and email. Please try again."
     * }
     */
    public function resetForm(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $token = $request->query('token');
            $email = $request->query('email');
            
            if (!$token || !$email) {
                DB::rollBack();
                return $this->errorResponse('Token and email are required', 400);
            }

            DB::commit();
            return $this->successResponse([
                'message' => 'Token and email retrieved successfully',
                'token' => $token,
                'email' => $email
            ], 'Success', 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Reset form failed for email: ' . $email . ', Error: ' . $e->getMessage());
            return $this->errorResponse('An error occurred while retrieving token and email. Please try again.', 500);
        }
    }


    /**
     * @method
     * Resets the user's password using the provided token and email.
     * 
     * @description Resets the user's password. The user must provide the valid token, email, and new password. Returns an error if validation fails or an exception occurs.
     * 
     * @bodyParam token string required The password reset token. Example: 0254315374cd2a6a6d7868d9cd9b9a9742dc5f9d1034c06b0af14af81c16b5d5
     * @bodyParam email string required The email address associated with the password reset. Example: admin@gmail.com
     * @bodyParam password string required The new password. Example: newpassword123
     * @bodyParam password_confirmation string required The confirmation of the new password. Example: newpassword123
     * 
     * @response scenario="success" {
     *   "status": "success",
     *   "message": "Success",
     *   "data": {
     *       "message": "Password reset successfully"
     *   }
     * }
     * 
     * @response 400 scenario="error" {
     *   "status": "error",
     *   "message": "The password field must be at least 6 characters."
     * }
     * 
     * @response 500 scenario="error" {
     *   "status": "error",
     *   "message": "An error occurred during password reset. Please try again."
     * }
     */
    public function reset(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed',
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->save();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                DB::commit();
                return $this->successResponse(['message' => 'Password reset successfully'], 'Success', 200);
            }

            DB::rollBack();
            return $this->errorResponse(__($status), 400);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Password reset failed for email: ' . $request->email . ', Error: ' . $e->getMessage());
            return $this->errorResponse('An error occurred during password reset. Please try again.', 500);
        }
    }

        
}
