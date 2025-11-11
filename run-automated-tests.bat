@echo off
echo ===============================================
echo   ETCHON WATER RIDES - AUTOMATED TEST SUITE
echo ===============================================
echo.

echo [1/6] Running Authentication Tests...
php artisan test tests/Feature/AuthenticationAutomatedTest.php
echo.

echo [2/6] Running Access Control Tests...
php artisan test tests/Feature/AccessControlAutomatedTest.php
echo.

echo [3/6] Running Staff Management Tests...
php artisan test tests/Feature/StaffManagementAutomatedTest.php
echo.

echo [4/6] Running Ride Rentals Tests...
php artisan test tests/Feature/RideRentalsAutomatedTest.php
echo.

echo [5/6] Running Reports Tests...
php artisan test tests/Feature/ReportsAutomatedTest.php
echo.

echo [6/6] Running All Tests Summary...
php artisan test
echo.

echo ===============================================
echo   TEST SUITE COMPLETED
echo ===============================================
pause
