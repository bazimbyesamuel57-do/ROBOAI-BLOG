@REM  "C:\xampp\mysql\bin\mysqldump.exe" -u root roboai_blog_db > database.sql
@REM  "C:\xampp\mysql\bin\mysqldump.exe" -u root roboai_blog_db > database.sql
@REM  pause
@REM  "C:\xampp\mysql\bin\mysqldump.exe" -u root --port=3307 roboai_blog_db > database.sql
@REM  pause
@REM  "C:\xampp\mysql\bin\mysqldump.exe" -u root --port=3307 roboai_blog_db > database.sql 

@REM  file inside the folder
@REM  "C:\xampp\mysql\bin\mysqldump.exe" -u root --port=3307 roboai_blog_db > roboai_db/database.sql


@REM  this file allows for auto backup of the database through double clicking a this  file
@REM  the data will update it self




@echo off
cd /d "%~dp0"
if not exist roboai_db mkdir roboai_db
"C:\xampp\mysql\bin\mysqldump.exe" -u root --port=3307 --databases roboai_blog_db > roboai_db\database.sql
if errorlevel 1 (
    echo Backup FAILED. Is MySQL running on port 3307?
) else (
    echo Backup saved to roboai_db\database.sql
)
pause


