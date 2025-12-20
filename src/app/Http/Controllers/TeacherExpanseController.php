<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeacherExpanse;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Registrant;
use Illuminate\Support\Facades\Log;

class TeacherExpanseController extends Controller
{   
    protected ?string $ay; // nullable in case month=8

    public function __construct()
    {
        $this->ay = request('ay') 
            ?? (now()->month >= 9 
                ? now()->year.'/'.(now()->year+1) 
                : (now()->year-1).'/'.now()->year);
    }
    public function index() {
        $data = TeacherExpanse::with('user')->get();
        return response()->json(['data' => $data], 200);
    }

    public function alls($month,$year) {
        $data = TeacherExpanse::where('month', $month)
                                ->where('year', $year)
                                ->with('user')->get();
        Log::alert($data);
        return response()->json(['data' => $data], 200);
    }
    public function all($month, $year)
{
    /* ================================
     * 1️⃣ Fetch teacher_expanses (UNCHANGED)
     * ================================ */
    $data = TeacherExpanse::where('month', $month)
        ->where('year', $year)
        ->with('user')
        ->get();

    // Extract group names already paid
    $paidGroups = $data->pluck('group')->filter()->unique()->values();

    /* ================================
     * 2️⃣ ORIGINAL allPayments LOGIC
     * ================================ */
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
    $currentDate = $year . '-' . $monthNumber . '-' . date('d');

    $groups = Group::with('teacher')->get();

    foreach ($groups as $group) {
        $group->payments = Payment::whereHas('registrant', function ($query) use ($currentDate) {
                $query->where('status', 1)
                      ->whereDate('enter_date', '<=', $currentDate);
            })
            ->whereNot('paid', -1)
            ->where('group_id', $group->id)
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $registrants = Registrant::where('group_id', $group->id)
            ->where('status', 1)
            ->whereDate('enter_date', '<=', $currentDate)
            ->forAY($this->ay)
            ->get();

        foreach ($registrants as $registrant) {
            $payment = Payment::where('student_id', $registrant->student_id)
                ->where('group_id', $group->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if (!$payment) {
                $price = $group->section->price;
                $pay = Payment::where('student_id', $registrant->student_id)
                    ->where('group_id', $group->id)
                    ->first();

                if ($pay && $pay->reduction) {
                    $reduction = $pay->reduction;
                    $total = $price * (100 - $pay->reduction) / 100;
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
                    'student_id' => $registrant->student['id'],
                    'month' => $month,
                    'year' => $year
                ];
            }
        }
    }

    /* ================================
     * 3️⃣ EXCLUDE PAID GROUPS (SAFE STEP)
     * ================================ */
    $groups = $groups->filter(function ($group) use ($paidGroups) {
        return !$paidGroups->contains($group->intitule);
    })->values();

    /* ================================
     * 4️⃣ Return BOTH datasets
     * ================================ */
    return response()->json([
        'data'   => $data,    // teacher_expanses (paid / approved)
        'groups' => $groups   // unpaid groups with ORIGINAL logic
    ], 200);
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
