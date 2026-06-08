<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

/**
 * 쓰기 엔드포인트 요청 빈도 제한 필터
 *
 * 라우트에서 인수로 제한값 지정:
 *   ['filter' => 'write-throttle:3,60']  → 60초에 3회
 */
class WriteThrottleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): RequestInterface|ResponseInterface|string|null
    {
        $limit   = (int) ($arguments[0] ?? 5);
        $seconds = (int) ($arguments[1] ?? 60);

        $throttler = Services::throttler();
        $key       = 'wt_' . md5($request->getIPAddress() . '_' . $request->getUri()->getPath());

        if ($throttler->check($key, $limit, $seconds)) {
            return null;
        }

        $wait = ceil($throttler->getTokenTime());
        $msg  = "요청이 너무 빠릅니다. {$wait}초 후 다시 시도해주세요.";

        if ($request instanceof IncomingRequest && $request->isAJAX()) {
            return Services::response()
                ->setStatusCode(429)
                ->setJSON(['error' => $msg]);
        }

        return redirect()->back()->withInput()->with('error', $msg);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ResponseInterface|null
    {
        return null;
    }
}
