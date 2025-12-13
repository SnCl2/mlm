# Quick Cron Job Setup Guide

## For Laravel 11 (Your Current Version)

### Step 1: Schedule the Command ✅ (Already Done)

The command is already scheduled in `routes/console.php`:
```php
Schedule::command('binary:match')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/binary-matching.log'));
```

This will run the command **every hour**.

### Step 2: Add Single Cron Entry to Server

You only need to add **ONE** cron entry that runs Laravel's scheduler:

#### For cPanel:

1. Log into **cPanel**
2. Go to **Cron Jobs** (search for "Cron" in cPanel)
3. Click **Add New Cron Job**
4. Use these settings:
   - **Minute**: `*`
   - **Hour**: `*`
   - **Day**: `*`
   - **Month**: `*`
   - **Weekday**: `*`
   - **Command**: 
     ```bash
     cd /home/u727229935/domains/mlm.valuerkkda.com/public_html && php artisan schedule:run >> /dev/null 2>&1
     ```
5. Click **Add New Cron Job**

#### For SSH/VPS:

```bash
crontab -e
```

Add this line:
```
* * * * * cd /home/u727229935/domains/mlm.valuerkkda.com/public_html && php artisan schedule:run >> /dev/null 2>&1
```

Save and exit (Ctrl+X, then Y, then Enter).

---

## Change Schedule Frequency

If you want to change how often it runs, edit `routes/console.php`:

### Every 15 Minutes:
```php
Schedule::command('binary:match')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground();
```

### Every 5 Minutes:
```php
Schedule::command('binary:match')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();
```

### Every 30 Minutes:
```php
Schedule::command('binary:match')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->runInBackground();
```

### Daily at 2 AM:
```php
Schedule::command('binary:match')
    ->dailyAt('02:00')
    ->withoutOverlapping();
```

---

## Test It

### 1. Test the command manually:
```bash
cd /home/u727229935/domains/mlm.valuerkkda.com/public_html
php artisan binary:match
```

### 2. Check scheduled tasks:
```bash
php artisan schedule:list
```

### 3. Run scheduler manually (to test):
```bash
php artisan schedule:run
```

### 4. Check logs:
```bash
tail -f storage/logs/laravel.log
# or
tail -f storage/logs/binary-matching.log
```

---

## Verify It's Working

1. Wait for the scheduled time (or run manually)
2. Check `storage/logs/binary-matching.log` for output
3. Check `storage/logs/laravel.log` for any errors
4. Verify main wallets were updated in database

---

## Troubleshooting

**Cron not running?**
- Check PHP path: `which php` (might need `/usr/bin/php` instead of `php`)
- Check file permissions: `chmod +x artisan`
- Check cron service: `systemctl status cron` (on VPS)

**Command not found?**
- Use full path: `/usr/bin/php artisan schedule:run`
- Or find PHP: `whereis php` or `which php`

**Permission denied?**
- Check directory permissions
- Ensure you're using the correct user

---

## That's It! 🎉

Once you add the single cron entry, Laravel will automatically run `binary:match` every hour (or as configured).

The scheduler runs every minute and checks if any scheduled tasks need to run, so you only need that one cron entry.

