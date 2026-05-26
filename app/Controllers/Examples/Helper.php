<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Helper extends BaseController
{
    public function index(): string
    {
        helper('playground');

        $demos = [
            'filesize' => [
                format_filesize(512),
                format_filesize(1536),
                format_filesize(2097152),
                format_filesize(1073741824),
            ],
            'time_ago' => [
                time_ago(time() - 30),
                time_ago(time() - 3600),
                time_ago(time() - 86400 * 3),
                time_ago(time() - 86400 * 45),
            ],
            'truncate' => [
                truncate_text('CodeIgniter 4는 PHP 웹 프레임워크입니다. 간결하고 빠른 개발을 지향합니다.', 20),
                truncate_text('짧은 텍스트', 20),
                truncate_text('A very long English text that needs to be truncated properly.', 30),
            ],
            'highlight' => [
                ['text' => 'CodeIgniter 4 프레임워크 예제', 'keyword' => 'CodeIgniter'],
                ['text' => 'PHP 웹 개발을 배워봅시다', 'keyword' => '개발'],
            ],
            'korean_number' => [
                korean_number(999),
                korean_number(12345),
                korean_number(50000000),
                korean_number(123456789),
            ],
        ];

        return view('examples/helper/index', [
            'title' => '커스텀 헬퍼',
            'demos' => $demos,
        ]);
    }
}
