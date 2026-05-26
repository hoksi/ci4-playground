<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Validation extends BaseController
{
    public function index(): string
    {
        return view('examples/validation/index', ['title' => '유효성 검사']);
    }

    public function basic()
    {
        $rules = [
            'name'  => ['label' => '이름',  'rules' => 'required|min_length[2]|max_length[20]'],
            'email' => ['label' => '이메일', 'rules' => 'required|valid_email'],
            'age'   => ['label' => '나이',   'rules' => 'required|integer|greater_than[0]|less_than[151]'],
        ];

        if (! $this->validate($rules)) {
            return view('examples/validation/index', [
                'title'      => '유효성 검사',
                'tab'        => 'basic',
                'errors'     => $this->validator->getErrors(),
                'old'        => $this->request->getPost(),
            ]);
        }

        return view('examples/validation/index', [
            'title'   => '유효성 검사',
            'tab'     => 'basic',
            'success' => '유효성 검사 통과! 입력값: ' . esc($this->request->getPost('name')) . ' / ' . esc($this->request->getPost('email')),
        ]);
    }

    public function custom()
    {
        $rules = [
            'username' => [
                'label' => '사용자명',
                'rules' => 'required|alpha_numeric|min_length[4]|max_length[16]',
                'errors' => [
                    'required'      => '{field}은(는) 필수 입력 항목입니다.',
                    'alpha_numeric' => '{field}은(는) 영문자와 숫자만 사용 가능합니다.',
                    'min_length'    => '{field}은(는) 최소 {param}자 이상이어야 합니다.',
                    'max_length'    => '{field}은(는) 최대 {param}자 이하여야 합니다.',
                ],
            ],
            'password' => [
                'label' => '비밀번호',
                'rules' => 'required|min_length[8]|regex_match[/^(?=.*[A-Z])(?=.*\d).+$/]',
                'errors' => [
                    'required'    => '{field}은(는) 필수 입력 항목입니다.',
                    'min_length'  => '{field}은(는) 최소 {param}자 이상이어야 합니다.',
                    'regex_match' => '{field}은(는) 대문자와 숫자를 각각 하나 이상 포함해야 합니다.',
                ],
            ],
            'password_confirm' => [
                'label' => '비밀번호 확인',
                'rules' => 'required|matches[password]',
                'errors' => [
                    'matches' => '비밀번호가 일치하지 않습니다.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return view('examples/validation/index', [
                'title'      => '유효성 검사',
                'tab'        => 'custom',
                'errors'     => $this->validator->getErrors(),
                'old'        => $this->request->getPost(),
            ]);
        }

        return view('examples/validation/index', [
            'title'   => '유효성 검사',
            'tab'     => 'custom',
            'success' => '회원가입 유효성 검사 통과! 사용자명: ' . esc($this->request->getPost('username')),
        ]);
    }
}
