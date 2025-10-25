<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        $this->call('UserSeeder');
        $this->call('PropertySeeder');
        $this->call('PropertystatushistorySeeder');
        $this->call('BookingsSeeder');
        $this->call('SessionSeeder');
        $this->call('MessageSeeder');

        echo "✅ All seeders executed successfully!\n";
    }
}
