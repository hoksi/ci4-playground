<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCatDeathMechanic extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('cats', [
            'critical_since' => ['type' => 'DATETIME', 'null' => true, 'default' => null, 'after' => 'act_pet'],
            'is_dead'        => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0, 'after' => 'critical_since'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('cats', ['critical_since', 'is_dead']);
    }
}
