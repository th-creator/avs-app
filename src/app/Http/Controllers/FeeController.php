<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Fee;
use App\Models\Student;
use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeeController extends Controller
{
    protected string $ay;

    public function __construct()
    {
        $this->ay = request('ay') 
            ?? (now()->month >= 8
                ? now()->year.'/'.(now()->year+1) 
                : (now()->year-1).'/'.now()->year);
    }
    public function index() {
        $data = Fee::with('student')->get();
        return response()->json(['data' => $data], 200);
    }

    public function show($id) {
        $data = Fee::where('student_id',$id)->forAY($this->ay)->with('user')->get();
        return response()->json(['data' => $data], 200);
    }

    public function fetchFinance(Request $request) {
        $fromDate = $request->input('from');
        $toDate = $request->input('to');
        $data = Fee::whereBetween('date', [$fromDate, $toDate])->get();
        return response()->json(['data' => $data], 200);
    }
    
    public function undandledFees()
{
    // ay like "2025/2026"
    [$ayStart, $ayEnd] = array_map('intval', explode('/', $this->ay));

    // AY window: Sep 1 (start) -> Jun 30 (end)
    $from = \Carbon\Carbon::create($ayStart, 8, 1)->startOfDay()->toDateString();   // YYYY-08-01
    $to   = \Carbon\Carbon::create($ayEnd,   6, 30)->endOfDay()->toDateString();     // (Y+1)-06-30

    // 1) Existing unpaid fees within the AY window
    // unpaid = rest is null OR rest != 0 OR amount_paid < total
    $activeStudentIds = Registrant::forAY($this->ay)
        ->where('status', 1)
        ->distinct()
        ->pluck('student_id');
    $existingUnpaid = Fee::whereBetween('date', [$from, $to])
        ->where(function ($q) {
            $q->whereNull('rest')
            ->orWhere('rest', '!=', 0);
        })
        ->whereIn('student_id', $activeStudentIds)
        ->with('student')
        ->get();

    // 2) Students who belong to this AY (at least one active registrant in AY)
    $studentIdsInAY = Registrant::forAY($this->ay)
        ->where('status', 1)
        ->pluck('student_id')
        ->unique();

    // 3) Students who already have ANY fee in the AY window
    $studentIdsWithFeeInAY = Fee::whereBetween('date', [$from, $to])
        ->pluck('student_id')
        ->unique();

    // 4) Students in AY who have NO fee yet -> should pay inscription fee this AY
    $missingIds = $studentIdsInAY->diff($studentIdsWithFeeInAY);

    // 5) Build placeholders for missing
    $missingStudents = Student::whereIn('id', $missingIds)->get();
    $placeholders = $missingStudents->map(function ($s) {
        return [
            'fullName'     => trim(($s->firstName ?? '') . ' ' . ($s->lastName ?? '')),
            'amount'       => 0,
            'reduction'    => '0',
            'rest'         => '0',
            'total'        => '0',
            'amount_paid'  => '0',
            'type'         => null,
            'receipt'      => null,
            'user_id'      => null,
            'student_id'   => $s->id,
            'date'         => null,   // no fee created yet
            'parent_id' => null
        ];
    });

    // 6) Merge unpaid + placeholders
    $data = $existingUnpaid->toBase()->merge($placeholders)->values();

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

        $existingPayment = Fee::where('student_id', $newData['student_id'])->where('date', $newData['date'])
                                  ->first();

        if ($existingPayment) {
            return response()->json(['message' => 'fee already exists'], 400);
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

    public function followUpStore(Request $request)
    {
        $validated = $request->validate([
            'parent_id'     => 'required|exists:fees,id',
            'date'          => 'nullable|date',
            'fullName'      => 'required|string',
            'amount'        => 'required|numeric',
            'reduction'     => 'required|numeric',
            'rest'          => 'required|numeric',
            'total'         => 'required|numeric',
            'amount_paid'   => 'nullable|numeric',
            'type'          => 'required|string',
            'bank'          => 'nullable|string',
            'bank_receipt'  => 'nullable|string',
            'receipt'       => 'nullable|string',
            'user_id'       => 'required|integer',
            'student_id'    => 'required|integer',
        ]);

        // 1) Get the parent fee
        $parent = Fee::findOrFail($validated['parent_id']);

        // 2) Set new fee as "handled"
        $validated['status'] = 1;

        // 3) Create the follow-up entry
        $childFee = Fee::create($validated);

        // 4) Update parent's remaining amount (same logic as payments)
        $remaining = max(0, $parent->rest - $validated['amount']);
        $parent->update(['rest' => $remaining]);

        // 5) Eager load relations for consistency
        $childFee->student = $childFee->student;
        $childFee->user = $childFee->user;

        return response()->json([
            'message' => 'Follow-up fee created successfully',
            'data' => $childFee,
        ], 201);
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
