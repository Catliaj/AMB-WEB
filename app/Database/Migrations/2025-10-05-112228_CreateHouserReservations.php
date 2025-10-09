<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHouserReservation extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'reservationID' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],

            'bookingID' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'constraint'     => 11
            ],

            'DownPayment' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,2',
                'null'           => true,
            ],

            'Term_Months' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'           => true,
            ],

            'Monthly_Amortization' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,2',
                'null'           => true,
            ],

            'Buyer_Signature' => [
                'type'           => 'BLOB',
                'null'           => true,
            ],

            'Status' => [
                'type'           => 'ENUM',
                'constraint'     => ['Ongoing',  'Completed', 'Defaulted'],
                'null'           => true,
                'default'        => 'Ongoing',
            ],


        ]);

              $this->forge->addKey('reservationID', true);
              $this->forge->addForeignKey('bookingID', 'booking', 'bookingID', 'CASCADE', 'CASCADE');
              $this->forge->createTable('houserReservation');

           
    }

    public function down()
    {
        //
        $this->forge->dropTable('houserReservation');
    }
}
