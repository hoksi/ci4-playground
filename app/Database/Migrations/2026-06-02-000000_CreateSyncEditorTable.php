<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSyncEditorTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'content'    => ['type' => 'TEXT', 'null' => true],
            'version'    => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'client_id'  => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('sync_docs', true);

        // 초기 문서 1건 삽입
        $this->db->table('sync_docs')->insert([
            'content'    => "CI4 Playground — 실시간 동기화 에디터\n\n이 텍스트를 수정해보세요.\n다른 탭이나 브라우저에서 이 페이지를 열면 변경 사항이 실시간으로 반영됩니다.",
            'version'    => 1,
            'client_id'  => null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('sync_docs', true);
    }
}
