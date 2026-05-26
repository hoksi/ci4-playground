<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQueueTables extends Migration
{
    public function up(): void
    {
        // 대기/처리 중 작업 테이블
        $this->forge->addField([
            'id'           => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'queue'        => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'default'],
            'job_class'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'payload'      => ['type' => 'TEXT'],
            'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'attempts'     => ['type' => 'INTEGER', 'constraint' => 11, 'default' => 0],
            'max_attempts' => ['type' => 'INTEGER', 'constraint' => 11, 'default' => 3],
            'available_at' => ['type' => 'INTEGER', 'constraint' => 11],
            'started_at'   => ['type' => 'INTEGER', 'constraint' => 11, 'null' => true],
            'created_at'   => ['type' => 'INTEGER', 'constraint' => 11],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('queue_jobs', true);

        // 실패 작업 테이블
        $this->forge->addField([
            'id'         => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'queue'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'job_class'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'payload'    => ['type' => 'TEXT'],
            'exception'  => ['type' => 'TEXT'],
            'attempts'   => ['type' => 'INTEGER', 'constraint' => 11, 'default' => 0],
            'failed_at'  => ['type' => 'INTEGER', 'constraint' => 11],
            'created_at' => ['type' => 'INTEGER', 'constraint' => 11],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('queue_failed_jobs', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('queue_jobs', true);
        $this->forge->dropTable('queue_failed_jobs', true);
    }
}
