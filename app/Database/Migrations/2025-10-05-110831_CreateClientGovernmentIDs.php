<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClientGovernmentID extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'clientGovernmentID' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],

            'UserID' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'constraint'     => 11
            ],
            
            'Government_ID' => [
                'type'           => 'BLOB',
                'null'           => true,
            ],

            'TIN_ID' => [
                'type'           => 'BLOB',
                'null'           => true,
            ],

            'Selfie_with_ID' => [
                'type'           => 'BLOB',
                'null'           => true,
            ],
        ]);

           $this->forge->addKey('clientGovernmentID', true);
           $this->forge->addForeignKey('UserID', 'users', 'UserID', 'CASCADE', 'CASCADE');
           $this->forge->createTable('clientGovernmentID');
    }

    public function down()
    {
        //
        $this->forge->dropTable('clientGovernmentID');
    }
}
