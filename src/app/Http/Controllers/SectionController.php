<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;

class SectionController extends Controller
{
    public function index() {
        $data = Section::get();
        return response()->json(['data' => $data], 200);
    }
    
    public function store(Request $request)
    {
        $newData = $request->validate([
            'subject' => 'required',
            'level' => 'required',
            'price' => 'required',
            'user_id' => 'required',
        ]);

        $data = Section::create($newData);
        
        if ($data) {
            return response()->json(['message' => 'Section created successfully', 'data' => $data], 200);
        } else {
            return response()->json(['message' => 'Failed to create Section'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Section::where('id',$id)->first();

        $userData = $request->validate([
            'subject' => 'required',
            'level' => 'required',
            'price' => 'required',
        ]);
        
        $data->update($userData);

        return response()->json(['message' => 'Section updated successfully', 'data' => $data], 200);
    }

    
    public function destroy($id)
    {
        $data = Section::where('id', $id)->first();

        if (!$data) {
            return response()->json(['message' => 'Section not found'], 404);
        }

        $data->delete();

        return response()->json(['message' => 'Section deleted successfully'], 200);
    }
}
