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
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return $this->successResponse([
                'token' => $token
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Login failed: ' . $e->getMessage());
            return $this->errorResponse('An error occurred during login. Please try again.', 500);
        }
    }

    public function user(Request $request)
    {
        try {
            return $this->successResponse($request->user());
        } catch (\Exception $e) {
            Log::error('User retrieval failed: ' . $e->getMessage());
            return $this->errorResponse('An error occurred while retrieving the user. Please try again.', 500);
        }
    }

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


    public function editProfile(Request $request){
        return $this->updateProfile($request);  
    }


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
