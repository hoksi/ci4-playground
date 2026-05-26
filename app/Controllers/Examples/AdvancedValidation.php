<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class AdvancedValidation extends BaseController
{
    public function index(): string
    {
        return view('examples/advancedvalidation/index', [
            'title' => '유효성 검사 고급',
        ]);
    }

    public function basic()
    {
        $rules = [
            'username' => [
                'label' => '사용자명',
                'rules' => 'required|min_length[3]|max_length[20]|alpha_numeric|not_reserved[admin,root,system,superuser]',
                'errors' => [
                    'required'      => '{field}은(는) 필수 입력 항목입니다.',
                    'min_length'    => '{field}은(는) 최소 {param}자 이상이어야 합니다.',
                    'alpha_numeric' => '{field}은(는) 영문자와 숫자만 허용됩니다.',
                ],
            ],
            'phone' => [
                'label' => '전화번호',
                'rules' => 'required|korean_phone',
                'errors' => [
                    'required' => '{field}은(는) 필수 입력 항목입니다.',
                ],
            ],
            'email' => [
                'label' => '이메일',
                'rules' => 'required|valid_email',
            ],
        ];

        $result = [
            'passed' => false,
            'errors' => [],
            'data'   => [],
        ];

        if ($this->validate($rules)) {
            $result['passed'] = true;
            $result['data'] = $this->request->getPost(['username', 'phone', 'email']);
        } else {
            $result['errors'] = $this->validator->getErrors();
        }

        return $this->response->setJSON($result);
    }

    public function group()
    {
        $validationRules = [
            'product_name' => 'required|min_length[2]|max_length[100]',
            'price'        => 'required|integer|greater_than[0]',
            'quantity'     => 'required|integer|greater_than_equal_to[1]',
            'category'     => 'required|in_list[electronics,clothing,food,books]',
        ];

        $validationMessages = [
            'product_name' => [
                'required'   => '상품명은 필수 입력 항목입니다.',
                'min_length' => '상품명은 최소 2자 이상이어야 합니다.',
            ],
            'price' => [
                'required'     => '가격은 필수 입력 항목입니다.',
                'integer'      => '가격은 정수여야 합니다.',
                'greater_than' => '가격은 0보다 커야 합니다.',
            ],
            'quantity' => [
                'required'              => '수량은 필수 입력 항목입니다.',
                'greater_than_equal_to' => '수량은 1 이상이어야 합니다.',
            ],
            'category' => [
                'in_list' => '유효한 카테고리를 선택하세요.',
            ],
        ];

        $result = ['passed' => false, 'errors' => [], 'data' => []];

        if ($this->validate($validationRules, $validationMessages)) {
            $result['passed'] = true;
            $result['data'] = $this->request->getPost(['product_name', 'price', 'quantity', 'category']);
        } else {
            $result['errors'] = $this->validator->getErrors();
        }

        return $this->response->setJSON($result);
    }

    public function conditional()
    {
        $rules = [
            'name'     => 'required|min_length[2]',
            'nickname' => 'permit_empty|if_exist|min_length[2]|max_length[20]',
            'age'      => 'permit_empty|if_exist|integer|greater_than[0]|less_than[150]',
            'website'  => 'permit_empty|valid_url_strict',
        ];

        $messages = [
            'name'     => ['required' => '이름은 필수입니다.', 'min_length' => '이름은 최소 2자입니다.'],
            'nickname' => ['min_length' => '닉네임은 최소 2자입니다.'],
            'age'      => ['integer' => '나이는 정수여야 합니다.', 'greater_than' => '나이는 0보다 커야 합니다.'],
            'website'  => ['valid_url_strict' => '유효한 URL 형식이 아닙니다. (https:// 포함)'],
        ];

        $result = ['passed' => false, 'errors' => [], 'data' => []];

        if ($this->validate($rules, $messages)) {
            $result['passed'] = true;
            $result['data'] = $this->request->getPost(['name', 'nickname', 'age', 'website']);
        } else {
            $result['errors'] = $this->validator->getErrors();
        }

        return $this->response->setJSON($result);
    }
}
