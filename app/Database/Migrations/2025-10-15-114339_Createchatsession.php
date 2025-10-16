<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Createchatsession extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'chatSessionID' => [
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

            'AgentID' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'constraint'     => 11
            ],

            'startTime' => [
                'type'           => 'DATETIME',
                'null'           => false,
            ],

            'endTime' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],

         ]);
         
            $this->forge->addKey('chatSessionID', true);
            $this->forge->addForeignKey('UserID', 'users', 'UserID', 'CASCADE', 'CASCADE');         
            $this->forge->createTable('chatSession');
    }

    public function down()
    {
        //
        $this->forge->dropTable('chatSession');
    }
}
