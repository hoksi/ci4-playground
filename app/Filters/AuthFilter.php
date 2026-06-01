<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): \CodeIgniter\HTTP\RedirectResponse|null
    {
        // 세션에서 로그인 상태 확인
        $session = session();

        if (! $session->get('filter_logged_in')) {
            // 미로그인 → 로그인 페이지로 리다이렉트 (플래시 메시지 포함)
            return redirect()
                ->to(base_url('examples/filters/login'))
                ->with('error', '로그인이 필요한 페이지입니다.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ?ResponseInterface
    {
        // After 필터: 응답 처리 후 실행
        // 예) 응답 헤더 추가, 로깅 등
        return null;
    }
}
