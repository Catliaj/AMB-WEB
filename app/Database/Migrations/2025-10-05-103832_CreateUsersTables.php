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

            'Role' => [
                'type'           => 'ENUM',
                'constraint'     => ['Admin', 'Agent', 'Client'],
                'default'        => 'Client',
            ],

            'status' => [
                'type'           => 'ENUM',
                'constraint'     => ['Active', 'Inactive'],
                'default'        => 'Active',
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
