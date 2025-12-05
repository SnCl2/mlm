<?php

namespace App\Http\Controllers;

use App\Models\IncomeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeSettingsController extends Controller
{
    /**
     * Display the income settings page
     */
    public function index()
    {
        $settings = IncomeSetting::orderBy('type')->orderBy('label')->get();
        
        return view('management.income_settings', compact('settings'));
    }

    /**
     * Update income settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->settings as $key => $value) {
                IncomeSetting::where('key', $key)->update([
                    'value' => $value,
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->back()->with('success', 'Income settings updated successfully!');
    }

    /**
     * Reset settings to default values
     */
    public function reset()
    {
        $defaults = [
            'referral_income_amount' => 300.00,
            'binary_matching_income' => 200.00,
            'points_per_activation' => 100.00,
            'upline_chain_levels' => 15.00,
            'points_per_match' => 100.00,
        ];

        DB::transaction(function () use ($defaults) {
            foreach ($defaults as $key => $value) {
                IncomeSetting::where('key', $key)->update([
                    'value' => $value,
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->back()->with('success', 'Settings reset to default values!');
    }
}

