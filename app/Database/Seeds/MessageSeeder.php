<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run()
    {
        // 'messageID', 'chatSessionID', 'senderRole', 'messageContent', 'timestamp' 

        $data = [
            [
                'messageID' => 1,
                'chatSessionID' => 1,
                'senderRole' => 'Client',
                'messageContent' => 'Hello, I am interested in property ID 1.',
                'timestamp' => '2024-09-01 10:05:00',
            ],
            [
                'messageID' => 2,
                'chatSessionID' => 1,
                'senderRole' => 'Agent',
                'messageContent' => 'Hi! I would be happy to assist you with that property.',
                'timestamp' => '2024-09-01 10:10:00',
            ],
            [
                'messageID' => 3,
                'chatSessionID' => 1,
                'senderRole' => 'Client',
                'messageContent' => 'Can you provide more details about the location?',
                'timestamp' => '2024-09-01 10:15:00',
            ],
            [
                'messageID' => 4,
                'chatSessionID' => 1,
                'senderRole' => 'Agent',
                'messageContent' => 'Sure! The property is located in a prime area with great amenities nearby.',
                'timestamp' => '2024-09-01 10:20:00',
            ],

             [
                'messageID' => 5,
                'chatSessionID' => 2,
                'senderRole' => 'Client',
                'messageContent' => 'Hello, I am interested in property ID 1.',
                'timestamp' => '2024-09-01 10:05:00',
            ],
            [
                'messageID' => 6,
                'chatSessionID' => 3,
                'senderRole' => 'Client',
                'messageContent' => 'Hello, I am interested in property ID 1.',
                'timestamp' => '2024-09-01 10:05:00',
            ],
        ];
        
        $this->db->table('messages')->insertBatch($data);
    }
}
