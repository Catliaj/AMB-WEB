<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContracts extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'contractID' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'bookingID' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'mode' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'monthly' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'proposedBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'confirmedBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => "'proposed','confirmed','rejected'",
                'default' => 'proposed'
            ],
            'confirmed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('contractID', true);
        $this->forge->addForeignKey('bookingID', 'booking', 'bookingID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('contract');
    }

    public function down()
    {
        $this->forge->dropTable('contract');
    }
}
