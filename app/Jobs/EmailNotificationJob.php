<?php

namespace App\Jobs;

class EmailNotificationJob extends BaseJob
{
    public function handle(): array
    {
        $to      = $this->payload['to'] ?? 'unknown@example.com';
        $subject = $this->payload['subject'] ?? '(제목 없음)';

        // 실제 이메일 발송 대신 처리 시뮬레이션
        usleep(200000); // 0.2초 처리 시간 시뮬레이션

        return [
            'result'  => 'success',
            'message' => "이메일 발송 완료 → {$to} / 제목: {$subject}",
        ];
    }
}
