<?php

namespace App\Jobs;

class ReportGenerateJob extends BaseJob
{
    public function handle(): array
    {
        $period = $this->payload['period'] ?? 'monthly';
        $format = $this->payload['format'] ?? 'pdf';

        usleep(400000); // 0.4초 처리 시간 시뮬레이션

        // 실패 시뮬레이션: payload에 force_fail 이 true 이면 예외 발생
        if (! empty($this->payload['force_fail'])) {
            throw new \RuntimeException('보고서 생성 중 서버 오류 발생 (강제 실패 시뮬레이션)');
        }

        $filename = "report_{$period}_" . date('Ymd') . ".{$format}";

        return [
            'result'   => 'success',
            'message'  => "{$period} 보고서 생성 완료",
            'filename' => $filename,
            'size'     => rand(50, 500) . 'KB',
        ];
    }
}
