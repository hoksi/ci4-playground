<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $this->db->table('accounts')->truncate();
        $this->db->table('accounts')->insertBatch([
            ['name' => '홍길동', 'balance' => 100000],
            ['name' => '김철수', 'balance' => 50000],
            ['name' => '이영희', 'balance' => 200000],
        ]);
    }
}
