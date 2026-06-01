<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Models\AuthUserModel;
use Config\Services;

class Auth extends BaseController
{
    private AuthUserModel $users;
    private \CodeIgniter\Session\Session $sess;

    public function __construct()
    {
        $this->users = new AuthUserModel();
        $this->sess  = Services::session();
    }

    public function index(): string
    {
        return view('examples/auth/index', [
            'title'      => '회원 인증 시스템',
            'authUser'   => $this->sess->get('auth_user'),
            'totalUsers' => $this->users->countAllResults(),
        ]);
    }

    // ─── 회원가입 ─────────────────────────────────────────
    public function register()
    {
        if ($this->request->is('post')) {
            $rules = [
                'username'         => 'required|min_length[2]|max_length[50]',
                'email'            => 'required|valid_email|is_unique[auth_users.email]',
                'password'         => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]',
            ];

            $messages = [
                'email'    => ['is_unique' => '이미 사용 중인 이메일입니다.'],
                'password_confirm' => ['matches' => '비밀번호 확인이 일치하지 않습니다.'],
            ];

            if (! $this->validate($rules, $messages)) {
                return redirect()->back()->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $id = $this->users->insert([
                'username' => trim((string) $this->request->getPost('username')),
                'email'    => strtolower(trim((string) $this->request->getPost('email'))),
                'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_BCRYPT, ['cost' => 12]),
            ], true);

            $user = $this->users->find($id);
            $this->sess->set('auth_user', [
                'id'       => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
            ]);

            return redirect()->to(base_url('examples/auth/dashboard'))
                ->with('success', '회원가입 완료! 환영합니다, ' . $user->username . '님.');
        }

        return view('examples/auth/register', [
            'title'  => '회원가입',
            'errors' => $this->sess->getFlashdata('errors') ?? [],
        ]);
    }

    // ─── 로그인 ───────────────────────────────────────────
    public function login()
    {
        if ($this->request->is('post')) {
            $email    = strtolower(trim((string) $this->request->getPost('email')));
            $password = (string) $this->request->getPost('password');

            if ($email === '' || $password === '') {
                return redirect()->back()->withInput()
                    ->with('error', '이메일과 비밀번호를 입력하세요.');
            }

            $user = $this->users->findByEmail($email);
            if (! $user || ! password_verify($password, $user->password)) {
                return redirect()->back()->withInput()
                    ->with('error', '이메일 또는 비밀번호가 올바르지 않습니다.');
            }

            $this->sess->set('auth_user', [
                'id'       => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
            ]);

            return redirect()->to(base_url('examples/auth/dashboard'))
                ->with('success', '로그인 성공! 환영합니다, ' . $user->username . '님.');
        }

        return view('examples/auth/login', [
            'title' => '로그인',
        ]);
    }

    // ─── 로그아웃 ─────────────────────────────────────────
    public function logout()
    {
        $this->sess->remove('auth_user');

        return redirect()->to(base_url('examples/auth/login'))
            ->with('success', '로그아웃 되었습니다.');
    }

    // ─── 보호 페이지: 대시보드 ──────────────────────────────
    public function dashboard()
    {
        $auth = $this->sess->get('auth_user');
        if (! $auth) {
            return redirect()->to(base_url('examples/auth/login'))
                ->with('error', '로그인이 필요합니다.');
        }

        $user = $this->users->find($auth['id']);
        if (! $user) {
            $this->sess->remove('auth_user');

            return redirect()->to(base_url('examples/auth/login'))
                ->with('error', '계정 정보를 찾을 수 없습니다. 다시 로그인하세요.');
        }

        return view('examples/auth/dashboard', [
            'title' => '대시보드',
            'user'  => $user,
            'auth'  => $auth,
        ]);
    }

    // ─── 프로필: 비밀번호 변경 ──────────────────────────────
    public function profile()
    {
        $auth = $this->sess->get('auth_user');
        if (! $auth) {
            return redirect()->to(base_url('examples/auth/login'))
                ->with('error', '로그인이 필요합니다.');
        }

        $current = (string) $this->request->getPost('current_password');
        $new     = (string) $this->request->getPost('new_password');
        $confirm = (string) $this->request->getPost('confirm_password');

        $user = $this->users->find($auth['id']);
        if (! is_object($user) || ! password_verify($current, $user->password)) {
            return redirect()->back()->with('error', '현재 비밀번호가 올바르지 않습니다.');
        }
        if (strlen($new) < 6) {
            return redirect()->back()->with('error', '새 비밀번호는 최소 6자 이상이어야 합니다.');
        }
        if ($new !== $confirm) {
            return redirect()->back()->with('error', '새 비밀번호 확인이 일치하지 않습니다.');
        }

        $this->users->update($auth['id'], [
            'password' => password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]),
        ]);

        return redirect()->to(base_url('examples/auth/dashboard'))
            ->with('success', '비밀번호가 변경되었습니다.');
    }
}
