<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;



class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::all();

        return response() -> json([
            'success' => true,
            'data' => $users
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request ->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'

        ]);

        if ($validator->fails()) {
            return response()-> json([
                'success' => false,
                'errors'=> $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response() -> json ([
            'success' => true,
            'message' => 'User Created Successfully',
            'data' => $user,

        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Basically displays the specified user

        $user = User::find($id);

        if (!$user) {
            return response() -> json([
                'success' => false,
                'message' => 'User not found'

            ], 404);
        };

        return response() -> json([
            'success' => true,
            'data' => $user,
            'message' => 'User created successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Update the specified user
        $user = User::find($id);

        if (!$user) {
            return response() -> json([
                'success' => false,
                'message' => 'Cannot find user'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8'
        ]);

        if (!$validator ->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        };

        if ($request->has('name')) {
            $user->name = $request->name;
        };

        if ($request-> has('email')){
            $user->email = $request->email;
        };

        if ($request -> has('password')){
            $user->password = Hash::make($request->password);
        };

        $user -> save();

        return response()->json([
            'success' => true,
            'message' => "User updated successfully",
            'data' => $user,

        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Remove specified user
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',

            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User Deleted Successfully',
        ], 200);

    }
}


