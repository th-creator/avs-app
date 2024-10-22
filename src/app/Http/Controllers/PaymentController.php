<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Payment;
use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function unhandledPayments($month,$year) {
        $months = [
            'Janvier' => 1,
            'Février' => 2,
            'Mars' => 3,
            'Avril' => 4,
            'Mai' => 5,
            'Juin' => 6,
            'Juillet' => 7,
            'Août' => 8,
            'Septembre' => 9,
            'Octobre' => 10,
            'Novembre' => 11,
            'Décembre' => 12,
        ];
        $monthNumber = $months[$month];
        $currentDate = $year.'-'.$$monthNumber.'-01';
        $data = Payment::whereNot('paid',-1)->where(function ($query) {
            $query->where('rest', '!=', 0)
                  ->orWhereNull('rest');
        })->where('month', $month)
        ->whereHas('registrant', function ($query) use ($currentDate) {
            $query->where('status', 1)->whereDate('enter_date', '<=', $currentDate);
        })
        ->get();
        $registrants = Registrant::where('status', 1)->whereDate('enter_date', '<=', $currentDate)->get();
        foreach ($registrants as $registrant) {
            $missingMonths = [];
            $payment = Payment::where('registrant_id', $registrant->id)
                                ->where('group_id', $registrant->group->id)
                                ->where('month', $month)
                                ->where('year', $year)
                                ->first();

            if (!$payment) {
                $data[] = [
                    'fullName' => $registrant->student['firstName'] . ' ' . $registrant->student['lastName'], 
                    'registrant_id' => $registrant->id,
                    'group' => $registrant->group->intitule,
                    'amount' => 0,
                    'reduction' => '0',
                    'rest' => '0',
                    'total' => '0',
                    'amount_paid' => '0',
                    'paid' => 0,
                    'type' => null,
                    'receipt' => null,
                    'user_id' => null,
                    'student_id' =>  $registrant->student['id'],
                    'paid' => 0,
                    'month' => $month,
                    'year' => $year
                ];
            }
        }
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

    public function groupPayments($id,$month,$year) {
        $currentDate = date('Y-m-d');
        $data = Payment::whereHas('registrant', function ($query) use ($id,$currentDate) {
            $query->where('status', 1)->whereDate('enter_date', '<=', $currentDate);
        })->whereNot('paid',-1)->where('group_id',$id)->get();
        $group = Group::find($id);

        foreach ($group->registrants as $registrant) {
            $payment = Payment::where('registrant_id', $registrant->id)
                                ->where('group_id', $id)
                                ->where('month', $month)
                                ->where('year', $year)
                                ->first();

            if (!$payment) {
                $data[] = [
                    'fullName' => $registrant->student['firstName'] . ' ' . $registrant->student['lastName'], 
                    'registrant_id' => $registrant->id,
                    'group' => $group->intitule,
                    'amount' => $group->section->price,
                    'reduction' => '0',
                    'rest' => '0',
                    'total' => $group->section->price,
                    'amount_paid' => '0',
                    'paid' => 0,
                    'type' => null,
                    'receipt' => null,
                    'user_id' => null,
                    'student_id' =>  $registrant->student['id'],
                    'paid' => 0,
                    'month' => $month,
                    'year' => $year
                ];
            }
        }
        return response()->json(['data' => $data], 200);
    }

    public function fetchFinance(Request $request) {
        $fromDate = $request->input('from');
        $toDate = $request->input('to');
        $data = Payment::whereBetween('date', [$fromDate, $toDate])->get();
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
        $group = group::where('id',$newData['group_id'])->first();

        $newData['group'] = $group['intitule'];
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
