<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();
        return response()->json(['data' => $users], 200);
    }
    
    public function store(Request $request)
    {
        $newUser = $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $newUser['status'] = 1;
        $newUser['path'] = 'https://expertsbyab.blob.core.windows.net/expertspublic/lawyers/profiles/default_profile.png';
        $newUser['password'] = Hash::make($request->password);
        // Create the user in the other service
        $user = User::create($newUser);
        // $user = User::where('email',$user->email)->first();
        $role = Role::where('name','employee')->first();
        $user->roles()->attach($role->id);
        if ($user) {
            return response()->json(['message' => 'User created successfully', 'data' => $user], 200);
        } else {
            return response()->json(['message' => 'Failed to create user'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id',$id)->first();

        $userData = $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, // Ignore the unique constraint for the current user
        ]);
        if($request->filled('password')){
            $request->validate([
                    'password' => 'required|string|min:6',
                ]);
                $userData["password"] = $request->password;
            }
        $user->update($userData);

        

        return response()->json(['message' => 'User updated successfully', 'data' => $user], 200);
    }

    public function toggle(Request $request, $id)
    {
        $user = User::where('id',$id)->first();

        $userData = $request->validate([
            'status' => 'required|integer',
        ]);
        $user->update($userData);

        

        return response()->json(['message' => 'User updated successfully', 'data' => $user], 200);
    }

    
    public function destroy($id)
    {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
