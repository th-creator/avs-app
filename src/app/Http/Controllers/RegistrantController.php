<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Payment;
use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RegistrantController extends Controller
{
    protected string $ay;

    public function __construct()
    {
        $this->ay = request('ay') 
            ?? (now()->month >= 9 
                ? now()->year.'/'.(now()->year+1) 
                : (now()->year-1).'/'.now()->year);
    }
    
    public function index() {
        $data = Registrant::with('student')
        ->forAY($this->ay) ->with('group','user')->orderBy('student_id', 'desc')->get();
        return response()->json(['data' => $data], 200);
    }
    public function doubled() {
        $data = Registrant::with('student')->with('group')->with('user')
            ->select('student_id', 'group_id')
            ->groupBy('student_id', 'group_id')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('student_id', 'desc')
            ->get();
        return response()->json(['data' => $data], 200);
    }

    public function show($id) {
        $data = Group::whereHas('registrants', function ($query) use ($id) {
            $query->where('student_id', $id);
        })->with('teacher')->with('section')->get();
        return response()->json(['data' => $data], 200);
    }
    
    // public function store(Request $request)
    // {
    //     $newData = $request->validate([
    //         'date' => 'required',
    //         'enter_date' => 'nullable',
    //         'center' => 'required',
    //         'user_id' => 'required',
    //         'student_id' => 'required',
    //         'group_id' => 'required',
    //     ]);
    //     $newRegistrants = [];
    //     foreach($newData['group_id'] as $group_id) {
    //         $newData['group_id'] = $group_id;
    //         $newData['status'] = 1;
    //         $existingRegistrant = Registrant::where('group_id', $newData['group_id'])
    //                                         ->where('student_id', $newData['student_id'])
    //                                         ->first();

    //         if ($existingRegistrant) {
    //             return response()->json(['message' => 'Registrant already exists'], 400);
    //         } else {
    //             $data = Registrant::create($newData);
    //         }
    //         $data->student = $data->student;
    //         $group = Group::where('id',$newData['group_id'])->with('section')->get()->first();
    
    //         $group->availability = $group->availability - 1;
    //         $group->save();
    //         $data->group = $data->group;
    //         $data->user = $data->user;
    //         array_push($newRegistrants, $data);
    //         if (!$data) {
    //             return response()->json(['message' => 'Failed to create Registrant'], 500);
    //         }
    //     }
    //     return response()->json(['message' => 'Registrant created successfully', 'data' => $data], 200);
    // }

    public function store(Request $request)
    {
        $newData = $request->validate([
            'date' => 'required',
            'enter_date' => 'nullable',
            'center' => 'required',
            'user_id' => 'required',
            'student_id' => 'required',
            'group_id' => 'required',
        ]);

        $newRegistrants = [];
        foreach ($newData['group_id'] as $group_id) {
            $newData['group_id'] = $group_id;
            $newData['status'] = 1;

            // -------- AY number allocation (reuse or create) --------
            $enter = $newData['enter_date'] ?? $newData['date'] ?? now()->toDateString();
            $dt = \Carbon\Carbon::parse($enter);
            $y  = (int) $dt->year;
            $m  = (int) $dt->month;
            $ay = $m >= 9 ? "{$y}/" . ($y + 1) : ($y - 1) . "/{$y}";

            // Try to reuse existing mapping (id + num)
            $mapping = \Illuminate\Support\Facades\DB::table('student_ay_numbers')
                ->select('id', 'num')
                ->where('ay', $ay)
                ->where('student_id', $newData['student_id'])
                ->first();

            $mappingId = $mapping->id  ?? null;
            $ayNum     = $mapping->num ?? null;

            if (!$mappingId) {
                // Allocate a new sequential number for this AY (race-safe)
                \Illuminate\Support\Facades\DB::transaction(function () use ($ay, $newData, &$mappingId, &$ayNum) {
                    $max = \Illuminate\Support\Facades\DB::table('student_ay_numbers')
                        ->where('ay', $ay)
                        ->lockForUpdate()
                        ->max('num');

                    $ayNum = (int) ($max ?? 0) + 1;

                    $mappingId = \Illuminate\Support\Facades\DB::table('student_ay_numbers')->insertGetId([
                        'ay'         => $ay,
                        'student_id' => $newData['student_id'],
                        'num'        => $ayNum,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });
            }

            // Denormalize onto registrant for zero-join reads
            $newData['ay_no'] = $ayNum;
            // Link to mapping row so DB can enforce per-AY uniqueness
            $newData['student_ay_number_id'] = $mappingId;
            // -------------------- END NEW --------------------

            // Duplicate check must be per AY (group + student_ay_number_id)
            $existingRegistrant = Registrant::where('group_id', $newData['group_id'])
                ->where('student_ay_number_id', $newData['student_ay_number_id'])->forAY($this->ay)
                ->first();

            if ($existingRegistrant) {
                return response()->json(['message' => 'Registrant already exists'], 400);
            } else {
                $data = Registrant::create($newData);
            }

            $data->student = $data->student;
            $group = Group::where('id', $newData['group_id'])->with('section')->first();

            $group->availability = $group->availability - 1;
            $group->save();

            $data->group = $data->group;
            $data->user  = $data->user;

            $newRegistrants[] = $data;

            if (!$data) {
                return response()->json(['message' => 'Failed to create Registrant'], 500);
            }
        }

        return response()->json(['message' => 'Registrant created successfully', 'data' => $data], 200);
    }



    public function update(Request $request, $id)
    {
        $data = Registrant::where('id',$id)->first();

        $userData = $request->validate([
            'date' => 'nullable',
            'enter_date' => 'required',
            'center' => 'required',
            'status' => 'required',
        ]);
        // if($request->status == -1) {
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
            // $monthsToDelete = array_slice($months, $currentMonth + 1); // Get the months to delete

            // Payment::where('registrant_id', $id)
            //     ->where('group_id', $data->group_id)
            //     ->whereIn('month', $monthsToDelete)
            //     ->where('year', '>=', $currentYear)
            //     ->delete();

            $monthName = $months[$currentMonth];
            $payment = Payment::where('group_id',$data->group_id)->where('registrant_id', $data->id)->where('month', $monthName)->where('year', $currentYear)->first();
        // }
        $data->update($userData);

        $data->student = $data->student;
        $data->group = $data->group;
        $data->user = $data->user;
        
        return response()->json(['message' => 'Registrant updated successfully', 'data' => $data, 'payment'=>$payment], 200);
    }
    public function refund(Request $request, $id)
    {
        $data = Registrant::where('id',$id)->first();

        $validateData = $request->validate([
            'amount' => 'required',
            'date_refund' => 'required',
        ]);
        if($request->status == -1) {
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
            // $monthsToDelete = array_slice($months, $currentMonth + 1); // Get the months to delete

            // Payment::where('registrant_id', $id)
            //     ->where('group_id', $data->group_id)
            //     ->whereIn('month', $monthsToDelete)
            //     ->where('year', '>=', $currentYear)
            //     ->delete();

            $monthName = $months[$currentMonth];
            $payment = Payment::where('group_id',$data->group_id)->where('registrant_id', $data->id)->where('month', $monthName)->where('year', $currentYear)->first();
            $payment->paid = -2;
            $payment->amount_paid = $payment->amount_paid - $request->amount;
            $payment->save();
            Payment::create([
                'date' => $request->date_refund,
                'fullName' => $payment->fullName,
                'amount' => $payment->amount,
                'reduction' => $payment->reduction,
                'rest' => 0,
                'total' => $payment->total,
                'amount_paid' => $request->amount,
                'month' => $payment->month,
                'year' => $payment->year,
                'type' => $payment->type,
                'bank' => $payment->bank,
                'bank_receipt' => $payment->bank_receipt,
                'receipt' => $payment->receipt,
                'group' => $payment->group,
                'user_id' => $payment->user_id,
                'student_id' => $payment->student_id,
                'registrant_id' => $payment->registrant_id,
                'group_id' => $payment->group_id,
                'paid' => -1,
            ]);
        }
        
        return response()->json(['message' => 'Registrant updated successfully'], 200);
    }

    public function transfer(Request $request, $id)
    {
        $data = Registrant::where('id',$id)->first();

        $userData = $request->validate([
            'group_id' => 'required',
            'user_id' => 'required',
        ]);
        $existingRegistrant = Registrant::where('group_id', $userData['group_id'])
                                            ->where('student_id', $data['student_id'])
                                            ->first();

        if ($existingRegistrant) {
            return response()->json(['message' => 'Registrant already exists'], 400);
        }

        $oldGroup = Group::where('id',$data['group_id'])->with('section')->get()->first();
        $group = Group::where('id',$userData['group_id'])->with('section')->get()->first();
        $currentYear = date('Y');
        $months = ['Septembre', 'Octobre', 'Novembre', 'Décembre', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'];
        $currentMonth = date('n'); // Get current month as a number (1-12)
        // // Adjust the index for the academic year starting in September
        // $flag = false;
        if ($currentMonth >= 9) {            
            $currentMonth -= 9; // For Sept to Dec, subtract 9 to get index 0-3
        } else {
            $currentMonth += 3; // For Jan to June, add 3 to get index 4-9
        }
        $monthsToDelete = array_slice($months, $currentMonth); // Get the months to delete
        Log::alert($monthsToDelete);

        Payment::where('registrant_id', $data->id)
        ->where('group_id', $oldGroup->id)
        ->whereIn('month', $monthsToDelete)
        ->where('year', '>=', $currentYear)
        ->update(['group_id'=> $group['id'],'group'=>$group['intitule'],'enter_date'=> date('Y-m-d')]);
        // ->orWhere('year', '>=', $currentYear +1)
        $data->update($userData);

        $oldGroup->availability = $oldGroup->availability + 1;
        $group->availability = $group->availability - 1;
        $oldGroup->save();
        $group->save();
        $data->student = $data->student;
        $data->group = $data->group;
        $data->user = $data->user;
        
        return response()->json(['message' => 'Registrant updated successfully', 'data' => $data], 200);
    }

    public function groupRegistrants($id) {
        $currentDate = date('Y-m-d');
        $data = Registrant::where('status',1)
                          ->where('group_id',$id)
                          ->whereDate('date', '<=', $currentDate)
                          ->forAY($this->ay) 
                          ->with('student')
                          ->with('payments')
                          ->get();
        return response()->json(['data' => $data], 200);
    }

    public function toggle(Request $request, $id)
    {
        $registrant = Registrant::where('id',$id)->first();

        $registrantData = $request->validate([
            'status' => 'required|integer',
            'user_id' => 'required',
        ]);
        
        if($request->status == 0) {
            
        } else if($request->status == 1) {
            $registrantData['enter_date'] = date('Y-m-d');
        }
        $registrant->update($registrantData);

        $registrant->student = $registrant->student;
        $registrant->group = $registrant->group;
        $registrant->user = $registrant->user;

        return response()->json(['message' => 'registrant updated successfully', 'data' => $registrant], 200);
    }
    
    public function destroy($id)
    {
        $data = Registrant::where('id', $id)->first();
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
        $monthsToDelete = array_slice($months, $currentMonth); // Get the months to delete

        Payment::where('registrant_id', $id)
            ->where('group_id', $data->group_id)
            ->whereIn('month', $monthsToDelete)
            ->where('year', '>=', $currentYear)
            ->delete();
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
