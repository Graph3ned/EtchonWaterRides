@echo off
echo ========================================
echo Building CSS and JavaScript Assets
echo ========================================
echo.

REM Check if node_modules exists
if not exist "node_modules" (
    echo Installing dependencies first...
    call npm install
    echo.
)

echo Building assets...
call npm run build

echo.
echo ========================================
echo Build Complete!
echo ========================================
echo.
echo Your CSS is now compiled and ready to use.
echo You can now run your Laravel app without 'npm run dev'
echo.
pause







