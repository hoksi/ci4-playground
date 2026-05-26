<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class MultiDb extends BaseController
{
    private string $secondaryPath = WRITEPATH . 'secondary.db';

    public function index(): string
    {
        // 보조 DB가 없으면 초기화
        $this->ensureSecondaryDb();

        $primary = \Config\Database::connect();
        $second  = $this->getSecondary();

        // 기본 DB 테이블 목록
        $primaryTables = $this->getTableList($primary);
        $secondTables  = $this->getTableList($second);

        return view('examples/multidb/index', [
            'title'          => '다중 DB 연결',
            'primaryTables'  => $primaryTables,
            'secondTables'   => $secondTables,
            'secondaryPath'  => str_replace(WRITEPATH, 'writable/', $this->secondaryPath),
        ]);
    }

    public function query()
    {
        $target = $this->request->getPost('target') === 'secondary' ? 'secondary' : 'default';

        try {
            $db = $target === 'secondary' ? $this->getSecondary() : \Config\Database::connect();

            $tables = $this->getTableList($db);
            $sample = [];
            $totalRows = 0;

            if (! empty($tables)) {
                // 첫 번째 테이블 샘플 조회
                $firstTable = $tables[0];
                $totalRows  = $db->table($firstTable)->countAllResults();
                $sample     = $db->table($firstTable)->limit(5)->get()->getResultArray();
            }

            return $this->response->setJSON([
                'success'     => true,
                'target'      => $target,
                'driver'      => $db->getPlatform(),
                'database'    => $db->getDatabase(),
                'tables'      => $tables,
                'first_table' => $tables[0] ?? null,
                'sample'      => $sample,
                'total_rows'  => $totalRows,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'target'  => $target,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // ─── 헬퍼 ─────────────────────────────────────────────
    private function getSecondary()
    {
        return \Config\Database::connect([
            'DSN'      => '',
            'hostname' => '',
            'username' => '',
            'password' => '',
            'database' => $this->secondaryPath,
            'DBDriver' => 'SQLite3',
            'DBPrefix' => '',
            'pConnect' => false,
            'DBDebug'  => true,
            'charset'  => 'utf8',
            'DBCollat' => 'utf8_general_ci',
            'swapPre'  => '',
            'encrypt'  => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port'     => 0,
        ]);
    }

    private function ensureSecondaryDb(): void
    {
        if (file_exists($this->secondaryPath)) {
            return;
        }
        // 빈 파일 생성
        if (! is_dir(WRITEPATH)) {
            mkdir(WRITEPATH, 0777, true);
        }
        touch($this->secondaryPath);

        // 샘플 테이블/데이터 생성
        try {
            $db = $this->getSecondary();
            $forge = \Config\Database::forge($db);

            $forge->addField([
                'id'         => ['type' => 'INTEGER', 'auto_increment' => true],
                'product'    => ['type' => 'VARCHAR', 'constraint' => 100],
                'price'      => ['type' => 'INTEGER', 'default' => 0],
                'stock'      => ['type' => 'INTEGER', 'default' => 0],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addPrimaryKey('id');
            $forge->createTable('products', true);

            $db->table('products')->insertBatch([
                ['product' => '키보드',     'price' => 89000,  'stock' => 12, 'created_at' => date('Y-m-d H:i:s')],
                ['product' => '마우스',     'price' => 32000,  'stock' => 45, 'created_at' => date('Y-m-d H:i:s')],
                ['product' => '모니터',     'price' => 380000, 'stock' => 8,  'created_at' => date('Y-m-d H:i:s')],
                ['product' => 'USB 허브',   'price' => 15000,  'stock' => 33, 'created_at' => date('Y-m-d H:i:s')],
                ['product' => '웹캠',       'price' => 65000,  'stock' => 17, 'created_at' => date('Y-m-d H:i:s')],
            ]);
        } catch (\Throwable $e) {
            // 무시 (UI에서 표시됨)
        }
    }

    private function getTableList($db): array
    {
        try {
            return $db->listTables() ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
