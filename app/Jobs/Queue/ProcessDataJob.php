<?php

namespace App\Jobs\Queue;

use CodeIgniter\Queue\BaseJob;
use CodeIgniter\Queue\Interfaces\JobInterface;

/**
 * 데이터 처리 잡 — 공식 codeigniter4/queue 패키지 사용
 */
class ProcessDataJob extends BaseJob implements JobInterface
{
    protected int $retryAfter = 60;
    protected int $tries      = 2;

    public function process(): void
    {
        $recordCount = $this->data['record_count'] ?? 100;
        $batchId     = $this->data['batch_id']     ?? 'unknown';

        // 처리 시뮬레이션 (레코드 수에 비례)
        usleep(min($recordCount * 1000, 800000));

        log_message('info', "[ProcessDataJob] 배치 {$batchId} 처리 완료 ({$recordCount}건)");
    }
}
