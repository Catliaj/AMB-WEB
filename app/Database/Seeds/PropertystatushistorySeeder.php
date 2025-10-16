<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PropertystatushistorySeeder extends Seeder
{
    public function run()
    {
        //'PropertyID', 'Old_Status', 'New_Status','Date'
        $data = [
            [
                'PropertyID' => 1,
                'Old_Status' => 'Available',
                'New_Status' => 'Reserved',
                'Date' => '2024-09-01 10:00:00',
            ],

            [
                'PropertyID' => 1,
                'Old_Status' => 'Reserved',
                'New_Status' => 'Sold',
                'Date' => '2024-09-05 14:30:00',
            ],
            
            [
                'PropertyID' => 2,
                'Old_Status' => 'Available',
                'New_Status' => 'Cancelled',
                'Date' => '2024-09-10 09:15:00',
            ],
            [
                'PropertyID' => 3,
                'Old_Status' => 'Available',
                'New_Status' => 'Available',
                'Date' => '2024-09-12 11:45:00',
            ],
            [
                'PropertyID' => 4,
                'Old_Status' => 'Available',
                'New_Status' => 'Sold',
                'Date' => '2024-09-15 16:00:00',
            ],
        ];

        // Using Query Builder
        $this->db->table('propertyStatusHistory')->insertBatch($data);

    }
}
