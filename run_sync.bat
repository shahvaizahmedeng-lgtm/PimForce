@echo off
REM Batch file to run katana_to_woo.py with logging

set timestamp=%date:~10,4%%date:~4,2%%date:~7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set timestamp=%timestamp: =0%
set logfile=sync_log_%timestamp%.txt

echo Running PimForce sync script...
echo Log file: %logfile%
echo.

python scripts/katana_to_woo.py --dry-run > %logfile% 2>&1

echo.
echo Sync completed. Check log file: %logfile%
pause
