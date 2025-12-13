# How to Set Up Binary Matching Cron Job

This guide provides step-by-step instructions for setting up the `binary:match` command as a cron job.

---

## Method 1: Laravel Task Scheduler (Recommended) ⭐

Laravel's built-in task scheduler is the recommended way to manage cron jobs. It provides better error handling, logging, and prevents overlapping executions.

### Step 1: Create/Update Console Kernel

Create or update `app/Console/Kernel.php`:

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Binary matching - runs every hour
        $schedule->command('binary:match')
                 ->hourly()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/binary-matching.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
```

### Step 2: Add Single Cron Entry to Server

Add this **single entry** to your server's crontab:

```bash
* * * * * cd /home/u727229935/domains/mlm.valuerkkda.com/public_html && php artisan schedule:run >> /dev/null 2>&1
```

This single cron entry runs Laravel's scheduler every minute, which then executes scheduled tasks at their specified times.

### Step 3: Edit Crontab

**For cPanel/Shared Hosting:**
1. Log into cPanel
2. Go to **Cron Jobs**
3. Add new cron job:
   - **Minute**: `*`
   - **Hour**: `*`
   - **Day**: `*`
   - **Month**: `*`
   - **Weekday**: `*`
   - **Command**: 
     ```bash
     cd /home/u727229935/domains/mlm.valuerkkda.com/public_html && php artisan schedule:run >> /dev/null 2>&1
     ```

**For SSH/VPS:**
```bash
crontab -e
```

Then add the line:
```
* * * * * cd /home/u727229935/domains/mlm.valuerkkda.com/public_html && php artisan schedule:run >> /dev/null 2>&1
```

### Step 4: Verify Setup

Test the scheduler:
```bash
php artisan schedule:list
```

This will show all scheduled tasks including `binary:match`.

Test the command manually:
```bash
php artisan binary:match
```

---

## Method 2: Direct Cron Job (Alternative)

If you prefer to set up the cron job directly without Laravel's scheduler:

### For cPanel/Shared Hosting:

1. Log into cPanel
2. Go to **Cron Jobs**
3. Add new cron job with these settings:

**Option A: Run Every Hour**
- **Minute**: `0`
- **Hour**: `*`
- **Day**: `*`
- **Month**: `*`
- **Weekday**: `*`
- **Command**:
  ```bash
  cd /home/u727229935/domains/mlm.valuerkkda.com/public_html && /usr/bin/php artisan binary:match >> /home/u727229935/domains/mlm.valuerkkda.com/public_html/storage/logs/binary-matching.log 2>&1
  ```

**Option B: Run Every 15 Minutes**
- **Minute**: `*/15`
- **Hour**: `*`
- **Day**: `*`
- **Month**: `*`
- **Weekday**: `*`
- **Command**: (same as above)

**Option C: Run Every 5 Minutes**
- **Minute**: `*/5`
- **Hour**: `*`
- **Day**: `*`
- **Month**: `*`
- **Weekday**: `*`
- **Command**: (same as above)

### For SSH/VPS:

```bash
crontab -e
```

Add one of these entries:

**Every Hour:**
```
0 * * * * cd /home/u727229935/domains/mlm.valuerkkda.com/public_html && /usr/bin/php artisan binary:match >> /home/u727229935/domains/mlm.valuerkkda.com/public_html/storage/logs/binary-matching.log 2>&1
```

**Every 15 Minutes:**
```
*/15 * * * * cd /home/u727229935/domains/mlm.valuerkkda.com/public_html && /usr/bin/php artisan binary:match >> /home/u727229935/domains/mlm.valuerkkda.com/public_html/storage/logs/binary-matching.log 2>&1
```

**Every 5 Minutes:**
```
*/5 * * * * cd /home/u727229935/domains/mlm.valuerkkda.com/public_html && /usr/bin/php artisan binary:match >> /home/u727229935/domains/mlm.valuerkkda.com/public_html/storage/logs/binary-matching.log 2>&1
```

---

## Schedule Frequency Recommendations

Choose based on your business needs:

| Frequency | Use Case | Cron Expression |
|-----------|----------|----------------|
| **Every 5 minutes** | High volume, real-time matching | `*/5 * * * *` |
| **Every 15 minutes** | Medium volume, near real-time | `*/15 * * * *` |
| **Every 30 minutes** | Medium volume, regular updates | `*/30 * * * *` |
| **Every hour** | Low-medium volume, standard | `0 * * * *` |
| **Every 6 hours** | Low volume, batch processing | `0 */6 * * *` |
| **Daily** | Very low volume | `0 0 * * *` |

**Recommended**: Start with **every hour** and adjust based on your needs.

---

## Important Notes

### 1. PHP Path
The PHP path might be different on your server. Common paths:
- `/usr/bin/php`
- `/usr/local/bin/php`
- `/opt/cpanel/ea-php81/root/usr/bin/php` (cPanel with PHP 8.1)
- `php` (if in PATH)

To find your PHP path:
```bash
which php
# or
whereis php
```

### 2. Working Directory
Always use `cd` to change to your project directory before running the command. This ensures:
- Correct file paths
- Proper `.env` file loading
- Correct autoloader paths

### 3. Logging
The command already logs to Laravel's log file (`storage/logs/laravel.log`). The cron examples above also redirect output to a dedicated log file.

### 4. Preventing Overlaps
If using Method 1 (Laravel Scheduler), `withoutOverlapping()` prevents the command from running if a previous instance is still running.

For Method 2, you can add a lock file check:
```bash
[ ! -f /tmp/binary-match.lock ] && touch /tmp/binary-match.lock && cd /path/to/project && php artisan binary:match && rm /tmp/binary-match.lock
```

---

## Testing the Cron Job

### 1. Test Command Manually
```bash
cd /home/u727229935/domains/mlm.valuerkkda.com/public_html
php artisan binary:match
```

### 2. Check Logs
```bash
tail -f storage/logs/laravel.log
# or
tail -f storage/logs/binary-matching.log
```

### 3. Verify Cron is Running
Check if cron is executing:
```bash
grep CRON /var/log/syslog
# or check cPanel cron job logs
```

### 4. Monitor Execution
Add this to see when cron runs:
```bash
echo "Cron executed at $(date)" >> /path/to/cron-test.log
```

---

## Troubleshooting

### Issue: Cron not running
**Solutions**:
1. Verify cron service is running: `systemctl status cron`
2. Check cron logs: `/var/log/cron` or cPanel logs
3. Verify file permissions: `chmod +x artisan`
4. Test PHP path: Use full path to PHP

### Issue: Command not found
**Solutions**:
1. Use full path to PHP: `/usr/bin/php` or find with `which php`
2. Use full path to artisan: `/full/path/to/artisan`
3. Ensure you're in the correct directory

### Issue: Permission denied
**Solutions**:
1. Check file permissions: `chmod 755 artisan`
2. Check directory permissions: `chmod 755 /path/to/project`
3. Run as correct user (not root if possible)

### Issue: Database connection errors
**Solutions**:
1. Ensure `.env` file is readable
2. Check database credentials in `.env`
3. Verify database server is accessible

### Issue: Memory errors
**Solutions**:
1. Increase PHP memory limit in `.env`: `PHP_MEMORY_LIMIT=256M`
2. Optimize the command (use chunking - see analysis document)
3. Run during off-peak hours

---

## Advanced: Using Laravel Scheduler with Different Frequencies

If using Method 1, you can schedule multiple times:

```php
protected function schedule(Schedule $schedule): void
{
    // Run every hour
    $schedule->command('binary:match')
             ->hourly()
             ->withoutOverlapping()
             ->runInBackground();
    
    // Or run every 15 minutes
    $schedule->command('binary:match')
             ->everyFifteenMinutes()
             ->withoutOverlapping()
             ->runInBackground();
    
    // Or run at specific times
    $schedule->command('binary:match')
             ->dailyAt('02:00')
             ->withoutOverlapping();
    
    // Or run every 5 minutes during business hours
    $schedule->command('binary:match')
             ->everyFiveMinutes()
             ->between('9:00', '17:00')
             ->withoutOverlapping();
}
```

---

## Quick Setup Checklist

- [ ] Create/update `app/Console/Kernel.php` (Method 1)
- [ ] Add cron entry to server crontab
- [ ] Test command manually: `php artisan binary:match`
- [ ] Check logs for errors
- [ ] Verify cron is executing (check logs after scheduled time)
- [ ] Monitor first few runs to ensure everything works

---

## Summary

**Recommended Approach**: Use **Method 1 (Laravel Scheduler)** because:
- ✅ Better error handling
- ✅ Prevents overlapping executions
- ✅ Centralized scheduling management
- ✅ Better logging
- ✅ Easy to add more scheduled tasks later

**Quick Setup**:
1. Create `app/Console/Kernel.php` with the code above
2. Add single cron entry: `* * * * * cd /path/to/project && php artisan schedule:run`
3. Done! The scheduler will run `binary:match` hourly (or as configured)

