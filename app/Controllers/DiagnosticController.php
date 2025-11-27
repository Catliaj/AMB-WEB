<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DiagnosticController extends BaseController
{
    /**
     * Diagnostic page to check and fix booking database issues
     * Access via: /diagnostic/booking
     */
    public function booking()
    {
        $session = session();
        
        // Only allow admins or in development
        if (ENVIRONMENT !== 'development' && (!$session->get('isLoggedIn') || $session->get('role') !== 'Admin')) {
            return $this->response->setStatusCode(403)->setBody('Access denied. This page is only available in development mode or for admins.');
        }

        $db = \Config\Database::connect();
        $results = [];
        $errors = [];
        $warnings = [];

        try {
            // Check 1: Table structure
            $results['table_structure'] = 'Checking...';
            $columns = $db->query("SHOW COLUMNS FROM `booking`")->getResultArray();
            $columnNames = array_column($columns, 'Field');
            
            $statusColumn = null;
            foreach ($columns as $col) {
                if (strtolower($col['Field']) === 'status') {
                    $statusColumn = $col;
                    break;
                }
            }
            
            if (!$statusColumn) {
                $errors[] = "Status column not found in booking table!";
            } else {
                if (stripos($statusColumn['Type'], 'enum') === false) {
                    $warnings[] = "Status column is not an ENUM type. Current type: " . $statusColumn['Type'];
                }
            }
            
            // Check 2: NULL or empty Status values
            $nullStatus = $db->query("SELECT COUNT(*) as count FROM `booking` WHERE `Status` IS NULL OR `Status` = '' OR TRIM(`Status`) = ''")->getRowArray();
            $nullCount = $nullStatus['count'] ?? 0;
            if ($nullCount > 0) {
                $warnings[] = "Found {$nullCount} bookings with NULL or empty Status";
            }
            
            // Check 3: Invalid Status values
            $invalidStatus = $db->query("SELECT COUNT(*) as count FROM `booking` WHERE `Status` NOT IN ('Pending', 'Scheduled', 'Cancelled', 'Rejected') AND `Status` IS NOT NULL")->getRowArray();
            $invalidCount = $invalidStatus['count'] ?? 0;
            if ($invalidCount > 0) {
                $warnings[] = "Found {$invalidCount} bookings with invalid Status values";
            }
            
            // Check 4: Test update
            $testBooking = $db->query("SELECT bookingID, Status FROM `booking` LIMIT 1")->getRowArray();
            $testResult = 'No bookings found to test';
            if ($testBooking) {
                $testId = $testBooking['bookingID'];
                $oldStatus = $testBooking['Status'] ?? 'Pending';
                
                // Try update
                $db->query("UPDATE `booking` SET `Status` = 'Scheduled', `updated_at` = NOW() WHERE `bookingID` = ?", [$testId]);
                
                // Verify
                $verify = $db->query("SELECT Status FROM `booking` WHERE `bookingID` = ?", [$testId])->getRowArray();
                $newStatus = $verify['Status'] ?? 'NULL';
                
                if ($newStatus === 'Scheduled') {
                    $testResult = "PASSED - Status updated successfully";
                    // Restore
                    $db->query("UPDATE `booking` SET `Status` = ?, `updated_at` = NOW() WHERE `bookingID` = ?", [$oldStatus, $testId]);
                } else {
                    $errors[] = "Update test FAILED - Status is '{$newStatus}' (expected 'Scheduled')";
                    $testResult = "FAILED";
                }
            }
            
            // Get statistics
            $stats = $db->query("
                SELECT 
                    Status,
                    COUNT(*) as count
                FROM `booking`
                GROUP BY Status
                ORDER BY Status
            ")->getResultArray();
            
            // Get recent bookings
            $recentBookings = $db->query("
                SELECT bookingID, UserID, PropertyID, Status, BookingDate, updated_at
                FROM `booking`
                ORDER BY updated_at DESC
                LIMIT 10
            ")->getResultArray();
            
            return view('Pages/diagnostic/booking', [
                'columns' => $columns,
                'statusColumn' => $statusColumn,
                'errors' => $errors,
                'warnings' => $warnings,
                'testResult' => $testResult,
                'stats' => $stats,
                'recentBookings' => $recentBookings,
                'nullCount' => $nullCount,
                'invalidCount' => $invalidCount,
            ]);
            
        } catch (\Exception $e) {
            return view('Pages/diagnostic/booking', [
                'errors' => ['Exception: ' . $e->getMessage()],
                'warnings' => [],
                'columns' => [],
                'statusColumn' => null,
                'testResult' => 'Error occurred',
                'stats' => [],
                'recentBookings' => [],
                'nullCount' => 0,
                'invalidCount' => 0,
            ]);
        }
    }
    
    /**
     * Fix booking database issues
     * POST /diagnostic/fixBooking
     */
    public function fixBooking()
    {
        $session = session();
        
        if (ENVIRONMENT !== 'development' && (!$session->get('isLoggedIn') || $session->get('role') !== 'Admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Access denied']);
        }

        $db = \Config\Database::connect();
        $fixes = [];
        
        try {
            // Fix 1: Ensure Status is ENUM
            try {
                $db->query("ALTER TABLE `booking` MODIFY COLUMN `Status` ENUM('Pending', 'Scheduled', 'Cancelled', 'Rejected') DEFAULT 'Pending' NULL");
                $fixes[] = "Fixed Status column type";
            } catch (\Exception $e) {
                $fixes[] = "Status column fix: " . $e->getMessage();
            }
            
            // Fix 2: Update NULL Status values
            $result = $db->query("UPDATE `booking` SET `Status` = 'Pending' WHERE `Status` IS NULL OR `Status` = '' OR TRIM(`Status`) = ''");
            $affected = $db->affectedRows();
            if ($affected > 0) {
                $fixes[] = "Updated {$affected} bookings with NULL/empty Status";
            }
            
            // Fix 3: Update invalid Status values
            $result = $db->query("UPDATE `booking` SET `Status` = 'Pending' WHERE `Status` NOT IN ('Pending', 'Scheduled', 'Cancelled', 'Rejected') AND `Status` IS NOT NULL");
            $affected = $db->affectedRows();
            if ($affected > 0) {
                $fixes[] = "Updated {$affected} bookings with invalid Status";
            }
            
            // Fix 4: Ensure updated_at exists
            try {
                $db->query("ALTER TABLE `booking` MODIFY COLUMN `updated_at` DATETIME NULL DEFAULT NULL");
                $fixes[] = "Ensured updated_at column exists";
            } catch (\Exception $e) {
                // Column might already exist, that's okay
            }
            
            // Fix 5: Update NULL updated_at
            $result = $db->query("UPDATE `booking` SET `updated_at` = NOW() WHERE `updated_at` IS NULL");
            $affected = $db->affectedRows();
            if ($affected > 0) {
                $fixes[] = "Updated {$affected} bookings with NULL updated_at";
            }
            
            return $this->response->setJSON([
                'success' => true,
                'fixes' => $fixes,
                'message' => 'Database fixes applied successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}

