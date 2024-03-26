<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Newuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

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
            ]);

            $user = new Newuser;
            $user->first_name = $req->first_name;
            $user->last_name = $req->last_name;
            $user->email = $req->email;
            $user->password = Hash::make($req->password);
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

            // Set a cookie with the token
            $cookie = cookie('token', $token, 60);
            // Below cookie will expire in 60 minutes, is secure and httpOnly
            // $cookie = cookie('token', $token, 60, null, null, false, false);

            return response()->json([
                'message' => 'User found',
                'data' => $user,
                'token' => $token
            ], 200)->withCookie($cookie);
        }
    }

    // Find User Function
    function findUser(Request $req)
    {
        // Get the user's ID from the token
        $id = $req->user()->id;

        $user = Newuser::find($id, ['id', 'first_name', 'last_name', 'email']);
        if (!$user) {
            return response()->json([
                'message' => "User not found",
            ], 400);
        } else {
            return response()->json([
                'message' => "User found",
                'data' => $user
            ], 200);
        }
    }

    // Logout Function
    function logout(Request $req)
    {
        try {
            // Revoke the user's token
            $req->user()->currentAccessToken()->delete();

            // Clear the cookie
            $cookie = Cookie::forget('token');

            return response()->json([
                'message' => 'Logged out successfully',
            ], 200)->withCookie($cookie);
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
            $users = Newuser::all(['id', 'first_name', 'last_name', 'email']);

            if ($users->count() > 0) {
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

    //Delete API
    function deleteUser(Request $req)
    {
        try {
            // Get the authenticated user
            $user = $req->user();

            // Delete the user
            $user->delete();

            return response()->json([
                'message' => "User deleted successfully!"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    //Update API
    function updateUser(Request $req)
    {
        try {
            // Get the authenticated user
            $user = $req->user();

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
