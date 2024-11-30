<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ApiAuthController extends BaseController
{

    /**
     * @method
     * Handles user login and generates an authentication token upon successful login.
     * 
     * @description Validates user credentials and generates a token for authentication. Returns an error if the credentials are invalid or any exception occurs during the process.
     * 
     * @bodyParam email string required The email address of the user. Example: admin@gmail.com
     * @bodyParam password string required The password of the user. Example: 123456789
     * 
     * @response scenario="success" {
     *   "status": "success",
     *   "message": "Login successful.",
     *   "data": {
     *       "token": "1|T8cDp5zUoS1NriCCU5z1Qkla3A48k5TS8NaELM1Z",
     *       "user": {
     *           "id": 1,
     *           "name": "admin",
     *           "email": "admin@gmail.com"
     *       }
     *   }
     * }
     * 
     * @response 401 scenario="unauthorized" {
     *   "status": "error",
     *   "message": "Invalid credentials. Please try again."
     * }
     * 
     * @response 500 scenario="error" {
     *   "status": "error",
     *   "message": "An error occurred during login. Please try again."
     * }
     */

     public function login(Request $request)
     {
         DB::beginTransaction();
     
         try {
             $request->validate([
                 'email' => 'required|email',
                 'password' => 'required',
             ]);
     
             $user = User::where('email', $request->email)->first();
     
             if (!$user || !Hash::check($request->password, $user->password)) {
                 return $this->errorResponse('Invalid credentials. Please try again.', 401);
             }
     
             $token = $user->createToken('auth_token')->plainTextToken;
     
             DB::commit();
     
             return $this->successResponse([
                 'token' => $token,
                 'user' => [
                     'id' => $user->id,
                     'name' => $user->name,
                     'email' => $user->email
                 ]
             ]);
     
         } catch (\Exception $e) {
             DB::rollBack();
             Log::error('Login failed for email: ' . $request->email . ', Error: ' . $e->getMessage());
             return $this->errorResponse('An error occurred during login. Please try again.', 500);
         }
     }
     


    /**
     * @method
     * Get Auth User Information
     * 
     * @description Retrieves the logged-in user's information, including ID, name, email, and timestamps.
     * 
     * @response scenario="success" {
     *   "status": "success",
     *   "message": "User information retrieved successfully.",
     *   "data": {
     *       "id": 1,
     *       "name": "admin",
     *       "email": "admin@gmail.com",
     *       "email_verified_at": null,
     *       "created_at": "2024-11-30T03:40:17.000000Z",
     *       "updated_at": "2024-11-30T03:40:17.000000Z"
     *   }
     * }
     * 
     * @response 401 scenario="unauthorized" {
     *   "status": "error",
     *   "message": "Unauthorized."
     * }
     * 
     * @response 500 scenario="error" {
     *   "status": "error",
     *   "message": "An error occurred while retrieving the user. Please try again."
     * }
     */
    public function user(Request $request)
    {
        if (!$request->user()) {
            return $this->errorResponse('Unauthorized', 401);
        }

        try {
            return $this->successResponse($request->user());
        } catch (\Exception $e) {
            Log::error('User retrieval failed for user ID: ' . optional($request->user())->id . ', Error: ' . $e->getMessage());
            return $this->errorResponse('An error occurred while retrieving the user. Please try again.', 500);
        }
    }



    /**
     * @method
     * User Logout
     * 
     * @description Logs the user out by deleting all active authentication tokens associated with the user. 
     * Ensures the user is fully logged out and cannot access authenticated routes until they log in again.
     * 
     * @response scenario="success" {
     *   "status": "success",
     *   "message": "Logout successful.",
     *   "data": {
     *       "message": "Logged out successfully."
     *   }
     * }
     * 
     * @response 401 scenario="unauthorized" {
     *   "status": "error",
     *   "message": "Unauthorized."
     * }
     * 
     * @response 500 scenario="error" {
     *   "status": "error",
     *   "message": "An error occurred while logging out. Please try again."
     * }
     */

    public function logout(Request $request)
    {
        if (!$request->user()) {
            return $this->errorResponse('Unauthorized', 401);
        }

        DB::beginTransaction();

        try {
            Log::info('Tokens before logout: ', $request->user()->tokens->toArray());

            $request->user()->tokens->each(function ($token) {
                $token->delete();
            });

            DB::commit();

            Log::info('Tokens after logout: ', $request->user()->tokens->toArray());

            return $this->successResponse(['message' => 'Logged out successfully.']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Logout transaction failed: ' . $e->getMessage());

            return $this->errorResponse('An error occurred while logging out. Please try again.', 500);
        }
    }


    /** 
     * @method
     * Edit User Profile
     * 
     * @description Allows the logged-in user to update their profile information, including their name, email, and password. 
     * It returns the updated user information after a successful update.
     * 
     * @bodyParam name string required The name of the user. Example: "Admin"
     * @bodyParam email string required The email address of the user. Example: "admin@example.com"
     * @bodyParam password string nullable The new password for the user. Minimum of 6 characters. Example: "newpassword123"
     * @bodyParam password_confirmation string nullable Confirmation of the new password. This field is required if the password is provided. Example: "newpassword123"
     * 
     * @response scenario="success" {
     *   "status": "success",
     *   "message": "Success",
     *   "data": {
     *       "message": "Profile updated successfully.",
     *       "data": {
     *           "id": 1,
     *           "name": "Admin",
     *           "email": "admin@example.com",
     *           "email_verified_at": null,
     *           "created_at": "2024-11-30T03:40:17.000000Z",
     *           "updated_at": "2024-11-30T05:18:57.000000Z"
     *       }
     *   }
     * }
     * 
     * @response 400 scenario="error" {
     *   "status": "error",
     *   "message": "Invalid data provided. Please check your inputs."
     * }
     * 
     * @response 401 scenario="unauthorized" {
     *   "status": "error",
     *   "message": "Unauthorized. You must be logged in to update your profile."
     * }
     * 
     * @response 500 scenario="error" {
     *   "status": "error",
     *   "message": "An error occurred while updating the profile. Please try again."
     * }
     */
    public function editProfile(Request $request) {
        return $this->updateProfile($request);
    }

        
    /** 
     * @method
     * Update User Profile
     * 
     * @description Allows the logged-in user to update their profile information, including their name, email, and optionally, their password. 
     * It returns the updated user information after a successful update.
     * 
     * @bodyParam name string required The name of the user. Example: "Admin"
     * @bodyParam email string required The email address of the user. Example: "admin@example.com"
     * @bodyParam password string nullable The new password for the user. Minimum of 6 characters. Example: "newpassword123"
     * @bodyParam password_confirmation string nullable Confirmation of the new password. This field is required if the password is provided. Example: "newpassword123"
     * 
     * @response scenario="success" {
     *   "status": "success",
     *   "message": "Success",
     *   "data": {
     *       "message": "Profile updated successfully.",
     *       "data": {
     *           "id": 1,
     *           "name": "Admin",
     *           "email": "admin@gmail.com",
     *           "email_verified_at": null,
     *           "created_at": "2024-11-30T05:36:54.000000Z",
     *           "updated_at": "2024-11-30T05:43:56.000000Z"
     *       }
     *   }
     * }
     * 
     * @response 400 scenario="error" {
     *   "status": "error",
     *   "message": "Invalid data provided. Please check your inputs."
     * }
     * 
     * @response 401 scenario="unauthorized" {
     *   "status": "error",
     *   "message": "Unauthorized. You must be logged in to update your profile."
     * }
     * 
     * @response 500 scenario="error" {
     *   "status": "error",
     *   "message": "An error occurred while updating the profile. Please try again."
     * }
     */
    public function updateProfile(Request $request) {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->user()->id,
                'password' => 'nullable|string|min:6|confirmed', 
            ]);

            $user = $request->user();

            // Update user details
            $user->name = $request->name;
            $user->email = $request->email;

            // Update password if provided
            if ($request->has('password') && $request->password) {
                $user->password = Hash::make($request->password);
            }

            // Save user profile
            $user->save();

            // Return success response
            return $this->successResponse([
                'message' => 'Profile updated successfully.',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage());
            return $this->errorResponse('An error occurred while updating the profile. Please try again.', 500);
        }
    }




    /** 
     * @method
     * Delete User Account
     * 
     * @description Allows the logged-in user to permanently delete their account. After deletion, the user will be logged out and their profile information will no longer exist in the system.
     * 
     * @response scenario="success" {
     *   "status": "success",
     *   "message": "Success",
     *   "data": {
     *       "message": "Account deleted successfully."
     *   }
     * }
     * 
     * @response 401 scenario="unauthorized" {
     *   "status": "error",
     *   "message": "Unauthorized. You must be logged in to delete your account."
     * }
     * 
     * @response 500 scenario="error" {
     *   "status": "error",
     *   "message": "An error occurred while deleting the account. Please try again."
     * }
     */
    public function deleteAccount(Request $request)
    {
        try {
            $user = $request->user();

            // Delete user account
            $user->delete();

            // Return success response
            return $this->successResponse([
                'message' => 'Account deleted successfully.',
            ]);

        } catch (\Exception $e) {
            Log::error('Account deletion failed: ' . $e->getMessage());
            return $this->errorResponse('An error occurred while deleting the account. Please try again.', 500);
        }
    }



}
