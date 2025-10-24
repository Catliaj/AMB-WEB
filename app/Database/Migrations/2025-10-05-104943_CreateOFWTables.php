<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOFWTable extends Migration
{
    public function up()
    {
        //
         $this->forge->addField([
            'ofwID' => [
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

            'Job_Contract' => [
                'type'           => 'BLOB',
                'null'           => true,
            ],

            'Passport' => [
                'type'           => 'BLOB',
                'null'           => true,
            ],

            'Official_Identity_Documents' => [
                'type'           => 'BLOB',
                'null'           => true,
            ],


         ]);
         
            $this->forge->addKey('ofwID', true);
            $this->forge->addForeignKey('UserID', 'users', 'UserID', 'CASCADE', 'CASCADE');
            
            $this->forge->createTable('ofw');
    }

    public function down()
    {
        //
        $this->forge->dropTable('ofw');
    }
}
