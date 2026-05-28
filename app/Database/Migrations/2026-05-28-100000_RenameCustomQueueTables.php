<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 공식 codeigniter4/queue 패키지와 커스텀 큐(#40)의 테이블 분리.
 *
 * 공식 패키지가 2023년 마이그레이션으로 queue_jobs(공식 스키마)를 먼저 생성하므로
 * 커스텀 큐는 custom_queue_jobs(job_class 포함) 테이블을 별도로 만든다.
 */
class RenameCustomQueueTables extends Migration
{
    public function up(): void
    {
        $db = \Config\Database::connect();

        // 커스텀 큐 잡 테이블 (job_class 컬럼 포함)
        if (! $db->tableExists('custom_queue_jobs')) {
            $this->forge->addField([
                'id'           => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
                'queue'        => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => false],
                'job_class'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
                'payload'      => ['type' => 'TEXT', 'null' => true],
                'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
                'attempts'     => ['type' => 'INTEGER', 'constraint' => 11, 'default' => 0],
                'max_attempts' => ['type' => 'INTEGER', 'constraint' => 11, 'default' => 3],
                'available_at' => ['type' => 'DATETIME', 'null' => true],
                'started_at'   => ['type' => 'DATETIME', 'null' => true],
                'created_at'   => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addPrimaryKey('id');
            $this->forge->createTable('custom_queue_jobs');
        }

        // 커스텀 큐 실패 잡 테이블 이름 변경 또는 생성
        if ($db->tableExists('queue_failed_jobs') && ! $db->tableExists('custom_queue_failed_jobs')) {
            $db->query('ALTER TABLE queue_failed_jobs RENAME TO custom_queue_failed_jobs');
        } elseif (! $db->tableExists('custom_queue_failed_jobs')) {
            $this->forge->addField([
                'id'         => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
                'queue'      => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => false],
                'job_class'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
                'payload'    => ['type' => 'TEXT', 'null' => true],
                'exception'  => ['type' => 'TEXT', 'null' => true],
                'attempts'   => ['type' => 'INTEGER', 'constraint' => 11, 'default' => 0],
                'failed_at'  => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addPrimaryKey('id');
            $this->forge->createTable('custom_queue_failed_jobs');
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('custom_queue_jobs', true);
        $this->forge->dropTable('custom_queue_failed_jobs', true);
    }
}
