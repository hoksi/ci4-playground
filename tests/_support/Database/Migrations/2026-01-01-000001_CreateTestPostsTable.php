<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTestPostsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INTEGER', 'auto_increment' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 200],
            'content'    => ['type' => 'TEXT'],
            'author'     => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => '익명'],
            'views'      => ['type' => 'INTEGER', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('posts');
    }

    public function down(): void
    {
        $this->forge->dropTable('posts');
    }
}
