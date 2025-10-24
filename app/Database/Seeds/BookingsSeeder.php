<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BookingsSeeder extends Seeder
{
    public function run()
    {
        //'userID', 'propertyID', 'bookingDate', 'status'
        $data = [
            [
                'userID' => 3,
                'propertyID' => 1,
                'bookingDate' => '2025-09-01 10:00:00',
                'status' => 'Pending'
            ],
            [
                'userID' => 3,
                'propertyID' => 2,
                'bookingDate' => '2025-09-05 14:30:00',
                'status' => 'Confirmed'
            ],
            [
                'userID' => 3,
                'propertyID' => 3,
                'bookingDate' => '2025-09-10 09:15:00',
                'status' => 'Cancelled'
            ],
            [
                'userID' => 3,
                'propertyID' => 4,
                'bookingDate' => '2025-09-12 11:45:00',
                'status' => 'Pending'
            ],
            [
                'userID' => 3,
                'propertyID' => 5,
                'bookingDate' => '2025-09-15 16:00:00',
                'status' => 'Confirmed'
            ],
        ];

        // Using Query Builder
        $this->db->table('booking')->insertBatch($data);
    }
}
