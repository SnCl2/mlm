<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\UserKyc;

class CheckUserKyc
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If not a logged-in user, continue as usual
        if (!$user) {
            return $next($request);
        }

        // If already KYC verified, allow access
        if ($user->is_kyc_verified) {
            return $next($request);
        }

        // If KYC record exists, redirect to edit page
        if (UserKyc::where('user_id', $user->id)->exists()) {
            return redirect()->route('kyc.edit');
            
        }

        // If KYC record does not exist, redirect to create page
        return redirect()->route('kyc.create');
    }
}
