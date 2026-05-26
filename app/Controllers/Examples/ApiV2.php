<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Models\AuthUserModel;

class ApiV2 extends BaseController
{
    private string $jwtSecret;
    private AuthUserModel $users;

    public function __construct()
    {
        $this->jwtSecret = (string) env('app.jwtSecret', 'playground-secret-key-change-in-production');
        $this->users     = new AuthUserModel();
    }

    public function index(): string
    {
        return view('examples/apiv2/index', [
            'title' => 'RESTful API v2 (JWT)',
        ]);
    }

    // ─── 토큰 발급 ─────────────────────────────────────────
    public function token()
    {
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');

        if ($email === '' || $password === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'email 과 password 가 필요합니다.',
            ]);
        }

        $user = $this->users->findByEmail($email);
        if (! $user || ! password_verify($password, $user->password)) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => '이메일 또는 비밀번호가 올바르지 않습니다.',
            ]);
        }

        $token = $this->generateJwt([
            'sub'      => $user->id,
            'username' => $user->username,
            'email'    => $user->email,
        ]);

        return $this->response->setJSON([
            'success'    => true,
            'token_type' => 'Bearer',
            'token'      => $token,
            'expires_in' => 3600,
            'user'       => [
                'id'       => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
            ],
        ]);
    }

    // ─── 보호 API: 사용자 목록 ───────────────────────────
    public function users()
    {
        $payload = $this->getAuthenticatedUser();
        if ($payload === null) {
            return $this->unauthorized();
        }

        $list = $this->users
            ->select('id, username, email, created_at, updated_at')
            ->orderBy('id', 'DESC')
            ->limit(20)
            ->find();

        return $this->response->setJSON([
            'success' => true,
            'auth'    => ['sub' => $payload['sub'] ?? null, 'email' => $payload['email'] ?? null],
            'count'   => count($list),
            'data'    => $list,
        ]);
    }

    // ─── 보호 API: 단일 사용자 ───────────────────────────
    public function user(int $id)
    {
        $payload = $this->getAuthenticatedUser();
        if ($payload === null) {
            return $this->unauthorized();
        }

        $user = $this->users
            ->select('id, username, email, created_at, updated_at')
            ->find($id);

        if (! $user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => '사용자를 찾을 수 없습니다.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $user,
        ]);
    }

    // ─── 보호 API: 사용자 생성 ───────────────────────────
    public function createUser()
    {
        $payload = $this->getAuthenticatedUser();
        if ($payload === null) {
            return $this->unauthorized();
        }

        $username = trim((string) $this->request->getPost('username'));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');

        if ($username === '' || $email === '' || strlen($password) < 6) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'username/email/password(6자 이상)가 필요합니다.',
            ]);
        }
        if ($this->users->findByEmail($email)) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => '이미 등록된 이메일입니다.',
            ]);
        }

        $id = $this->users->insert([
            'username' => $username,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
        ], true);

        return $this->response->setStatusCode(201)->setJSON([
            'success' => true,
            'message' => '사용자가 생성되었습니다.',
            'data'    => [
                'id'       => $id,
                'username' => $username,
                'email'    => $email,
            ],
        ]);
    }

    // ─── JWT 유틸리티 ─────────────────────────────────────
    private function generateJwt(array $payload): string
    {
        $header  = $this->b64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = $this->b64UrlEncode(json_encode(array_merge($payload, [
            'iat' => time(),
            'exp' => time() + 3600,
        ])));
        $sig = $this->b64UrlEncode(hash_hmac('sha256', "{$header}.{$payload}", $this->jwtSecret, true));

        return "{$header}.{$payload}.{$sig}";
    }

    private function verifyJwt(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        [$header, $payload, $sig] = $parts;

        $expected = $this->b64UrlEncode(hash_hmac('sha256', "{$header}.{$payload}", $this->jwtSecret, true));
        if (! hash_equals($expected, $sig)) {
            return null;
        }
        $data = json_decode($this->b64UrlDecode($payload), true);
        if (! is_array($data) || ! isset($data['exp']) || $data['exp'] < time()) {
            return null;
        }

        return $data;
    }

    private function getAuthenticatedUser(): ?array
    {
        $auth = $this->request->getHeaderLine('Authorization');
        if (! str_starts_with($auth, 'Bearer ')) {
            return null;
        }

        return $this->verifyJwt(substr($auth, 7));
    }

    private function unauthorized()
    {
        return $this->response->setStatusCode(401)->setJSON([
            'success' => false,
            'message' => '유효한 Bearer JWT 토큰이 필요합니다.',
        ]);
    }

    private function b64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function b64UrlDecode(string $data): string
    {
        $pad = strlen($data) % 4;
        if ($pad > 0) {
            $data .= str_repeat('=', 4 - $pad);
        }

        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }
}
