<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBooking extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'bookingID' => [
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

            'PropertyID' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'constraint'     => 11
            ],

            'BookingDate' => [
                'type'           => 'DATE',
                'null'           => true,
            ],

            'Status' => [
                'type'           => 'ENUM',
                'constraint'     => ['Pending',  'Confirmed', 'Cancelled'],
                'null'           => true,
                'default'        => 'Pending',
            ],

         ]);

            $this->forge->addKey('bookingID', true);
            $this->forge->addForeignKey('UserID', 'users', 'UserID', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('PropertyID', 'property', 'PropertyID', 'CASCADE', 'CASCADE');  
            $this->forge->createTable('booking');
    }

    public function down()
    {
        //
        $this->forge->dropTable('booking');
    }
}
