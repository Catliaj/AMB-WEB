<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'FirstName'   => 'Admin',
                'MiddleName'  => 'System',
                'LastName'    => 'User',
                'Birthdate'   => '1990-01-01',
                'phoneNumber' => '09123456789',
                'Email'       => 'admin@gmail.com',
                'Password'    => password_hash('admin123', PASSWORD_BCRYPT),
                'Role'        => 'Admin',
                'status'      => 'Offline',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'FirstName'   => 'Agent',
                'MiddleName'  => 'Support',
                'LastName'    => 'User',
                'Birthdate'   => '1995-03-15',
                'phoneNumber' => '09234567890',
                'Email'       => 'agent@gmail.com',
                'Password'    => password_hash('agent123', PASSWORD_BCRYPT),
                'Role'        => 'Agent',
                'status'      => 'Offline',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'FirstName'   => 'Client',
                'MiddleName'  => 'Test',
                'LastName'    => 'User',
                'Birthdate'   => '2000-07-20',
                'phoneNumber' => '09345678901',
                'Email'       => 'client@gmail.com',
                'Password'    => password_hash('client123', PASSWORD_BCRYPT),
                'Role'        => 'Client',
                'status'      => 'Offline',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
             [
                'FirstName'   => 'marga',
                'MiddleName'  => 'Test',
                'LastName'    => 'User',
                'Birthdate'   => '2000-07-20',
                'phoneNumber' => '09345678911',
                'Email'       => 'marga@gmail.com',
                'Password'    => password_hash('client123', PASSWORD_BCRYPT),
                'Role'        => 'Client',
                'status'      => 'Offline',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
             ],
             [
                'FirstName'   => 'AJ',
                'MiddleName'  => 'Support',
                'LastName'    => 'User',
                'Birthdate'   => '1995-03-15',
                'phoneNumber' => '09234561110',
                'Email'       => 'aj@gmail.com',
                'Password'    => password_hash('agent123', PASSWORD_BCRYPT),
                'Role'        => 'Agent',
                'status'      => 'Offline',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert all users
        $this->db->table('users')->insertBatch($data);
    }
}
