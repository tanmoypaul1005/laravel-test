<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Import Hash facade
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
class UserController extends Controller

{
    // add user
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

    //get user
    public function getUserInfo($userId)
    {
        // Find the user by ID
        $user = User::find($userId);

        // Check if the user exists
        if (!$user) {
            return ResponseHelper::error('User not found', 404);
        }

        // Return user information without the password
        $userInfo = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'remember_token' => $user->remember_token,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];

        return response()->json($userInfo);
    }


    public function addUsersFromCsv(Request $request)
    {
        try {
            // Validate the incoming CSV file
            $validator = Validator::make($request->all(), [
                'csv_file' => 'required|file|mimes:csv,txt',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::error('Validation error', 422);
            }

            $csvFile = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($csvFile->getRealPath()));

            // Assuming the first row of the CSV contains column headers
            $headers = array_map('trim', $csvData[0]);

            // Validate CSV headers
            $headerValidator = Validator::make($headers, [
                'name', 'email', 'type', 'password', 'profile_picture',
            ]);

            if ($headerValidator->fails()) {
                return ResponseHelper::error('Invalid CSV format', 422);
            }

            // Remove headers from CSV data
            array_shift($csvData);

            $users = [];

            foreach ($csvData as $row) {
                $userData = array_combine($headers, $row);

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
            return ResponseHelper::success('Users added successfully from CSV', $user,200);
        } catch (\Exception $e) {
            return ResponseHelper::error('An error occurred', 500);
        }
    }

    // login user
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                // $token = $user->createToken('authToken')->accessToken;

                return response()->json(['message' => 'Login successful', 'user' => $user]);
            }

            return response()->json(['message' => 'Invalid credentials'], 401);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    //logout user
    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            return ResponseHelper::success('Logout successful', [],200);
        } catch (\Exception $e) {
            return ResponseHelper::error('An error occurred', 500);
        }
    }

    //filter user
    public function filterUsers(Request $request)
    {
        try {
            $query = User::query();

            // Sorting
            if ($request->has('sort_by')) {
                $sortField = $request->input('sort_by');
                $sortDirection = $request->input('sort_direction', 'asc');

                $query->orderBy($sortField, $sortDirection);
            }

            // Filtering
            if ($request->has('filter')) {
                $filter = $request->input('filter');
                $query->where(function ($q) use ($filter) {
                    $q->where('name', 'like', "%$filter%")
                      ->orWhere('email', 'like', "%$filter%")
                      ->orWhere('type', 'like', "%$filter%");
                });
            }

            $users = $query->get();
            return ResponseHelper::success('An error occUsers filtered successfully', $users,200);
        } catch (\Exception $e) {
            return ResponseHelper::error('An error occurred', 500);
        }
    }



}

