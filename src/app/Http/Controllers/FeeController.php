<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fee;

class FeeController extends Controller
{
    public function index() {
        $data = Fee::with('student')->get();
        return response()->json(['data' => $data], 200);
    }

    public function show($id) {
        $data = Fee::where('student_id',$id)->with('student')->get();
        return response()->json(['data' => $data], 200);
    }

    public function store(Request $request)
    {
        $newData = $request->validate([
            'date' => 'nullable',
            'fullName' => 'required',
            'amount' => 'required',
            'reduction' => 'required',
            'rest' => 'nullable',
            'type' => 'nullable',
            'bank' => 'nullable',
            'receipt' => 'nullable',
            'group' => 'nullable',
            'user_id' => 'required',
            'student_id' => 'required',
        ]);

        $data = Fee::create($newData);
        
        $data->student = $data->student;

        if ($data) {
            return response()->json(['message' => 'Fee created successfully', 'data' => $data], 200);
        } else {
            return response()->json(['message' => 'Failed to create Fee'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Fee::where('id',$id)->first();

        $userData = $request->validate([
            'date' => 'nullable',
            'fullName' => 'required',
            'amount' => 'required',
            'reduction' => 'nullable',
            'rest' => 'required',
            'type' => 'nullable',
            'bank' => 'nullable',
            'receipt' => 'nullable',
        ]);
        
        $data->update($userData);

        $data->student = $data->student;
        
        return response()->json(['message' => 'Fee updated successfully', 'data' => $data], 200);
    }


    public function destroy($id)
    {
        $data = Fee::where('id', $id)->first();

        if (!$data) {
            return response()->json(['message' => 'Fee not found'], 404);
        }

        // Delete the data
        $data->delete();

        return response()->json(['message' => 'Fee deleted successfully'], 200);
    }
}
