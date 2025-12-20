<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Registrant;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
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
        $data = Group::with('teacher')->get();
        return response()->json(['data' => $data], 200);
    }

    public function groupPayments($month,$id,$year) {
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
        $currentDate = $year.'-'.$monthNumber.'-'.date('d');
        $data = Group::where('id',$id)->first();
        $price = $data->section->price;
        $data->payments = Payment::whereHas('registrant', function ($query) use ($currentDate) {
            $query->where('status', 1)->whereDate('enter_date', '<=', $currentDate);
        })->whereNot('paid',-1)->where('group_id',$data->id)
        ->where('month', $month)
        ->where('year', $year)->get();
        $registrants = Registrant::where('group_id',$id)->where('status', 1)->whereDate('enter_date', '<=', $currentDate)->get();
        foreach ($registrants as $registrant) {
            $payment = Payment::where('student_id', $registrant->student_id)
                                ->where('group_id', $data->id)
                                ->where('month', $month)
                                ->where('year', $year)
                                ->first();

            if (!$payment) {
                $pay = Payment::where('student_id',$registrant->student_id)->where('group_id',$data->id)->first();
                Log::alert($pay);
                if($pay && $pay->reduction) {
                    $reduction = $pay->reduction;
                    $total = $price*(100-$pay->reduction)/100;
                } else {
                    $reduction = 0;
                    $total = $price;
                }
                $data->payments[] = [
                    'amount' => $data->section->price,
                    'reduction' => $reduction,
                    'total' => $total,
                    'month' => $month,
                    'year' => $year
                ];
            }
        }
        return response()->json(['data' => $data], 200);
    }
    public function allPayments($month,$year) {
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
        $currentDate = $year.'-'.$monthNumber.'-'.date('d');
        $data = Group::with('teacher')->get();
        foreach ($data as $group) {
            $group->payments = Payment::whereHas('registrant', function ($query) use ($data,$currentDate) {
                $query->where('status', 1)->whereDate('enter_date', '<=', $currentDate);
            })->whereNot('paid',-1)->where('group_id',$group->id)
            ->where('month', $month)
            ->where('year', $year)->get();
            $registrants = Registrant::where('group_id',$group->id)->where('status', 1)->whereDate('enter_date', '<=', $currentDate)->forAY($this->ay)->get();
            foreach ($registrants as $registrant) {
                $payment = Payment::where('student_id', $registrant->student_id)
                                    ->where('group_id', $group->id)
                                    ->where('month', $month)
                                    ->where('year', $year)
                                    ->first();
    
                if (!$payment) {
                    $price = $group->section->price;
                    $pay = Payment::where('student_id',$registrant->student_id)->where('group_id',$group->id)->first();
                    if($pay && $pay->reduction) {
                        $reduction = $pay->reduction;
                        $total = $price*(100-$pay->reduction)/100;
                    } else {
                        $reduction = 0;
                        $total = $price;
                    }
                    $group->payments[] = [
                        'fullName' => $registrant->student['firstName'] . ' ' . $registrant->student['lastName'], 
                        'registrant_id' => $registrant->id,
                        'group' => $group->intitule,
                        'amount' => $price,
                        'reduction' => $reduction,
                        'rest' => '0',
                        'total' => $total,
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
        }
        return response()->json(['data' => $data], 200);
    }
    public function studentGroups($id) {
        $data = Group::whereHas('registrants', function ($query) use ($id) {
            $query->where('student_id', $id)->forAY($this->ay);
        })->with('section')->get();
        return response()->json(['data' => $data], 200);
    }
    
    public function store(Request $request)
    {
        $newData = $request->validate([
            'intitule' => 'nullable',
            'n_place' => 'nullable',
            'availability' => 'nullable',
            'timing' => 'nullable',
            'salle' => 'nullable',
            'teacher_id' => 'required',
            'section_id' => 'required',
            'user_id' => 'required',
        ]);

        $newData['date'] = now();
        if ($request['timing']) {
            $newData['timing'] = json_encode($request['timing']);
        }

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
        
        if ($request['timing']) {
            $newData['timing'] = json_encode($request['timing']);
        }
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