<?php

namespace App\Http\Controllers;

use App\Models\UserKyc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserKycController extends Controller
{
    public function create()
    {
        return view('kyc.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'profile_image'         => 'required|image|max:2048',
            'pan_card_image'        => 'required|image|max:2048',
            'aadhar_card_image'     => 'required|image|max:2048',
            'alternate_phone'       => 'required|string|max:20',
            'bank_account_number'   => 'required|string',
            'ifsc_code'             => 'required|string',
            'upi_id'                => 'nullable|string',
            'aadhar_number'         => 'required|string',
            'pan_card'              => 'required|string',
            'bank_name'             => 'required|string',
            'country'               => 'required|string',
            'state'                 => 'required|string',
            'city'                  => 'required|string',
            'pincode'               => 'required|string',
            'address'               => 'required|string',
        ]);

        $kyc = new UserKyc();
        $kyc->user_id = Auth::id();

        $kyc->profile_image      = $request->file('profile_image')->store('kyc/profile', 'public');
        $kyc->pan_card_image     = $request->file('pan_card_image')->store('kyc/pan', 'public');
        $kyc->aadhar_card_image  = $request->file('aadhar_card_image')->store('kyc/aadhar', 'public');

        $kyc->alternate_phone       = $request->alternate_phone;
        $kyc->bank_account_number   = $request->bank_account_number;
        $kyc->ifsc_code             = $request->ifsc_code;
        $kyc->upi_id                = $request->upi_id;

        $kyc->aadhar_number         = $request->aadhar_number;
        $kyc->pan_card              = $request->pan_card;
        $kyc->bank_name             = $request->bank_name;
        $kyc->country               = $request->country;
        $kyc->state                 = $request->state;
        $kyc->city                  = $request->city;
        $kyc->pincode               = $request->pincode;
        $kyc->address               = $request->address;

        $kyc->status = 'pending';

        $kyc->save();

        return redirect()->back()->with('success', 'KYC submitted successfully.');
    }

    public function edit()
    {
        $kyc = UserKyc::where('user_id', Auth::id())->firstOrFail();
        return view('kyc.edit', compact('kyc'));
    }

    public function update(Request $request)
    {
        $kyc = UserKyc::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'profile_image'         => 'nullable|image|max:2048',
            'pan_card_image'        => 'nullable|image|max:2048',
            'aadhar_card_image'     => 'nullable|image|max:2048',
            'alternate_phone'       => 'required|string|max:20',
            'bank_account_number'   => 'required|string',
            'ifsc_code'             => 'required|string',
            'upi_id'                => 'nullable|string',
            'aadhar_number'         => 'required|string',
            'pan_card'              => 'required|string',
            'bank_name'             => 'required|string',
            'country'               => 'required|string',
            'state'                 => 'required|string',
            'city'                  => 'required|string',
            'pincode'               => 'required|string',
            'address'               => 'required|string',
        ]);

        if ($request->hasFile('profile_image')) {
            Storage::disk('public')->delete($kyc->profile_image);
            $kyc->profile_image = $request->file('profile_image')->store('kyc/profile', 'public');
        }
        if ($request->hasFile('pan_card_image')) {
            Storage::disk('public')->delete($kyc->pan_card_image);
            $kyc->pan_card_image = $request->file('pan_card_image')->store('kyc/pan', 'public');
        }
        if ($request->hasFile('aadhar_card_image')) {
            Storage::disk('public')->delete($kyc->aadhar_card_image);
            $kyc->aadhar_card_image = $request->file('aadhar_card_image')->store('kyc/aadhar', 'public');
        }

        $kyc->alternate_phone       = $request->alternate_phone;
        $kyc->bank_account_number   = $request->bank_account_number;
        $kyc->ifsc_code             = $request->ifsc_code;
        $kyc->upi_id                = $request->upi_id;

        $kyc->aadhar_number         = $request->aadhar_number;
        $kyc->pan_card              = $request->pan_card;
        $kyc->bank_name             = $request->bank_name;
        $kyc->country               = $request->country;
        $kyc->state                 = $request->state;
        $kyc->city                  = $request->city;
        $kyc->pincode               = $request->pincode;
        $kyc->address               = $request->address;

        $kyc->save();

        return redirect()->back()->with('success', 'KYC updated successfully.');
    }
}
