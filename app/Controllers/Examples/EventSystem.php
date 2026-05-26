<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use CodeIgniter\Events\Events as CI4Events;

class EventSystem extends BaseController
{
    public function index(): string
    {
        return view('examples/events/index', ['title' => '이벤트 시스템']);
    }

    public function trigger()
    {
        $log     = [];
        $scenario = $this->request->getPost('scenario') ?? 'basic';

        if ($scenario === 'basic') {
            CI4Events::on('playground_hello', function (string $name) use (&$log) {
                $log[] = "[리스너 1] 안녕하세요, {$name}님!";
            });
            CI4Events::on('playground_hello', function (string $name) use (&$log) {
                $log[] = "[리스너 2] Hello, {$name}! (두 번째 리스너)";
            });

            CI4Events::trigger('playground_hello', '홍길동');

        } elseif ($scenario === 'priority') {
            CI4Events::on('playground_order', function () use (&$log) {
                $log[] = "[우선순위 10] 나중에 실행 (기본값)";
            }, CI4Events::PRIORITY_NORMAL);

            CI4Events::on('playground_order', function () use (&$log) {
                $log[] = "[우선순위 1] 가장 먼저 실행 (높은 우선순위)";
            }, CI4Events::PRIORITY_HIGH);

            CI4Events::on('playground_order', function () use (&$log) {
                $log[] = "[우선순위 200] 가장 나중에 실행 (낮은 우선순위)";
            }, CI4Events::PRIORITY_LOW);

            CI4Events::trigger('playground_order');

        } elseif ($scenario === 'halt') {
            CI4Events::on('playground_halt', function () use (&$log) {
                $log[] = "[리스너 1] 실행됨 — false 반환으로 체인 중단";
                return false;
            });
            CI4Events::on('playground_halt', function () use (&$log) {
                $log[] = "[리스너 2] 이 리스너는 실행되지 않음";
            });

            $result = CI4Events::trigger('playground_halt');
            $log[] = "trigger() 반환값: " . ($result ? 'true' : 'false') . " (false = 중단됨)";
        }

        return view('examples/events/index', [
            'title'    => '이벤트 시스템',
            'log'      => $log,
            'scenario' => $scenario,
            'tab'      => 'demo',
        ]);
    }
}
