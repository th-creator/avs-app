<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index() {
        $currentMonth = date('n'); // Get the current month as a number (1-12)
        $currentYear = date('Y'); // Get the current year
        $months = ['Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet'];
        // Adjust the index for the academic year starting in August
        if ($currentMonth >= 8) {
            $currentMonth -= 8; // For Aug to Dec, subtract 8 to get index 0-4
        } else {
            $currentMonth += 4; // For Jan to July, add 4 to get index 5-11
        }
        $monthsToGet = array_slice($months, 0, $currentMonth + 1);
        Log::alert($monthsToGet);
        $data = Payment::where(function ($query) {
            $query->where('rest', '!=', 0)
                  ->orWhereNull('rest');
        })->whereIn('month', $monthsToGet)
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
            'total' => 'required',
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
            'total' => 'required',
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
