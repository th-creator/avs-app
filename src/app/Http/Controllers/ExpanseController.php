<?php

namespace App\Http\Controllers;

use App\Models\Expanse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Storage;

class ExpanseController extends Controller
{   
    public function index() {
        $data = Expanse::with('user')->get();
        return response()->json(['data' => $data], 200);
    }

    public function fetchFinance(Request $request) {
        $fromDate = $request->input('from');
        $toDate = $request->input('to');
        $data = Expanse::whereBetween('date', [$fromDate, $toDate])->get();
        return response()->json(['data' => $data], 200);
    }

    public function store(Request $request)
    {
        $newData = $request->validate([
            'date' => 'nullable',
            'title' => 'required',
            'amount' => 'required',
            'type' => 'nullable',
            'bank' => 'nullable',
            'bank_receipt' => 'nullable',
            'paid_by' => 'nullable',
            'user_id' => 'required',
        ]);
        if($request->hasFile('file')) {
            $path = $request->file('file')->store('files', 'public');
            
            $url = config('services.app.url');
            $urlFile = Storage::url($path);

            $newData['file'] = $url.$urlFile;
        }
        $data = Expanse::create($newData);
        
        $data->user = $data->user;

        if ($data) {
            return response()->json(['message' => 'Expanse created successfully', 'data' => $data], 200);
        } else {
            return response()->json(['message' => 'Failed to create Expanse'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Expanse::where('id',$id)->first();

        $userData = $request->validate([
            'date' => 'nullable',
            'amount' => 'required',
            'title' => 'required',
            'type' => 'nullable',
            'bank' => 'nullable',
            'bank_receipt' => 'nullable',
            'paid_by' => 'nullable',
        ]);
        
        $data->update($userData);

        $data->user = $data->user;
        
        return response()->json(['message' => 'Expanse updated successfully', 'data' => $data], 200);
    }


    public function destroy($id)
    {
        $data = Expanse::where('id', $id)->first();

        if (!$data) {
            return response()->json(['message' => 'Expanse not found'], 404);
        }

        $fileUrl = $data->file;
        
        $filename = basename($fileUrl);
        $filePath = 'files/' . $filename;
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        $data->delete();

        return response()->json(['message' => 'Expanse deleted successfully'], 200);
    }
}
