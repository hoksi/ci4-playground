<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * 플레이그라운드 전체 리셋 커맨드.
 * 공개 서버에서 주기적으로 데이터를 초기 상태로 복원할 때 사용합니다.
 *
 * cron 등록 예시:
 *   0 3 * * * cd /var/www/playground && php spark playground:reset >> writable/logs/reset.log 2>&1
 */
class PlaygroundReset extends BaseCommand
{
    protected $group       = 'Playground';
    protected $name        = 'playground:reset';
    protected $description = '플레이그라운드 데이터·업로드 파일·캐시를 초기 상태로 리셋합니다.';
    protected $usage       = 'playground:reset [--db-only] [--files-only] [--quiet]';
    protected $options     = [
        '--db-only'    => 'DB 데이터만 리셋 (파일·캐시 유지)',
        '--files-only' => '업로드 파일만 리셋 (DB 유지)',
        '--quiet'      => '출력 최소화 (cron 로그용)',
    ];

    /** 리셋 대상 테이블 → 시더 매핑 (null = 시더 없이 truncate만) */
    private array $tables = [
        'posts'             => 'PostSeeder',
        'accounts'          => 'AccountSeeder',
        'playground_products' => 'ProductsSeeder',
        'users_demo'        => null,
        'api_keys'          => null,
        'auth_users'        => null,
        'queue_jobs'        => null,
        'queue_failed_jobs' => null,
    ];

    public function run(array $params): void
    {
        $dbOnly    = array_key_exists('db-only',    $params);
        $filesOnly = array_key_exists('files-only', $params);
        $quiet     = array_key_exists('quiet',      $params);

        $this->log('========================================', $quiet);
        $this->log('Playground Reset — ' . date('Y-m-d H:i:s'), $quiet);
        $this->log('========================================', $quiet);

        if (! $filesOnly) {
            $this->resetDatabase($quiet);
        }

        if (! $dbOnly) {
            $this->resetUploads($quiet);
            $this->resetCache($quiet);
        }

        CLI::write('[완료] 플레이그라운드 리셋이 완료되었습니다.', 'green');
        $this->log('[완료] ' . date('Y-m-d H:i:s'), $quiet);
    }

    // ─── DB 리셋 ─────────────────────────────────────────────────────────────

    private function resetDatabase(bool $quiet): void
    {
        $this->log('[DB] 데이터 초기화 시작...', $quiet, 'yellow');
        $db = \Config\Database::connect();

        $db->transStart();

        foreach ($this->tables as $table => $seeder) {
            if (! $db->tableExists($table)) {
                $this->log("  - {$table} 테이블 없음, 건너뜀", $quiet, 'dark_gray');
                continue;
            }
            $db->table($table)->truncate();
            $this->log("  - {$table} truncate 완료", $quiet);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            CLI::error('[DB] truncate 실패 — 트랜잭션 롤백됨');
            return;
        }

        // 시더 실행
        $seederRunner = \Config\Database::seeder();
        $seededTables = [];

        foreach ($this->tables as $table => $seederClass) {
            if ($seederClass === null || in_array($seederClass, $seededTables)) {
                continue;
            }
            try {
                $seederRunner->call($seederClass);
                $seededTables[] = $seederClass;
                $this->log("  - {$seederClass} 시드 완료", $quiet);
            } catch (\Throwable $e) {
                CLI::error("  - {$seederClass} 시드 실패: " . $e->getMessage());
            }
        }

        $this->log('[DB] 완료', $quiet, 'green');
    }

    // ─── 업로드 파일 리셋 ────────────────────────────────────────────────────

    private function resetUploads(bool $quiet): void
    {
        $uploadsDir = WRITEPATH . 'uploads/';
        $seedDir    = WRITEPATH . 'uploads_seed/';

        if (! is_dir($seedDir)) {
            $this->log('[Files] uploads_seed/ 디렉터리 없음, 건너뜀', $quiet, 'dark_gray');
            return;
        }

        $this->log('[Files] 업로드 파일 초기화 시작...', $quiet, 'yellow');

        // uploads/ 내 파일 삭제 (index.html 제외)
        $deleted = $this->cleanDirectory($uploadsDir, ['index.html']);
        $this->log("  - {$deleted}개 파일 삭제", $quiet);

        // uploads_seed/ → uploads/ 복사
        $copied = $this->copyDirectory($seedDir, $uploadsDir);
        $this->log("  - {$copied}개 파일 복원", $quiet);

        $this->log('[Files] 완료', $quiet, 'green');
    }

    // ─── 캐시 리셋 ───────────────────────────────────────────────────────────

    private function resetCache(bool $quiet): void
    {
        $this->log('[Cache] 캐시 초기화 시작...', $quiet, 'yellow');

        try {
            $cache   = \Config\Services::cache();
            $cache->clean();
            $this->log('[Cache] 완료', $quiet, 'green');
        } catch (\Throwable $e) {
            CLI::error('[Cache] 실패: ' . $e->getMessage());
        }
    }

    // ─── 헬퍼 ───────────────────────────────────────────────────────────────

    /**
     * 디렉터리 내 파일을 재귀 삭제. $skip 파일명은 보존.
     */
    private function cleanDirectory(string $dir, array $skip = []): int
    {
        if (! is_dir($dir)) {
            return 0;
        }

        $deleted = 0;
        $items   = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isFile() && ! in_array($item->getFilename(), $skip)) {
                unlink($item->getRealPath());
                $deleted++;
            } elseif ($item->isDir()) {
                // uploads_seed 에서 복원되므로 빈 하위 디렉터리 삭제
                @rmdir($item->getRealPath());
            }
        }

        return $deleted;
    }

    /**
     * $src 디렉터리를 $dst 로 재귀 복사.
     */
    private function copyDirectory(string $src, string $dst): int
    {
        $copied = 0;
        $items  = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            $target = $dst . $items->getSubPathname();
            if ($item->isDir()) {
                if (! is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                copy($item->getRealPath(), $target);
                $copied++;
            }
        }

        return $copied;
    }

    private function log(string $message, bool $quiet, string $color = 'white'): void
    {
        if (! $quiet) {
            CLI::write($message, $color);
        }
        // cron 환경에서는 STDOUT으로 항상 기록
        if ($quiet) {
            echo $message . PHP_EOL;
        }
    }
}
