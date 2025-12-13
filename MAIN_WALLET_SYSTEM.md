# Main Wallet System - Implementation Summary

## Overview
The system has been refactored to use a **Main Wallet** where all money accumulates, and withdrawals happen from this single wallet. The dashboard now displays comprehensive wallet and income information.

---

## System Architecture

### Main Wallet (`MainWallet`)
- **Purpose**: Central wallet where all earnings accumulate
- **Location**: `app/Models/MainWallet.php`
- **Fields**: `user_id`, `balance`
- **Relationship**: `User hasOne MainWallet`

### Income Sources (Tracking Only)
These tables track income history but money goes to Main Wallet:

1. **Referral Income** (`ReferralIncome`)
   - Tracks referral earnings
   - Money added to Main Wallet when user activates

2. **Binary Income** (`BinaryIncome`)
   - Tracks binary matching earnings
   - Money added to Main Wallet when matches occur

3. **Cashback Income** (`CashbackIncome`)
   - Tracks cashback from shop transactions
   - Money added to Main Wallet at level 1 commission

---

## Dashboard Display

The dashboard now shows:

### 1. **Main Wallet**
- Current balance in the main wallet
- This is where all money accumulates

### 2. **Referral Income**
- Total referral income earned (historical)
- Shows total from all referral activations

### 3. **Binary Income**
- Total binary matching income earned (historical)
- Shows total from all binary matches

### 4. **Cashback Income**
- Total cashback earned (historical)
- Shows total from all shop transactions

### 5. **Total Withdrawal**
- Sum of all approved withdrawals
- Historical total of money withdrawn

### 6. **Pending Withdrawal**
- Sum of all pending withdrawal requests
- Amount currently locked awaiting approval

### 7. **Available Balance**
- **Formula**: Main Wallet - Pending Withdrawals
- This is the amount available for new withdrawal requests

---

## Withdrawal Process

### User Flow:
1. User enters withdrawal amount (minimum ₹500)
2. System checks available balance
3. Amount is deducted from Main Wallet immediately
4. Withdrawal record created with status "pending"
5. Admin approves/rejects
6. If rejected, amount is refunded to Main Wallet

### Withdrawal Form:
- Single amount field (no separate wallet fields)
- Shows available balance
- Shows pending withdrawal amount
- Minimum: ₹500
- Maximum: Available balance

---

## Code Changes

### 1. User Model (`app/Models/User.php`)
**Added Relationships:**
```php
public function mainWallet()
public function referralIncomes()
public function binaryIncomes()
public function cashbackIncomes()
```

**Updated Method:**
```php
public function getTotalWithdrawableBalance()
{
    // Returns:
    // - main_wallet: Current balance
    // - referral_income: Total earned
    // - binary_income: Total earned
    // - cashback_income: Total earned
    // - total_income: Sum of all income
    // - total_withdrawn: Approved withdrawals
    // - pending_withdrawn: Pending withdrawals
    // - available_balance: Main wallet - Pending
}
```

### 2. WithdrawalController (`app/Http/Controllers/WithdrawalController.php`)
- Updated to use single `amount` field
- Deducts from Main Wallet only
- Validates against available balance

### 3. Dashboard View (`resources/views/dashboard/dashboard_earnings.blade.php`)
- Displays all 6 wallet/income cards
- Shows available balance prominently
- Single amount withdrawal form
- Binary points display

---

## Income Flow

### Referral Income:
```
User Activation → ReferralIncome created → MainWallet.balance += amount
```

### Binary Income:
```
Binary Match → BinaryIncome created → MainWallet.balance += amount
```

### Cashback Income:
```
Shop Transaction → CashbackIncome created → MainWallet.balance += amount
```

---

## Database Tables

### `main_wallets`
- `id`
- `user_id` (foreign key)
- `balance` (decimal 10,2)
- `timestamps`

### `referral_incomes`
- `id`
- `user_id`
- `new_user_id`
- `amount`
- `description`
- `timestamps`

### `binary_incomes`
- `id`
- `user_id`
- `amount`
- `matches`
- `description`
- `timestamps`

### `cashback_incomes`
- `id`
- `user_id`
- `shop_id`
- `shop_transaction_id`
- `amount`
- `description`
- `timestamps`

### `withdrawals`
- `id`
- `user_id`
- `total_amount` (single amount)
- `status` (pending/approved/rejected)
- `note`
- `timestamps`

---

## Benefits

1. **Simplified Withdrawal**: Single wallet, single amount field
2. **Clear Visibility**: Users see all income sources and withdrawal history
3. **Better Tracking**: Income sources tracked separately for reporting
4. **Atomic Operations**: All wallet operations use database transactions
5. **Real-time Balance**: Available balance calculated dynamically

---

## User Experience

### Dashboard Shows:
- ✅ Main Wallet balance (where money accumulates)
- ✅ Referral Income total (how much earned from referrals)
- ✅ Binary Income total (how much earned from binary matches)
- ✅ Cashback Income total (how much earned from cashback)
- ✅ Total Withdrawal (how much already withdrawn)
- ✅ Pending Withdrawal (how much waiting for approval)
- ✅ Available Balance (how much can be withdrawn now)

### Withdrawal Form Shows:
- ✅ Available balance prominently
- ✅ Pending withdrawal warning (if any)
- ✅ Single amount input field
- ✅ Minimum/maximum validation

---

## Migration Notes

The old wallet system (CashbackWallet, ReferralWallet, BinaryWallet) is kept for backward compatibility but is no longer used for withdrawals. All new income goes to Main Wallet.

---

## Testing Checklist

- [ ] Main wallet balance displays correctly
- [ ] Income sources show correct totals
- [ ] Available balance calculation is correct
- [ ] Withdrawal form validates minimum amount
- [ ] Withdrawal form validates maximum (available balance)
- [ ] Withdrawal deducts from main wallet
- [ ] Pending withdrawal is subtracted from available balance
- [ ] Rejection refunds to main wallet
- [ ] Approval doesn't change balance (already deducted)

