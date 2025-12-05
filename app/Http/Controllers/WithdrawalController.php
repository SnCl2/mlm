<?php

namespace App\Http\Controllers;
use App\Models\CashbackWallet;
use App\Models\ReferralWallet;
use App\Models\BinaryWallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    /**
     * User requests a withdrawal (combined from all 3 wallets)
     */
    public function request(Request $request)
    {
        $user = Auth::user();
        $available = $user->getTotalWithdrawableBalance();
    
        // Validate inputs
        $request->validate([
            'cashback_amount' => 'nullable|numeric|min:0',
            'referral_amount' => 'nullable|numeric|min:0',
            'binary_amount'   => 'nullable|numeric|min:0',
            'note'            => 'nullable|string|max:255',
        ]);
    
        $cashbackAmount = $request->cashback_amount ?? 0;
        $referralAmount = $request->referral_amount ?? 0;
        $binaryAmount   = $request->binary_amount ?? 0;
        $total = $cashbackAmount + $referralAmount + $binaryAmount;
    
        if ($total < 500) {
            return back()->with('error', 'Minimum ₹500 required to request withdrawal.');
        }
    
        // Deduct from wallets AT REQUEST TIME (atomic transaction is recommended)
        \DB::beginTransaction();
        try {
            // Cashback Wallet
            if ($cashbackAmount > 0) {
                $cashbackWallet = CashbackWallet::firstOrCreate(['user_id' => $user->id]);
                if ($cashbackWallet->cashback_amount < $cashbackAmount) {
                    throw new \Exception('Insufficient Cashback Wallet balance.');
                }
                $cashbackWallet->cashback_amount -= $cashbackAmount;
                $cashbackWallet->save();
            }
    
            // Referral Wallet (deduct oldest entries first)
            if ($referralAmount > 0) {
                $referralWalletTotal = ReferralWallet::where('user_id', $user->id)->sum('amount');
                if ($referralWalletTotal < $referralAmount) {
                    throw new \Exception('Insufficient Referral Wallet balance.');
                }
                $remaining = $referralAmount;
                $entries = ReferralWallet::where('user_id', $user->id)->orderBy('id')->get();
                foreach ($entries as $entry) {
                    if ($remaining <= 0) break;
                    if ($entry->amount <= $remaining) {
                        $remaining -= $entry->amount;
                        $entry->delete();
                    } else {
                        $entry->amount -= $remaining;
                        $entry->save();
                        $remaining = 0;
                    }
                }
            }
    
            // Binary Wallet
            if ($binaryAmount > 0) {
                $binaryWallet = BinaryWallet::firstOrCreate(['user_id' => $user->id]);
                if ($binaryWallet->matching_amount < $binaryAmount) {
                    throw new \Exception('Insufficient Binary Wallet balance.');
                }
                $binaryWallet->matching_amount -= $binaryAmount;
                $binaryWallet->save();
            }
    
            // Record the withdrawal as "pending"
            Withdrawal::create([
                'user_id'         => $user->id,
                'cashback_amount' => $cashbackAmount,
                'referral_amount' => $referralAmount,
                'binary_amount'   => $binaryAmount,
                'total_amount'    => $total,
                'status'          => 'pending',
                'note'            => $request->note,
            ]);
    
            \DB::commit();
            return back()->with('success', 'Withdrawal request submitted and amounts deducted.');
        } catch (\Exception $e) {
            \DB::rollBack();
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
    
        // Refund
        \DB::beginTransaction();
        try {
            $userId = $withdrawal->user_id;
    
            // Cashback
            if ($withdrawal->cashback_amount > 0) {
                $cashbackWallet = CashbackWallet::firstOrCreate(['user_id' => $userId]);
                $cashbackWallet->cashback_amount += $withdrawal->cashback_amount;
                $cashbackWallet->save();
            }
    
            // Referral (re-add a single entry or as per your business rule)
            if ($withdrawal->referral_amount > 0) {
                ReferralWallet::create([
                    'user_id' => $userId,
                    'amount'  => $withdrawal->referral_amount,
                ]);
            }
    
            // Binary
            if ($withdrawal->binary_amount > 0) {
                $binaryWallet = BinaryWallet::firstOrCreate(['user_id' => $userId]);
                $binaryWallet->matching_amount += $withdrawal->binary_amount;
                $binaryWallet->save();
            }
    
            // Mark as rejected
            $withdrawal->status = 'rejected';
            $withdrawal->save();
    
            \DB::commit();
            return back()->with('success', 'Withdrawal rejected and all amounts refunded.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Error refunding: ' . $e->getMessage());
        }
    }

}
