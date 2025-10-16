<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Createmessages extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'messageID' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],

            'chatSessionID' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'constraint'     => 11
            ],

            'senderRole' => [
                'type'           => 'ENUM',
                'constraint'     => ['User', 'Agent'],
                'null'           => false,
            ],

            'messageContent' => [
                'type'           => 'TEXT',
                'null'           => false,
            ],

            'timestamp' => [
                'type'           => 'DATETIME',
                'null'           => false,
            ],
         ]);
         
            $this->forge->addKey('messageID', true);
            $this->forge->addForeignKey('chatSessionID', 'chatSession', 'chatSessionID', 'CASCADE', 'CASCADE');         
            $this->forge->createTable('messages');
    }

    public function down()
    {
        //
        $this->forge->dropTable('messages');
    }
}
