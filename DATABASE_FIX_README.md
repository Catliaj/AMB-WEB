# Database Fix Instructions for Booking System

This guide will help you fix the booking database issues where scheduled bookings are not appearing after agent approval.

## Quick Fix Options

### Option 1: Use the Web Diagnostic Tool (Recommended)

1. **Access the diagnostic page:**
   - URL: `http://your-domain/diagnostic/booking`
   - Or: `http://localhost/AMB-WEB/public/diagnostic/booking`
   - Note: Only works in development mode or for Admin users

2. **Review the diagnostic results:**
   - The page will show any errors or warnings
   - Check the table structure
   - Review booking statistics

3. **Click "Apply Fixes" button:**
   - This will automatically fix common issues
   - The page will reload after fixes are applied

### Option 2: Run the PHP Script

1. **Open a terminal/command prompt**
2. **Navigate to your project directory:**
   ```bash
   cd "C:\Users\Huawei Matebook\Desktop\AMB WEB\AMB-WEB"
   ```

3. **Run the fix script:**
   ```bash
   php fix_booking_db.php
   ```

4. **Review the output:**
   - The script will check and fix database issues
   - It will show what was fixed

### Option 3: Run SQL Script Directly

1. **Open your database management tool** (phpMyAdmin, MySQL Workbench, etc.)

2. **Select your database**

3. **Open and run the SQL file:**
   - File: `fix_booking_database.sql`
   - Review the script first to understand what it does
   - **IMPORTANT: Backup your database before running!**

4. **Execute the SQL script**

## What These Fixes Do

The fixes will:

1. **Ensure Status column is correct:**
   - Makes sure `Status` is an ENUM with values: 'Pending', 'Scheduled', 'Cancelled', 'Rejected'
   - Sets default to 'Pending'

2. **Fix NULL or empty Status values:**
   - Updates any bookings with NULL or empty Status to 'Pending'

3. **Fix invalid Status values:**
   - Updates any bookings with invalid status values to 'Pending'

4. **Ensure updated_at column exists:**
   - Creates the column if it doesn't exist
   - Updates NULL values to current timestamp

5. **Test update functionality:**
   - Verifies that status updates actually work

## After Running the Fixes

1. **Test the approval flow:**
   - Log in as an agent
   - Approve a pending booking
   - Check if it appears as "Scheduled" on the client's bookings page

2. **Check the logs:**
   - Look in `writable/logs/` for any error messages
   - The debug logs will show what's happening during updates

3. **If issues persist:**
   - Check the diagnostic page again
   - Review the recent bookings table
   - Check if the Status column is being updated correctly

## Manual Database Check

If you want to manually check your database:

```sql
-- Check table structure
SHOW COLUMNS FROM `booking`;

-- Check for NULL Status values
SELECT COUNT(*) FROM `booking` WHERE `Status` IS NULL OR `Status` = '';

-- Check all bookings with their statuses
SELECT bookingID, UserID, PropertyID, Status, BookingDate, updated_at 
FROM `booking` 
ORDER BY updated_at DESC;

-- Test updating a booking
UPDATE `booking` 
SET `Status` = 'Scheduled', `updated_at` = NOW() 
WHERE `bookingID` = 1;

-- Verify the update
SELECT bookingID, Status FROM `booking` WHERE `bookingID` = 1;
```

## Common Issues and Solutions

### Issue: "Status column not found"
**Solution:** The column might be named differently (e.g., `status` instead of `Status`). Check your actual table structure and adjust the fix script accordingly.

### Issue: "Update test failed"
**Solution:** This could indicate:
- Column permissions issue
- Foreign key constraint
- Database connection problem
- Check database user permissions

### Issue: "Column casing mismatch"
**Solution:** MySQL on Windows is case-insensitive, but it's better to match the expected casing. The fix script will handle this.

## Files Created

1. **`fix_booking_database.sql`** - SQL script for manual database fixes
2. **`fix_booking_db.php`** - PHP script to run from command line
3. **`app/Controllers/DiagnosticController.php`** - Web-based diagnostic tool
4. **`app/Views/Pages/diagnostic/booking.php`** - Diagnostic page view

## Support

If you continue to experience issues after running these fixes:

1. Check the application logs in `writable/logs/`
2. Review the diagnostic page output
3. Verify database user has UPDATE permissions on the `booking` table
4. Check if there are any database triggers or constraints that might be preventing updates

## Notes

- Always backup your database before running any fix scripts
- The diagnostic page is only accessible in development mode or for Admin users
- The fixes are safe to run multiple times (they're idempotent)

