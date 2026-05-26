<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Logging extends BaseController
{
    private string $logPath = WRITEPATH . 'logs/';

    public function index(): string
    {
        return view('examples/logging/index', [
            'title'       => '로깅',
            'recentLogs'  => $this->getRecentLogs(),
            'logFiles'    => $this->getLogFiles(),
        ]);
    }

    public function write()
    {
        $level   = $this->request->getPost('level') ?? 'info';
        $message = $this->request->getPost('message') ?? '테스트 로그 메시지';

        $allowed = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        if (! in_array($level, $allowed)) {
            $level = 'info';
        }

        log_message($level, '[Playground] ' . esc($message, 'raw'));

        return redirect()->back()->with('success', "[{$level}] 로그가 기록되었습니다.");
    }

    private function getRecentLogs(int $lines = 30): array
    {
        $files = glob($this->logPath . 'log-*.log');
        if (empty($files)) {
            return [];
        }

        rsort($files);
        $content = file_get_contents($files[0]);
        if ($content === false) {
            return [];
        }

        $rows    = array_filter(array_map('trim', explode("\n", $content)));
        $rows    = array_reverse(array_values($rows));
        $parsed  = [];

        foreach (array_slice($rows, 0, $lines) as $row) {
            if (preg_match('/^(DEBUG|INFO|NOTICE|WARNING|ERROR|CRITICAL|ALERT|EMERGENCY)\s+-\s+(\S+\s+\S+)\s+-->\s+(.+)$/', $row, $m)) {
                $parsed[] = ['level' => $m[1], 'time' => $m[2], 'message' => $m[3]];
            } else {
                $parsed[] = ['level' => '', 'time' => '', 'message' => $row];
            }
        }

        return $parsed;
    }

    private function getLogFiles(): array
    {
        $files = glob($this->logPath . 'log-*.log');
        if (empty($files)) {
            return [];
        }

        rsort($files);
        return array_map(fn($f) => [
            'name' => basename($f),
            'size' => filesize($f),
            'time' => filemtime($f),
        ], array_slice($files, 0, 5));
    }
}
