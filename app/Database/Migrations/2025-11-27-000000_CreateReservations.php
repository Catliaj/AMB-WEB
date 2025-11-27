<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReservations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'reservationID' => [
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
            'DownPayment' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0,
            ],
            'Term_Months' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'Monthly_Amortization' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'Buyer_Signature' => [
                'type' => 'BLOB',
                'null' => true,
            ],
            'Status' => [
                'type' => 'ENUM',
                'constraint' => "'Ongoing','Completed','Defaulted'",
                'default' => 'Ongoing'
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

        $this->forge->addKey('reservationID', true);
        $this->forge->addForeignKey('bookingID', 'booking', 'bookingID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('reservations');
    }

    public function down()
    {
        $this->forge->dropTable('reservations');
    }
}
