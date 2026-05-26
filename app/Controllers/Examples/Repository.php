<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Interfaces\PostRepositoryInterface;
use App\Models\PostModel;
use App\Repositories\PostRepository;

class Repository extends BaseController
{
    private PostRepositoryInterface $repo;

    public function __construct()
    {
        // Repository 패턴: Interface 타입 힌트, 구현체 주입
        $this->repo = new PostRepository(new PostModel());
    }

    public function index(): string
    {
        return view('examples/repository/index', [
            'title' => 'Repository 패턴',
        ]);
    }

    public function list()
    {
        $posts = $this->repo->findRecent(10);

        return $this->response->setJSON([
            'success' => true,
            'count'   => count($posts),
            'posts'   => array_map(static function ($p) {
                return [
                    'id'      => $p->id,
                    'title'   => $p->title,
                    'author'  => $p->author,
                    'excerpt' => method_exists($p, 'getExcerpt') ? $p->getExcerpt(60) : mb_substr($p->content ?? '', 0, 60),
                    'created' => $p->created_at ? $p->created_at->format('Y-m-d H:i') : '',
                ];
            }, $posts),
        ]);
    }

    public function store()
    {
        $data = [
            'title'   => trim((string) $this->request->getPost('title')),
            'content' => trim((string) $this->request->getPost('content')),
            'author'  => trim((string) $this->request->getPost('author')) ?: '익명',
        ];

        if ($data['title'] === '' || $data['content'] === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => '제목과 내용을 입력하세요.',
            ]);
        }

        $id = $this->repo->create($data);
        if ($id === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '생성 실패',
            ]);
        }

        $created = $this->repo->findById($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Repository를 통해 새 포스트가 생성되었습니다.',
            'post'    => [
                'id'      => $created->id,
                'title'   => $created->title,
                'author'  => $created->author,
                'created' => $created->created_at ? $created->created_at->format('Y-m-d H:i') : '',
            ],
        ]);
    }
}
