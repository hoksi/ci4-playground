<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class PlaygroundConfig extends BaseConfig
{
    public string $appName   = 'CI4 Playground';
    public string $version   = '1.0.0';
    public bool   $debugMode = false;   // .env: playground.debugMode = true
    public int    $maxUpload = 5;       // MB
    public string $timezone  = 'Asia/Seoul';
}
