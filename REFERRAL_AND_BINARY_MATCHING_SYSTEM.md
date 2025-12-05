# Referral and Binary Matching Income System

## 📋 Table of Contents
1. [Overview](#overview)
2. [Direct Referral Income](#direct-referral-income)
3. [Binary Tree Structure](#binary-tree-structure)
4. [Left Side Referral](#left-side-referral)
5. [Right Side Referral](#right-side-referral)
6. [Binary Matching System](#binary-matching-system)
7. [Examples](#examples)
8. [Important Notes](#important-notes)

---

## Overview

This system has two main income streams:
1. **Direct Referral Income** - Earned when someone you directly refer activates
2. **Binary Matching Income** - Earned when your binary tree has balanced left and right sides

---

## Direct Referral Income

### What Happens When You Refer Someone?

When a user you referred **activates** their account:

✅ **You receive:**
- **₹300** directly credited to your **Referral Wallet**
- This is a one-time payment per activation

### Example:
```
User A refers User B
User B activates → User A gets ₹300 in Referral Wallet
```

---

## Binary Tree Structure

The binary tree is a hierarchical structure where:
- Each user can have **2 children** (left and right)
- Users are placed in the tree based on their `place_under` and `position` (left/right)
- Points flow **upward** through the tree (up to 15 levels)

```
        User A (You)
       /          \
   Left Child   Right Child
     /  \         /  \
   ...  ...     ...  ...
```

---

## Left Side Referral

### What Happens When Someone Joins Your LEFT Side?

When a user activates and is placed on your **LEFT** side of the binary tree:

✅ **You receive:**
- **100 points** added to your **Left Points** (`left_points`)
- Points are added to your account if the new user is anywhere in your left subtree (up to 15 levels deep)

### How It Works:
1. User activates on your left side (or in your left subtree)
2. System traces the upline chain from the new user up to you (maximum 15 levels)
3. **100 points** are added to your `left_points` for each activation on your left side

### Example:
```
        You (User A)
       /          \
   User B      User C
   (LEFT)      (RIGHT)
     |
   User D
   (LEFT of B, but still in YOUR left subtree)
```

When User D activates:
- User B gets ₹300 (direct referral)
- **You get 100 left points** (because D is in your left subtree)

---

## Right Side Referral

### What Happens When Someone Joins Your RIGHT Side?

When a user activates and is placed on your **RIGHT** side of the binary tree:

✅ **You receive:**
- **100 points** added to your **Right Points** (`right_points`)
- Points are added to your account if the new user is anywhere in your right subtree (up to 15 levels deep)

### How It Works:
1. User activates on your right side (or in your right subtree)
2. System traces the upline chain from the new user up to you (maximum 15 levels)
3. **100 points** are added to your `right_points` for each activation on your right side

### Example:
```
        You (User A)
       /          \
   User B      User C
   (LEFT)      (RIGHT)
                  |
               User E
            (RIGHT of C, but still in YOUR right subtree)
```

When User E activates:
- User C gets ₹300 (direct referral)
- **You get 100 right points** (because E is in your right subtree)

---

## Binary Matching System

### How Binary Matching Works

Binary matching is the process where your **left points** and **right points** are matched together to generate income.

### Matching Formula:

```
Match Count = Minimum of:
  - Floor(Left Points ÷ 100)
  - Floor(Right Points ÷ 100)

Income per Match = ₹200
Total Income = Match Count × ₹200
```

### Step-by-Step Process:

1. **Check for Matches:**
   - System checks if you have at least **100 left points** AND **100 right points**

2. **Calculate Matches:**
   - If you have 350 left points and 250 right points:
     - Left matches: floor(350 ÷ 100) = **3 matches**
     - Right matches: floor(250 ÷ 100) = **2 matches**
     - **Actual matches: min(3, 2) = 2 matches**

3. **Credit Income:**
   - Income = 2 matches × ₹200 = **₹400**
   - This is credited to your **Binary Wallet** (`matching_amount`)

4. **Deduct Points:**
   - Left points: 350 - (2 × 100) = **150 points remaining**
   - Right points: 250 - (2 × 100) = **50 points remaining**
   - Unmatched points carry forward for future matches

### Matching Examples:

#### Example 1: Perfect Match
```
Left Points: 500
Right Points: 300

Matches: min(floor(500/100), floor(300/100)) = min(5, 3) = 3 matches
Income: 3 × ₹200 = ₹600
Remaining: 200 left points, 0 right points
```

#### Example 2: Uneven Match
```
Left Points: 150
Right Points: 250

Matches: min(floor(150/100), floor(250/100)) = min(1, 2) = 1 match
Income: 1 × ₹200 = ₹200
Remaining: 50 left points, 150 right points
```

#### Example 3: No Match
```
Left Points: 80
Right Points: 250

Matches: min(floor(80/100), floor(250/100)) = min(0, 2) = 0 matches
Income: ₹0
Remaining: 80 left points, 250 right points (waiting for more left points)
```

### When Does Matching Occur?

Matching happens automatically via:
- **Cron Job**: Runs periodically (command: `php artisan binary:match`)
- **Real-time**: Some actions trigger immediate matching (e.g., user activation status changes)

---

## Examples

### Complete Scenario:

Let's say you are **User A**:

1. **You refer User B (placed on LEFT):**
   - ✅ You get ₹300 in Referral Wallet
   - ✅ You get 100 left points

2. **You refer User C (placed on RIGHT):**
   - ✅ You get ₹300 in Referral Wallet
   - ✅ You get 100 right points

3. **Binary Matching:**
   - Left points: 100
   - Right points: 100
   - Matches: min(1, 1) = **1 match**
   - ✅ You get ₹200 in Binary Wallet
   - Remaining: 0 left points, 0 right points

4. **User B refers User D (LEFT of B, still in your LEFT subtree):**
   - User B gets ₹300 (direct referral)
   - ✅ You get 100 left points (D is in your left subtree)

5. **User C refers User E (RIGHT of C, still in your RIGHT subtree):**
   - User C gets ₹300 (direct referral)
   - ✅ You get 100 right points (E is in your right subtree)

6. **Binary Matching Again:**
   - Left points: 100
   - Right points: 100
   - Matches: **1 match**
   - ✅ You get ₹200 in Binary Wallet
   - Remaining: 0 left points, 0 right points

### Income Summary:
- **Referral Income**: ₹600 (2 direct referrals × ₹300)
- **Binary Matching Income**: ₹400 (2 matches × ₹200)
- **Total Income**: ₹1,000

---

## Important Notes

### ⚠️ Key Points to Remember:

1. **Direct Referral Income (₹300):**
   - Only the **direct referrer** gets this
   - Paid once per activation
   - Goes to Referral Wallet

2. **Binary Points (100 points per activation):**
   - Points flow **upward** through the tree (up to 15 levels)
   - Points are added based on which **side** (left/right) the new user is placed
   - Points accumulate until matched

3. **Binary Matching Income (₹200 per match):**
   - Requires **both** left AND right points
   - Minimum 100 points on each side to create 1 match
   - Unmatched points carry forward
   - Income goes to Binary Wallet

4. **Tree Depth Limit:**
   - Points are distributed up to **15 levels** in the upline chain
   - Beyond 15 levels, no points are distributed

5. **Point Distribution:**
   - When a user activates, **all upline users** (up to 15 levels) in the same side get points
   - This creates multiple income opportunities from a single activation

6. **Matching Priority:**
   - System always matches the **maximum possible** number of pairs
   - Remaining unmatched points wait for future matches

### 💡 Tips for Maximizing Income:

1. **Build Both Sides:** Focus on building both left and right sides of your tree to maximize matching opportunities

2. **Direct Referrals:** Each direct referral gives you ₹300 immediately

3. **Team Building:** Help your downline build their teams - you'll earn points from their referrals too (up to 15 levels)

4. **Balance is Key:** A balanced tree (equal left and right growth) maximizes matching income

---

## System Flow Diagram

```
User Activation
      ↓
┌─────────────────┐
│ Direct Referrer │ → ₹300 to Referral Wallet
└─────────────────┘
      ↓
┌─────────────────────────────────────┐
│ Upline Chain (up to 15 levels)      │
│                                     │
│ For each upline user:               │
│   - If new user is on LEFT:         │
│     → Add 100 to left_points        │
│   - If new user is on RIGHT:        │
│     → Add 100 to right_points       │
└─────────────────────────────────────┘
      ↓
┌─────────────────────────────────────┐
│ Binary Matching (Automatic)         │
│                                     │
│ Check: left_points ≥ 100 AND        │
│        right_points ≥ 100           │
│                                     │
│ Calculate:                          │
│   matches = min(floor(left/100),    │
│                floor(right/100))    │
│                                     │
│ Income: matches × ₹200              │
│ → Credit to Binary Wallet          │
│                                     │
│ Deduct: matched points from both    │
│         sides                       │
└─────────────────────────────────────┘
```

---

## Summary

| Action | Left Side | Right Side | Income Type |
|--------|-----------|------------|-------------|
| Direct Referral | - | - | ₹300 (Referral Wallet) |
| User activates on LEFT | +100 points | - | Points only |
| User activates on RIGHT | - | +100 points | Points only |
| Binary Match (100L + 100R) | -100 points | -100 points | ₹200 (Binary Wallet) |

**Remember:**
- **Direct referrals** = Immediate ₹300 income
- **Binary points** = Accumulate until matched
- **Binary matching** = ₹200 per match (100 left + 100 right points)

---

*Last Updated: Based on system analysis*
*For questions or clarifications, please refer to the codebase or contact system administrator.*

