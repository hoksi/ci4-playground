<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Models\UserCallbackModel;

class ModelCallback extends BaseController
{
    public function index(): string
    {
        $model = new UserCallbackModel();
        $users = $model->findAll();

        return view('examples/modelcallback/index', [
            'title' => 'Model 콜백 (Callbacks)',
            'users' => $users,
        ]);
    }

    public function store()
    {
        $model = new UserCallbackModel();

        $rules = [
            'username' => 'required|min_length[2]|max_length[100]',
            'email'    => 'required|valid_email|max_length[150]',
            'password' => 'required|min_length[4]',
            'role'     => 'required|in_list[user,admin,editor]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model->insert([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role'     => $this->request->getPost('role'),
            'status'   => 1,
        ]);

        return redirect()->to(base_url('examples/modelcallback'))
            ->with('success', '유저가 생성됐습니다. beforeInsert 콜백으로 비밀번호가 자동 해싱됐습니다.');
    }

    public function reset()
    {
        $db = \Config\Database::connect();
        $db->table('users_demo')->truncate();

        $model = new UserCallbackModel();
        $model->allowCallbacks(true);

        $samples = [
            ['username' => 'admin',   'email' => 'admin@example.com',   'password' => 'admin1234',  'role' => 'admin',  'status' => 1],
            ['username' => 'editor',  'email' => 'editor@example.com',  'password' => 'edit5678',   'role' => 'editor', 'status' => 1],
            ['username' => 'honggil', 'email' => 'honggil@example.com', 'password' => 'pass9999',   'role' => 'user',   'status' => 1],
        ];

        foreach ($samples as $sample) {
            $model->insert($sample);
        }

        return redirect()->to(base_url('examples/modelcallback'))
            ->with('success', '샘플 유저 3명이 삽입됐습니다. (비밀번호는 beforeInsert 콜백으로 해싱됨)');
    }
}
