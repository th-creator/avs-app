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
    public function all($from,$to) {
        $data = Expanse::whereBetween('date', [$from, $to])->with('user')->get();
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
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('files'), $filename);

            $url = config('services.app.url');
            $newData['file'] = $url . '/files/' . $filename;
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

        
        if($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('files'), $filename);

            $url = config('services.app.url');
            $filesName = $url . '/files/' . $filename;
            $fileUrl = $data->file;
            if(!empty($fileUrl)) {
                $filename = basename($fileUrl);
                $filePath = public_path('files/' . $filename);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }
            $data->update([
                'file'=>$filesName
            ]);
        }            

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
        $filePath = public_path('files/' . $filename);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
        $data->delete();

        return response()->json(['message' => 'Expanse deleted successfully'], 200);
    }
}
