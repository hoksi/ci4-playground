<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use CodeIgniter\Throttle\Throttler as CI4Throttler;

class Throttler extends BaseController
{
    /** 데모: IP당 10초에 5회 */
    private const CAPACITY = 5;
    private const SECONDS  = 10;

    public function index(): string
    {
        return view('examples/throttler/index', ['title' => 'Throttler (요청 속도 제한)']);
    }

    public function hit()
    {
        /** @var CI4Throttler $throttler */
        $throttler = service('throttler');
        $key       = 'playground_throttle_' . $this->request->getIPAddress();

        $allowed   = $throttler->check($key, self::CAPACITY, self::SECONDS);
        $remaining = self::CAPACITY - (int) ceil(
            (microtime(true) - $throttler->getTokenTime()) / (self::SECONDS / self::CAPACITY)
        );
        // 남은 토큰은 capacity - 소비량으로 단순 계산
        $remaining = $allowed ? max(0, self::CAPACITY - 1) : 0;

        if (! $allowed) {
            $retryAfter = $throttler->getTokenTime();
            return $this->response
                ->setStatusCode(429)
                ->setHeader('Retry-After', (string) $retryAfter)
                ->setJSON([
                    'allowed'     => false,
                    'message'     => '요청 한도 초과 (429 Too Many Requests)',
                    'retry_after' => $retryAfter . '초 후 재시도',
                    'limit'       => self::CAPACITY,
                    'window'      => self::SECONDS . '초',
                ]);
        }

        return $this->response->setJSON([
            'allowed'   => true,
            'message'   => '요청 허용됨',
            'limit'     => self::CAPACITY,
            'window'    => self::SECONDS . '초',
        ]);
    }

    public function reset()
    {
        $key = 'playground_throttle_' . $this->request->getIPAddress();
        cache()->delete($key);
        return redirect()->to(base_url('examples/throttler'))->with('success', '카운터가 초기화됐습니다.');
    }
}
