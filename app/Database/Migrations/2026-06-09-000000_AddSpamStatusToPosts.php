<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSpamStatusToPosts extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('posts', [
            'spam_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'approved',
                'after'      => 'author',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('posts', 'spam_status');
    }
}
