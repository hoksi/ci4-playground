<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCatsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 32, 'default' => '냥이'],
            'hunger'       => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 70],
            'happiness'    => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 70],
            'energy'       => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 70],
            'last_updated' => ['type' => 'DATETIME', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('cats', true);

        $now = date('Y-m-d H:i:s');
        $this->db->table('cats')->insert([
            'name'         => '냥이',
            'hunger'       => 70,
            'happiness'    => 70,
            'energy'       => 70,
            'last_updated' => $now,
            'created_at'   => $now,
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('cats', true);
    }
}
