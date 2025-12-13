# Wallet System and Withdrawal Method Analysis

## Overview
The system implements a multi-wallet architecture with three distinct wallet types that users can withdraw from. The withdrawal process is atomic and includes proper transaction handling.

---

## Wallet System Architecture

### 1. **Cashback Wallet** (`CashbackWallet`)
- **Model**: `app/Models/CashbackWallet.php`
- **Table**: `cashback_wallets`
- **Structure**:
  - `user_id` (foreign key)
  - `shop_id` (foreign key) - links to shop that generated cashback
  - `cashback_amount` (decimal 10,2)
  - `timestamps`

- **Purpose**: Stores cashback earnings from shop transactions
- **Relationship**: `User hasMany CashbackWallet`
- **Usage**: Accumulates cashback from commission level 1 (typically 5% of commission)

### 2. **Referral Wallet** (`ReferralWallet`)
- **Model**: `app/Models/ReferralWallet.php`
- **Table**: `referral_wallets`
- **Structure**:
  - `user_id` (foreign key) - who earned the income
  - `new_user_id` (nullable foreign key) - who joined
  - `parent_id` (nullable foreign key) - tree parent
  - `amount` (decimal 10,2)
  - `timestamps`

- **Purpose**: Stores referral income earned when new users join
- **Relationship**: `User hasMany ReferralWallet`
- **Special Behavior**: 
  - Uses FIFO (First In, First Out) for withdrawals
  - Each entry represents a separate referral commission
  - Tracks which new user and parent generated the income

### 3. **Binary Wallet** (`BinaryWallet`)
- **Model**: `app/Models/BinaryWallet.php`
- **Table**: `binary_wallets`
- **Structure**:
  - `user_id` (foreign key)
  - `matching_amount` (decimal 10,2)
  - `timestamps`

- **Purpose**: Stores binary matching income from balanced left/right tree points
- **Relationship**: `User hasOne BinaryWallet`
- **Usage**: Accumulates income when binary tree has matching left and right points

---

## User Balance Calculation

### Method: `getTotalWithdrawableBalance()`
**Location**: `app/Models/User.php` (lines 172-184)

```php
public function getTotalWithdrawableBalance()
{
    $cashback = $this->cashbackWallets()->sum('cashback_amount');
    $referral = $this->referralWallets()->sum('amount');
    $binary   = $this->binaryWallet ? $this->binaryWallet->matching : 0;  // ⚠️ BUG FOUND

    return [
        'cashback' => $cashback,
        'referral' => $referral,
        'binary'   => $binary,
        'total'    => $cashback + $referral + $binary,
    ];
}
```

**⚠️ CRITICAL BUG**: Line 176 uses `$this->binaryWallet->matching` but the actual column is `matching_amount`. This will cause errors when accessing binary wallet balance.

**Fix Required**: Change `matching` to `matching_amount`

---

## Withdrawal System

### Controller: `WithdrawalController`
**Location**: `app/Http/Controllers/WithdrawalController.php`

### 1. **Request Withdrawal** (`request()` method)

#### Flow:
1. **Validation** (lines 22-27):
   - Validates amounts are numeric and non-negative
   - Optional note field (max 255 chars)

2. **Minimum Amount Check** (line 34):
   - Minimum withdrawal: ₹500
   - Returns error if total is less than minimum

3. **Atomic Transaction** (lines 38-98):
   - Uses database transaction (`\DB::beginTransaction()`)
   - Deducts from wallets **immediately** at request time
   - Creates withdrawal record with status 'pending'

#### Wallet Deduction Logic:

**Cashback Wallet** (lines 41-49):
- Checks if balance is sufficient
- Deducts amount directly from `cashback_amount`
- Uses `firstOrCreate` to ensure wallet exists

**Referral Wallet** (lines 51-70):
- **FIFO Implementation**: Deducts oldest entries first
- Checks total balance first
- Iterates through entries ordered by `id` (oldest first)
- Deletes entries if fully consumed
- Partially reduces entry if only part is needed

**Binary Wallet** (lines 72-80):
- Checks if balance is sufficient
- Deducts amount directly from `matching_amount`
- Uses `firstOrCreate` to ensure wallet exists

4. **Withdrawal Record** (lines 82-91):
   - Creates `Withdrawal` record with:
     - `user_id`
     - `cashback_amount`
     - `referral_amount`
     - `binary_amount`
     - `total_amount`
     - `status` = 'pending'
     - `note` (optional)

5. **Transaction Commit/Rollback**:
   - On success: commits transaction
   - On error: rolls back all changes

### 2. **Admin View** (`index()` method)

**Features**:
- Lists all withdrawal requests with pagination (20 per page)
- Search by user name or referral code
- Filter by status (pending/approved/rejected)
- Date range filtering
- Statistics:
  - Total amount requested
  - Total payable (with 18% deduction calculation)
  - Count by status (approved/pending/rejected)

**Note**: Line 135 calculates `totalPayable` with 18% deduction, but this appears to be for display only - actual payment processing is not implemented.

### 3. **Approve Withdrawal** (`approve()` method)

**Flow** (lines 151-161):
1. Finds withdrawal by ID
2. Checks if status is 'pending'
3. Updates status to 'approved'
4. **No refund logic** - amounts already deducted at request time

**⚠️ ISSUE**: Approval is just a status change. No actual payment processing or external API integration.

### 4. **Reject Withdrawal** (`reject()` method)

**Flow** (lines 168-212):
1. Finds withdrawal by ID
2. Checks if status is 'pending'
3. **Refunds amounts back to wallets**:
   - Cashback: Adds back to `cashback_amount`
   - Referral: Creates new entry (loses FIFO order)
   - Binary: Adds back to `matching_amount`
4. Updates status to 'rejected'

**⚠️ ISSUE**: Referral wallet refund creates a new entry instead of restoring original entries, losing FIFO order.

---

## Database Schema

### `withdrawals` Table
```sql
- id (primary key)
- user_id (foreign key → users)
- cashback_amount (decimal 10,2, default 0)
- referral_amount (decimal 10,2, default 0)
- binary_amount (decimal 10,2, default 0)
- total_amount (decimal 10,2)
- status (enum: 'pending', 'approved', 'rejected', default 'pending')
- note (text, nullable)
- created_at, updated_at
```

---

## Issues and Recommendations

### 🔴 Critical Issues

1. **Bug in `getTotalWithdrawableBalance()`**:
   - **Location**: `app/Models/User.php:176`
   - **Issue**: Uses `matching` instead of `matching_amount`
   - **Impact**: Will cause errors when accessing binary wallet balance
   - **Fix**: Change to `$this->binaryWallet->matching_amount ?? 0`

2. **No Payment Processing**:
   - Approval only changes status
   - No integration with payment gateways
   - No actual money transfer mechanism

3. **Referral Wallet Refund Issue**:
   - Rejection creates new entry instead of restoring original FIFO order
   - Loses historical tracking

### 🟡 Potential Issues

1. **Race Condition Risk**:
   - Multiple simultaneous withdrawal requests could cause balance issues
   - Consider using row-level locking (`lockForUpdate()`)

2. **No Maximum Withdrawal Limit**:
   - Only minimum (₹500) is enforced
   - Consider adding maximum per request or per day

3. **No Validation Against Available Balance**:
   - Method `getTotalWithdrawableBalance()` is called but not used for validation
   - User could potentially request more than available if frontend is bypassed

4. **Cashback Wallet Structure Issue**:
   - **Location**: `database/migrations/2025_07_05_100314_create_cashback_wallets_table.php:17`
   - **Issue**: `shop_id` is NOT nullable (required foreign key)
   - **Problem**: `WithdrawalController` creates cashback wallet without `shop_id`:
     - Line 43: `CashbackWallet::firstOrCreate(['user_id' => $user->id])`
     - Line 182: `CashbackWallet::firstOrCreate(['user_id' => $userId])`
   - **Impact**: Will cause database constraint violation errors
   - **Fix Required**: Either:
     - Make `shop_id` nullable in migration, OR
     - Provide a default shop_id when creating wallet in withdrawal context

5. **Transaction Isolation**:
   - No explicit isolation level set
   - Could lead to dirty reads in high-concurrency scenarios

### 🟢 Recommendations

1. **Add Balance Validation**:
   ```php
   $available = $user->getTotalWithdrawableBalance();
   if ($total > $available['total']) {
       return back()->with('error', 'Insufficient balance.');
   }
   ```

2. **Add Row Locking**:
   ```php
   $cashbackWallet = CashbackWallet::where('user_id', $user->id)
       ->lockForUpdate()
       ->firstOrCreate(['user_id' => $user->id]);
   ```

3. **Improve Referral Refund**:
   - Store original entry IDs in withdrawal record
   - Restore original entries on rejection

4. **Add Payment Gateway Integration**:
   - Integrate with payment providers (Razorpay, Paytm, etc.)
   - Process actual payments on approval

5. **Add Withdrawal Limits**:
   - Daily withdrawal limit
   - Maximum per transaction
   - KYC verification requirement

6. **Add Audit Trail**:
   - Log all withdrawal operations
   - Track who approved/rejected
   - Store payment transaction IDs

7. **Add Status Timestamps**:
   - `approved_at`
   - `rejected_at`
   - `processed_at`

---

## Security Considerations

1. ✅ **Transaction Safety**: Uses database transactions
2. ✅ **Authentication**: Uses `Auth::user()` for user identification
3. ⚠️ **Authorization**: No check if user can withdraw (e.g., KYC status)
4. ⚠️ **Input Validation**: Basic validation exists but could be stricter
5. ⚠️ **CSRF Protection**: Should be handled by Laravel middleware

---

## Summary

The wallet and withdrawal system is well-structured with proper transaction handling, but has several critical bugs and missing features:

**Strengths**:
- Atomic transactions prevent data corruption
- FIFO logic for referral wallet
- Proper error handling with rollback
- Multi-wallet support

**Weaknesses**:
- Critical bug in balance calculation
- No actual payment processing
- Missing balance validation
- Referral refund loses FIFO order
- No withdrawal limits or KYC checks

