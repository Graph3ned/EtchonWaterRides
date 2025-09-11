# Test Cases - Bogac Rides Management System

## Test Case Documentation

**Standard Expected Results Format:**
- ✅ **Success**: "System displays success message: [specific message]"
- ❌ **Error**: "System displays validation error message: [specific message]"
- 🔒 **Security**: "System blocks access and redirects to login"
- ⚠️ **Warning**: "System displays warning message: [specific message]"

| No. | Modules / Unit | Test Case | Steps | Cases |
|-----|----------------|-----------|-------|-------|
| 1 | **Login** | Valid Login Credentials | 1. Open login page<br>2. Enter valid username<br>3. Enter valid password<br>4. Click Login button | Username: `admin1`<br>Password: `Admin@123`<br>✅ Expected: System displays success message: "Login successful" and redirects to dashboard |
| 2 | **Login** | Invalid Username | 1. Open login page<br>2. Enter invalid username<br>3. Enter valid password<br>4. Click Login button | Username: `wronguser`<br>Password: `Admin@123`<br>❌ Expected: System displays validation error message: "These credentials do not match our records" |
| 3 | **Login** | Invalid Password | 1. Open login page<br>2. Enter valid username<br>3. Enter wrong password<br>4. Click Login button | Username: `admin1`<br>Password: `wrong123`<br>❌ Expected: System displays validation error message: "These credentials do not match our records" |
| 4 | **Login** | Empty Fields | 1. Open login page<br>2. Leave username blank<br>3. Leave password blank<br>4. Click Login button | Username: `(empty)`<br>Password: `(empty)`<br>❌ Expected: System displays validation error message: "Username is required" and "Password is required" |
| 5 | **Login** | SQL Injection Attempt | 1. Open login page<br>2. Enter SQL injection in username<br>3. Enter any password<br>4. Click Login button | Username: `' OR '1'='1`<br>Password: `any`<br>❌ Expected: System displays validation error message: "These credentials do not match our records" |
| 6 | **Login** | Session Management | 1. Login successfully<br>2. Logout<br>3. Press browser back button<br>4. Try to access dashboard | 🔒 Expected: System blocks access and redirects to login page |
| 7 | **Staff Management** | Add Staff (Valid) | 1. Admin → Staff Management<br>2. Click "Add Staff"<br>3. Enter valid details<br>4. Click Save | Name: `John Doe`<br>Email: `john@mail.com`<br>Password: `Staff@123`<br>UserType: Staff |
| 8 | **Staff Management** | Add Staff (Duplicate Email) | 1. Admin → Staff Management<br>2. Click "Add Staff"<br>3. Enter existing email<br>4. Click Save | Email: `admin@mail.com`<br>Expected: Show "Email already exists" error |
| 9 | **Staff Management** | Invalid Email Format | 1. Admin → Staff Management<br>2. Click "Add Staff"<br>3. Enter invalid email format<br>4. Click Save | Email: `invalidmail.com`<br>Expected: Show email validation error |
| 10 | **Staff Management** | Weak Password | 1. Admin → Staff Management<br>2. Click "Add Staff"<br>3. Enter short password<br>4. Click Save | Password: `12345`<br>Expected: Show password strength error |
| 11 | **Staff Management** | Edit Staff | 1. Admin → Staff Management<br>2. Click "Edit" on staff<br>3. Modify name, email, username, userType<br>4. Click Save | Name: `John Doe` → `John Smith`<br>Email: `john@mail.com` → `johnnew@mail.com`<br>Username: `john1` → `johnsmith`<br>UserType: `Staff` → `Admin`<br>✅ Expected: Staff updated successfully |
| 12 | **Staff Management** | Delete Staff | 1. Admin → Staff Management<br>2. Click "Delete" on staff<br>3. Confirm deletion<br>4. Verify deletion | Staff: `John Doe`<br>Expected: Staff removed from list |
| 13 | **Ride Rentals** | Start Rental | 1. Staff → Add Ride<br>2. Select ride type<br>3. Select classification<br>4. Enter duration<br>5. Enter start time<br>6. Click Submit | Ride Type: `Water_Ride`<br>Classification: `Kayak`<br>Duration: `120 minutes`<br>Start Time: `09:00:00` |
| 14 | **Ride Rentals** | End Rental & Billing | 1. Staff → Edit Ride<br>2. Select ongoing rental<br>3. Update end time<br>4. Change status to completed<br>5. Verify total price calculation | Duration: `2 hours`<br>Price/Hour: `₱500`<br>Expected: Total = `₱1000` |
| 15 | **Ride Rentals** | Life Jacket Usage | 1. Staff → Add Ride<br>2. Enter life jacket count<br>3. Submit rental<br>4. Verify tracking | Life Jackets: `3`<br>Expected: Counted in safety report |
| 16 | **Ride Rentals** | Custom Duration | 1. Staff → Add Ride<br>2. Select "Custom Duration"<br>3. Enter custom minutes<br>4. Verify calculation | Custom Duration: `90 minutes`<br>Expected: Price calculated correctly |
| 17 | **Sales Reports** | Generate Daily Report | 1. Admin → Sales<br>2. Select "Today" filter<br>3. Click "Generate Report"<br>4. Download CSV | Date: `2025-09-10`<br>Expected: CSV with today's sales |
| 18 | **Sales Reports** | Generate Weekly Report | 1. Admin → Sales<br>2. Select "This Week" filter<br>3. Click "Generate Report"<br>4. Download CSV | Week: `Sept 1-7, 2025`<br>Expected: CSV with weekly sales |
| 19 | **Sales Reports** | Generate Monthly Report | 1. Admin → Sales<br>2. Select "This Month" filter<br>3. Click "Generate Report"<br>4. Download CSV | Month: `Sept 2025`<br>Expected: CSV with monthly sales |
| 20 | **Sales Reports** | Filter by Staff | 1. Admin → Sales<br>2. Select specific staff member<br>3. Apply filter<br>4. View results | Staff: `John Doe`<br>Expected: Only John's rentals shown |
| 21 | **Sales Reports** | Filter by Ride Type | 1. Admin → Sales<br>2. Select ride type<br>3. Apply filter<br>4. View results | Ride Type: `Water_Ride`<br>Expected: Only water rides shown |
| 22 | **Sales Reports** | Empty Report | 1. Admin → Sales<br>2. Select date with no rentals<br>3. Generate report<br>4. Verify empty result | Date: `2025-09-12`<br>Expected: Empty CSV file |
| 23 | **Ride Types Management** | Add Ride Type | 1. Admin → Rides Rate<br>2. Click "Add New Ride"<br>3. Enter ride type<br>4. Enter classification<br>5. Set price per hour<br>6. Save | Ride Type: `Jet_Ski`<br>Classification: `Standard`<br>Price: `₱800/hour` |
| 24 | **Ride Types Management** | Edit Ride Type | 1. Admin → Rides Rate<br>2. Click "Edit" on ride type<br>3. Modify details<br>4. Save changes | Change: `Jet_Ski` → `Speed_Boat`<br>Expected: Updated in system |
| 25 | **Ride Types Management** | Delete Ride Type | 1. Admin → Rides Rate<br>2. Click "Delete" on ride type<br>3. Confirm deletion<br>4. Verify removal | Ride Type: `Jet_Ski`<br>Expected: Removed from system |
| 26 | **Ride Types Management** | Add Classification | 1. Admin → View Details<br>2. Click "Add Classification"<br>3. Enter classification name<br>4. Set price<br>5. Save | Classification: `Premium`<br>Price: `₱1000/hour` |
| 27 | **Ride Types Management** | Edit Classification | 1. Admin → View Details<br>2. Click "Edit" on classification<br>3. Modify price<br>4. Save | Change: `Premium` → `VIP`<br>Price: `₱1200/hour` |
| 28 | **Ride Types Management** | Delete Classification | 1. Admin → View Details<br>2. Click "Delete" on classification<br>3. Confirm deletion<br>4. Verify removal | Classification: `VIP`<br>Expected: Removed from system |
| 29 | **Password Reset** | Forgot Password (Valid) | 1. Click "Forgot Password"<br>2. Enter username<br>3. Enter email<br>4. Click "Send Reset Link" | Username: `admin1`<br>Email: `admin@mail.com`<br>Expected: Reset email sent |
| 30 | **Password Reset** | Forgot Password (Invalid) | 1. Click "Forgot Password"<br>2. Enter wrong username<br>3. Enter wrong email<br>4. Click "Send Reset Link" | Username: `wronguser`<br>Email: `wrong@mail.com`<br>Expected: Error message |
| 31 | **Password Reset** | Reset Password (Valid) | 1. Click reset link in email<br>2. Enter new password<br>3. Confirm password<br>4. Click "Reset Password" | New Password: `NewPass@123`<br>Confirm: `NewPass@123`<br>Expected: Password updated |
| 32 | **Password Reset** | Reset Password (Mismatch) | 1. Click reset link in email<br>2. Enter new password<br>3. Enter different confirmation<br>4. Click "Reset Password" | Password: `NewPass@123`<br>Confirm: `Different@123`<br>Expected: Validation error |
| 33 | **Profile Management (Admin)** | Update Admin Profile (No Email Change) | 1. Login as Admin<br>2. Click Profile menu<br>3. Update name/username only<br>4. Enter current password<br>5. Click Save<br>6. Verify changes | Name: `Admin User` → `Admin Manager`<br>Username: `admin1` → `admin_mgr`<br>Current Password: `Admin@123`<br>✅ Expected: System displays success message: "Profile updated successfully" |
| 34 | **Profile Management (Admin)** | Admin Email Change with OTP | 1. Login as Admin<br>2. Click Profile menu<br>3. Change email address<br>4. Enter current password<br>5. Click Save<br>6. Verify OTP field appears<br>7. Check email for OTP<br>8. Enter OTP code<br>9. Submit | New Email: `newadmin@mail.com`<br>Current Password: `Admin@123`<br>OTP: `123456`<br>✅ Expected: System displays success message: "Email updated successfully" |
| 35 | **Profile Management (Admin)** | Admin Invalid OTP | 1. Login as Admin<br>2. Change email address<br>3. Enter current password<br>4. Click Save<br>5. Enter wrong OTP<br>6. Submit | OTP: `999999`<br>❌ Expected: System displays validation error message: "Invalid OTP code" |
| 36 | **Profile Management (Staff)** | Update Staff Profile | 1. Login as Staff<br>2. Click Profile menu<br>3. Update username only<br>4. Click Save<br>5. Verify changes | Username: `staff1` → `staff_user`<br>✅ Expected: System displays success message: "Profile updated successfully" |
| 37 | **Profile Management (Staff)** | Staff Change Password | 1. Login as Staff<br>2. Click Profile menu<br>3. Enter current password<br>4. Enter new password<br>5. Confirm new password<br>6. Click Save | Current: `Staff@123`<br>New: `NewStaff@456`<br>✅ Expected: System displays success message: "Password updated successfully" |
| 38 | **Profile Management (Staff)** | Staff Password Mismatch | 1. Login as Staff<br>2. Click Profile menu<br>3. Enter different passwords<br>4. Click Save | New: `NewPass@123`<br>Confirm: `Different@456`<br>❌ Expected: System displays validation error message: "Password confirmation does not match" |
| 39 | **Profile Management (Admin)** | Admin Change Password | 1. Login as Admin<br>2. Click Profile menu<br>3. Enter current password<br>4. Enter new password<br>5. Confirm new password<br>6. Click Save | Current: `Admin@123`<br>New: `NewAdmin@456`<br>Confirm: `NewAdmin@456`<br>✅ Expected: System displays success message: "Password updated successfully" |
| 40 | **Profile Management (Staff)** | Staff Cannot Change Email | 1. Login as Staff<br>2. Click Profile menu<br>3. Verify no email change option | Expected: No email change form visible for staff users |
| 41 | **Staff Dashboard** | Staff Dashboard Access | 1. Login as Staff<br>2. Verify dashboard loads<br>3. Check available functions | ✅ Expected: Dashboard shows Add Ride, Edit Ride, Delete Ride, Profile options only |
| 42 | **Staff Dashboard** | Staff Add Ride Function | 1. Login as Staff<br>2. Click "Add Ride"<br>3. Verify form loads<br>4. Test ride creation | ✅ Expected: Staff can create new ride rentals |
| 43 | **Staff Dashboard** | Staff Edit Ride Function | 1. Login as Staff<br>2. Click "Edit Ride"<br>3. Select existing rental<br>4. Modify details<br>5. Save changes | ✅ Expected: Staff can edit existing ride rentals |
| 44 | **Staff Dashboard** | Staff Delete Ride Function | 1. Login as Staff<br>2. Click "Delete Ride"<br>3. Select rental to delete<br>4. Confirm deletion | ✅ Expected: Staff can delete ride rentals |
| 45 | **Staff Dashboard** | Staff Profile Management | 1. Login as Staff<br>2. Click "Profile"<br>3. Verify available fields | ✅ Expected: Only name, username, and password fields visible |
| 46 | **Staff Dashboard** | Staff Cannot Access Admin Functions | 1. Login as Staff<br>2. Try to access admin pages<br>3. Verify access denied | ❌ Expected: Staff cannot access admin-only functions |
| 47 | **Staff Profile Management** | Staff Update Name | 1. Login as Staff<br>2. Click Profile<br>3. Update name only<br>4. Save changes | Name: `John Doe` → `John Smith`<br>✅ Expected: Name updated successfully |
| 48 | **Staff Profile Management** | Staff Update Username | 1. Login as Staff<br>2. Click Profile<br>3. Update username only<br>4. Save changes | Username: `staff1` → `staff_user`<br>✅ Expected: Username updated successfully |
| 49 | **Staff Profile Management** | Staff Update Password | 1. Login as Staff<br>2. Click Profile<br>3. Enter current password<br>4. Enter new password<br>5. Confirm new password<br>6. Save changes | Current: `Staff@123`<br>New: `NewStaff@456`<br>✅ Expected: Password updated successfully |
| 50 | **Staff Profile Management** | Staff Cannot Edit Email | 1. Login as Staff<br>2. Click Profile<br>3. Verify email field is read-only | ❌ Expected: Email field is disabled/read-only |
| 51 | **Staff Profile Management** | Staff No OTP Required | 1. Login as Staff<br>2. Click Profile<br>3. Make changes<br>4. Save without OTP | ✅ Expected: Changes saved without OTP verification |
| 52 | **Profile Management (Admin)** | Admin Invalid Current Password | 1. Login as Admin<br>2. Click Profile menu<br>3. Enter wrong current password<br>4. Click Save | Current Password: `WrongPass@123`<br>❌ Expected: System displays validation error message: "Current password is incorrect" |
| 53 | **Profile Management (Admin)** | Admin OTP Expired | 1. Login as Admin<br>2. Change email address<br>3. Enter current password<br>4. Click Save<br>5. Wait for OTP to expire<br>6. Enter OTP code | OTP: `123456` (expired)<br>❌ Expected: System displays validation error message: "OTP code has expired" |
| 54 | **Profile Management (Admin)** | Admin OTP Field Appears | 1. Login as Admin<br>2. Change email address<br>3. Enter current password<br>4. Click Save<br>5. Verify OTP field is shown | ✅ Expected: OTP input field appears after validation |
| 55 | **Activity Logs** | View All Logs | 1. Admin → Activity Logs<br>2. Select "Show All Logs"<br>3. Verify all log entries displayed | ✅ Expected: All log entries are displayed with timestamps and details |
| 56 | **Activity Logs** | Filter - Show Deleted Records | 1. Admin → Activity Logs<br>2. Select "Show Deleted Records"<br>3. Verify only deletion logs shown | ✅ Expected: Only records with "deleted" action are displayed |
| 57 | **Activity Logs** | Filter - Show Edited Records | 1. Admin → Activity Logs<br>2. Select "Show Edited Records"<br>3. Verify only edit logs shown | ✅ Expected: Only records with "edited" action are displayed |
| 58 | **Activity Logs** | Filter - Show Created Records | 1. Admin → Activity Logs<br>2. Select "Show Created Records"<br>3. Verify only creation logs shown | ✅ Expected: Only records with "created" action are displayed |
| 59 | **Activity Logs** | Log Entry Details | 1. Admin → Activity Logs<br>2. Click on any log entry<br>3. View detailed information | ✅ Expected: Detailed log information displayed (user, action, timestamp, changes) |
| 61 | **Activity Logs** | Logs with No Data | 1. Admin → Activity Logs<br>2. Select filter with no matching records<br>3. Verify empty state | ✅ Expected: "No logs found" message displayed |
| 62 | **Security** | HTTPS Enforcement | 1. Access system using HTTP<br>2. Verify redirect | 🔒 Expected: System blocks access and redirects to HTTPS |
| 63 | **Security** | Unauthorized Access | 1. Access admin page without login<br>2. Verify redirect | 🔒 Expected: System blocks access and redirects to login page |
| 64 | **Security** | Password Encryption | 1. Check database password storage<br>2. Verify hashed passwords | ✅ Expected: System displays success message: "Passwords stored as hash, not plain text" |
| 65 | **Security** | Session Timeout | 1. Login to system<br>2. Stay idle for 30 minutes<br>3. Try to perform action | 🔒 Expected: System blocks access and redirects to login page |
| 66 | **Security** | Role-based Access | 1. Login as Staff<br>2. Try to access admin pages<br>3. Verify access denied | 🔒 Expected: System blocks access and redirects to login page |
| 67 | **Data Validation** | Invalid Duration | 1. Staff → Add Ride<br>2. Enter negative duration<br>3. Submit | Duration: `-60`<br>❌ Expected: System displays validation error message: "Duration must be greater than 0" |
| 68 | **Data Validation** | Invalid Time Format | 1. Staff → Add Ride<br>2. Enter invalid time format<br>3. Submit | Time: `25:00:00`<br>❌ Expected: System displays validation error message: "Time format is invalid" |
| 69 | **Data Validation** | Required Fields | 1. Staff → Add Ride<br>2. Leave required fields empty<br>3. Submit | ❌ Expected: System displays validation error message: "Required fields cannot be empty" |
| 70 | **Performance** | Large Dataset | 1. Generate report with 1000+ records<br>2. Measure response time<br>3. Verify performance | ✅ Expected: Response time < 5 seconds, system handles large dataset |
| 71 | **Performance** | Concurrent Users | 1. Multiple users login simultaneously<br>2. Perform operations<br>3. Verify system stability | ✅ Expected: System handles concurrent access without issues |
| 72 | **Error Handling** | Database Connection Error | 1. Simulate database down<br>2. Try to perform operation<br>3. Verify error handling | ⚠️ Expected: System displays warning message: "Database connection error. Please try again later" |
| 73 | **Error Handling** | Network Timeout | 1. Simulate slow network<br>2. Perform operation<br>3. Verify timeout handling | ⚠️ Expected: System displays warning message: "Request timeout. Please try again" |
| 74 | **Password Reset** | Expired Reset Token | 1. Request password reset<br>2. Wait 24+ hours<br>3. Click reset link<br>4. Try to reset password | ❌ Expected: System displays validation error message: "Invalid or expired reset token" |
| 75 | **Ride Rentals** | Overlapping Time Slots | 1. Staff creates rental 09:00-11:00<br>2. Another staff tries same ride 10:00-12:00<br>3. Submit second rental | ❌ Expected: System displays validation error message: "Ride is already booked during this time slot" |
| 76 | **Ride Rentals** | Invalid Duration (Text) | 1. Staff → Add Ride<br>2. Enter text in duration field<br>3. Submit | Duration: `abc`<br>❌ Expected: System displays validation error message: "Duration must be a number" |
| 78 | **Ride Rentals** | Invalid Price (Text) | 1. Admin → Edit Price<br>2. Enter text in price field<br>3. Save | Price: `abc`<br>❌ Expected: System displays validation error message: "Price must be a number" |
| 79 | **Ride Rentals** | Negative Price | 1. Admin → Edit Price<br>2. Enter negative price<br>3. Save | Price: `-1000`<br>❌ Expected: System displays validation error message: "Price must be greater than 0" |
| 80 | **Sales Reports** | Boundary Date (Month Start) | 1. Admin → Sales<br>2. Select first day of month<br>3. Generate report | Date: `2025-09-01`<br>✅ Expected: System displays success message: "Report generated successfully" |
| 81 | **Sales Reports** | Boundary Date (Month End) | 1. Admin → Sales<br>2. Select last day of month<br>3. Generate report | Date: `2025-09-30`<br>✅ Expected: System displays success message: "Report generated successfully" |
| 82 | **Security** | XSS Prevention | 1. Staff → Add Ride<br>2. Enter script in note field<br>3. Submit | Note: `<script>alert('XSS')</script>`<br>✅ Expected: Script is sanitized and displayed as plain text |
| 83 | **Security** | CSRF Protection | 1. Login to system<br>2. Copy form data<br>3. Submit from external source | ❌ Expected: System displays validation error message: "CSRF token mismatch" |
| 84 | **Performance** | Large Dataset (5000+ records) | 1. Generate report with 5000+ records<br>2. Measure response time<br>3. Verify performance | ✅ Expected: Response time < 10 seconds, memory usage < 512MB |
| 85 | **Performance** | High Concurrent Users | 1. 50+ users login simultaneously<br>2. Perform operations<br>3. Verify system stability | ✅ Expected: System handles 50+ concurrent users without crashes |
| 86 | **Performance** | Memory Usage Under Load | 1. Generate multiple large reports<br>2. Monitor memory usage<br>3. Verify no memory leaks | ✅ Expected: Memory usage remains stable, no memory leaks |
| 87 | **Mobile Responsiveness** | Small Screen Login | 1. Access login on mobile device<br>2. Test form usability<br>3. Verify responsive design | ✅ Expected: Login form is fully functional on 320px width |
| 88 | **Mobile Responsiveness** | Mobile Dashboard | 1. Login on mobile<br>2. Navigate dashboard<br>3. Test all functions | ✅ Expected: All dashboard functions work on mobile |
| 89 | **Data Integrity** | Concurrent Edit Prevention | 1. Two users edit same rental<br>2. Both save changes<br>3. Verify data integrity | ✅ Expected: Last save wins, no data corruption |
| 90 | **Data Integrity** | Database Transaction Rollback | 1. Simulate database error during save<br>2. Verify transaction rollback<br>3. Check data consistency | ✅ Expected: Partial data is rolled back, no incomplete records |
| 91 | **Pagination** | Default Pagination (10 items) | 1. Admin → Sales<br>2. Verify default page shows 10 items<br>3. Check pagination controls | ✅ Expected: Shows 10 items per page, pagination controls visible |
| 92 | **Pagination** | Change Items Per Page | 1. Admin → Sales<br>2. Change pagination to 25 items<br>3. Verify display updates | ✅ Expected: Shows 25 items per page, pagination updates |
| 93 | **Pagination** | Navigate to Next Page | 1. Admin → Sales<br>2. Click "Next" button<br>3. Verify page changes | ✅ Expected: Navigates to page 2, shows different items |
| 94 | **Pagination** | Navigate to Previous Page | 1. Admin → Sales<br>2. Go to page 2<br>3. Click "Previous" button | ✅ Expected: Navigates back to page 1 |
| 95 | **Pagination** | Navigate to Specific Page | 1. Admin → Sales<br>2. Click page number "3"<br>3. Verify page loads | ✅ Expected: Navigates to page 3, shows correct items |
| 96 | **Pagination** | Last Page Navigation | 1. Admin → Sales<br>2. Navigate to last page<br>3. Verify no "Next" button | ✅ Expected: Shows last page, "Next" button disabled/hidden |
| 97 | **Pagination** | First Page Navigation | 1. Admin → Sales<br>2. Go to page 2<br>3. Navigate back to page 1 | ✅ Expected: Shows first page, "Previous" button disabled/hidden |
| 98 | **Pagination** | Pagination with Staff Filter | 1. Admin → Sales<br>2. Apply staff filter<br>3. Verify pagination works with filtered data | ✅ Expected: Pagination works correctly with staff-filtered results |
| 99 | **Pagination** | Pagination with Ride Type Filter | 1. Admin → Sales<br>2. Apply ride type filter<br>3. Navigate through pages | ✅ Expected: Pagination works with ride type-filtered data |
| 100 | **Pagination** | Pagination with Classification Filter | 1. Admin → Sales<br>2. Apply classification filter<br>3. Navigate through pages | ✅ Expected: Pagination works with classification-filtered data |
| 101 | **Pagination** | Pagination with Date Range Filter | 1. Admin → Sales<br>2. Select "This Month"<br>3. Navigate through pages | ✅ Expected: Pagination works with date-filtered data |
| 102 | **Pagination** | Large Dataset Pagination | 1. Generate 100+ rental records<br>2. Test pagination performance<br>3. Verify smooth navigation | ✅ Expected: Pagination handles large datasets efficiently |
| 103 | **Filter Options** | Staff Filter - All Staff | 1. Admin → Sales<br>2. Select "All Staff"<br>3. Verify all staff rentals shown | ✅ Expected: Shows rentals from all staff members |
| 104 | **Filter Options** | Staff Filter - Specific Staff | 1. Admin → Sales<br>2. Select specific staff member<br>3. Verify only their rentals shown | ✅ Expected: Shows only rentals from selected staff |
| 105 | **Filter Options** | Ride Type Filter - All Types | 1. Admin → Sales<br>2. Select "All Types"<br>3. Verify all ride types shown | ✅ Expected: Shows rentals from all ride types |
| 106 | **Filter Options** | Ride Type Filter - Specific Type | 1. Admin → Sales<br>2. Select specific ride type<br>3. Verify only that type shown | ✅ Expected: Shows only rentals from selected ride type |
| 107 | **Filter Options** | Classification Filter - All Classifications | 1. Admin → Sales<br>2. Select "All Classifications"<br>3. Verify all classifications shown | ✅ Expected: Shows rentals from all classifications |
| 108 | **Filter Options** | Classification Filter - Specific Classification | 1. Admin → Sales<br>2. Select specific classification<br>3. Verify only that classification shown | ✅ Expected: Shows only rentals from selected classification |
| 109 | **Filter Options** | Date Range - Today | 1. Admin → Sales<br>2. Select "Today"<br>3. Verify only today's rentals shown | ✅ Expected: Shows only rentals from today |
| 110 | **Filter Options** | Date Range - Select Day | 1. Admin → Sales<br>2. Select specific date<br>3. Verify only that day's rentals shown | ✅ Expected: Shows only rentals from selected date |
| 111 | **Filter Options** | Date Range - Select Month | 1. Admin → Sales<br>2. Select specific month<br>3. Verify only that month's rentals shown | ✅ Expected: Shows only rentals from selected month |
| 112 | **Filter Options** | Date Range - Current Month | 1. Admin → Sales<br>2. Select "Current Month"<br>3. Verify current month rentals shown | ✅ Expected: Shows only rentals from current month |
| 113 | **Filter Options** | Date Range - Custom Date Range | 1. Admin → Sales<br>2. Select "Custom"<br>3. Set start and end dates<br>4. Verify range rentals shown | ✅ Expected: Shows only rentals within custom date range |
| 114 | **Filter Options** | Reset Filter | 1. Admin → Sales<br>2. Apply multiple filters<br>3. Click "Reset Filter"<br>4. Verify all filters cleared | ✅ Expected: All filters reset to default, all data shown |
| 115 | **Filter Options** | Multiple Filters Combined | 1. Admin → Sales<br>2. Apply staff + ride type + date filters<br>3. Verify combined results | ✅ Expected: Shows rentals matching all selected filters |
| 116 | **Filter Options** | Filter with No Results | 1. Admin → Sales<br>2. Apply filters with no matching data<br>3. Verify empty state | ✅ Expected: Shows "No rentals found" message |
| 117 | **Rides Rate Page** | Add New Water Ride Modal | 1. Admin → Rides Rate<br>2. Click "Add New Water Ride"<br>3. Fill ride type, classification, price<br>4. Save | Ride Type: `Kayak`<br>Classification: `Standard`<br>Price: `₱500/hour`<br>✅ Expected: New ride type created successfully |
| 118 | **Rides Rate Page** | View Details Navigation | 1. Admin → Rides Rate<br>2. Click "View Details" on ride type<br>3. Verify details page loads | ✅ Expected: Shows classifications for selected ride type |
| 119 | **Rides Rate Page** | Back to Prices Button | 1. Admin → View Details<br>2. Click "Back to Prices"<br>3. Verify returns to main page | ✅ Expected: Returns to Rides Rate main page |
| 120 | **Rides Rate Page** | Add Classification Modal | 1. Admin → View Details<br>2. Click "Add Classification"<br>3. Fill classification and price<br>4. Save | Classification: `Premium`<br>Price: `₱800/hour`<br>✅ Expected: New classification added |
| 121 | **Rides Rate Page** | Edit Ride Type Button | 1. Admin → View Details<br>2. Click "Edit" on ride type<br>3. Modify ride type name<br>4. Save | Change: `Kayak` → `Canoe`<br>✅ Expected: Ride type name updated |
| 122 | **Rides Rate Page** | Delete Ride Type Button | 1. Admin → View Details<br>2. Click "Delete" on ride type<br>3. Confirm deletion<br>4. Verify removal | ✅ Expected: Ride type and all classifications deleted |
| 123 | **Rides Rate Page** | Edit Classification Button | 1. Admin → View Details<br>2. Click "Edit" on classification<br>3. Modify classification details<br>4. Save | Change: `Standard` → `Basic`<br>Price: `₱400/hour`<br>✅ Expected: Classification updated |
| 124 | **Rides Rate Page** | Delete Classification Button | 1. Admin → View Details<br>2. Click "Delete" on classification<br>3. Confirm deletion<br>4. Verify removal | ✅ Expected: Classification deleted from ride type |
| 125 | **Rides Rate Page** | Add Classification - Ride Type Locked | 1. Admin → View Details<br>2. Click "Add Classification"<br>3. Verify ride type field is not editable | ✅ Expected: Ride type field is read-only/disabled |
| 126 | **Rides Rate Page** | Edit Ride Type - Only Name Editable | 1. Admin → View Details<br>2. Click "Edit" on ride type<br>3. Verify only ride type name is editable | ✅ Expected: Only ride type name field is editable |
| 127 | **Staff Management** | Edit Staff Username - Valid Change | 1. Admin → Staff Management<br>2. Click "Edit" on staff<br>3. Change username to new unique value<br>4. Save changes | Username: `john1` → `johnsmith`<br>✅ Expected: Username updated successfully |
| 128 | **Staff Management** | Edit Staff Username - Duplicate Username | 1. Admin → Staff Management<br>2. Click "Edit" on staff<br>3. Enter existing username<br>4. Try to save | Username: `john1` → `admin1`<br>❌ Expected: System displays validation error: "Username already exists" |
| 129 | **Staff Management** | Edit Staff Username - Invalid Format | 1. Admin → Staff Management<br>2. Click "Edit" on staff<br>3. Enter invalid username format<br>4. Try to save | Username: `john1` → `john smith`<br>❌ Expected: System displays validation error: "Username format is invalid" |
| 130 | **Staff Management** | Edit Staff Username - Empty Username | 1. Admin → Staff Management<br>2. Click "Edit" on staff<br>3. Clear username field<br>4. Try to save | Username: `john1` → ``<br>❌ Expected: System displays validation error: "Username is required" |
| 131 | **Ride Time Alert** | Timeout Alert Trigger | 1. Staff → Add Ride<br>2. Set duration and start time<br>3. Wait for end time to match current time<br>4. Verify alert triggers | End Time: `14:30:00`<br>Current Time: `14:30:00`<br>✅ Expected: Modal popup shows "Ride Time Alert!" with ride details |
| 132 | **Ride Time Alert** | Sound Alarm Playback | 1. Staff → Add Ride<br>2. Wait for timeout alert<br>3. Verify sound plays | Alert triggered at end time<br>✅ Expected: System plays alarm.mp3 sound continuously |
| 133 | **Ride Time Alert** | Alert Modal Display | 1. Staff → Add Ride<br>2. Wait for timeout alert<br>3. Check modal content | Ride Type: `Kayak`<br>Classification: `Standard`<br>✅ Expected: Modal shows "Kayak Standard has ended" |
| 134 | **Ride Time Alert** | Stop Alarm Function | 1. Staff → View timeout alert<br>2. Click "Confirm & Stop Alarm"<br>3. Verify alert stops | Alert modal is open<br>✅ Expected: Modal closes and sound stops playing |
| 135 | **Ride Time Alert** | Real-time Countdown | 1. Staff → Add Ride<br>2. Check remaining time display<br>3. Wait and verify updates | Duration: `60 minutes`<br>✅ Expected: Remaining time updates every second showing countdown |
| 136 | **Ride Time Alert** | Mark as Done Function | 1. Staff → View ongoing ride<br>2. Click "Mark as Done" button<br>3. Verify status change | Ride status: `Ongoing`<br>✅ Expected: Ride marked as done, remaining time shows "Ended" |

## Test Data

### Valid Test Users
- **Admin User**: Username: `admin1`, Email: `admin@mail.com`, Password: `Admin@123`
- **Staff User**: Username: `staff1`, Email: `staff@mail.com`, Password: `Staff@123`

### Sample Ride Types
- **Water_Ride**: Kayak (₱500/hr), Paddle_Boat (₱400/hr), Jet_Ski (₱800/hr)
- **Land_Ride**: Bicycle (₱200/hr), Scooter (₱300/hr)

### Test Scenarios
- **Duration**: 30, 60, 90, 120 minutes
- **Life Jackets**: 0, 1, 2, 3, 4
- **Time Ranges**: 09:00-17:00 (business hours)
- **Status**: 0 (Ongoing), 1 (Completed)

## Test Execution Summary

### **Test Coverage by Module:**
| Module | Total Tests | Critical | High | Medium | Low |
|--------|-------------|----------|------|--------|-----|
| **Login & Authentication** | 8 | 6 | 2 | 0 | 0 |
| **Staff Management** | 6 | 4 | 2 | 0 | 0 |
| **Ride Rentals** | 8 | 5 | 2 | 1 | 0 |
| **Sales Reports** | 6 | 3 | 2 | 1 | 0 |
| **Ride Types Management** | 6 | 4 | 2 | 0 | 0 |
| **Password Reset** | 4 | 3 | 1 | 0 | 0 |
| **Profile Management** | 12 | 8 | 4 | 0 | 0 |
| **Staff Dashboard** | 6 | 4 | 2 | 0 | 0 |
| **Staff Profile Management** | 5 | 4 | 1 | 0 | 0 |
| **Activity Logs** | 6 | 4 | 2 | 0 | 0 |
| **Security** | 6 | 6 | 0 | 0 | 0 |
| **Data Validation** | 5 | 3 | 2 | 0 | 0 |
| **Performance** | 4 | 2 | 2 | 0 | 0 |
| **Error Handling** | 2 | 1 | 1 | 0 | 0 |
| **Mobile Responsiveness** | 2 | 1 | 1 | 0 | 0 |
| **Data Integrity** | 2 | 2 | 0 | 0 | 0 |
| **Pagination** | 10 | 8 | 2 | 0 | 0 |
| **Filter Options** | 14 | 12 | 2 | 0 | 0 |
| **Rides Rate Page** | 10 | 8 | 2 | 0 | 0 |
| **Ride Time Alert** | 6 | 4 | 2 | 0 | 0 |
| **TOTAL** | **132** | **95** | **33** | **2** | **0** |

### **Priority Levels:**
- **🔴 Critical (95 tests)**: Core functionality, security, data integrity
- **🟡 High (33 tests)**: Important features, edge cases
- **🟢 Medium (2 tests)**: Nice-to-have features
- **⚪ Low (0 tests)**: Cosmetic issues

### **Expected Results Summary**

| Module | Success Criteria | Failure Criteria |
|--------|------------------|------------------|
| **Login** | ✅ Successful authentication, proper redirects | ❌ Invalid credentials rejected, SQL injection blocked |
| **Staff Management** | ✅ CRUD operations work, validation enforced | ❌ Duplicate emails blocked, weak passwords rejected |
| **Ride Rentals** | ✅ Accurate calculations, proper tracking | ❌ Invalid data rejected, calculations incorrect |
| **Sales Reports** | ✅ Accurate data, proper filtering | ❌ Missing data, incorrect calculations |
| **Ride Types** | ✅ CRUD operations work, pricing accurate | ❌ Deletion of active types blocked |
| **Security** | ✅ Proper access control, data protection | ❌ Unauthorized access, data exposure |
| **Performance** | ✅ Fast response times, stable operation | ❌ Slow responses, system crashes |

### **Test Execution Guidelines**

#### **Pre-Test Setup:**
1. **Environment**: Use dedicated test environment (not production)
2. **Data Reset**: Clean database before each test run
3. **User Accounts**: Create test users with known credentials
4. **Test Data**: Prepare sample ride types, classifications, and rentals

#### **Test Execution Order:**
1. **Phase 1**: Critical tests (Login, Security, Data Validation)
2. **Phase 2**: Core functionality (Ride Rentals, Staff Management)
3. **Phase 3**: Advanced features (Reports, Performance)
4. **Phase 4**: Edge cases and error handling

#### **Success Criteria:**
- **Critical Tests**: 100% pass rate required
- **High Priority**: 95% pass rate required
- **Medium Priority**: 90% pass rate required
- **Performance**: Response times within specified limits
- **Security**: All security tests must pass

#### **Test Environment Requirements:**
- **Database**: Fresh installation with test data
- **Server**: Minimum 4GB RAM, 2 CPU cores
- **Network**: Stable internet connection
- **Browsers**: Chrome, Firefox, Safari, Edge
- **Mobile**: iOS Safari, Android Chrome

#### **Reporting:**
- **Daily**: Critical test results
- **Weekly**: Full test suite results
- **Release**: Complete test report with pass/fail rates

## Notes
- All test cases should be executed in a test environment
- Test data should be reset between test runs
- Performance tests should be run with realistic data volumes
- Security tests should include penetration testing
- All user inputs should be validated and sanitized
- Test results should be documented with screenshots for failures
- Failed tests should be retested after fixes are implemented
