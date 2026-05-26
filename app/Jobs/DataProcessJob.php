<?php

namespace App\Jobs;

class DataProcessJob extends BaseJob
{
    public function handle(): array
    {
        $items = (int) ($this->payload['items'] ?? 100);
        $type  = $this->payload['type'] ?? 'csv';

        usleep(300000); // 0.3초 처리 시간 시뮬레이션

        $processed = $items;
        $skipped   = (int) ($items * 0.05);

        return [
            'result'    => 'success',
            'message'   => "{$type} 데이터 처리 완료",
            'processed' => $processed,
            'skipped'   => $skipped,
        ];
    }
}
