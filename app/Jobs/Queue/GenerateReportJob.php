<?php

namespace App\Jobs\Queue;

use CodeIgniter\Queue\BaseJob;
use CodeIgniter\Queue\Interfaces\JobInterface;
use RuntimeException;

/**
 * 보고서 생성 잡 — 실패 시뮬레이션 포함
 */
class GenerateReportJob extends BaseJob implements JobInterface
{
    protected int $retryAfter = 45;
    protected int $tries      = 2;

    public function process(): void
    {
        $reportType = $this->data['report_type'] ?? 'daily';
        $forceFail  = $this->data['force_fail']  ?? false;

        if ($forceFail) {
            throw new RuntimeException("[GenerateReportJob] 보고서 생성 실패 (force_fail=true)");
        }

        // 생성 시뮬레이션
        usleep(random_int(200000, 700000));

        log_message('info', "[GenerateReportJob] {$reportType} 보고서 생성 완료");
    }
}
