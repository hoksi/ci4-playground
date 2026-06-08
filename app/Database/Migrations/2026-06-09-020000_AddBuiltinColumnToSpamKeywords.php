<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBuiltinColumnToSpamKeywords extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('spam_keywords', [
            'is_builtin' => [
                'type'    => 'TINYINT',
                'default' => 0,
                'after'   => 'active',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('spam_keywords', 'is_builtin');
    }
}
