<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registrant;

class RegistrantController extends Controller
{
    public function index() {
        $data = Registrant::with('student')->get();
        return response()->json(['data' => $data], 200);
    }
    
    public function store(Request $request)
    {
        $newData = $request->validate([
            'date' => 'nullable',
            'center' => 'required',
            'user_id' => 'required',
            'student_id' => 'required',
            'group_id' => 'required',
        ]);

        $data = Registrant::create($newData);
        
        $data->student = $data->student;

        if ($data) {
            return response()->json(['message' => 'Registrant created successfully', 'data' => $data], 200);
        } else {
            return response()->json(['message' => 'Failed to create Registrant'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Registrant::where('id',$id)->first();

        $userData = $request->validate([
            'date' => 'nullable',
            'center' => 'required',
            'group_id' => 'required',
        ]);
        
        $data->update($userData);

        $data->student = $data->student;
        
        return response()->json(['message' => 'Registrant updated successfully', 'data' => $data], 200);
    }

    
    public function destroy($id)
    {
        $data = Registrant::where('id', $id)->first();

        if (!$data) {
            return response()->json(['message' => 'Registrant not found'], 404);
        }

        // Delete the data
        $data->delete();

        return response()->json(['message' => 'Registrant deleted successfully'], 200);
    }
}
