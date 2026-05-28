<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 공식 codeigniter4/queue 패키지용 queue_jobs 테이블 생성.
 * (이전 마이그레이션에서 tableExists() 캐시 문제로 생성 누락된 것을 보완)
 */
class CreateOfficialQueueJobsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'           => ['type' => 'bigint', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'queue'        => ['type' => 'varchar', 'constraint' => 64, 'null' => false],
            'payload'      => ['type' => 'text', 'null' => false],
            'priority'     => ['type' => 'varchar', 'constraint' => 64, 'null' => false, 'default' => 'default'],
            'status'       => ['type' => 'tinyint', 'unsigned' => true, 'null' => false, 'default' => 0],
            'attempts'     => ['type' => 'tinyint', 'unsigned' => true, 'null' => false, 'default' => 0],
            'available_at' => ['type' => 'int', 'unsigned' => true, 'null' => false],
            'created_at'   => ['type' => 'int', 'unsigned' => true, 'null' => false],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['queue', 'status', 'available_at']);
        $this->forge->createTable('queue_jobs', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('queue_jobs', true);
    }
}
