-- =====================================================
-- Database Fix Script for Booking System
-- Run this script to fix booking table structure and data
-- =====================================================

-- Step 1: Check and fix the booking table structure
-- First, let's ensure the Status column exists and is properly defined

-- If Status column doesn't exist or is wrong type, we'll need to alter it
-- Note: This will work if the column exists. If it doesn't, you'll need to add it separately.

-- Fix 1: Ensure Status column is ENUM with correct values
ALTER TABLE `booking` 
MODIFY COLUMN `Status` ENUM('Pending', 'Scheduled', 'Cancelled', 'Rejected') 
DEFAULT 'Pending' 
NULL;

-- Fix 2: Ensure all column names are PascalCase (if they're not already)
-- Check and rename columns if needed (uncomment if your columns are lowercase)

-- ALTER TABLE `booking` CHANGE COLUMN `userID` `UserID` INT(11) UNSIGNED;
-- ALTER TABLE `booking` CHANGE COLUMN `propertyID` `PropertyID` INT(11) UNSIGNED;
-- ALTER TABLE `booking` CHANGE COLUMN `bookingDate` `BookingDate` DATE NULL;
-- ALTER TABLE `booking` CHANGE COLUMN `status` `Status` ENUM('Pending', 'Scheduled', 'Cancelled', 'Rejected') DEFAULT 'Pending' NULL;

-- Fix 3: Update any NULL or empty Status values to 'Pending'
UPDATE `booking` 
SET `Status` = 'Pending' 
WHERE `Status` IS NULL OR `Status` = '' OR TRIM(`Status`) = '';

-- Fix 4: Ensure updated_at column exists and is set properly
ALTER TABLE `booking` 
MODIFY COLUMN `updated_at` DATETIME NULL DEFAULT NULL;

-- Fix 5: Update all bookings to have updated_at set if NULL
UPDATE `booking` 
SET `updated_at` = NOW() 
WHERE `updated_at` IS NULL;

-- Fix 6: Ensure created_at column exists
ALTER TABLE `booking` 
MODIFY COLUMN `created_at` DATETIME NULL DEFAULT NULL;

-- Fix 7: Update all bookings to have created_at set if NULL
UPDATE `booking` 
SET `created_at` = NOW() 
WHERE `created_at` IS NULL;

-- Step 2: Verify the structure (run these SELECT queries to check)
-- SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'booking' 
-- ORDER BY ORDINAL_POSITION;

-- Step 3: Check current booking data
-- SELECT bookingID, UserID, PropertyID, BookingDate, Status, created_at, updated_at 
-- FROM booking 
-- ORDER BY bookingID DESC 
-- LIMIT 10;

-- Step 4: Fix any bookings with invalid Status values
UPDATE `booking` 
SET `Status` = 'Pending' 
WHERE `Status` NOT IN ('Pending', 'Scheduled', 'Cancelled', 'Rejected') 
   OR `Status` IS NULL;

-- Step 5: Ensure houserreservation table structure is correct
-- Check if the table exists and has correct structure
-- Note: Adjust table name if it's different (houserReservation vs houserreservation)

-- Fix houserreservation Status column if needed
ALTER TABLE `houserreservation` 
MODIFY COLUMN `Status` ENUM('Ongoing', 'Completed', 'Defaulted', 'Cancelled') 
DEFAULT 'Ongoing' 
NULL;

-- Add Cancelled to the ENUM if it doesn't exist (for when reservations are cancelled)
-- If the above fails, you may need to drop and recreate or use a different approach

-- Step 6: Update any NULL Status in houserreservation
UPDATE `houserreservation` 
SET `Status` = 'Ongoing' 
WHERE `Status` IS NULL OR `Status` = '' OR TRIM(`Status`) = '';

-- Step 7: Ensure foreign key relationships are intact
-- Verify foreign keys exist (these should already be set from migrations)
-- If not, add them:
-- ALTER TABLE `booking` 
-- ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ALTER TABLE `booking` 
-- ADD CONSTRAINT `fk_booking_property` FOREIGN KEY (`PropertyID`) REFERENCES `property` (`PropertyID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ALTER TABLE `houserreservation` 
-- ADD CONSTRAINT `fk_reservation_booking` FOREIGN KEY (`bookingID`) REFERENCES `booking` (`bookingID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- =====================================================
-- TEST QUERIES (Run these to verify everything works)
-- =====================================================

-- Test 1: Check if we can update a booking status
-- UPDATE `booking` SET `Status` = 'Scheduled', `updated_at` = NOW() WHERE `bookingID` = 1;

-- Test 2: Verify the update worked
-- SELECT bookingID, Status, updated_at FROM booking WHERE bookingID = 1;

-- Test 3: Check all bookings with their statuses
-- SELECT bookingID, UserID, PropertyID, Status, BookingDate, created_at, updated_at 
-- FROM booking 
-- ORDER BY updated_at DESC;

-- =====================================================
-- NOTES:
-- 1. Make sure to backup your database before running this script
-- 2. If you get errors about column names, check your actual table structure first
-- 3. The column rename commands are commented out - uncomment only if needed
-- 4. Adjust table names (houserreservation vs houserReservation) based on your actual table name
-- =====================================================

