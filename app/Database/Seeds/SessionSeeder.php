<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SessionSeeder extends Seeder
{
    public function run()
    {
        // 'chatSessionID', 'UserID', 'AgentID', 'startTime', 'endTime'
        $data = [
            [
                'chatSessionID' => 1,
                'UserID' => 3,
                'AgentID' => 2,
                'startTime' => '2024-09-01 10:00:00',
                'endTime' => '2024-09-01 10:30:00',
            ],
            [
                'chatSessionID' => 2,
                'UserID' => 4,
                'AgentID' => 2,
                'startTime' => '2024-09-01 10:00:00',
                'endTime' => '2024-09-01 10:30:00',
            ],
            [
                'chatSessionID' => 3,
                'UserID' => 4,
                'AgentID' => 5,
                'startTime' => '2024-09-01 10:00:00',
                'endTime' => '2024-09-01 10:30:00',
            ],
        ];
        // Using Query Builder
        $this->db->table('chatSession')->insertBatch($data);

    }
}
