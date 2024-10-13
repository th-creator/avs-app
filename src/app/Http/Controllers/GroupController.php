<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;

class GroupController extends Controller
{
    public function index() {
        $data = Group::with('teacher')->get();
        return response()->json(['data' => $data], 200);
    }

    public function allPayments($month) {
        $data = Group::with(['teacher', 'payments' => function ($query) use ($month) {
            $query->where('month', $month);
        }])->get();
        return response()->json(['data' => $data], 200);
    }
    public function studentGroups($id) {
        $data = Group::whereHas('registrants', function ($query) use ($id) {
            $query->where('student_id', $id);
        })->with('section')->get();
        return response()->json(['data' => $data], 200);
    }
    
    public function store(Request $request)
    {
        $newData = $request->validate([
            'intitule' => 'nullable',
            'n_place' => 'nullable',
            'availability' => 'nullable',
            'timing' => 'required',
            'salle' => 'nullable',
            'teacher_id' => 'required',
            'section_id' => 'required',
            'user_id' => 'required',
        ]);

        $newData['date'] = now();
        $newData['timing'] = json_encode($request['timing']);

        $data = Group::create($newData);

        $data->teacher = $data->teacher;
        
        if ($data) {
            return response()->json(['message' => 'Group created successfully', 'data' => $data], 200);
        } else {
            return response()->json(['message' => 'Failed to create Group'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Group::where('id',$id)->first();

        $newData = $request->validate([
            'intitule' => 'nullable',
            'n_place' => 'nullable',
            'availability' => 'nullable',
            'timing' => 'nullable',
            'salle' => 'nullable',
            'teacher_id' => 'required',
            'section_id' => 'required',
        ]);
        
        $newData['timing'] = json_encode($request['timing']);
        $data->update($newData);

        $data->teacher = $data->teacher;
        
        return response()->json(['message' => 'Group updated successfully', 'data' => $data], 200);
    }

    public function show($id) {
        $data = Group::where('id',$id)->first();
        return response()->json(['data' => $data], 200);
    }

    
    public function destroy($id)
    {
        $data = Group::where('id', $id)->first();

        if (!$data) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $data->delete();

        return response()->json(['message' => 'Group deleted successfully'], 200);
    }
}