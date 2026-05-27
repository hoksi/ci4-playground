<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AllSeeder extends Seeder
{
    public function run(): void
    {
        $this->call('PostSeeder');
        $this->call('AccountSeeder');
        $this->call('ProductsSeeder');
    }
}
