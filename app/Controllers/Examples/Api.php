<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Models\PostModel;
use CodeIgniter\HTTP\ResponseInterface;

class Api extends BaseController
{
    public function index(): string
    {
        return view('examples/api/index', ['title' => 'RESTful API']);
    }

    public function users(): ResponseInterface
    {
        // 데모용 가상 사용자 데이터
        $users = [
            ['id' => 1, 'name' => '김철수', 'email' => 'kim@example.com', 'role' => 'admin'],
            ['id' => 2, 'name' => '이영희', 'email' => 'lee@example.com', 'role' => 'user'],
            ['id' => 3, 'name' => '박민준', 'email' => 'park@example.com', 'role' => 'user'],
        ];

        return $this->response->setStatusCode(200)->setJSON([
            'success' => true,
            'count'   => count($users),
            'data'    => $users,
        ]);
    }

    public function user(int $id): ResponseInterface
    {
        $users = [
            1 => ['id' => 1, 'name' => '김철수', 'email' => 'kim@example.com', 'role' => 'admin'],
            2 => ['id' => 2, 'name' => '이영희', 'email' => 'lee@example.com', 'role' => 'user'],
            3 => ['id' => 3, 'name' => '박민준', 'email' => 'park@example.com', 'role' => 'user'],
        ];

        if (! isset($users[$id])) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => "ID {$id}에 해당하는 사용자가 없습니다.",
            ]);
        }

        return $this->response->setStatusCode(200)->setJSON([
            'success' => true,
            'data'    => $users[$id],
        ]);
    }

    public function createUser(): ResponseInterface
    {
        $json = $this->request->getJSON(true);

        $rules = [
            'name'  => 'required|min_length[2]',
            'email' => 'required|valid_email',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        return $this->response->setStatusCode(201)->setJSON([
            'success' => true,
            'message' => '사용자가 생성되었습니다. (데모)',
            'data'    => array_merge(['id' => rand(100, 999)], $json ?? []),
        ]);
    }

    // 게시글 JSON API
    public function posts(): ResponseInterface
    {
        $model = new PostModel();
        $posts = $model->select('id, title, author, views, created_at')
                       ->orderBy('created_at', 'DESC')
                       ->findAll();

        return $this->response->setStatusCode(200)->setJSON([
            'success' => true,
            'count'   => count($posts),
            'data'    => $posts,
        ]);
    }
}
