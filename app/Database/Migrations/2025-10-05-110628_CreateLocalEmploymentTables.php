<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLocalEmploymentTable extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'localEmploymentID' => [
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
            
            'Id_With_Signature' => [
                'type'           => 'BLOB',
                'null'           => true,
            ],

            'Payslip' => [
                'type'           => 'BLOB',
                'null'           => true,
            ],

            'proof_of_billing' => [
                'type'           => 'BLOB',
                'null'           => true,
            ],
        ]);

           $this->forge->addKey('localEmploymentID', true);
           $this->forge->addForeignKey('UserID', 'users', 'UserID', 'CASCADE', 'CASCADE');
           $this->forge->createTable('localEmployment');
    }

    public function down()
    {
        //
        $this->forge->dropTable('localEmployment');
    }
}
