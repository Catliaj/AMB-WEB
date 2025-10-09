<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertyImage extends Migration
{
    public function up()
    {
        //
            $this->forge->addField([
                'propertyImageID' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true
                ],
    
                'PropertyID' => [
                    'type'           => 'INT',
                    'unsigned'       => true,
                    'constraint'     => 11
                ],
    
                'Image' => [
                    'type'           => 'BLOB',
                    'null'           => true,
                ],
    
            ]);
            
                $this->forge->addKey('propertyImageID', true);
                $this->forge->addForeignKey('PropertyID', 'property', 'PropertyID', 'CASCADE', 'CASCADE');
                $this->forge->createTable('propertyImage');
    }

    public function down()
    {
        //
        $this->forge->dropTable('propertyImage');
    }
}
