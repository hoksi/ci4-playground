<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSpamKeywordsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'keyword' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'frequency' => [
                'type'    => 'INTEGER',
                'default' => 1,
            ],
            'active' => [
                'type'    => 'TINYINT',
                'default' => 1,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('keyword');
        $this->forge->createTable('spam_keywords');
    }

    public function down(): void
    {
        $this->forge->dropTable('spam_keywords');
    }
}
