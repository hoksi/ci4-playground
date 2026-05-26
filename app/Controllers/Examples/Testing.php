<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Testing extends BaseController
{
    public function index(): string
    {
        $output   = null;
        $exitCode = null;

        if ($this->request->getMethod() === 'post') {
            $suite = $this->request->getPost('suite') ?? 'all';
            $cmd   = $this->buildCommand($suite);
            exec($cmd, $lines, $exitCode);
            $output = implode("\n", $lines);
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

        return match($suite) {
            'helper'  => $base . ' tests/app/Helpers/PlaygroundHelperTest.php --testdox 2>&1',
            'service' => $base . ' tests/app/Services/PostServiceTest.php --testdox 2>&1',
            default   => $base . ' --testdox 2>&1',
        };
    }
}
