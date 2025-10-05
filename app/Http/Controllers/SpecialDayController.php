<?php

namespace App\Http\Controllers;

use App\Models\SpecialDay;
use Illuminate\Http\Request;

class SpecialDayController extends Controller
{
    public function index()
    {
        // Get all approved special days
        $specialDays = SpecialDay::all();

        return response()->json($specialDays);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'repeats' => 'boolean',
        ]);

        $specialDay = SpecialDay::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'repeats' => $validated['repeats']
        ]);

        return response()->json([
            'message' => 'Īpašā diena apstipināta veiksmīgi.',
            'special_day' => $specialDay
        ], 201);
    }

    public function userSpecialDays()
    {
        // Get current user's special days
        $specialDays = SpecialDay::where('user_id', auth()->id())
            ->orderBy('date')
            ->get();

        return response()->json($specialDays);
    }
}