@echo off
cd /d "%~dp0"
echo This replaces the current roboai_blog_db with roboai_db\database.sql
echo Close this window to cancel.
pause
"C:\xampp\mysql\bin\mysql.exe" -u root --port=3307 < roboai_db\database.sql
if errorlevel 1 (
    echo Restore FAILED.
) else (
    echo Restore finished.
)
pause