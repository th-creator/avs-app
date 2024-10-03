<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File; 

class ProfileController extends Controller
{
    public function editInfos(Request $request, $id)
    {
        $user = User::where('id',$id)->first();

        $userData = $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, // Ignore the unique constraint for the current user
        ]);
        $user->update($userData);

        return response()->json(['message' => 'User updated successfully', 'data' => $user], 200);
    }

    public function editProfilePicture(Request $request,$id)
    {
            $data=$request->validate([
                'image'=>'required|image'
            ]);
        try{
            
        if($request->hasFile('image')) {
            $path = $request->file('image')->store('profile', 'public');
            
            $url = config('services.app.url');
            $urlFile = Storage::url($path);

            $imageName = $url.$urlFile;
        }            
            $user = User::where('id',$id)->get()->first();
            $fileUrl = $user->path;
            
            $filename = basename($fileUrl);
            $filePath = 'profile/' . $filename;
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            
            $user->update([
                'path'=>$imageName
            ]);
            return response()->json(['message'=>'profile picture has been updated','data'=>$imageName], 200);
        } catch (\Exception $e) {
            Log::error('Error in querying all roles: ' . $e->getMessage());
            return response()->json(['error' => 'Could not update profile picture: '.$e], 500);
        }
    }

    public function changePassword(Request $request)
    {
        
        try {
            $request->validate([
                'oldPassword' => 'required',
                'password' => 'required|confirmed|string|min:6'
            ]);
            $user = User::find($request->id);
            // Log::alert($request->id);

            // Check if the old password matches the current password
            if(!Hash::check($request->oldPassword, $user->password)) {
                return response()->json("The old password is incorrect.",422);
            }
            // Update the password
                $user->password = Hash::make($request->password);
                $user->save();
            return response()->json(['message' => 'Account password changed'], 200);
        } catch (\Exception $e) {
            Log::error('Error in updating password: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
