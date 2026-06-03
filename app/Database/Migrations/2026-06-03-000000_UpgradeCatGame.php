<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpgradeCatGame extends Migration
{
    public function up(): void
    {
        // cats 테이블에 레벨·경험치 컬럼 추가
        $this->forge->addColumn('cats', [
            'level' => ['type' => 'INT', 'unsigned' => true, 'default' => 1, 'after' => 'energy'],
            'exp'   => ['type' => 'INT', 'unsigned' => true, 'default' => 0, 'after' => 'level'],
        ]);

        // 상태 히스토리 테이블
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'cat_id'      => ['type' => 'INT', 'unsigned' => true],
            'hunger'      => ['type' => 'TINYINT', 'unsigned' => true],
            'happiness'   => ['type' => 'TINYINT', 'unsigned' => true],
            'energy'      => ['type' => 'TINYINT', 'unsigned' => true],
            'level'       => ['type' => 'INT', 'unsigned' => true, 'default' => 1],
            'recorded_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('cat_id');
        $this->forge->createTable('cat_history', true);
    }

    public function down(): void
    {
        $this->forge->dropColumn('cats', ['level', 'exp']);
        $this->forge->dropTable('cat_history', true);
    }
}
