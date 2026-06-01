<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class OfficialQueue extends BaseController
{
    private string $queueName = 'playground';

    public function index(): string
    {
        return view('examples/official_queue/index', $this->stats());
    }

    // ─── 잡 추가 ─────────────────────────────────────────────────────────────

    public function push(): ResponseInterface
    {
        $jobKey = $this->request->getPost('job');
        $delay  = (int) $this->request->getPost('delay', FILTER_SANITIZE_NUMBER_INT);

        $dataMap = [
            'send-email' => [
                'to'      => $this->request->getPost('email') ?: 'user@example.com',
                'subject' => '플레이그라운드 알림',
            ],
            'process-data' => [
                'batch_id'     => 'BATCH-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                'record_count' => random_int(50, 500),
            ],
            'generate-report' => [
                'report_type' => $this->request->getPost('report_type') ?: 'daily',
                'force_fail'  => (bool) $this->request->getPost('force_fail'),
            ],
        ];

        if (! isset($dataMap[$jobKey])) {
            return $this->response->setJSON(['ok' => false, 'message' => '잘못된 잡 유형입니다.']);
        }

        $queue  = service('queue'); // @phpstan-ignore-line
        $result = $delay > 0
            ? $queue->setDelay($delay)->push($this->queueName, $jobKey, $dataMap[$jobKey]) // @phpstan-ignore-line
            : $queue->push($this->queueName, $jobKey, $dataMap[$jobKey]); // @phpstan-ignore-line

        if ($result->getStatus()) {
            return $this->response->setJSON([
                'ok'      => true,
                'message' => "잡 [{$jobKey}] 추가 완료 (ID: {$result->getJobId()})",
                'stats'   => $this->stats(),
            ]);
        }

        return $this->response->setJSON(['ok' => false, 'message' => '잡 추가 실패: ' . $result->getError()]);
    }

    // ─── 잡 처리 (1건) ───────────────────────────────────────────────────────

    public function processNext(): ResponseInterface
    {
        $config = config('Queue');
        $queue  = service('queue'); // @phpstan-ignore-line

        $work = $queue->pop($this->queueName, ['default']); // @phpstan-ignore-line

        if ($work === null) {
            return $this->response->setJSON([
                'ok'      => false,
                'status'  => 'empty',
                'message' => '처리할 잡이 없습니다.',
                'stats'   => $this->stats(),
            ]);
        }

        $payload = $work->payload;
        $start   = microtime(true);

        try {
            $class = $config->resolveJobClass($payload['job']);
            $job   = new $class($payload['data']);
            $job->process();

            $queue->done($work); // @phpstan-ignore-line
            $elapsed = round(microtime(true) - $start, 3);

            return $this->response->setJSON([
                'ok'      => true,
                'status'  => 'success',
                'message' => "잡 [{$payload['job']}] ID #{$work->id} 처리 완료 ({$elapsed}s)",
                'job'     => $payload['job'],
                'elapsed' => $elapsed,
                'stats'   => $this->stats(),
            ]);
        } catch (Throwable $e) {
            $job     ??= null;
            $tries    = isset($job) ? $job->getTries() : 1;
            $after    = isset($job) ? $job->getRetryAfter() : 60;

            if (++$work->attempts < $tries) {
                $queue->later($work, $after); // @phpstan-ignore-line
                $status  = 'retry';
                $message = "잡 [{$payload['job']}] 재시도 예약 (시도 {$work->attempts}/{$tries})";
            } else {
                $queue->failed($work, $e, $config->keepFailedJobs); // @phpstan-ignore-line
                $status  = 'failed';
                $message = "잡 [{$payload['job']}] 실패: " . $e->getMessage();
            }

            return $this->response->setJSON([
                'ok'      => false,
                'status'  => $status,
                'message' => $message,
                'stats'   => $this->stats(),
            ]);
        }
    }

    // ─── 통계 JSON ───────────────────────────────────────────────────────────

    public function statsJson(): ResponseInterface
    {
        return $this->response->setJSON($this->stats());
    }

    // ─── 실패 잡 재시도 ──────────────────────────────────────────────────────

    public function retryFailed(): ResponseInterface
    {
        $id    = (int) $this->request->getPost('id');
        $count = service('queue')->retry($id ?: null, $this->queueName); // @phpstan-ignore-line

        return $this->response->setJSON([
            'ok'      => true,
            'message' => "{$count}건 재시도 큐에 추가됨",
            'stats'   => $this->stats(),
        ]);
    }

    // ─── 큐 초기화 ───────────────────────────────────────────────────────────

    public function clear(): ResponseInterface
    {
        service('queue')->clear($this->queueName); // @phpstan-ignore-line

        return $this->response->setJSON([
            'ok'      => true,
            'message' => '큐가 초기화되었습니다.',
            'stats'   => $this->stats(),
        ]);
    }

    // ─── 헬퍼 ───────────────────────────────────────────────────────────────

    private function stats(): array
    {
        $db      = \Config\Database::connect();
        $pending = $db->table('queue_jobs')
            ->where('queue', $this->queueName)
            ->where('status', 0)
            ->countAllResults();

        $processing = $db->table('queue_jobs')
            ->where('queue', $this->queueName)
            ->where('status', 1)
            ->countAllResults();

        $failed = $db->table('queue_jobs_failed')
            ->where('queue', $this->queueName)
            ->countAllResults();

        $pendingJobs = $db->table('queue_jobs')
            ->where('queue', $this->queueName)
            ->where('status !=', 1)
            ->orderBy('id', 'DESC')
            ->limit(20)
            ->get()->getResultArray();

        $failedJobs = $db->table('queue_jobs_failed')
            ->where('queue', $this->queueName)
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        return compact('pending', 'processing', 'failed', 'pendingJobs', 'failedJobs');
    }
}
