<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use CodeIgniter\Tasks\Scheduler;

class TaskScheduler extends BaseController
{
    public function index(): string
    {
        $tasks = $this->collectTasks();
        return view('examples/task_scheduler/index', ['tasks' => $tasks]);
    }

    public function run(): \CodeIgniter\HTTP\ResponseInterface
    {
        $name      = $this->request->getPost('name');
        $scheduler = $this->buildScheduler();

        foreach ($scheduler->getTasks() as $task) {
            if ($task->name !== $name) {
                continue;
            }

            try {
                ob_start();
                $result = $task->run();
                $buffered = ob_get_clean();

                $output = ($result !== null && $result !== '')
                    ? (is_string($result) ? $result : json_encode($result, JSON_UNESCAPED_UNICODE))
                    : $buffered;

                return $this->response->setJSON([
                    'ok'     => true,
                    'output' => $output ?: '실행 완료 (출력 없음)',
                ]);
            } catch (\Throwable $e) {
                ob_end_clean();
                return $this->response->setJSON([
                    'ok'    => false,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->response->setJSON(['ok' => false, 'error' => "태스크 '{$name}'을 찾을 수 없습니다."]);
    }

    // ─── 내부 헬퍼 ───────────────────────────────────────────────────────────

    private function collectTasks(): array
    {
        $list = [];
        foreach ($this->buildScheduler()->getTasks() as $task) {
            $expr  = $task->getExpression();
            $list[] = [
                'name'       => $task->name,
                'type'       => $task->getType(),
                'action'     => $this->describeAction($task),
                'expression' => $expr,
                'schedule'   => $this->humanSchedule($expr),
                'shouldRun'  => $task->shouldRun(),
                'runnable'   => in_array($task->getType(), ['closure', 'command'], true),
            ];
        }
        return $list;
    }

    private function buildScheduler(): Scheduler
    {
        $config    = config('Tasks');
        $scheduler = new Scheduler();
        $config->init($scheduler);
        return $scheduler;
    }

    private function describeAction(object $task): string
    {
        $action = $task->getAction();
        return match ($task->getType()) {
            'command' => 'spark ' . $action,
            'shell'   => (string) $action,
            'url'     => (string) $action,
            'queue'   => 'Queue → ' . (string) $action,
            default   => '클로저 함수',
        };
    }

    private function humanSchedule(string $expr): string
    {
        return match ($expr) {
            '* * * * *'   => '매분',
            '*/5 * * * *' => '5분마다',
            '0 * * * *'   => '매시간 정각',
            '0 0 * * *'   => '매일 자정',
            '0 3 * * *'   => '매일 새벽 3시',
            '0 0 * * 1'   => '매주 월요일',
            '0 0 1 * *'   => '매월 1일',
            default       => $expr,
        };
    }
}
