<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expanse;

class ExpanseController extends Controller
{   
    public function index() {
        $data = Expanse::with('user')->get();
        return response()->json(['data' => $data], 200);
    }

    public function store(Request $request)
    {
        $newData = $request->validate([
            'date' => 'nullable',
            'title' => 'required',
            'amount' => 'required',
            'user_id' => 'required',
        ]);

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

        // Delete the data
        $data->delete();

        return response()->json(['message' => 'Expanse deleted successfully'], 200);
    }
}
