# Main Wallet System Implementation

## Overview
The system has been successfully refactored to use a **Main Wallet** where all money accumulates, and withdrawals happen from this single wallet. The dashboard now displays comprehensive wallet and income information.

---

## System Architecture

### Main Wallet (`MainWallet`)
- **Purpose**: Central wallet where all earnings accumulate
- **Location**: `app/Models/MainWallet.php`
- **Fields**: `user_id`, `balance`
- **Relationship**: `User hasOne MainWallet`
- **Migration**: `database/migrations/2025_12_07_175212_create_main_wallets_table.php`

### Income Sources (Tracking Only)
These tables track income history but money goes to Main Wallet:

1. **Referral Income** (`ReferralIncome`)
   - Tracks referral earnings
   - Money added to Main Wallet when user activates
   - Migration: `database/migrations/2025_12_07_175719_create_referral_incomes_table.php`

2. **Binary Income** (`BinaryIncome`)
   - Tracks binary matching earnings
   - Money added to Main Wallet when matches occur
   - Migration: `database/migrations/2025_12_07_175719_create_binary_incomes_table.php`

3. **Cashback Income** (`CashbackIncome`)
   - Tracks cashback from shop transactions
   - Money added to Main Wallet at level 1 commission
   - Migration: `database/migrations/2025_12_07_175720_create_cashback_incomes_table.php`

---

## Dashboard Display

The dashboard now shows:

### 1. **Main Wallet**
- Current balance in the main wallet
- This is where all money accumulates
- Displayed with primary color (cyan)

### 2. **Referral Income**
- Total referral income earned (historical)
- Shows total from all referral activations
- Displayed with blue color

### 3. **Binary Income**
- Total binary matching income earned (historical)
- Shows total from all binary matches
- Displayed with indigo color

### 4. **Cashback Income**
- Total cashback earned (historical)
- Shows total from all shop transactions
- Displayed with green color

### 5. **Total Withdrawal**
- Sum of all approved withdrawals
- Historical total of money withdrawn
- Displayed with yellow color

### 6. **Pending Withdrawal**
- Sum of all pending withdrawal requests
- Amount currently locked awaiting approval
- Displayed with orange color

### 7. **Available Balance** (Highlighted)
- **Formula**: Main Wallet - Pending Withdrawals
- This is the amount available for new withdrawal requests
- Displayed prominently with gradient background

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
- Shows available balance prominently
- Shows pending withdrawal amount
- Minimum: ₹500
- Maximum: Available balance

---

## Code Changes Summary

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
- Refunds to Main Wallet on rejection

### 3. Dashboard View (`resources/views/dashboard/dashboard_earnings.blade.php`)
- Displays all 6 wallet/income cards
- Shows available balance prominently
- Single amount withdrawal form
- Binary points display

### 4. Income Distribution Updates

**Referral Income** (`app/Listeners/HandleUserActivationChange.php`):
- Creates `ReferralIncome` record
- Adds amount to `MainWallet.balance`
- Removes from main wallet on deactivation

**Binary Income** (`app/Console/Commands/BinaryMatchingCron.php`):
- Creates `BinaryIncome` record
- Adds amount to `MainWallet.balance`
- Runs via cron job

**Cashback Income** (`app/Http/Controllers/ShopDashboardController.php`):
- Creates `CashbackIncome` record
- Adds amount to `MainWallet.balance`
- Triggered on shop transactions

---

## Income Flow

### Referral Income:
```
User Activation 
  → ReferralIncome created 
  → MainWallet.balance += amount
```

### Binary Income:
```
Binary Match (Cron Job)
  → BinaryIncome created 
  → MainWallet.balance += amount
```

### Cashback Income:
```
Shop Transaction
  → CashbackIncome created 
  → MainWallet.balance += amount
```

---

## Database Tables

### `main_wallets`
- `id`
- `user_id` (foreign key, unique)
- `balance` (decimal 15,2, default 0)
- `timestamps`

### `referral_incomes`
- `id`
- `user_id` (foreign key)
- `new_user_id` (nullable foreign key)
- `amount` (decimal 10,2)
- `description` (text, nullable)
- `timestamps`

### `binary_incomes`
- `id`
- `user_id` (foreign key)
- `amount` (decimal 10,2)
- `matches` (integer, default 0)
- `description` (text, nullable)
- `timestamps`

### `cashback_incomes`
- `id`
- `user_id` (foreign key)
- `shop_id` (nullable foreign key)
- `shop_transaction_id` (nullable foreign key)
- `amount` (decimal 10,2)
- `description` (text, nullable)
- `timestamps`

### `withdrawals` (Updated Usage)
- `id`
- `user_id` (foreign key)
- `total_amount` (decimal 10,2) - **Only this field is used now**
- `status` (enum: pending, approved, rejected)
- `note` (text, nullable)
- `timestamps`

**Note**: The `cashback_amount`, `referral_amount`, and `binary_amount` fields still exist in the table but are no longer used. They can be removed in a future migration if desired.

---

## Migration Instructions

If you need to run the migrations:

```bash
php artisan migrate
```

The following migrations should exist:
- `2025_12_07_175212_create_main_wallets_table.php`
- `2025_12_07_175719_create_referral_incomes_table.php`
- `2025_12_07_175719_create_binary_incomes_table.php`
- `2025_12_07_175720_create_cashback_incomes_table.php`

---

## Testing Checklist

- [ ] User can see main wallet balance on dashboard
- [ ] Referral income is added to main wallet on user activation
- [ ] Binary income is added to main wallet on matching
- [ ] Cashback income is added to main wallet on shop transaction
- [ ] Withdrawal request deducts from main wallet
- [ ] Available balance = Main Wallet - Pending Withdrawals
- [ ] Rejected withdrawal refunds to main wallet
- [ ] Dashboard shows all 6 cards correctly
- [ ] Withdrawal form validates minimum ₹500
- [ ] Withdrawal form validates maximum available balance

---

## Notes

1. **Old Wallet Tables**: The old `cashback_wallets`, `referral_wallets`, and `binary_wallets` tables are no longer used for withdrawals but may still exist in the database. They can be kept for historical data or removed in a future cleanup.

2. **Income Tracking**: All income is now tracked in separate income tables for reporting and audit purposes, while the actual money is stored in the main wallet.

3. **Backward Compatibility**: The withdrawal table still has the old fields (`cashback_amount`, `referral_amount`, `binary_amount`) but they are set to 0. Only `total_amount` is used.

---

## Future Enhancements

1. Add withdrawal history page
2. Add income history/details page
3. Add wallet transaction log
4. Add withdrawal limits (daily/monthly)
5. Add KYC verification requirement for withdrawals
6. Add payment gateway integration for actual payouts




