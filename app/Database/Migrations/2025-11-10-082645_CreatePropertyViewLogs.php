<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertyViewLogs extends Migration
{
    public function up()
    {
        //
         $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'UserID' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'constraint'     => 11,
            ],
            'PropertyID' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'constraint'     => 11
            ],

             'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            

        ]);
         $this->forge->addKey('id', true);
         $this->forge->addForeignKey('UserID', 'users', 'UserID', 'CASCADE', 'CASCADE');       
         $this->forge->addForeignKey('PropertyID', 'property', 'PropertyID', 'CASCADE', 'CASCADE');     
         $this->forge->createTable('propertyviewlogs');    
    }

    public function down()
    {
        //
         $this->forge->dropTable('propertyviewlogs');  
    }
}
