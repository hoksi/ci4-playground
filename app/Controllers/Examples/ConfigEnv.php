<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use Config\PlaygroundConfig;

class ConfigEnv extends BaseController
{
    public function index(): string
    {
        /** @var PlaygroundConfig $cfg */
        $cfg = config('PlaygroundConfig');

        $data = [
            'title'       => 'Config 환경 분리',
            'cfg'         => $cfg,
            'environment' => ENVIRONMENT,
            'ciVersion'   => \CodeIgniter\CodeIgniter::CI_VERSION,
            'phpVersion'  => phpversion(),
            'baseUrl'     => env('app.baseURL', config('App')->baseURL),
            'dbDriver'    => env('database.default.DBDriver', config('Database')->default['DBDriver'] ?? 'unknown'),
            'envFile'     => file_exists(ROOTPATH . '.env') ? '.env 파일 존재' : '.env 파일 없음',
        ];

        return view('examples/configenv/index', $data);
    }
}
