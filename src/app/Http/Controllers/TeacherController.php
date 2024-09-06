<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;

class TeacherController extends Controller
{
    public function index() {
        $data = Teacher::get();
        return response()->json(['data' => $data], 200);
    }
    
    public function store(Request $request)
    {
        $newData = $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'nullable|string|email|max:255',
            'date' => 'nullable',
            'phone' => 'nullable|min:10',
            'subject' => 'nullable',
            'user_id' => 'required',
        ]);

        $data = Teacher::create($newData);
        
        if ($data) {
            return response()->json(['message' => 'teacher created successfully', 'data' => $data], 200);
        } else {
            return response()->json(['message' => 'Failed to create teacher'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Teacher::where('id',$id)->first();

        $userData = $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'nullable|string|email|max:255',
            'date' => 'nullable',
            'phone' => 'nullable|min:10',
            'subject' => 'nullable',
        ]);
        
        $data->update($userData);

        return response()->json(['message' => 'teacher updated successfully', 'data' => $data], 200);
    }

    
    public function destroy($id)
    {
        $data = Teacher::where('id', $id)->first();

        if (!$data) {
            return response()->json(['message' => 'teacher not found'], 404);
        }

        $data->delete();

        return response()->json(['message' => 'teacher deleted successfully'], 200);
    }
}
