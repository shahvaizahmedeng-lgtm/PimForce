# Laravel Scheduler Setup

This project has been configured with Laravel's task scheduler to run commands every 15 minutes.

## What's Been Set Up

1. **Console Kernel** (`app/Console/Kernel.php`) - Configures the scheduler
2. **Custom Command** (`app/Console/Commands/YourCustomCommand.php`) - Your command template
3. **Console Routes** (`routes/console.php`) - For console commands

## How to Use

### 1. Test Your Command Manually

```bash
php artisan your:custom-command
```

### 2. Set Up the Cron Job

Add this line to your server's crontab to run the scheduler every minute:

```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Customize Your Command

Edit `app/Console/Commands/YourCustomCommand.php` and add your logic in the `handle()` method.

### 4. Available Scheduling Methods

You can change the frequency in `app/Console/Kernel.php`:

```php
// Every 15 minutes (current)
$schedule->command('your:custom-command')->everyFifteenMinutes();

// Other options:
$schedule->command('your:custom-command')->everyMinute();
$schedule->command('your:custom-command')->hourly();
$schedule->command('your:custom-command')->daily();
$schedule->command('your:custom-command')->weekly();
$schedule->command('your:custom-command')->monthly();
$schedule->command('your:custom-command')->quarterly();
$schedule->command('your:custom-command')->yearly();

// Custom cron expression
$schedule->command('your:custom-command')->cron('*/15 * * * *');
```

### 5. View Scheduled Tasks

```bash
php artisan schedule:list
```

### 6. Test the Scheduler

```bash
php artisan schedule:run
```

## Example Use Cases

- Process integrations every 15 minutes
- Sync data between systems
- Send notifications
- Clean up temporary files
- Generate reports
- Update cache

## Important Notes

- The scheduler only runs when the cron job executes `php artisan schedule:run`
- Make sure your server has cron enabled
- Test your command manually before setting up the scheduler
- Consider logging and error handling in your command
