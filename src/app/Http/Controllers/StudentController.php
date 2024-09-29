<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index() {
        $data = Student::with('user')->get();
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
            'parent_phone' => 'nullable|min:10',
            'field' => 'nullable',
            'level' => 'nullable',
            'school' => 'nullable',
            'user_id' => 'required',
        ]);

        $data = Student::create($newData);

        $data->user = $data->user;
        
        if ($data) {
            return response()->json(['message' => 'Student created successfully', 'data' => $data], 200);
        } else {
            return response()->json(['message' => 'Failed to create Student'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Student::where('id',$id)->first();

        $userData = $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'nullable|string|email|max:255',
            'date' => 'nullable',
            'phone' => 'nullable|min:10',
            'parent_phone' => 'nullable|min:10',
            'field' => 'nullable',
            'level' => 'nullable',
            'school' => 'nullable',
        ]);
        
        $data->update($userData);

        $data->user = $data->user;
        return response()->json(['message' => 'Student updated successfully', 'data' => $data], 200);
    }

    public function show($id) {
        $data = Student::where('id',$id)->get()->first();
        return response()->json(['data' => $data], 200);
    }
    
    public function destroy($id)
    {
        $data = Student::where('id', $id)->first();

        if (!$data) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        // Delete the data
        $data->delete();

        return response()->json(['message' => 'Student deleted successfully'], 200);
    }
}
