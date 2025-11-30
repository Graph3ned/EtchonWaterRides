# Boat Rental Management System
## User Manual

**Version:** 1.0  
**Last Updated:** 2025

---

## Table of Contents

1. [Introduction](#introduction)
2. [Getting Started](#getting-started)
3. [System Overview](#system-overview)
4. [Staff User Guide](#staff-user-guide)
5. [Admin User Guide](#admin-user-guide)
6. [Common Tasks & Workflows](#common-tasks--workflows)
7. [Troubleshooting](#troubleshooting)
8. [Appendix](#appendix)

---

## Introduction

### About the System

The **Boat Rental Management System** is a comprehensive web-based application designed to manage water ride rentals efficiently. The system automates rental transactions, tracks sales, manages staff, and provides real-time reporting capabilities.

### Key Features

- **Automated Rental Management**: Create, edit, and complete rental transactions with automatic time tracking and price calculations
- **Real-time Availability**: View which rides are available or currently in use
- **Sales & Reporting**: Generate detailed financial and operational reports with filtering options
- **Staff Management**: Manage staff accounts and track their activities
- **Role-Based Security**: Separate access levels for Admin and Staff users
- **Activity Logging**: Complete audit trail of staff activities

### System Requirements

- **Web Browser**: Chrome, Firefox, Safari, or Edge (latest versions)
- **Internet Connection**: Required for accessing the system
- **Device**: Desktop, laptop, tablet, or mobile phone

---

## Getting Started

### Accessing the System

1. Open your web browser
2. Navigate to the system URL provided by your administrator
3. You will be redirected to the login page

### User Roles

The system has two user roles:

- **Staff**: Can create and manage rental transactions
- **Admin**: Full system access including staff management, reports, and system configuration

### Logging In

1. On the login page, enter your **username** and **password**
2. Click the **"Dive In"** button (or "Log in" button)
3. You will be redirected to your dashboard based on your role:
   - Staff users → Staff Dashboard
   - Admin users → Admin Dashboard

**Note**: If you don't have an account, contact your administrator to create one.

### Password Reset

If you've forgotten your password:

1. Click **"Forgot your password?"** on the login page
2. Enter your **email address**
3. Click **"Email Password Reset Link"** or **"Send Reset Link"**
4. Check your email for the password reset link
5. Click the link in the email
6. Enter your new password and confirm it
7. Click **"Reset Password"**

---

## System Overview

### Navigation

The system uses a navigation menu that appears at the top of each page. Available menu items depend on your user role:

**Staff Navigation:**
- Dashboard
- Ride Availability
- Profile

**Admin Navigation:**
- Dashboard
- Sales
- Reports
- Rides Rate
- Ride Availability
- Staffs
- Logs
- Profile

### Dashboard Overview

Both Staff and Admin users have access to dashboards that provide:
- Quick overview of current rentals
- Income information (Staff: daily total, Admin: overall total)
- Filtering options
- Action buttons for common tasks

---

## Staff User Guide

### Staff Dashboard

The Staff Dashboard is your main workspace for managing rental transactions.

#### Features

- **View All Rides**: See all rental transactions for today (ongoing and completed)
- **Filter Rides**: Filter by "All Rides", "Ongoing Rides", or "Ended Rides"
- **Total Income Display**: View total income for today's rentals
- **Add Rental Button**: Quick access to create new rentals

#### Viewing Rentals

The dashboard displays today's rentals in a table format showing:
- Ride Type & Classification
- Identification (ride identifier)
- Duration
- Life Jackets used
- Total price
- Start time
- End time
- Remaining time (for ongoing rentals)
- Notes
- Action buttons (Edit/End Rental)

**Mobile View**: On smaller screens, some columns are hidden but information is displayed in a condensed format below the ride type.

### Creating a New Rental

1. Click the **"Add Rental"** button on the Staff Dashboard
2. Fill in the rental form:

   **Step 1: Select Ride Type**
   - Choose a **Ride Type** from the dropdown (e.g., Water Rides)
   - The system will automatically load available classifications

   **Step 2: Select Classification**
   - Choose a **Classification** from the dropdown (e.g., Kayak, Boat)
   - The system will show available rides for that classification

   **Step 3: Select Specific Ride**
   - Choose the specific **Ride** (identifier) you want to rent
   - Only available rides will be shown

   **Step 4: Set Duration**
   - Choose a **predefined duration** (30 min, 1 hour, 2 hours, 3 hours, 4 hours)
   - OR toggle **"Use Custom Duration"** to enter a specific number of minutes (1-500 minutes)

   **Step 5: Enter Life Jackets**
   - Enter the number of **life jackets** needed (0 or more)

   **Step 6: Add Notes (Optional)**
   - Add any additional notes about the rental

3. Review the **Total Price** (automatically calculated)
4. Click **"Start Rental"** button
   - The start time is automatically set to the current time
   - The end time is calculated based on the duration
   - The ride status changes to "In Use"

**Important Notes:**
- The system automatically calculates the total price based on duration and price per hour
- Once a rental is started, the ride becomes unavailable for other rentals
- All fields marked with * are required

### Editing a Rental

You can edit rental information for both active and completed rentals:

1. On the Staff Dashboard, find the rental you want to edit
2. Click the **"Edit"** button for that rental
3. Modify any of the following:
   - Duration (will recalculate end time and total price)
   - Life jacket quantity
   - Notes
   - Ride selection
4. Click **"Update Rental"** to save changes

**Note**: Both active and completed rentals can be edited. When you change the duration, the system will recalculate the end time and total price based on the new duration.

### Ending a Rental

When a rental is complete:

1. On the Staff Dashboard, find the rental you want to end
2. Click the **"Edit"** button for that rental
3. Click the **"End Rental"** button
4. The system will:
   - Mark the rental as completed (status changes to completed)
   - Make the ride available again for new rentals

**Important Notes**: 
- If you need to update the end time or recalculate the price based on actual duration, you must edit the rental manually before or after ending it

### Viewing Rental Details

- All rental information is displayed in the dashboard table
- For ongoing rentals, you can see the **remaining time** countdown
- Click "Edit" to view and modify full details

### Ride Availability

Staff users can view real-time availability of all rides in the system.

#### Features

- **Real-time Status**: See which rides are available or currently in use
- **Filter by Ride Type**: Filter to see availability for specific ride types
- **Visual Indicators**: 
  - Available rides are clearly marked
  - Rides in use show rental information

#### Accessing Ride Availability

1. Navigate to **"Ride Availability"** from the navigation menu
2. View all rides and their current status
3. Use filters to narrow down by ride type if needed

**Note**: This feature helps staff quickly identify which rides are available when creating new rentals.

---

## Admin User Guide

### Admin Dashboard

The Admin Dashboard provides an overview of ride pricing and management options.

#### Features

- **View All Ride Prices**: See all ride types, classifications, and their prices
- **Add Ride Price**: Create new ride types and set pricing
- **Edit/Delete Prices**: Modify existing ride pricing

### Sales Management

Access the Sales page from the Admin navigation menu.

#### Sales Dashboard Features

**Filter Options:**
- **Staff Filter**: Filter sales by specific staff member or view all staff
- **Ride Type Filter**: Filter by specific ride type (e.g., Water Rides)
- **Classification Filter**: Filter by classification (requires ride type selection first)
- **Identifier Filter**: Filter by specific ride identifier (requires classification selection first)
- **Date Range Filter**: Multiple options:
  - All Time
  - Today
  - Yesterday
  - Select Day (choose specific date)
  - This Week
  - Last Week
  - This Month (default)
  - Last Month
  - Select Month (choose specific month)
  - This Year
  - Last Year
  - Select Year (choose specific year)
  - Custom Range (select start and end dates)

**Sales Information:**
- **Total Sales**: Displays total sales amount in Philippine Peso (₱) based on current filters
- **Sales Chart**: Visual line chart showing daily sales trends

**Sales Table:**
- Displays all rental transactions matching the filters
- Shows: Staff name, Ride Type, Classification, Identifier, Duration, Jackets, Total, Start time, End time, Date, Notes
- **Pagination**: Control how many entries to show (10, 15, 20, 50, 100, 150, 200)

**Editing Sales Records:**
- Click on a sales record to edit details (Admin only)
- Can modify rental information if needed

### Reports

Access comprehensive reporting from the Reports page.

#### Report Types

1. **Financial Reports**
   - Total revenue
   - Sales by date range
   - Growth calculations
   - Export to PDF or CSV

2. **Operational Reports**
   - Rental statistics
   - Ride utilization
   - Staff performance
   - Export to PDF or CSV

#### Generating Reports

1. Navigate to **Reports** from the Admin menu
2. Select report type (Financial or Operational)
3. Choose date range and filters
4. Click **"Generate Report"**
5. Review the report
6. Export if needed (PDF or CSV)

### Ride Management

#### Adding a New Ride Type

1. Navigate to **"Rides Rate"** from Admin menu
2. Click **"Add Ride Price"** button
3. **Step 1 - Create Ride Type:**
   - Enter **Ride Type Name** (e.g., "Water Rides", "Land Rides")
   - Upload an **image** for the ride type (optional)
   - Click **"Next"**

4. **Step 2 - Add Classifications:**
   - For each classification:
     - Enter **Classification Name** (e.g., "Kayak", "Boat")
     - Enter **Price Per Hour** (in Philippine Peso)
     - Upload **Classification Image** (optional)
     - Add **Identifiers** (specific ride IDs):
       - Click **"Add Identifier"** for each ride
       - Enter identifier name (e.g., "KAY-001", "BOAT-001")
       - Upload identifier image (optional)
   - Click **"Add Another Classification"** to add more
   - Click **"Save All"** when done

#### Editing Ride Types

1. Navigate to **"Rides Rate"** from Admin menu
2. Find the ride type you want to edit
3. Click **"View Details"** (or "View Rides") for that ride type
4. On the View Details page, click the **"Edit"** button
5. Modify:
   - Ride Type name
   - Image
6. Click **"Update"**

#### Editing Classifications

1. Navigate to **"Rides Rate"** from Admin menu
2. Click **"View Details"** for the ride type
3. Find the classification you want to edit
4. Click **"Edit"** button
5. Modify:
   - Classification name
   - Price per hour
   - Image
   - Add/remove identifiers
6. Click **"Update"**

#### Editing Prices

1. Navigate to **"Rides Rate"** from Admin menu
2. Find the price entry you want to edit
3. Click **"Edit"** button
4. Modify the **Price Per Hour**
5. Click **"Update"**

#### Deleting Rides/Prices

1. Navigate to **"Rides Rate"** from Admin menu
2. Find the entry you want to delete
3. Click **"Delete"** button
4. Confirm deletion in the popup

### Ride Availability

View real-time availability of all rides in the system.

#### Features

- **Real-time Status**: See which rides are available or currently in use
- **Filter by Ride Type**: Filter to see availability for specific ride types
- **Visual Indicators**: 
  - Available rides are clearly marked
  - Rides in use show rental information

### Staff Management

Manage staff accounts and permissions.

#### Viewing Staff List

1. Navigate to **"Staffs"** from Admin menu
2. View all staff members in a table showing:
   - Name
   - Username
   - Email
   - Actions

#### Adding New Staff

1. Navigate to **"Staffs"** from Admin menu
2. Click **"Register New Staff"** or **"Add Staff"** button
3. Fill in the registration form:
   - **Name**: Full name of the staff member
   - **Username**: Unique username (used for login)
   - **Email**: Unique email address
   - **Password**: Create a secure password
   - **Confirm Password**: Re-enter password
4. Click **"Register"**

**Note**: Both username and email addresses must be unique. The system will prevent duplicate accounts.

#### Editing Staff Information

1. Navigate to **"Staffs"** from Admin menu
2. Find the staff member you want to edit
3. Click **"Edit"** button
4. Modify:
   - Name
   - Username
   - Email
   - Password (optional - only if you want to change it)
   - Confirm Password (required if changing password)
5. Click **"Save Changes"**

**Note**: Staff members can also edit their own profile information from the Profile page.

#### Deleting Staff

1. Navigate to **"Staffs"** from Admin menu
2. Find the staff member you want to delete
3. Click **"Delete"** button
4. Confirm deletion

### Activity Logs

View a complete audit trail of staff activities.

#### Features

- **Complete Activity History**: See all actions performed by staff members
- **Filter by Action**: Filter logs by specific action types
- **Filter by User**: Filter logs by specific staff members
- **Date/Time Stamps**: See when each action occurred
- **Detailed Information**: View full details of each logged action

#### Viewing Logs

1. Navigate to **"Logs"** from Admin menu
2. Use filters to narrow down the log entries:
   - Select action type from dropdown
   - Select user from dropdown
3. Review the log entries in the table
4. Use pagination to navigate through logs

---

## Common Tasks & Workflows

### Complete Rental Workflow (Staff)

1. **Customer arrives** and requests a ride
2. **Check availability** on the dashboard
3. **Create new rental**:
   - Click "Add Rental"
   - Select ride type, classification, and specific ride
   - Set duration
   - Enter life jacket quantity
   - Add any notes
   - Click "Start Rental"
4. **Monitor rental** on dashboard (see remaining time)
5. **When rental ends**:
   - Click "Edit" button, then "End Rental" button
   - System marks rental as completed
   - Ride becomes available again
   - **Note**: If you need to update the end time or recalculate price, edit the rental before or after ending it

### Daily Sales Review (Admin)

1. Navigate to **"Sales"** page
2. Set date filter to **"Today"**
3. Review total sales amount
4. Check sales chart for trends
5. Review individual transactions in the table

### Monthly Report Generation (Admin)

1. Navigate to **"Reports"** page
2. Select **"Financial Report"** or **"Operational Report"**
3. Set date range to **"This Month"** or **"Select Month"**
4. Apply any additional filters (staff, ride type, etc.)
5. Click **"Generate Report"**
6. Review the report
7. Export to PDF or CSV for distribution

### Adding a New Ride to Inventory (Admin)

1. Navigate to **"Rides Rate"** page
2. Click **"Add Ride Price"**
3. If creating new ride type:
   - Enter ride type name and upload image
   - Click "Next"
4. Add classification:
   - Enter classification name
   - Set price per hour
   - Upload classification image
5. Add identifiers:
   - For each physical ride, add an identifier (e.g., "KAY-001")
   - Upload identifier image if available
6. Click **"Save All"**
7. Verify the new ride appears in the list

### Staff Account Setup (Admin)

1. Navigate to **"Staffs"** page
2. Click **"Register New Staff"**
3. Enter staff information:
   - Full name
   - Username (must be unique, used for login)
   - Email (must be unique)
   - Password
4. Click **"Register"**
5. Inform the staff member of their login credentials (username and password)
6. Staff member can change password after first login

---

## Troubleshooting

### Login Issues

**Problem**: Cannot log in with correct credentials
- **Solution**: 
  - Verify you're using the correct username (not email)
  - Check if Caps Lock is on
  - Try password reset if password is forgotten
  - Contact administrator if account is locked

**Problem**: Forgot password
- **Solution**: 
  - Click "Forgot your password?" on login page
  - Follow email instructions to reset

### Rental Issues

**Problem**: Cannot find a specific ride when creating rental
- **Solution**: 
  - Check if the ride is currently in use (check Ride Availability page)
  - Verify the ride type and classification are selected correctly
  - Contact admin if ride should be available but isn't showing

**Problem**: Price calculation seems incorrect
- **Solution**: 
  - Verify the duration is set correctly
  - Check the price per hour for that classification
  - System calculates: (Duration in hours) × (Price per hour)
  - Contact admin if price per hour needs to be updated

**Problem**: Cannot edit a rental
- **Solution**: 
  - Both active and completed rentals can be edited
  - If you cannot edit a rental, refresh the page
  - Make sure you're clicking the "Edit" button for the correct rental

### Display Issues

**Problem**: Page looks broken or elements are missing
- **Solution**: 
  - Refresh the page (F5 or Ctrl+R)
  - Clear browser cache
  - Try a different browser
  - Check internet connection

**Problem**: Table columns are hidden on mobile
- **Solution**: 
  - This is normal behavior for mobile devices
  - All information is still available, just displayed differently
  - Rotate device to landscape for more columns
  - Scroll down to see additional details below ride type

### Performance Issues

**Problem**: Page loads slowly
- **Solution**: 
  - Check internet connection
  - Reduce the number of entries shown per page
  - Use filters to narrow down results
  - Contact administrator if issue persists

**Problem**: Chart not displaying
- **Solution**: 
  - Refresh the page
  - Check if JavaScript is enabled in browser
  - Try a different browser
  - Clear browser cache

### Data Issues

**Problem**: Sales data doesn't match expectations
- **Solution**: 
  - Check date range filter
  - Verify staff filter settings
  - Check ride type/classification filters
  - Click "Reset" to clear all filters
  - Contact admin to verify data integrity

**Problem**: Cannot delete a ride/price
- **Solution**: 
  - Some items may be protected if they have associated rentals
  - Contact administrator for assistance
  - Consider editing instead of deleting

---

## Appendix

### Keyboard Shortcuts

- **F5** or **Ctrl+R**: Refresh page
- **Ctrl+F**: Search/find on page
- **Tab**: Navigate between form fields
- **Enter**: Submit forms

### Browser Compatibility

The system is tested and works best with:
- **Chrome** (recommended)
- **Firefox**
- **Safari**
- **Edge**

### Mobile Usage Tips

- The system is fully responsive and works on mobile devices
- Use landscape orientation for better table viewing
- Some features may be optimized for touch interaction
- All core functions are available on mobile

### Data Export Formats

- **CSV**: Comma-separated values, opens in Excel or any spreadsheet application
- **PDF**: Portable Document Format, requires PDF reader

### Currency

All prices are displayed in **Philippine Peso (₱)**.

### Time Format

- Times are displayed in 12-hour format with AM/PM
- Dates are displayed as: Month Day, Year (e.g., "Jan 15, 2025")
- System uses Asia/Manila timezone

### Contact & Support

For technical issues or questions:
- Contact your system administrator
- Provide details about the issue
- Include screenshots if possible
- Note your user role and the page where the issue occurred

### System Updates

The system may be updated periodically. New features and improvements will be communicated by your administrator.

---

## Quick Reference Guide

### Staff Quick Actions

| Task | Steps |
|------|-------|
| Create Rental | Dashboard → Add Rental → Fill form → Start Rental |
| Edit Rental | Dashboard → Find rental → Click Edit → Update fields → Save |
| End Rental | Dashboard → Find rental → Click End Rental |
| View Income | Dashboard → Check "Total Income" display (shows today's total) |

### Admin Quick Actions

| Task | Steps |
|------|-------|
| View Sales | Sales → Apply filters → Review data |
| Generate Report | Reports → Select type → Set filters → Generate → Export |
| Add Staff | Staffs → Register New Staff → Fill form → Register |
| Add Ride | Rides Rate → Add Ride Price → Follow steps → Save |
| View Logs | Logs → Apply filters → Review entries |
| Check Availability | Ride Availability → View status |

---

**End of User Manual**

For the most up-to-date information, please refer to your system administrator.

