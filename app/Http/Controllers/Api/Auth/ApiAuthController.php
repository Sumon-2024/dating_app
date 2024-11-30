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
     * @description Updates the logged-in user's profile with new data.
     * 
     * @bodyParam name string required Example: John Doe
     * @bodyParam email string required Example: john.doe@example.com
     * @bodyParam password string optional Example: 12345678
     * @bodyParam confirm-password string optional Example: 12345678
     * 
     * @response scenario="Successful Profile Edit" {
     * "status": "success",
     * "message": "Profile updated successfully.",
     * "data": {
     *   "name": "John Doe",
     *   "email": "john.doe@example.com"
     * }
     * }
     * 
     * @response 500 scenario="Server Error" {
     * "status": "error",
     * "message": "An error occurred while updating the profile. Please try again."
     * }
     */

    public function editProfile(Request $request){
        return $this->updateProfile($request);  
    }


    /**
     * @method
     * Update User Profile
     * 
     * @description Updates the logged-in user's profile with validated data.
     * 
     * @bodyParam name string required Example: John Doe
     * @bodyParam email string required Example: john.doe@example.com
     * @bodyParam password string optional Example: 12345678
     * 
     * @response scenario="Successful Profile Update" {
     * "status": "success",
     * "message": "Profile updated successfully.",
     * "data": {
     *   "name": "John Doe",
     *   "email": "john.doe@example.com"
     * }
     * }
     * 
     * @response 500 scenario="Server Error" {
     * "status": "error",
     * "message": "An error occurred while updating the profile. Please try again."
     * }
     */

    public function updateProfile(Request $request){
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->user()->id,
                'password' => 'nullable|string|min:6|confirmed', 
            ]);

            $user = $request->user();

            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->has('password') && $request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

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
     * @description Deletes the logged-in user's account.
     * 
     * @response scenario="Successful Account Deletion" {
     * "status": "success",
     * "message": "Account deleted successfully."
     * }
     * 
     * @response 500 scenario="Server Error" {
     * "status": "error",
     * "message": "An error occurred while deleting the account. Please try again."
     * }
     */

    public function deleteAccount(Request $request){
        try {
            $user = $request->user();

            $user->delete();
            
            return $this->successResponse([
                'message' => 'Account deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Account deletion failed: ' . $e->getMessage());
            return $this->errorResponse('An error occurred while deleting the account. Please try again.', 500);
        }
    }


}
