<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Models\PostModel;

class Board extends BaseController
{
    protected PostModel $model;

    public function __construct()
    {
        $this->model = new PostModel();
    }

    public function index(): string
    {
        $posts = $this->model->orderBy('created_at', 'DESC')->paginate(5, 'default');
        return view('examples/board/index', [
            'title' => '게시판',
            'posts' => $posts,
            'pager' => $this->model->pager,
        ]);
    }

    public function create()
    {
        return redirect()->to(base_url('examples/board'))
            ->with('error', '읽기 전용 데모입니다. 쓰기 기능은 비활성화되어 있습니다.');
    }

    public function store()
    {
        return redirect()->to(base_url('examples/board'))
            ->with('error', '읽기 전용 데모입니다. 쓰기 기능은 비활성화되어 있습니다.');
    }

    public function show(int $id): string
    {
        $post = $this->model->find($id);
        if (! $post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->model->incrementViews($id);

        return view('examples/board/show', [
            'title' => $post->title,
            'post'  => $post,
        ]);
    }

    public function edit(int $id)
    {
        return redirect()->to(base_url("examples/board/{$id}"))
            ->with('error', '읽기 전용 데모입니다. 쓰기 기능은 비활성화되어 있습니다.');
    }

    public function update(int $id)
    {
        return redirect()->to(base_url("examples/board/{$id}"))
            ->with('error', '읽기 전용 데모입니다. 쓰기 기능은 비활성화되어 있습니다.');
    }

    public function delete(int $id)
    {
        return redirect()->to(base_url('examples/board'))
            ->with('error', '읽기 전용 데모입니다. 쓰기 기능은 비활성화되어 있습니다.');
    }
}
