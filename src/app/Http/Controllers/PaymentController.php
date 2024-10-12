<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Payment;
use App\Models\Registrant;
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
        $monthsToGet = array_slice($months, $currentMonth -1, $currentMonth);
        Log::alert($monthsToGet);
        $data = Payment::whereNot('paid',-1)->where(function ($query) {
            $query->where('rest', '!=', 0)
                  ->orWhereNull('rest');
        })->whereIn('month', $monthsToGet)
        ->get();
        $registrants = Registrant::with('student')->get();
        $missingPayments = [];

        foreach ($registrants as $registrant) {
            $payments = Payment::where('registrant_id', $registrant->id)->whereIn('month', $monthsToGet)->get();
            $id = $registrant->id;
            // $group = Group::whereHas('registrants', function ($query) use ($id) {
            //     $query->where('id', $id);
            // })->with('section')->first();
            $paidMonths = $payments->pluck('month')->toArray();

            $missingMonths = array_diff($monthsToGet, $paidMonths);

            if (!empty($missingMonths)) {
                $missingPayments[] = [
                    'fullName' => $registrant->student['firstName'] . ' ' . $registrant->student['lastName'], 
                    'registrant_id' => $registrant->id,
                    // 'group' => $group->intitule,
                    // 'group_id' => $registrant->group_id,
                    'paid' => 0,
                    'missing_months' => $missingMonths
                ];
            }
        }
        Log::alert($missingPayments);
        return response()->json(['data' => $data], 200);
    }
    public function fetchFinance() {
        $currentMonth = date('n'); // Get the current month as a number (1-12)
        $currentYear = date('Y'); // Get the current year
        $yearsToGet = [];
        $months = ['Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet'];
        // Adjust the index for the academic year starting in August
        if ($currentMonth >= 8) {
            $currentMonth -= 8; // For Aug to Dec, subtract 8 to get index 0-4
            $yearsToGet = [$currentYear,$currentYear+1]; // For Jan to July, add 4 to get index 5-11
        } else {
            $currentMonth += 4; // For Jan to July, add 4 to get index 5-11
            $yearsToGet = [$currentYear-1,$currentYear]; // For Jan to July, add 4 to get index 5-11
        }
        $monthsToGet = array_slice($months, 0, $currentMonth);
        Log::alert($monthsToGet);
        $data = Payment::whereNotIn('paid',[-1,0])
        ->whereYear('date', $yearsToGet)
        ->selectRaw('MONTH(date) as monthDate, sum(amount_paid) as total')
        ->groupBy('monthDate')
        ->get();
        return response()->json(['data' => $data], 200);
    }

    public function all() {
        $data = Payment::get();
        return response()->json(['data' => $data], 200);
    }

    public function show($id) {
        $data = Payment::where('student_id',$id)->with('user')->get();
        return response()->json(['data' => $data], 200);
    }

    public function groupPayments($id) {
        $data = Payment::whereNot('paid',-1)->where('group_id',$id)->get();
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
            'amount_paid' => 'nullable',
            'month' => 'nullable',
            'year' => 'nullable',
            'type' => 'nullable',
            'bank' => 'nullable',
            'bank_receipt' => 'nullable',
            'receipt' => 'nullable',
            'group' => 'nullable',
            'user_id' => 'required',
            'student_id' => 'required',
            'group_id' => 'required',
        ]);
        $registrant = Registrant::where('group_id',$newData['group_id'])->where('student_id',$newData['student_id'])->first();
        Log::alert($newData['group_id']);

        $newData['paid'] = 1;
        $newData['registrant_id'] = $registrant['id'];

        $existingPayment = Payment::where('group_id', $newData['group_id'])
                                  ->where('registrant_id', $newData['registrant_id'])
                                  ->where('month', $newData['month'])
                                  ->first();

        if ($existingPayment) {
            return response()->json(['message' => 'Payment already exists'], 400);
        } else {
            $data = Payment::create($newData);
        }
        
        $data->user = $data->user;

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
            'amount_paid' => 'nullable',
            'type' => 'nullable',
            'bank' => 'nullable',
            'bank_receipt' => 'nullable',
            'receipt' => 'nullable',
        ]);
        $userData['paid'] = 1;
        $data->update($userData);
        $data->user = $data->user;
        
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
