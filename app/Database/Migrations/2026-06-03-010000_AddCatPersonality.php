<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCatPersonality extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('cats', [
            'personality' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'default' => null, 'after' => 'exp'],
            'act_feed'    => ['type' => 'INT', 'unsigned' => true, 'default' => 0, 'after' => 'personality'],
            'act_play'    => ['type' => 'INT', 'unsigned' => true, 'default' => 0, 'after' => 'act_feed'],
            'act_sleep'   => ['type' => 'INT', 'unsigned' => true, 'default' => 0, 'after' => 'act_play'],
            'act_pet'     => ['type' => 'INT', 'unsigned' => true, 'default' => 0, 'after' => 'act_sleep'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('cats', ['personality', 'act_feed', 'act_play', 'act_sleep', 'act_pet']);
    }
}
