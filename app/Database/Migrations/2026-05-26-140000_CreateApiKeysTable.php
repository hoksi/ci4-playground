<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApiKeysTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'           => ['type' => 'INTEGER', 'auto_increment' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'api_key'      => ['type' => 'VARCHAR', 'constraint' => 64, 'unique' => true],
            'is_active'    => ['type' => 'TINYINT', 'default' => 1],
            'last_used_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('api_keys');

        // 샘플 키 1개 삽입
        $this->db->table('api_keys')->insert([
            'name'       => '샘플 API 키 (데모용)',
            'api_key'    => bin2hex(random_bytes(32)),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('api_keys');
    }
}
