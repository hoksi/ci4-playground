<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Libraries\QueueManager;

class Queue extends BaseController
{
    private QueueManager $queue;

    public function __construct()
    {
        $this->queue = new QueueManager();
    }

    public function index(): string
    {
        $queueName = $this->request->getGet('queue') ?? 'default';

        return view('examples/queue/index', [
            'title'     => '큐(Queue) 시스템',
            'queueName' => $queueName,
            'stats'     => $this->queue->stats($queueName),
            'pending'   => $this->queue->getPending($queueName),
            'done'      => $this->queue->getDone($queueName, 10),
            'failed'    => $this->queue->getFailed($queueName, 10),
        ]);
    }

    // ─── 잡 추가 ──────────────────────────────────────────
    public function push()
    {
        $jobType = $this->request->getPost('job_type') ?? 'email';
        $queue   = $this->request->getPost('queue') ?? 'default';
        $delay   = max(0, (int) ($this->request->getPost('delay') ?? 0));

        $jobMap = [
            'email'  => \App\Jobs\EmailNotificationJob::class,
            'data'   => \App\Jobs\DataProcessJob::class,
            'report' => \App\Jobs\ReportGenerateJob::class,
        ];

        if (! isset($jobMap[$jobType])) {
            return $this->response->setJSON(['success' => false, 'message' => '알 수 없는 잡 유형입니다.']);
        }

        $payload = match ($jobType) {
            'email'  => [
                'to'      => $this->request->getPost('to') ?? 'user@example.com',
                'subject' => $this->request->getPost('subject') ?? '알림 메일',
            ],
            'data'   => [
                'items' => (int) ($this->request->getPost('items') ?? 100),
                'type'  => $this->request->getPost('data_type') ?? 'csv',
            ],
            'report' => [
                'period'     => $this->request->getPost('period') ?? 'monthly',
                'format'     => $this->request->getPost('format') ?? 'pdf',
                'force_fail' => (bool) $this->request->getPost('force_fail'),
            ],
            default => [],
        };

        $id = $this->queue->push($jobMap[$jobType], $payload, $queue, $delay);

        return $this->response->setJSON([
            'success' => true,
            'message' => "잡 #{$id} 이 '{$queue}' 큐에 추가되었습니다." . ($delay > 0 ? " ({$delay}초 후 실행)" : ''),
            'job_id'  => $id,
            'stats'   => $this->queue->stats($queue),
        ]);
    }

    // ─── 다음 잡 처리 ─────────────────────────────────────
    public function process()
    {
        $queue = $this->request->getPost('queue') ?? 'default';
        $result = $this->queue->processNext($queue);
        $result['stats'] = $this->queue->stats($queue);

        return $this->response->setJSON($result);
    }

    // ─── 실패 잡 재시도 ───────────────────────────────────
    public function retry()
    {
        $failedId = (int) ($this->request->getPost('failed_id') ?? 0);
        $queue    = $this->request->getPost('queue') ?? 'default';

        $ok = $this->queue->retry($failedId);

        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? "실패 잡 #{$failedId} 을 재시도 큐에 등록했습니다." : '실패 잡을 찾을 수 없습니다.',
            'stats'   => $this->queue->stats($queue),
        ]);
    }

    // ─── 큐 초기화 ────────────────────────────────────────
    public function clear()
    {
        $queue = $this->request->getGet('queue') ?? 'default';
        $this->queue->clear($queue);

        return redirect()->to(base_url("examples/queue?queue={$queue}"))
            ->with('success', "'{$queue}' 큐가 초기화되었습니다.");
    }

    // ─── 통계 (AJAX polling) ──────────────────────────────
    public function stats()
    {
        $queue = $this->request->getGet('queue') ?? 'default';

        return $this->response->setJSON($this->queue->stats($queue));
    }
}
