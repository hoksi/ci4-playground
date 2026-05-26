<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Models\PostModel;

class Board extends BaseController
{
    /**
     * 읽기 전용 모드 스위치
     *
     * true  → 쓰기/수정/삭제 비활성화 (데모 공개 환경)
     * false → 모든 기능 활성화 (직접 실습할 때 false 로 변경)
     */
    private const READ_ONLY = true;

    protected PostModel $model;

    public function __construct()
    {
        $this->model = new PostModel();
    }

    private function denyIfReadOnly(string $redirectUrl): ?\CodeIgniter\HTTP\RedirectResponse
    {
        if (! self::READ_ONLY) {
            return null;
        }

        return redirect()->to(base_url($redirectUrl))
            ->with('error', '읽기 전용 데모입니다. 직접 실습하려면 Board.php 의 READ_ONLY 를 false 로 변경하세요.');
    }

    public function index(): string
    {
        $posts = $this->model->orderBy('created_at', 'DESC')->paginate(5, 'default');
        return view('examples/board/index', [
            'title'    => '게시판',
            'posts'    => $posts,
            'pager'    => $this->model->pager,
            'readOnly' => self::READ_ONLY,
        ]);
    }

    public function create()
    {
        if ($deny = $this->denyIfReadOnly('examples/board')) {
            return $deny;
        }

        return view('examples/board/create', ['title' => '게시글 작성']);
    }

    public function store()
    {
        if ($deny = $this->denyIfReadOnly('examples/board')) {
            return $deny;
        }

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
            'title'    => $post->title,
            'post'     => $post,
            'readOnly' => self::READ_ONLY,
        ]);
    }

    public function edit(int $id)
    {
        $post = $this->model->find($id);
        if (! $post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($deny = $this->denyIfReadOnly("examples/board/{$id}")) {
            return $deny;
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

        if ($deny = $this->denyIfReadOnly("examples/board/{$id}")) {
            return $deny;
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

        if ($deny = $this->denyIfReadOnly('examples/board')) {
            return $deny;
        }

        $this->model->delete($id);

        return redirect()->to(base_url('examples/board'))
            ->with('success', '게시글이 삭제되었습니다.');
    }
}
