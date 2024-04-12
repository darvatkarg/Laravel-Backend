<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Newuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class NewUserController extends Controller
{
    //Registration API
    function addUser(Request $req)
    {
        try {

            $req->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => ['required', 'email'],
                'password' => ['required', 'min:8'],
                'image' => ['required', 'image'], // Add validation for image
            ]);

            $user = new Newuser;
            $user->first_name = $req->first_name;
            $user->last_name = $req->last_name;
            $user->email = $req->email;
            $user->password = Hash::make($req->password);

            if ($req->hasFile('image')) {
                //To store image name as it is
                // $path = $req->file('image')->store('images', 'public');
                // $user->image_path = $path;

                //To give a custom name to the image
                $file = $req->file('image');
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $timestamp = now()->timestamp;
                $newFilename = "{$filename}-{$timestamp}-{$user->first_name}.{$file->getClientOriginalExtension()}";
                // Move the file to the desired directory
                $file->move(public_path('storage/images'), $newFilename);

                // Save the path (relative to the public directory) in the database
                $user->image_path = 'storage/images/' . $newFilename;
            }

            $user->save();

            return response()->json([
                'message' => 'User registered successfully',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //Code while using token from sanctum

    // Login Function
    function loginUser(Request $req)
    {
        $user = Newuser::where('email', $req->email)->first();
        if (!$user || !Hash::check($req->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 400);
        } else {
            // Create a token for the user
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'User found',
                'data' => $user,
                'token' => $token
            ], 200);
        }
    }

    // Find User Function
    function findUser(Request $req, $id)
    {
        try {
            // $id = $req->user()->id;
            $user = Newuser::find($id, ['id', 'first_name', 'last_name', 'email', 'image_path']);

            if (!$user) {
                return response()->json([
                    'message' => "User not found",
                ], 400);
            } else {

                //Code to show image
                if ($user->image_path) {
                    $user->image_path = url('/' . $user->image_path);
                }

                return response()->json([
                    'message' => "User found",
                    'data' => $user
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    function logout(Request $req)
    {
        try {
            $req->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //FindAllUsers API
    function findAllUsers()
    {
        try {
            $users = Newuser::all(['id', 'first_name', 'last_name', 'email', 'image_path']);

            if ($users->count() > 0) {
                foreach ($users as $user) {
                    if ($user->image_path) {
                        $user->image_path = url('/' . $user->image_path);
                    }
                }
                return response()->json([
                    'message' => "Users found",
                    'data' => $users
                ], 200);
            } else {
                return response()->json([
                    'message' => "No users found",
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Find User using token
    function findUserByToken(Request $req)
    {
        try {
            $id = $req->user()->id;
            $user = Newuser::find($id, ['first_name', 'last_name', 'image_path']);

            if (!$user) {
                return response()->json([
                    'message' => "User not found",
                ], 400);
            } else {
                if ($user->image_path) {
                    $user->image_path = url('/' . $user->image_path);
                }
                return response()->json([
                    'message' => "User found",
                    'data' => $user
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    //Delete API
    function deleteUser(Request $req, $id)
    {
        try {
            // $id = $req->user()->id;
            $user = Newuser::find($id);

            if (!$user) {
                return response()->json([
                    'message' => "User not found",
                ], 400);
            } else {

                // Delete the user's tokens
                // $user->tokens()->delete();

                // Delete the user
                $user->delete();

                return response()->json([
                    'message' => "User deleted successfully",
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    //Update API
    function updateUser(Request $req, $id)
    {
        try {

            // $id = $req->user()->id;

            $user = Newuser::find($id);

            if ($req->has('first_name')) {
                $user->first_name = $req->first_name;
            }
            if ($req->has('last_name')) {
                $user->last_name = $req->last_name;
            }
            if ($req->has('email')) {
                $req->validate([
                    'email' => ['email'],
                ]);
                $user->email = $req->email;
            }
            // Handle the image upload
            if ($req->hasFile('image')) {
                $req->validate([
                    'image' => ['image'], // Add validation for image
                ]);

                $file = $req->file('image');
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $timestamp = now()->timestamp;
                $newFilename = "{$filename}-{$timestamp}-{$user->first_name}.{$file->getClientOriginalExtension()}";
                $path = $file->storeAs('images', $newFilename, 'public');
                $user->image_path = $path;
            }

            $user->save();
            return response()->json([
                'data' => $user,
                'message' => "User has been updated"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
