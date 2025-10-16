<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertyStatusHistory extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'propertStatusID' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],

            'PropertyID' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'constraint'     => 11
            ],

            'Old_Status' => [
                'type'           => 'ENUM',
                'constraint'     => ['Available',  'Sold', 'Reserved', 'Cancelled'],
                'null'           => true,
                'default'        => 'Available',
            ],

            'New_Status' => [
                'type'           => 'ENUM',
                'constraint'     => ['Available',  'Sold', 'Reserved', 'Cancelled'],
                'null'           => true,
                'default'        => 'Available',
            ],

            'Date' => [
                'type'           => 'DATETIME',
                'null'           => false,
            ]
        ]);
            $this->forge->addKey('propertStatusID', true);
            $this->forge->addForeignKey('PropertyID', 'property', 'PropertyID', 'CASCADE', 'CASCADE');         
            $this->forge->createTable('propertyStatusHistory');
    }

    public function down()
    {
        //
        $this->forge->dropTable('propertyStatusHistory');
    }
}
