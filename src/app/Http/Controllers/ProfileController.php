<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Storage;

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
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('profiles'), $filename);
    
                $url = config('services.app.url');
                $imageName = $url . '/profiles/' . $filename;
            }            
            $user = User::where('id',$id)->first();
            $fileUrl = $user->path;
            
            $filename = basename($fileUrl);
            $filePath = public_path('profiles/' . $filename);
            if (File::exists($filePath)) {
                File::delete($filePath);
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
