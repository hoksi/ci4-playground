<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'category'   => ['type' => 'VARCHAR', 'constraint' => 50],
            'price'      => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'stock'      => ['type' => 'INTEGER', 'constraint' => 11, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('playground_products', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('playground_products', true);
    }
}
