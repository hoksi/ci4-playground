<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

class QueueManager
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
        $this->ensureTables();
    }

    // ─── 잡 추가 ──────────────────────────────────────────
    public function push(string $jobClass, array $payload = [], string $queue = 'default', int $delaySeconds = 0): int
    {
        $now = time();
        $this->db->table('queue_jobs')->insert([
            'queue'        => $queue,
            'job_class'    => $jobClass,
            'payload'      => json_encode($payload),
            'status'       => 'pending',
            'attempts'     => 0,
            'max_attempts' => 3,
            'available_at' => $now + $delaySeconds,
            'started_at'   => null,
            'created_at'   => $now,
        ]);

        return (int) $this->db->insertID();
    }

    // ─── 다음 잡 처리 ─────────────────────────────────────
    public function processNext(string $queue = 'default'): array
    {
        $now = time();

        // pending 상태이고 available_at 이 지난 가장 오래된 잡 선택
        $job = $this->db->table('queue_jobs')
            ->where('queue', $queue)
            ->where('status', 'pending')
            ->where('available_at <=', $now)
            ->orderBy('created_at', 'ASC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (! $job) {
            return ['success' => false, 'message' => '처리할 작업이 없습니다.'];
        }

        // processing 으로 상태 변경
        $this->db->table('queue_jobs')->update(
            ['status' => 'processing', 'started_at' => $now, 'attempts' => (int) $job['attempts'] + 1],
            ['id' => $job['id']]
        );

        $payload = json_decode($job['payload'], true) ?? [];

        try {
            $jobClass  = $job['job_class'];
            $instance  = new $jobClass($payload);
            $result    = $instance->handle();

            // 완료
            $this->db->table('queue_jobs')->update(
                ['status' => 'done'],
                ['id' => $job['id']]
            );

            return [
                'success'  => true,
                'job_id'   => $job['id'],
                'job'      => class_basename($jobClass),
                'queue'    => $queue,
                'attempts' => (int) $job['attempts'] + 1,
                'result'   => $result,
            ];
        } catch (\Throwable $e) {
            $attempts = (int) $job['attempts'] + 1;

            if ($attempts >= (int) $job['max_attempts']) {
                // 최대 재시도 초과 → 실패 테이블로 이동
                $this->db->table('queue_failed_jobs')->insert([
                    'queue'      => $job['queue'],
                    'job_class'  => $job['job_class'],
                    'payload'    => $job['payload'],
                    'exception'  => $e->getMessage(),
                    'attempts'   => $attempts,
                    'failed_at'  => time(),
                    'created_at' => (int) $job['created_at'],
                ]);
                $this->db->table('queue_jobs')->delete(['id' => $job['id']]);

                return [
                    'success'   => false,
                    'job_id'    => $job['id'],
                    'job'       => class_basename($job['job_class']),
                    'queue'     => $queue,
                    'attempts'  => $attempts,
                    'failed'    => true,
                    'exception' => $e->getMessage(),
                ];
            }

            // 재시도 대기 (5초 후)
            $this->db->table('queue_jobs')->update(
                ['status' => 'pending', 'available_at' => time() + 5],
                ['id' => $job['id']]
            );

            return [
                'success'   => false,
                'job_id'    => $job['id'],
                'job'       => class_basename($job['job_class']),
                'queue'     => $queue,
                'attempts'  => $attempts,
                'failed'    => false,
                'exception' => $e->getMessage(),
                'retry_in'  => 5,
            ];
        }
    }

    // ─── 실패 잡 재시도 ───────────────────────────────────
    public function retry(int $failedJobId): bool
    {
        $failed = $this->db->table('queue_failed_jobs')
            ->where('id', $failedJobId)
            ->get()->getRowArray();

        if (! $failed) {
            return false;
        }

        $this->push(
            $failed['job_class'],
            json_decode($failed['payload'], true) ?? [],
            $failed['queue']
        );

        $this->db->table('queue_failed_jobs')->delete(['id' => $failedJobId]);

        return true;
    }

    // ─── 통계 ─────────────────────────────────────────────
    public function stats(string $queue = 'default'): array
    {
        $pending    = $this->db->table('queue_jobs')->where('queue', $queue)->where('status', 'pending')->countAllResults();
        $processing = $this->db->table('queue_jobs')->where('queue', $queue)->where('status', 'processing')->countAllResults();
        $done       = $this->db->table('queue_jobs')->where('queue', $queue)->where('status', 'done')->countAllResults();
        $failed     = $this->db->table('queue_failed_jobs')->where('queue', $queue)->countAllResults();

        return compact('pending', 'processing', 'done', 'failed');
    }

    // ─── 목록 조회 ────────────────────────────────────────
    public function getPending(string $queue = 'default', int $limit = 20): array
    {
        return $this->db->table('queue_jobs')
            ->where('queue', $queue)
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'ASC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    public function getDone(string $queue = 'default', int $limit = 20): array
    {
        return $this->db->table('queue_jobs')
            ->where('queue', $queue)
            ->where('status', 'done')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    public function getFailed(string $queue = 'default', int $limit = 20): array
    {
        return $this->db->table('queue_failed_jobs')
            ->where('queue', $queue)
            ->orderBy('failed_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    // ─── 전체 초기화 ──────────────────────────────────────
    public function clear(string $queue = 'default'): void
    {
        $this->db->table('queue_jobs')->where('queue', $queue)->delete();
        $this->db->table('queue_failed_jobs')->where('queue', $queue)->delete();
    }

    // ─── 테이블 자동 생성 ─────────────────────────────────
    private function ensureTables(): void
    {
        if (! $this->db->tableExists('queue_jobs')) {
            \Config\Services::migrations()->latest();
        }
    }
}
