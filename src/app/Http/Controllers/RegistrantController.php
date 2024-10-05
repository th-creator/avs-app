<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Payment;
use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegistrantController extends Controller
{
    public function index() {
        $data = Registrant::with('student')->with('group')->orderBy('student_id', 'desc')->get();
        return response()->json(['data' => $data], 200);
    }

    public function show($id) {
        $data = Group::whereHas('registrants', function ($query) use ($id) {
            $query->where('student_id', $id);
        })->with('teacher')->with('section')->get();
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
        $newRegistrants = [];
        foreach($newData['group_id'] as $group_id) {
            $newData['group_id'] = $group_id;
            $newData['status'] = 1;
            $data = Registrant::create($newData);
            $data->student = $data->student;
            $group = Group::where('id',$newData['group_id'])->with('section')->get()->first();
            $currentYear = date('Y');
            $months = ['Septembre', 'Octobre', 'Novembre', 'Décembre', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'];
            $currentMonth = date('n'); // Get current month as a number (1-12)
            // Adjust the index for the academic year starting in September
            $flag = false;
            if ($currentMonth >= 9) {
                $flag = true;
                
                $currentMonth -= 9; // For Sept to Dec, subtract 9 to get index 0-3
            } else {
                $currentMonth += 3; // For Jan to June, add 3 to get index 4-9
            }
            
            for ($i = $currentMonth; $i < count($months); $i++) { // Loop until June
                if ($i > 3 && $flag == true) {
                    $monthName = $months[$i];
                    $yearName = $currentYear+1;
                } else {
                    $yearName = $currentYear;
                    $monthName = $months[$i];
                }
                Payment::create([
                    'group' => $group['intitule'],
                    'month' => $monthName,
                    'year' => $yearName,
                    'amount' => $group['section']['price'],
                    'fullName' => $data['student']['firstName']. ' ' . $data['student']['lastName'],
                    'user_id' => $newData['user_id'],
                    'student_id' => $newData['student_id'],
                    'group_id' => $group['id'],
                    'registrant_id' => $data['id']
                ]);
            }
    
            $group->availability = $group->availability - 1;
            $group->save();
            $data->group = $data->group;
            array_push($newRegistrants, $data);
            if (!$data) {
                return response()->json(['message' => 'Failed to create Registrant'], 500);
            }
        }
        return response()->json(['message' => 'Registrant created successfully', 'data' => $data], 200);
        // $newData['status'] = 1;
        // $data = Registrant::create($newData);
        // $data->student = $data->student;
        // $group = Group::where('id',$newData['group_id'])->with('section')->get()->first();
        // $currentYear = date('Y');
        // $months = ['Septembre', 'Octobre', 'Novembre', 'Décembre', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'];
        // $currentMonth = date('n'); // Get current month as a number (1-12)
        // // Adjust the index for the academic year starting in September
        // $flag = false;
        // if ($currentMonth >= 9) {
        //     $flag = true;
            
        //     $currentMonth -= 9; // For Sept to Dec, subtract 9 to get index 0-3
        // } else {
        //     $currentMonth += 3; // For Jan to June, add 3 to get index 4-9
        // }
        
        // for ($i = $currentMonth; $i < count($months); $i++) { // Loop until June
        //     if ($i > 3 && $flag == true) {
        //         $monthName = $months[$i];
        //         $yearName = $currentYear+1;
        //     } else {
        //         $yearName = $currentYear;
        //         $monthName = $months[$i];
        //     }
        //     Payment::create([
        //         'group' => $group['intitule'],
        //         'month' => $monthName,
        //         'year' => $yearName,
        //         'amount' => $group['section']['price'],
        //         'fullName' => $data['student']['firstName']. ' ' . $data['student']['lastName'],
        //         'user_id' => $newData['user_id'],
        //         'student_id' => $newData['student_id'],
        //         'group_id' => $group['id'],
        //         'registrant_id' => $data['id']
        //     ]);
        // }

        // $group->availability = $group->availability - 1;
        // $group->save();
        // $data->group = $data->group;
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
        ]);
        
        $data->update($userData);

        $data->student = $data->student;
        $data->group = $data->group;
        
        return response()->json(['message' => 'Registrant updated successfully', 'data' => $data], 200);
    }

    public function transfer(Request $request, $id)
    {
        $data = Registrant::where('id',$id)->first();

        $userData = $request->validate([
            'group_id' => 'required',
            'user_id' => 'required',
        ]);
        

        $oldGroup = Group::where('id',$data['group_id'])->with('section')->get()->first();
        $group = Group::where('id',$userData['group_id'])->with('section')->get()->first();
        $currentYear = date('Y');
        $months = ['Septembre', 'Octobre', 'Novembre', 'Décembre', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'];
        $currentMonth = date('n'); // Get current month as a number (1-12)
        // Adjust the index for the academic year starting in September
        $flag = false;
        if ($currentMonth >= 9) {
            $flag = true;
            
            $currentMonth -= 9; // For Sept to Dec, subtract 9 to get index 0-3
        } else {
            $currentMonth += 3; // For Jan to June, add 3 to get index 4-9
        }
        for ($i = $currentMonth; $i < count($months); $i++) { // Loop until June
            if ($i > 3 && $flag == true) {
                $monthName = $months[$i];
                $yearName = $currentYear+1;
            } else {
                $yearName = $currentYear;
                $monthName = $months[$i];
            }
            Log::alert('monthsToAdd'.$monthName);
            Payment::create([
                'group' => $group['intitule'],
                'month' => $monthName,
                'year' => $yearName,
                'amount' => $group['section']['price'],
                'fullName' => $data['student']['firstName']. ' ' . $data['student']['lastName'],
                'user_id' => $userData['user_id'],
                'student_id' => $data['student_id'],
                'group_id' => $group['id'],
                'registrant_id' => $data['id']
            ]);
        }

        $monthName = $months[$currentMonth];
        $currentYear;
        $oldPayment = Payment::where('group_id',$data->group_id)->where('registrant_id', $data->id)->where('month', $monthName)->where('year', $currentYear)->first();
        $newPayment = Payment::where('group_id',$group->id)->where('registrant_id', $data->id)->where('month', $monthName)->where('year', $currentYear)->first();
        Log::alert('monthsToDelete'.$monthName);
        Log::alert('monthsToDelete'.$currentYear);
        Log::alert($oldPayment);
        Log::alert($newPayment);
        $reduction = isset($oldPayment->reduction) ? $oldPayment->reduction : null;
        $rest = isset($oldPayment->rest) ? $oldPayment->rest : null;
        $total = isset($oldPayment->total) ? $oldPayment->total : null;
        $amount_paid = isset($oldPayment->amount_paid) ? $oldPayment->amount_paid : null;
        $type = isset($oldPayment->type) ? $oldPayment->type : null;
        $bank = isset($oldPayment->bank) ? $oldPayment->bank : null;
        $bank_receipt = isset($oldPayment->bank_receipt) ? $oldPayment->bank_receipt : null;
        $receipt = isset($oldPayment->receipt) ? $oldPayment->receipt : null;

        $newPayment = $newPayment->update([
            'reduction' => $reduction,
            'rest' => $rest,
            'total' => $total,
            'amount_paid' => $amount_paid,
            'type' => $type,
            'bank' => $bank,
            'bank_receipt' => $bank_receipt,
            'receipt' => $receipt,
        ]);
        $newPayment = $newPayment->update([
            'reduction' => $oldPayment->reduction,
            'rest' => $oldPayment->rest,
            'total' => $oldPayment->total,
            'amount_paid' => $oldPayment->amount_paid,
            'type' => $oldPayment->type,
            'bank' => $oldPayment->bank,
            'bank_receipt' => $oldPayment->bank_receipt,
            'receipt' => $oldPayment->receipt,
        ]);
        // if ($currentMonth == count($months)) {
        //     return;
        // }
        $monthsToDelete = array_slice($months, $currentMonth); // Get the months to delete
        Log::alert($monthsToDelete);

        Payment::where('registrant_id', $id)
        ->where('group_id', $data->group_id)
        ->where('registrant_id', $data->id)
        ->whereIn('month', $monthsToDelete)
        ->where('year', '>=', $currentYear)
        ->delete();
        // ->orWhere('year', '>=', $currentYear +1)
        $data->update($userData);

        $oldGroup->availability = $oldGroup->availability + 1;
        $group->availability = $group->availability - 1;
        $oldGroup->save();
        $group->save();
        $data->student = $data->student;
        $data->group = $data->group;
        
        return response()->json(['message' => 'Registrant updated successfully', 'data' => $data], 200);
    }

    public function groupRegistrants($id) {
        $data = Registrant::where('group_id',$id)->with('student')->with('payments')->get();
        return response()->json(['data' => $data], 200);
    }

    public function toggle(Request $request, $id)
    {
        $registrant = Registrant::where('id',$id)->first();

        $registrantData = $request->validate([
            'status' => 'required|integer',
            'user_id' => 'required',
        ]);
        $registrant->update($registrantData);

        $registrant->student = $registrant->student;
        $registrant->group = $registrant->group;
        if($request->status == 0) {
            $currentMonth = date('n'); // Get the current month as a number (1-12)
            $currentYear = date('Y'); // Get the current year
            $months = ['Septembre', 'Octobre', 'Novembre', 'Décembre', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'];

            // Adjust the index for the academic year starting in September
            if ($currentMonth >= 9) {
                $currentMonth -= 9; // For Sept to Dec, subtract 9 to get index 0-3
            } else {
                $currentMonth += 3; // For Jan to June, add 3 to get index 4-9
            }
            if ($currentMonth == count($months) - 1) {
                return;
            }
            $monthsToDelete = array_slice($months, $currentMonth + 1); // Get the months to delete

            Payment::where('registrant_id', $id)
                ->where('group_id', $registrant->group_id)
                ->where('registrant_id', $registrant->id)
                ->whereIn('month', $monthsToDelete)
                ->where('year', '>=', $currentYear)
                ->delete();
        } else {
            $group = Group::where('id',$registrant['group_id'])->with('section')->get()->first();
            $currentYear = date('Y');
            $months = ['Septembre', 'Octobre', 'Novembre', 'Décembre', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'];
            $currentMonth = date('n'); // Get current month as a number (1-12)
            // Adjust the index for the academic year starting in September
            $flag = false;
            if ($currentMonth >= 9) {
                $flag = true;
                
                $currentMonth -= 9; // For Sept to Dec, subtract 9 to get index 0-3
            } else {
                $currentMonth += 3; // For Jan to June, add 3 to get index 4-9
            }
            
            for ($i = $currentMonth; $i < count($months); $i++) { // Loop until June
                if ($i > 3 && $flag == true) {
                    $monthName = $months[$i];
                    $yearName = $currentYear+1;
                } else {
                    $yearName = $currentYear;
                    $monthName = $months[$i];
                }
                Payment::create([
                    'group' => $group['intitule'],
                    'month' => $monthName,
                    'year' => $yearName,
                    'amount' => $group['section']['price'],
                    'fullName' => $registrant['student']['firstName']. ' ' . $registrant['student']['lastName'],
                    'user_id' => $registrantData['user_id'],
                    'student_id' => $registrant['student_id'],
                    'group_id' => $group['id'],
                    'registrant_id' => $registrant['id']
                ]);
            }
        }

        return response()->json(['message' => 'registrant updated successfully', 'data' => $registrant], 200);
    }
    
    public function destroy($id)
    {
        $data = Registrant::where('id', $id)->first();

        if (!$data) {
            return response()->json(['message' => 'Registrant not found'], 404);
        }
        $group = Group::where('id', $data->group_id)->first();
        $group->availability = $group->availability + 1;
        $group->save();
        // Delete the data
        $data->delete();

        return response()->json(['message' => 'Registrant deleted successfully'], 200);
    }
}
