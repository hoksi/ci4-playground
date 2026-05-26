<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Controllers extends BaseController
{
    public function index(): string
    {
        return view('examples/controllers/index', ['title' => '컨트롤러']);
    }

    public function request(): string
    {
        $data = [
            'title'      => '컨트롤러 — Request 객체',
            'method'     => $this->request->getMethod(),
            'ipAddress'  => $this->request->getIPAddress(),
            'userAgent'  => $this->request->getUserAgent()->getAgentString(),
            'uri'        => (string) $this->request->getUri(),
            'queryParams'=> $this->request->getGet(),
        ];
        return view('examples/controllers/request', $data);
    }

    public function store(): ResponseInterface
    {
        $name  = $this->request->getPost('name');
        $email = $this->request->getPost('email');

        // 간단한 유효성 검사
        $rules = [
            'name'  => 'required|min_length[2]',
            'email' => 'required|valid_email',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "안녕하세요, {$name}님! ({$email})",
        ]);
    }

    public function response(): string
    {
        return view('examples/controllers/response', ['title' => '컨트롤러 — Response 활용']);
    }
}
