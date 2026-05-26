<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Routing extends BaseController
{
    public function index(): string
    {
        return view('examples/routing/index', ['title' => '라우팅']);
    }

    public function params(int $id): string
    {
        return view('examples/routing/params', [
            'title' => '라우팅 — URL 파라미터',
            'id'    => $id,
        ]);
    }

    public function named(): string
    {
        // Named Route 사용 예시
        $url = route_to('routing.named');
        return view('examples/routing/named', [
            'title'        => '라우팅 — Named Route',
            'generatedUrl' => $url,
        ]);
    }

    public function method(): string
    {
        $httpMethod = $this->request->getMethod();
        return view('examples/routing/method', [
            'title'      => '라우팅 — HTTP 메서드',
            'httpMethod' => strtoupper($httpMethod),
        ]);
    }

    public function redirect()
    {
        return redirect()->to(base_url('examples/routing/redirected'));
    }

    public function redirected(): string
    {
        return view('examples/routing/redirected', [
            'title' => '라우팅 — 리다이렉트 결과',
        ]);
    }
}
