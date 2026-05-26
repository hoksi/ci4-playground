<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class CliCommand extends BaseController
{
    public function index(): string
    {
        $sparkList = shell_exec('cd ' . ROOTPATH . ' && php spark list Playground 2>&1');

        return view('examples/cli/index', [
            'title'     => 'CLI 커맨드',
            'sparkList' => $sparkList,
        ]);
    }

    public function run()
    {
        $cmd     = $this->request->getPost('cmd') ?? 'stats';
        $allowed = ['playground:stats', 'playground:stats --json', 'playground:seed 3'];

        if (! in_array($cmd, $allowed)) {
            return redirect()->back()->with('error', '허용되지 않는 명령어입니다.');
        }

        $output = shell_exec('cd ' . ROOTPATH . ' && php spark ' . escapeshellcmd($cmd) . ' 2>&1');

        return view('examples/cli/index', [
            'title'     => 'CLI 커맨드',
            'sparkList' => shell_exec('cd ' . ROOTPATH . ' && php spark list Playground 2>&1'),
            'output'    => $output,
            'ran'       => $cmd,
            'tab'       => 'run',
        ]);
    }
}
