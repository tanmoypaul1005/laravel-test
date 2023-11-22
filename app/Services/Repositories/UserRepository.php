<?php

namespace App\Repositories;
use App\Helpers\ResponseHelper;
use App\Interfaces\UserInterface;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserRepository implements UserInterface
{
    public function addUsers(Request $request)
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                '*.name' => 'required|string',
                '*.email' => 'required|email|unique:users,email',
                '*.type' => 'required|in:admin,general',
                '*.password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                '*.profile_picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ], [
                '*.password.regex' => 'The password must be at least 8 characters long and include at least one lowercase letter, one uppercase letter, one number, and one special character.',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::error('Validation error', 422);
            }

            $users = [];

            foreach ($validator->validated() as $userData) {
                // Check if user already exists by email
                if (User::where('email', $userData['email'])->exists()) {
                    continue; // Skip creating the user if already exists
                }

                // Handle profile picture upload
                $profilePicturePath = null;
                if (array_key_exists('profile_picture', $userData) && $request->file($userData['profile_picture'])) {
                    $profilePicturePath = $request->file($userData['profile_picture'])->store('profile_pictures');
                }

                // Create a new user
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'type' => $userData['type'],
                    'password' => Hash::make($userData['password']), // Hash the password
                    'profile_picture' => $profilePicturePath,
                ]);

                $users[] = $user;
            }
            return ResponseHelper::success('Users added successfully', $users, 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('An error occurred', 500);
        }
    }
}
