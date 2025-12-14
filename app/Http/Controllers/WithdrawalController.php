<?php

namespace App\Http\Controllers;
use App\Models\MainWallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * User requests a withdrawal from main wallet
     */
    public function request(Request $request)
    {
        $user = Auth::user();
    
        // Validate inputs
        $request->validate([
            'amount' => 'required|numeric|min:500',
            'note'   => 'nullable|string|max:255',
        ]);
    
        $amount = $request->amount;
    
        if ($amount < 500) {
            return back()->with('error', 'Minimum ₹500 required to request withdrawal.');
        }
    
        // Use database transaction to ensure atomicity
        // Note: lockForUpdate() only locks during the transaction, allowing multiple sequential requests
        DB::beginTransaction();
        try {
            // Lock the main wallet row for update to prevent race conditions during this transaction
            // The lock is released after commit, allowing the next request to proceed
            $mainWallet = MainWallet::where('user_id', $user->id)->lockForUpdate()->first();
            
            if (!$mainWallet) {
                $mainWallet = MainWallet::create(['user_id' => $user->id, 'balance' => 0]);
            }
            
            // Calculate available balance WITHIN transaction using fresh data
            // Available = Main Wallet Balance - Sum of ALL pending withdrawals
            $mainBalance = $mainWallet->balance;
            $pendingWithdrawn = Withdrawal::where('user_id', $user->id)
                ->where('status', 'pending')
                ->sum('total_amount');
            $availableBalance = $mainBalance - $pendingWithdrawn;
            
            // Validate against fresh available balance
            if ($amount > $availableBalance) {
                DB::rollBack();
                return back()->with('error', 'Insufficient balance. Available: ₹' . number_format($availableBalance, 2));
            }
            
            if ($mainBalance < $amount) {
                throw new \Exception('Insufficient Main Wallet balance.');
            }
            
            // Deduct from main wallet
            $mainWallet->balance -= $amount;
            $mainWallet->save();
    
            // Record the withdrawal as "pending"
            Withdrawal::create([
                'user_id'      => $user->id,
                'total_amount' => $amount,
                'status'       => 'pending',
                'note'         => $request->note,
            ]);
    
            DB::commit();
            return back()->with('success', 'Withdrawal request submitted and amount deducted from main wallet.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }



    /**
     * Admin view of all withdrawal requests
     */
    public function index(Request $request)
    {
        $query = Withdrawal::with('user');
    
        // Search by user name or referral code
        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('referral_code', 'LIKE', "%$search%");
            });
        }
    
        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
    
        // Date range filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->input('from_date').' 00:00:00',
                $request->input('to_date').' 23:59:59',
            ]);
        }
    
        // For analysis/summary statistics (all data, not just current page)
        $allRequests = $query->get(); // careful with large data, otherwise improve
    
        $totalAmount = $allRequests->sum('total_amount');
        $totalPayable = $allRequests->sum(fn($r) => $r->total_amount - ($r->total_amount * 0.18));
        $approvedCount = $allRequests->where('status', 'approved')->count();
        $pendingCount = $allRequests->where('status', 'pending')->count();
        $rejectedCount = $allRequests->where('status', 'rejected')->count();
    
        // Pagination (applies after filtering)
        $requests = $query->latest()->paginate(20)->withQueryString();
    
        return view('management.withdrawals.index', compact(
            'requests', 'totalAmount', 'totalPayable', 'approvedCount', 'pendingCount', 'rejectedCount'
        ));
    }

    /**
     * Admin approves a withdrawal
     */
    public function approve($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'This withdrawal request is already processed.');
        }
        $withdrawal->status = 'approved';
        $withdrawal->save();
    
        return back()->with('success', 'Withdrawal approved.');
    }



    /**
     * Admin rejects a withdrawal
     */
    public function reject($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'This withdrawal request is already processed.');
        }
    
        // Refund to main wallet
        DB::beginTransaction();
        try {
            $userId = $withdrawal->user_id;
            $mainWallet = MainWallet::firstOrCreate(['user_id' => $userId], ['balance' => 0]);
            
            // Refund the amount back to main wallet
            $mainWallet->balance += $withdrawal->total_amount;
            $mainWallet->save();
    
            // Mark as rejected
            $withdrawal->status = 'rejected';
            $withdrawal->save();
    
            DB::commit();
            return back()->with('success', 'Withdrawal rejected and amount refunded to main wallet.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error refunding: ' . $e->getMessage());
        }
    }

}
