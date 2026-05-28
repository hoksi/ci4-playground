<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Tasks\Config\Tasks as BaseTasks;
use CodeIgniter\Tasks\Scheduler;

class Tasks extends BaseTasks
{
    public bool $logPerformance = false;
    public int $maxLogsPerTask  = 20;

    public function init(Scheduler $schedule): void
    {
        // ① 헬스체크 — 매분 (DB 연결 + 테이블 수 확인)
        $schedule->call(static function () {
            $db     = \Config\Database::connect();
            $tables = count($db->listTables());
            log_message('info', "[Task:health-check] 테이블 {$tables}개 정상");
            return "DB 연결 정상 · 테이블 {$tables}개";
        })->everyMinute()->named('health-check');

        // ② 큐 모니터링 — 5분마다 (대기/실패 잡 수 기록)
        $schedule->call(static function () {
            $db      = \Config\Database::connect();
            $pending = $db->tableExists('queue_jobs')
                ? $db->table('queue_jobs')->where('status', 0)->countAllResults()
                : 0;
            $failed = $db->tableExists('queue_jobs_failed')
                ? $db->table('queue_jobs_failed')->countAllResults()
                : 0;
            log_message('info', "[Task:queue-monitor] 대기 {$pending} · 실패 {$failed}");
            return "대기 잡 {$pending}개 · 실패 잡 {$failed}개";
        })->everyFiveMinutes()->named('queue-monitor');

        // ③ 캐시 정리 — 매시간 (Spark 커맨드 방식)
        $schedule->command('cache:clear')->hourly()->named('cache-clear');

        // ④ 일일 통계 — 매일 자정 (게시글·계정 수 집계)
        $schedule->call(static function () {
            $db       = \Config\Database::connect();
            $posts    = $db->tableExists('posts')
                ? $db->table('posts')->where('deleted_at IS NULL', null, false)->countAllResults()
                : 0;
            $accounts = $db->tableExists('accounts')
                ? $db->table('accounts')->countAllResults()
                : 0;
            log_message('info', "[Task:daily-stats] 게시글 {$posts} · 계정 {$accounts}");
            return "게시글 {$posts}개 · 계정 {$accounts}개";
        })->daily()->named('daily-stats');

        // ⑤ 플레이그라운드 초기화 — 매일 새벽 3시, 운영 환경에서만
        $schedule->command('playground:reset --db-only --quiet')
            ->daily('3:00 am')
            ->environments('production')
            ->named('playground-reset');
    }
}
