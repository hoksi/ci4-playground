<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Services\PostService;
use App\Models\PostModel;

class ServiceLayer extends BaseController
{
    private PostService $postService;

    public function __construct()
    {
        $this->postService = new PostService(new PostModel());
    }

    public function index(): string
    {
        return view('examples/servicelayer/index', [
            'title'   => '서비스 레이어',
            'top'     => $this->postService->getTopPosts(5),
            'summary' => $this->postService->getSummary(),
        ]);
    }

    public function search(): string
    {
        $keyword = $this->request->getGet('q') ?? '';
        return view('examples/servicelayer/index', [
            'title'   => '서비스 레이어',
            'top'     => $this->postService->getTopPosts(5),
            'summary' => $this->postService->getSummary(),
            'results' => $this->postService->search($keyword),
            'keyword' => $keyword,
            'tab'     => 'search',
        ]);
    }

    public function create()
    {
        $data = [
            'title'   => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'author'  => $this->request->getPost('author') ?: '익명',
        ];

        $result = $this->postService->create($data);

        if (! $result['success']) {
            return view('examples/servicelayer/index', [
                'title'   => '서비스 레이어',
                'top'     => $this->postService->getTopPosts(5),
                'summary' => $this->postService->getSummary(),
                'errors'  => $result['errors'],
                'old'     => $this->request->getPost(),
                'tab'     => 'create',
            ]);
        }

        return redirect()->to(base_url('examples/servicelayer'))
            ->with('success', "게시물 #" . $result['id'] . " 생성 완료 (서비스 레이어 통해 저장)");
    }
}
