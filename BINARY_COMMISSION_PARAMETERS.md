# Binary Tree Commission System - Parameters & Controls

## 📊 Parameters Controlling Binary Commission

### 1. **Points Per Match** (`points_per_match`)
- **Default Value**: `100`
- **Location**: `income_settings` table
- **Purpose**: Number of points required on BOTH left and right sides to create ONE match
- **Formula**: `matches = min(floor(left_points / points_per_match), floor(right_points / points_per_match))`

**Example:**
- Left Points: 350
- Right Points: 250
- Points Per Match: 100
- **Result**: `min(floor(350/100), floor(250/100)) = min(3, 2) = 2 matches`

---

### 2. **Binary Matching Income** (`binary_matching_income`)
- **Default Value**: `₹200`
- **Location**: `income_settings` table
- **Purpose**: Amount credited to main wallet for EACH match
- **Formula**: `total_income = match_count × binary_matching_income`

**Example:**
- Matches: 2
- Income Per Match: ₹200
- **Result**: `2 × ₹200 = ₹400` credited to main wallet

---

### 3. **Points Per Activation** (`points_per_activation`)
- **Default Value**: `100`
- **Location**: `income_settings` table
- **Purpose**: Points added to upline users when a new user activates
- **Distribution**: Points are added to `left_points` or `right_points` based on which side the new user is placed

**Example:**
- New user activates on LEFT side
- Points Per Activation: 100
- **Result**: All upline users (up to configured levels) get `+100` to their `left_points`

---

### 4. **Upline Chain Levels** (`upline_chain_levels`)
- **Default Value**: `15`
- **Location**: `income_settings` table
- **Purpose**: Maximum number of levels in the upline chain that receive points
- **How It Works**: When a user activates, points are distributed to ALL upline users up to this level

**Example:**
- New user activates
- Upline Chain Levels: 15
- **Result**: Points are added to parent, grandparent, great-grandparent... up to 15 levels above

---

## 🔄 Left Points vs Right Points

### How Points Are Added:

1. **When a new user activates:**
   - System determines if the new user is on the LEFT or RIGHT side of their parent
   - Points are added to the corresponding side (`left_points` or `right_points`) for all upline users

2. **Point Distribution Flow:**
   ```
   New User (Level 0) activates
        ↓
   Parent (Level 1) gets points on the side where new user is placed
        ↓
   Grandparent (Level 2) gets points on the same side
        ↓
   ... continues up to 15 levels (or configured limit)
   ```

3. **Binary Matching:**
   - System matches `left_points` with `right_points`
   - Matches = minimum of (left_points ÷ points_per_match, right_points ÷ points_per_match)
   - After matching, matched points are deducted from both sides

**Example:**
```
User A has:
- left_points: 350
- right_points: 250
- points_per_match: 100

Matches = min(350/100, 250/100) = min(3, 2) = 2 matches
Income = 2 × ₹200 = ₹400

After matching:
- left_points: 350 - (2 × 100) = 150
- right_points: 250 - (2 × 100) = 50
```

---

## 📈 Level Distribution

### How Many Levels Get Commission?

**Answer: Controlled by `upline_chain_levels` setting (default: 15 levels)**

### Distribution Process:

1. **When User Activates:**
   ```php
   // From HandleUserActivationChange.php
   $uplineLevels = IncomeSetting::getValue('upline_chain_levels', 15);
   $chain = BinaryNode::getUplineChainToLevel($user->id, $uplineLevels);
   
   foreach ($chain as $upline) {
       if ($upline['child_position'] === 'left') {
           $uplineNode->left_points += $pointsPerActivation;
       } elseif ($upline['child_position'] === 'right') {
           $uplineNode->right_points += $pointsPerActivation;
       }
   }
   ```

2. **Visual Example (15 levels):**
   ```
   Level 15 (Top)     ← Gets points
   Level 14           ← Gets points
   Level 13           ← Gets points
   ...
   Level 2            ← Gets points
   Level 1 (Parent)   ← Gets points
   Level 0 (New User) ← Activates
   ```

3. **Important Notes:**
   - **All levels** up to the limit receive points
   - Points are added to the **same side** (left or right) throughout the chain
   - If the chain is shorter than the limit, only existing levels get points

---

## 🎯 Summary Table

| Parameter | Key | Default | Controls |
|-----------|-----|---------|----------|
| **Points Per Match** | `points_per_match` | 100 | How many points needed for 1 match |
| **Income Per Match** | `binary_matching_income` | ₹200 | Money earned per match |
| **Points Per Activation** | `points_per_activation` | 100 | Points added when user activates |
| **Upline Chain Levels** | `upline_chain_levels` | 15 | How many levels get points |

---

## 🔧 Where to Change These Settings

### Management Panel:
- **Route**: `/management/income-settings`
- **Controller**: `IncomeSettingsController`
- **View**: `resources/views/management/income_settings.blade.php`

### Database:
- **Table**: `income_settings`
- **Model**: `App\Models\IncomeSetting`

### Programmatic Access:
```php
// Get a setting
$pointsPerMatch = IncomeSetting::getValue('points_per_match', 100);
$incomePerMatch = IncomeSetting::getValue('binary_matching_income', 200);
$uplineLevels = IncomeSetting::getValue('upline_chain_levels', 15);

// Update a setting
IncomeSetting::setValue('points_per_match', 150);
```

---

## 📝 Binary Matching Process

### Automated Cron Job:
- **Command**: `php artisan binary:match`
- **Frequency**: Should run hourly (configure in `app/Console/Kernel.php`)
- **Process**:
  1. Gets all `BinaryNode` records
  2. Calculates matches for each user
  3. Credits income to `MainWallet`
  4. Creates `BinaryIncome` record
  5. Deducts matched points from both sides

### Manual Execution:
```bash
php artisan binary:match
```

---

## 💡 Key Insights

1. **Left/Right Points**: Determined by the position of new users in the binary tree
2. **Matching**: Only happens when BOTH sides have enough points
3. **Level Distribution**: ALL levels up to the limit get points (not just one level)
4. **Income**: Only earned when points are matched, not when points are added
5. **Unmatched Points**: Remain in the system until enough points accumulate on the other side

---

## 🔍 Example Scenario

**Settings:**
- `points_per_match`: 100
- `binary_matching_income`: ₹200
- `points_per_activation`: 100
- `upline_chain_levels`: 15

**Scenario:**
1. User B activates under User A (LEFT side)
2. User A gets `+100` to `left_points`
3. User A's parent gets `+100` to `left_points`
4. ... continues up 15 levels

**After Multiple Activations:**
- User A: `left_points = 350`, `right_points = 250`
- Matches: `min(350/100, 250/100) = 2 matches`
- Income: `2 × ₹200 = ₹400`
- Remaining: `left_points = 150`, `right_points = 50`

---

**Last Updated**: Based on current codebase analysis
**Files Referenced**:
- `app/Console/Commands/BinaryMatchingCron.php`
- `app/Listeners/HandleUserActivationChange.php`
- `app/Models/BinaryNode.php`
- `app/Models/IncomeSetting.php`
- `app/Http/Controllers/IncomeSettingsController.php`

