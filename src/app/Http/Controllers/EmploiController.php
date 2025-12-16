<?php

namespace App\Http\Controllers;

use App\Models\Emploi;
use App\Models\Group;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmploiController extends Controller
{
    // -----------------------------
    // CRUD
    // -----------------------------

    public function index(Request $request)
    {
        $request->validate([
            'subject' => 'nullable|string',
            'level' => 'nullable|string',
        ]);

        $query = Emploi::query();

        // Filter by subject if provided
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }

        // Filter by level if provided
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Fetch results (filtered or unfiltered)
        $emplois = $query->get();

        return response()->json($emplois);
    }


    public function show($id)
    {
        return Emploi::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'group_id' => 'required|integer|exists:groups,id',
            'salle' => 'required|string',
            'timing' => 'required|array',
            'type' => 'required|in:default,period',
            'from' => 'nullable|date',
            'to' => 'nullable|date'
        ]);

        if ($data['type'] === 'period' && (!$data['from'] || !$data['to'])) {
            return response()->json(['error' => 'From and To are required'], 422);
        }

        $group = Group::with(['teacher', 'section'])->findOrFail($data['group_id']);

        $emploi = Emploi::create([
            'group_id'     => $group->id,
            'group'        => $group->intitule,
            'teacher_name' => $group->teacher ? $group->teacher->firstName . ' ' . $group->teacher->lastName : null,
            'subject'      => $group->section?->subject,
            'level'        => $group->section?->level,
            'salle'        => $data['salle'],
            'timing'       => $data['timing'],
            'type'         => $data['type'],
            'from'         => $data['type'] === 'period' ? $data['from'] : null,
            'to'           => $data['type'] === 'period' ? $data['to'] : null,
        ]);

        return response()->json($emploi, 201);
    }


    public function update(Request $request, $id)
    {
        $emploi = Emploi::findOrFail($id);

        $data = $request->validate([
            'salle' => 'nullable|string',
            'timing' => 'nullable|array',
            'type' => 'nullable|in:default,period',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        if (($data['type'] ?? $emploi->type) === 'period') {
            if (empty($data['from']) && !$emploi->from) {
                return response()->json(['error' => 'From date required'], 422);
            }
            if (empty($data['to']) && !$emploi->to) {
                return response()->json(['error' => 'To date required'], 422);
            }
        }

        // prevent changing group-related fields
        unset($data['group_id'], $data['group'], $data['teacher_name'], $data['subject'], $data['level']);

        $emploi->update($data);

        return response()->json($emploi);
    }


    public function destroy($id)
    {
        $emploi = Emploi::findOrFail($id);
        $emploi->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    // -------------------------------------------------------
    // ACTIVE TIMETABLE LOGIC (DEFAULT + PERIOD OVERRIDE)
    // -------------------------------------------------------

    /**
     * Fetch active timetable for a given group for today's date.
     * Logic:
     *  - If a period emploi exists for today → return it
     *  - Otherwise return the default emploi
     */
    public function activeForGroup($groupId)
    {
        $today = Carbon::today();

        // 1. Fetch period emplois active today
        $periodEmplois = Emploi::where('group_id', $groupId)
            ->where('type', 'period')
            ->where('from', '<=', $today)
            ->where('to', '>=', $today)
            ->get();

        if ($periodEmplois->count() > 0) {
            return $periodEmplois; // overrides default
        }

        // 2. Otherwise return default emplois
        return Emploi::where('group_id', $groupId)
            ->where('type', 'default')
            ->get();
    }

    /**
     * Fetch the full timetable for a specific date.
     * Returns either default or period emplois depending on date.
     */
    public function timetableForDate(Request $request)
    {
        $date = Carbon::parse($request->date);

        $period = Emploi::where('type', 'period')
            ->where('from', '<=', $date)
            ->where('to', '>=', $date)
            ->get();

        if ($period->count() > 0) {
            return $period;
        }

        return Emploi::where('type', 'default')->get();
    }
}
