<?php

namespace App\Http\Controllers;

use App\Models\CommissionLevel;
use Illuminate\Http\Request;

class CommissionLevelController extends Controller
{
    public function index()
    {
        $levels = CommissionLevel::orderBy('level')->get();
        return view('commission_levels.index', compact('levels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'level' => 'required|integer|unique:commission_levels,level',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        CommissionLevel::create($request->only('level', 'percentage'));

        return redirect()->back()->with('success', 'Commission level added successfully.');
    }

    public function update(Request $request, CommissionLevel $commissionLevel)
    {
        $request->validate([
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $commissionLevel->update([
            'percentage' => $request->percentage,
        ]);

        return redirect()->back()->with('success', 'Commission percentage updated.');
    }

    public function destroy(CommissionLevel $commissionLevel)
    {
        $commissionLevel->delete();

        return redirect()->back()->with('success', 'Commission level deleted.');
    }
}
