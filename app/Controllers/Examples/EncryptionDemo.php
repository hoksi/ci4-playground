<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class EncryptionDemo extends BaseController
{
    public function index(): string
    {
        return view('examples/encryption/index', [
            'title' => '암호화 & 해싱',
        ]);
    }

    public function hash()
    {
        $plain = (string) $this->request->getPost('password');
        if ($plain === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => '비밀번호를 입력하세요.',
            ]);
        }

        $started = microtime(true);
        $hash    = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);
        $elapsed = round((microtime(true) - $started) * 1000, 2);

        $info = password_get_info($hash);

        return $this->response->setJSON([
            'success'    => true,
            'plain'      => $plain,
            'hash'       => $hash,
            'algorithm'  => $info['algoName'] ?? 'unknown',
            'cost'       => $info['options']['cost'] ?? null,
            'length'     => strlen($hash),
            'elapsed_ms' => $elapsed,
        ]);
    }

    public function verify()
    {
        $plain = (string) $this->request->getPost('password');
        $hash  = (string) $this->request->getPost('hash');

        if ($plain === '' || $hash === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => '평문과 해시를 모두 입력하세요.',
            ]);
        }

        $started = microtime(true);
        $match   = password_verify($plain, $hash);
        $elapsed = round((microtime(true) - $started) * 1000, 2);

        $info = password_get_info($hash);
        $needsRehash = password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);

        return $this->response->setJSON([
            'success'      => true,
            'match'        => $match,
            'info'         => $info,
            'needs_rehash' => $needsRehash,
            'elapsed_ms'   => $elapsed,
        ]);
    }
}
