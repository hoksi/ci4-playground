<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Testing extends BaseController
{
    public function index(): string
    {
        $output   = null;
        $exitCode = null;

        if ($this->request->is('post')) {
            $phpunit = ROOTPATH . 'vendor/bin/phpunit';

            if (!file_exists($phpunit)) {
                $output   = "[오류] vendor/bin/phpunit 을 찾을 수 없습니다.\n"
                          . "PHPUnit은 개발 의존성(require-dev)으로 배포 서버에는 설치되지 않습니다.\n\n"
                          . "로컬 또는 개발 환경에서 실행하거나,\n"
                          . "배포 서버에서 'composer install' (--no-dev 없이) 실행 후 다시 시도하세요.";
                $exitCode = 127;
            } else {
                $suite = $this->request->getPost('suite') ?? 'all';
                $cmd   = $this->buildCommand($suite);
                exec($cmd, $lines, $exitCode);
                $output = implode("\n", $lines);
            }
        }

        return view('examples/testing/index', [
            'title'    => '테스팅',
            'output'   => $output,
            'exitCode' => $exitCode,
        ]);
    }

    private function buildCommand(string $suite): string
    {
        $base = 'cd ' . escapeshellarg(ROOTPATH) . ' && ./vendor/bin/phpunit';

        // --do-not-cache-result: 프로젝트 루트 .phpunit.result.cache 쓰기 방지 (배포 서버 권한 오류)
        return match($suite) {
            'helper'  => $base . ' tests/app/Helpers/PlaygroundHelperTest.php --testdox --no-coverage --do-not-cache-result 2>&1',
            'service' => $base . ' tests/app/Services/PostServiceTest.php --testdox --no-coverage --do-not-cache-result 2>&1',
            default   => $base . ' --testdox --no-coverage --do-not-cache-result 2>&1',
        };
    }
}
