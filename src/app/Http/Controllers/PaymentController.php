<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index() {
        $data = Payment::where(function ($query) {
            $query->where('rest', '!=', 0)
                  ->orWhereNull('rest');
        })
        ->get();
        return response()->json(['data' => $data], 200);
    }

    public function show($id) {
        $data = Payment::where('student_id',$id)->with('student')->get();
        return response()->json(['data' => $data], 200);
    }

    public function groupPayments($id) {
        $data = Payment::where('group_id',$id)->get();
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
            'month' => 'nullable',
            'type' => 'nullable',
            'bank' => 'nullable',
            'bank_receipt' => 'nullable',
            'receipt' => 'nullable',
            'group' => 'nullable',
            'user_id' => 'required',
            'student_id' => 'required',
            'group_id' => 'required',
            'registrant_id' => 'required',
        ]);

        $data = Payment::create($newData);
        
        $data->student = $data->student;

        if ($data) {
            return response()->json(['message' => 'Payment created successfully', 'data' => $data], 200);
        } else {
            return response()->json(['message' => 'Failed to create Payment'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Payment::where('id',$id)->first();

        $userData = $request->validate([
            'date' => 'nullable',
            'amount' => 'required',
            'reduction' => 'nullable',
            'rest' => 'required',
            'type' => 'nullable',
            'bank' => 'nullable',
            'bank_receipt' => 'nullable',
            'receipt' => 'nullable',
        ]);
        $userData['paid'] = 1;
        $data->update($userData);
        
        return response()->json(['message' => 'Payment updated successfully', 'data' => $data], 200);
    }

    
    public function destroy($id)
    {
        $data = Payment::where('id', $id)->first();

        if (!$data) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Delete the data
        $data->delete();

        return response()->json(['message' => 'Payment deleted successfully'], 200);
    }
}
