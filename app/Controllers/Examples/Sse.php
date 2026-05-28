<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Sse extends BaseController
{
    public function index(): string
    {
        return view('examples/sse/index');
    }

    /**
     * SSE 스트림 엔드포인트.
     * text/event-stream 응답으로 최대 60초간 이벤트를 푸시한다.
     */
    public function stream(): void
    {
        @set_time_limit(120);
        @ini_set('output_buffering', 'off');

        header('Content-Type: text/event-stream; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');   // nginx 버퍼링 비활성화
        header('Access-Control-Allow-Origin: *');

        // 헤더 즉시 전송
        if (ob_get_level()) {
            ob_end_flush();
        }

        $lastId   = (int) ($_SERVER['HTTP_LAST_EVENT_ID'] ?? 0);
        $maxTicks = 30;   // 최대 30회 전송 후 재연결 유도
        $db       = \Config\Database::connect();

        for ($tick = 0; $tick < $maxTicks; $tick++) {
            if (connection_aborted()) {
                break;
            }

            $eventId = $lastId + $tick + 1;

            // ① system 이벤트 — 서버 시각·PHP 버전
            $this->sendEvent('system', [
                'time'      => date('Y-m-d H:i:s'),
                'php'       => PHP_VERSION,
                'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 1),
                'tick'      => $tick + 1,
            ], $eventId);

            // ② queue 이벤트 — 큐 잡 현황
            $pending  = $db->tableExists('queue_jobs')
                ? $db->table('queue_jobs')->where('status', 0)->countAllResults()
                : 0;
            $failed   = $db->tableExists('queue_jobs_failed')
                ? $db->table('queue_jobs_failed')->countAllResults()
                : 0;
            $this->sendEvent('queue', [
                'pending' => $pending,
                'failed'  => $failed,
            ], $eventId . 'q');

            // ③ notify 이벤트 — 랜덤 알림 (5초마다)
            if ($tick % 3 === 0) {
                $messages = [
                    '새 게시글이 등록되었습니다.',
                    '캐시가 자동으로 갱신되었습니다.',
                    '헬스체크 태스크가 실행되었습니다.',
                    '큐 잡이 정상 처리되었습니다.',
                    '시스템이 정상 운영 중입니다.',
                ];
                $this->sendEvent('notify', [
                    'message' => $messages[array_rand($messages)],
                    'level'   => 'info',
                ], $eventId . 'n');
            }

            flush();
            sleep(2);
        }

        // 재연결 유도 이벤트
        $this->sendEvent('reconnect', ['reason' => 'max ticks reached'], $eventId + 1);
        flush();
    }

    // ─── 내부 헬퍼 ────────────────────────────────────────────────────────────

    private function sendEvent(string $event, array $data, string|int $id = ''): void
    {
        if ($id !== '') {
            echo "id: {$id}\n";
        }
        echo "event: {$event}\n";
        echo 'data: ' . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n\n";
    }
}
