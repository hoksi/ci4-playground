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

    public function create(): string
    {
        return view('examples/board/create', ['title' => '게시글 작성']);
    }

    public function store()
    {
        if (! $this->validate($this->model->validationRules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->model->insert([
            'title'   => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'author'  => $this->request->getPost('author'),
        ]);

        return redirect()->to(base_url('examples/board'))
            ->with('success', '게시글이 작성되었습니다.');
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

    public function edit(int $id): string
    {
        $post = $this->model->find($id);
        if (! $post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('examples/board/edit', [
            'title' => '게시글 수정',
            'post'  => $post,
        ]);
    }

    public function update(int $id)
    {
        $post = $this->model->find($id);
        if (! $post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->model->validationRules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'title'   => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'author'  => $this->request->getPost('author'),
        ]);

        return redirect()->to(base_url("examples/board/{$id}"))
            ->with('success', '게시글이 수정되었습니다.');
    }

    public function delete(int $id)
    {
        $post = $this->model->find($id);
        if (! $post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->model->delete($id);

        return redirect()->to(base_url('examples/board'))
            ->with('success', '게시글이 삭제되었습니다.');
    }
}
