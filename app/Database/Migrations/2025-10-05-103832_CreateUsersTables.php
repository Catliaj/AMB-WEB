<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'UserID' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],

            'FirstName' => [
                'type'           => 'VARCHAR',
                'constraint'     => '255'
            ],

            'MiddleName' => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => true,
            ],

            'LastName' => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
            ],

            'Birthdate' => [
                'type'           => 'DATE',
                'null'           => true,
            ],

            'phoneNumber' => [
                'type'           => 'VARCHAR',
                'constraint'     => '15',
                'null'           => true,
                'unique'         => true,
            ],

            'Email' => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => true,
                'unique'         => true,
            ],

            'Password' => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
            ],

            // Profile image filename (stored as filename only; views build full URL using employmentStatus)
            'Image' => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => true,
                'default'        => null,
            ],

            // Government ID image filename
            'GovIDImage' => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => true,
                'default'        => null,
            ],

            // Government ID metadata
            'gov_id_type' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => true,
                'default'        => null,
            ],
            'gov_id_number' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => true,
                'default'        => null,
            ],

            // Employment status used to determine uploads folder (e.g., 'ofw' or 'locallyemployed')
            'employmentStatus' => [
                'type'           => 'VARCHAR',
                'constraint'     => '50',
                'null'           => true,
                'default'        => null,
            ],

            'Role' => [
                'type'           => 'ENUM',
                'constraint'     => ['Admin', 'Agent', 'Client'],
                'default'        => 'Client',
            ],

            'status' => [
                'type'           => 'ENUM',
                'constraint'     => ['Offline', 'Online'],
                'default'        => 'Offline',
            ],

            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],

            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        $this->forge->addKey('UserID', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
