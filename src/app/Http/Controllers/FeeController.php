<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $studentsWithoutFees = Student::whereDoesntHave('fees')->get();

        $data = Fee::where(function ($query) {
            $query->where('rest', '!=', 0)
                  ->orWhereNull('rest')
                  ->whereColumn('total', '!=', 'amount_paid');
        })->get();
        foreach($studentsWithoutFees as $student) {
            $data[] = [
                'fullName' => $student['firstName'] . ' ' . $student['lastName'],
                'amount' => 0,
                'reduction' => '0',
                'rest' => '0',
                'total' => '0',
                'amount_paid' => '0',
                'type' => null,
                'receipt' => null,
                'user_id' => null,
                'student_id' => $student['id'],
            ];
        }
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
            'type' => 'required',
            'bank' => 'nullable',
            'total' => 'required',
            'amount_paid' => 'required',
            'bank_receipt' => 'nullable',
            'receipt' => 'nullable',
            'user_id' => 'required',
            'student_id' => 'required',
        ]);

        $existingPayment = Fee::where('student_id', $newData['student_id'])
                                  ->first();

        if ($existingPayment) {
            return response()->json(['message' => 'Payment already exists'], 400);
        } else {
            $data = Fee::create($newData);
        }
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
