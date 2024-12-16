<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeacherExpanse;
use Illuminate\Support\Facades\Log;

class TeacherExpanseController extends Controller
{   
    public function index() {
        $data = TeacherExpanse::with('user')->get();
        return response()->json(['data' => $data], 200);
    }
    public function all($month,$year) {
        $data = TeacherExpanse::where('month', $month)
                                ->where('year', $year)
                                ->with('user')->get();
        Log::alert($data);
        return response()->json(['data' => $data], 200);
    }

    public function fetchFinance(Request $request) {
        $fromDate = $request->input('from');
        $toDate = $request->input('to');
        $data = TeacherExpanse::whereBetween('date', [$fromDate, $toDate])->get();
        return response()->json(['data' => $data], 200);
    }

    public function store(Request $request)
    {
        $newData = $request->validate([
            'date' => 'nullable',
            'teacher' => 'required',
            'group' => 'required',
            'amount' => 'required',
            'rest' => 'nullable',
            'total' => 'required',
            'percentage' => 'nullable',
            'month' => 'required',
            'year' => 'required',
            'user_id' => 'required',
            'teacher_id' => 'required',
        ]);
        $data = TeacherExpanse::create($newData);
        
        $data->user = $data->user;

        if ($data) {
            return response()->json(['message' => 'TeacherExpanse created successfully', 'data' => $data], 200);
        } else {
            return response()->json(['message' => 'Failed to create TeacherExpanse'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = TeacherExpanse::where('id',$id)->first();

        $userData = $request->validate([
            'date' => 'nullable',
            'amount' => 'required',
            'rest' => 'nullable',
            'total' => 'required',
            'percentage' => 'nullable',
            'month' => 'required',
            'year' => 'required',
        ]);
        
        $data->update($userData);

        $data->user = $data->user;
        
        return response()->json(['message' => 'TeacherExpanse updated successfully', 'data' => $data], 200);
    }


    public function destroy($id)
    {
        $data = TeacherExpanse::where('id', $id)->first();

        if (!$data) {
            return response()->json(['message' => 'TeacherExpanse not found'], 404);
        }

        $data->delete();

        return response()->json(['message' => 'TeacherExpanse deleted successfully'], 200);
    }
}
