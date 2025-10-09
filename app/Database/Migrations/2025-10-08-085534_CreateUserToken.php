<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserToken extends Migration
{
    public function up()
    {
        //
         $this->forge->addField([
            'tokenID' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],

            'userID' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'constraint'     => 11
                
            ],

            'token' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => false,
            ],

            'expires_at' => [
                'type'           => 'DATETIME',
                'null'           => false,
            ],

            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => false,
            ],
        ]);

              $this->forge->addKey('tokenID', true);
              $this->forge->addForeignKey('userID', 'users', 'userID', 'CASCADE', 'CASCADE');
              $this->forge->createTable('user_tokens');


    }

    public function down()
    {
        //
        $this->forge->dropTable('user_tokens');
    }
}
