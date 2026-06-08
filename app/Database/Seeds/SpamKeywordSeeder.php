<?php

namespace App\Database\Seeds;

use App\Services\SpamChecker;
use CodeIgniter\Database\Seeder;

class SpamKeywordSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        foreach (SpamChecker::BUILTIN_KEYWORDS as $keyword) {
            $exists = $this->db->table('spam_keywords')
                ->where('keyword', mb_strtolower($keyword))
                ->countAllResults();

            if ($exists === 0) {
                $this->db->table('spam_keywords')->insert([
                    'keyword'    => mb_strtolower($keyword),
                    'frequency'  => 0,
                    'active'     => 1,
                    'is_builtin' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
