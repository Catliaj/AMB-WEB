<?php
/**
 * Database Fix Script for Booking System
 * Run this file directly: php fix_booking_db.php
 * Or access via browser if placed in public folder
 */

// Bootstrap CodeIgniter
require_once __DIR__ . '/vendor/autoload.php';

// Load environment
$pathsConfig = require __DIR__ . '/app/Config/Paths.php';
$pathsConfig->systemDirectory = __DIR__ . '/vendor/codeigniter4/framework/system';

require __DIR__ . '/vendor/codeigniter4/framework/system/bootstrap.php';

use CodeIgniter\Config\Services;

echo "=== Booking Database Fix Script ===\n\n";

$db = \Config\Database::connect();

try {
    // Step 1: Check current table structure
    echo "Step 1: Checking booking table structure...\n";
    $columns = $db->query("SHOW COLUMNS FROM `booking`")->getResultArray();
    
    $columnNames = array_column($columns, 'Field');
    echo "Current columns: " . implode(', ', $columnNames) . "\n\n";
    
    // Check if Status column exists and its type
    $statusColumn = null;
    foreach ($columns as $col) {
        if (strtolower($col['Field']) === 'status') {
            $statusColumn = $col;
            break;
        }
    }
    
    if (!$statusColumn) {
        echo "ERROR: Status column not found! Creating it...\n";
        $db->query("ALTER TABLE `booking` ADD COLUMN `Status` ENUM('Pending', 'Scheduled', 'Cancelled', 'Rejected') DEFAULT 'Pending' NULL AFTER `BookingDate`");
        echo "Status column created.\n\n";
    } else {
        echo "Status column found. Type: " . $statusColumn['Type'] . "\n";
        
        // Check if it's the correct ENUM
        if (stripos($statusColumn['Type'], 'enum') === false) {
            echo "WARNING: Status is not an ENUM. Fixing...\n";
            $db->query("ALTER TABLE `booking` MODIFY COLUMN `Status` ENUM('Pending', 'Scheduled', 'Cancelled', 'Rejected') DEFAULT 'Pending' NULL");
            echo "Status column fixed.\n\n";
        } else {
            echo "Status column type is correct.\n\n";
        }
    }
    
    // Step 2: Check for NULL or empty Status values
    echo "Step 2: Checking for NULL or empty Status values...\n";
    $nullStatus = $db->query("SELECT COUNT(*) as count FROM `booking` WHERE `Status` IS NULL OR `Status` = '' OR TRIM(`Status`) = ''")->getRowArray();
    $nullCount = $nullStatus['count'] ?? 0;
    
    if ($nullCount > 0) {
        echo "Found {$nullCount} bookings with NULL or empty Status. Fixing...\n";
        $db->query("UPDATE `booking` SET `Status` = 'Pending' WHERE `Status` IS NULL OR `Status` = '' OR TRIM(`Status`) = ''");
        echo "Fixed {$nullCount} bookings.\n\n";
    } else {
        echo "No NULL or empty Status values found.\n\n";
    }
    
    // Step 3: Check for invalid Status values
    echo "Step 3: Checking for invalid Status values...\n";
    $invalidStatus = $db->query("SELECT COUNT(*) as count FROM `booking` WHERE `Status` NOT IN ('Pending', 'Scheduled', 'Cancelled', 'Rejected') AND `Status` IS NOT NULL")->getRowArray();
    $invalidCount = $invalidStatus['count'] ?? 0;
    
    if ($invalidCount > 0) {
        echo "Found {$invalidCount} bookings with invalid Status. Fixing...\n";
        $db->query("UPDATE `booking` SET `Status` = 'Pending' WHERE `Status` NOT IN ('Pending', 'Scheduled', 'Cancelled', 'Rejected') AND `Status` IS NOT NULL");
        echo "Fixed {$invalidCount} bookings.\n\n";
    } else {
        echo "No invalid Status values found.\n\n";
    }
    
    // Step 4: Ensure updated_at column exists and is set
    echo "Step 4: Checking updated_at column...\n";
    $hasUpdatedAt = in_array('updated_at', $columnNames) || in_array('Updated_at', $columnNames);
    
    if (!$hasUpdatedAt) {
        echo "updated_at column not found. Creating...\n";
        $db->query("ALTER TABLE `booking` ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL");
        echo "updated_at column created.\n\n";
    } else {
        echo "updated_at column exists.\n";
        
        // Update NULL updated_at values
        $nullUpdated = $db->query("SELECT COUNT(*) as count FROM `booking` WHERE `updated_at` IS NULL")->getRowArray();
        $nullUpdatedCount = $nullUpdated['count'] ?? 0;
        
        if ($nullUpdatedCount > 0) {
            echo "Found {$nullUpdatedCount} bookings with NULL updated_at. Setting to current time...\n";
            $db->query("UPDATE `booking` SET `updated_at` = NOW() WHERE `updated_at` IS NULL");
            echo "Fixed {$nullUpdatedCount} bookings.\n\n";
        } else {
            echo "All bookings have updated_at set.\n\n";
        }
    }
    
    // Step 5: Test update functionality
    echo "Step 5: Testing update functionality...\n";
    
    // Get a test booking
    $testBooking = $db->query("SELECT bookingID, Status FROM `booking` LIMIT 1")->getRowArray();
    
    if ($testBooking) {
        $testId = $testBooking['bookingID'];
        $oldStatus = $testBooking['Status'] ?? 'Pending';
        
        echo "Testing update on booking ID: {$testId} (current status: {$oldStatus})\n";
        
        // Try to update to Scheduled
        $updateResult = $db->query("UPDATE `booking` SET `Status` = 'Scheduled', `updated_at` = NOW() WHERE `bookingID` = ?", [$testId]);
        
        // Verify the update
        $verify = $db->query("SELECT Status FROM `booking` WHERE `bookingID` = ?", [$testId])->getRowArray();
        $newStatus = $verify['Status'] ?? 'NULL';
        
        if ($newStatus === 'Scheduled') {
            echo "✓ Update test PASSED! Status changed to: {$newStatus}\n";
            
            // Restore original status
            $db->query("UPDATE `booking` SET `Status` = ?, `updated_at` = NOW() WHERE `bookingID` = ?", [$oldStatus, $testId]);
            echo "Restored original status: {$oldStatus}\n\n";
        } else {
            echo "✗ Update test FAILED! Status is: {$newStatus} (expected: Scheduled)\n\n";
        }
    } else {
        echo "No bookings found to test.\n\n";
    }
    
    // Step 6: Show current booking statistics
    echo "Step 6: Current booking statistics...\n";
    $stats = $db->query("
        SELECT 
            Status,
            COUNT(*) as count
        FROM `booking`
        GROUP BY Status
        ORDER BY Status
    ")->getResultArray();
    
    foreach ($stats as $stat) {
        echo "  {$stat['Status']}: {$stat['count']} bookings\n";
    }
    echo "\n";
    
    // Step 7: Check column name casing
    echo "Step 7: Checking column name casing...\n";
    $expectedColumns = ['bookingID', 'UserID', 'PropertyID', 'BookingDate', 'Status', 'Reason', 'Notes', 'created_at', 'updated_at'];
    $actualColumns = array_map('strtolower', $columnNames);
    $expectedColumnsLower = array_map('strtolower', $expectedColumns);
    
    $missingColumns = array_diff($expectedColumnsLower, $actualColumns);
    if (empty($missingColumns)) {
        echo "All expected columns found.\n";
        
        // Check if casing matches
        $caseIssues = [];
        foreach ($expectedColumns as $expected) {
            $found = false;
            foreach ($columnNames as $actual) {
                if (strtolower($actual) === strtolower($expected)) {
                    if ($actual !== $expected) {
                        $caseIssues[] = "Column '{$actual}' should be '{$expected}'";
                    }
                    $found = true;
                    break;
                }
            }
        }
        
        if (empty($caseIssues)) {
            echo "Column casing is correct.\n\n";
        } else {
            echo "WARNING: Column casing issues found:\n";
            foreach ($caseIssues as $issue) {
                echo "  - {$issue}\n";
            }
            echo "Note: MySQL on Windows is case-insensitive, but it's better to match the expected casing.\n\n";
        }
    } else {
        echo "WARNING: Missing columns: " . implode(', ', $missingColumns) . "\n\n";
    }
    
    echo "=== Fix Script Completed ===\n";
    echo "If you see any errors above, please address them.\n";
    echo "Try approving a booking again and check if it works.\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

