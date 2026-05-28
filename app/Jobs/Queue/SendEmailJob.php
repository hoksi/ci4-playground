<?php

namespace App\Jobs\Queue;

use CodeIgniter\Queue\BaseJob;
use CodeIgniter\Queue\Interfaces\JobInterface;

/**
 * 이메일 발송 잡 — 공식 codeigniter4/queue 패키지 사용
 */
class SendEmailJob extends BaseJob implements JobInterface
{
    protected int $retryAfter = 30;
    protected int $tries      = 3;

    public function process(): void
    {
        $to      = $this->data['to']      ?? 'unknown@example.com';
        $subject = $this->data['subject'] ?? '(제목 없음)';

        // 실제 발송 대신 처리 시뮬레이션 (0.1~0.5초)
        usleep(random_int(100000, 500000));

        log_message('info', "[SendEmailJob] 이메일 발송 완료 → {$to} / {$subject}");
    }
}
