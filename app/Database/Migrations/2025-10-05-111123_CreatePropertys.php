<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProperty extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'PropertyID' => [
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

            'Title' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => true,
            ],

            'Description' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],

            'Property_Type' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => true,
            ],


            'Price' => [
                'type'           => 'DECIMAL',
                'constraint'     => '15,2',
                'null'           => true,
            ],

            'Location' => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => true,
            ],

            'Size' => [
                'Type'          => 'DOUBLE',
                'null'          => true,
            ],

            'Bedrooms' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'           => true,
            ],

            'Bathrooms' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'           => true,
            ],

            'Parking_Spaces' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'           => true,
            ],

            'agent_assigned' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'           => true,
            ],

            'Corporation' => [
                'type'           => 'ENUM',
                'constraint'     => ['BellaVita', 'RCD'],
                'null'           => true,
            ],
            
        ]);

           $this->forge->addKey('PropertyID', true);
           $this->forge->addForeignKey('UserID', 'users', 'UserID', 'CASCADE', 'CASCADE');
           $this->forge->createTable('property');
    }

    public function down()
    {
        //
        $this->forge->dropTable('property');
    }
}
