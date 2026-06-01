<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChatMessagesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nickname'   => ['type' => 'VARCHAR', 'constraint' => 32],
            'content'    => ['type' => 'TEXT'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('chat_messages', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('chat_messages', true);
    }
}
