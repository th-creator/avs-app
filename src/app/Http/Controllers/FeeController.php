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
        $data = Fee::where('student_id',$id)->with('user')->get();
        return response()->json(['data' => $data], 200);
    }

    public function fetchFinance(Request $request) {
        $fromDate = $request->input('from');
        $toDate = $request->input('to');
        $data = Fee::whereBetween('date', [$fromDate, $toDate])->get();
        return response()->json(['data' => $data], 200);
    }

    public function undandledFees() {
        $data = Fee::where(function ($query) {
            $query->where('rest', '!=', 0)
                  ->orWhereNull('rest')
                  ->whereColumn('total', '!=', 'amount_paid');
        })->get();
        return response()->json(['data' => $data], 200);
    }

    public function store(Request $request)
    {
        $newData = $request->validate([
            'date' => 'nullable',
            'fullName' => 'required',
            'amount' => 'required',
            'reduction' => 'required',
            'rest' => 'required',
            'type' => 'nullable',
            'bank' => 'nullable',
            'total' => 'required',
            'amount_paid' => 'required',
            'bank_receipt' => 'nullable',
            'receipt' => 'nullable',
            'group' => 'nullable',
            'user_id' => 'required',
            'student_id' => 'required',
        ]);

        $data = Fee::create($newData);
        
        $data->student = $data->student;
        $data->user = $data->user;

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
            'amount' => 'required',
            'reduction' => 'nullable',
            'rest' => 'required',
            'total' => 'required',
            'amount_paid' => 'required',
            'type' => 'nullable',
            'bank' => 'nullable',
            'bank_receipt' => 'nullable',
            'receipt' => 'nullable',
        ]);
        
        $data->update($userData);

        $data->student = $data->student;
        $data->user = $data->user;
        
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
