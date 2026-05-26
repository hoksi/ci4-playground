<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Filters extends BaseController
{
    public function index(): string
    {
        return view('examples/filters/index', ['title' => '필터']);
    }

    public function publicPage(): string
    {
        return view('examples/filters/public', ['title' => '필터 — 공개 페이지']);
    }

    public function protectedPage(): string
    {
        // AuthFilter가 이 메서드 실행 전에 로그인을 검사합니다
        // (Routes.php의 'filter' => 'auth-example' 적용 시)
        $user = session()->get('filter_user') ?? '알 수 없음';
        return view('examples/filters/protected', [
            'title' => '필터 — 보호된 페이지',
            'user'  => $user,
        ]);
    }

    public function login(): string
    {
        if ($this->request->getMethod() === 'post') {
            $id       = $this->request->getPost('user_id');
            $password = $this->request->getPost('password');

            // 데모용 간단 인증 (실제로는 DB 비교)
            if ($id === 'demo' && $password === '1234') {
                session()->set([
                    'filter_logged_in' => true,
                    'filter_user'      => $id,
                ]);
                return redirect()
                    ->to(base_url('examples/filters/protected'))
                    ->with('success', '로그인 성공!');
            }

            return redirect()
                ->back()
                ->with('error', '아이디: demo, 비밀번호: 1234 로 로그인하세요.');
        }

        return view('examples/filters/login', ['title' => '필터 — 로그인']);
    }

    public function logout()
    {
        session()->remove(['filter_logged_in', 'filter_user']);
        return redirect()
            ->to(base_url('examples/filters'))
            ->with('success', '로그아웃되었습니다.');
    }
}
