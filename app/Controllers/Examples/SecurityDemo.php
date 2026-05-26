<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class SecurityDemo extends BaseController
{
    public function index(): string
    {
        return view('examples/securitydemo/index', [
            'title' => 'Security 클래스',
        ]);
    }

    public function sanitize()
    {
        $input = $this->request->getPost('input') ?? '';

        $security = \Config\Services::security();

        $results = [
            'original'         => $input,
            'esc_html'         => esc($input, 'html'),
            'esc_js'           => esc($input, 'js'),
            'esc_attr'         => esc($input, 'attr'),
            'strip_tags'       => strip_tags($input),
            'htmlspecialchars' => htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'sanitizeFilename' => $security->sanitizeFilename($input),
        ];

        return $this->response->setJSON($results);
    }

    public function xss()
    {
        $input = $this->request->getPost('input') ?? '';

        $results = [
            'original' => $input,
            'esc_html' => esc($input, 'html'),
            'esc_js'   => esc($input, 'js'),
            'esc_attr' => esc($input, 'attr'),
            'esc_url'  => esc($input, 'url'),
            'is_dangerous' => (
                str_contains(strtolower($input), '<script') ||
                str_contains(strtolower($input), 'javascript:') ||
                str_contains(strtolower($input), 'onerror') ||
                str_contains(strtolower($input), 'onload') ||
                str_contains(strtolower($input), 'onclick')
            ),
        ];

        return $this->response->setJSON($results);
    }
}
