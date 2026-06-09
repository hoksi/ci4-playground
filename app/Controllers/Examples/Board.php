<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Models\PostModel;
use App\Services\SpamChecker;

class Board extends BaseController
{
    /**
     * 읽기 전용 모드 스위치
     *
     * true  → 쓰기/수정/삭제 비활성화 (데모 공개 환경)
     * false → 모든 기능 활성화 (직접 실습할 때 false 로 변경)
     */
    private const READ_ONLY = false;

    /**
     * 스팸 감지 스위치
     *
     * true  → 게시글 등록 시 스팸 감지 활성화 (규칙→StopForumSpam→AI)
     * false → 스팸 감지 비활성화
     */
    private const SPAM_CHECK = true;

    protected PostModel $model;

    public function __construct()
    {
        $this->model = new PostModel();
    }

    private function checkWriteRules(string $title, string $content): ?\CodeIgniter\HTTP\RedirectResponse
    {
        $ip = $this->request->getIPAddress();

        // 쿨다운: 동일 IP 30초 이내 재작성 차단
        $cdKey = 'board_cd_' . md5($ip);
        if (cache($cdKey)) {
            return redirect()->back()->withInput()
                ->with('error', '너무 빠르게 작성하고 있습니다. 30초 후 다시 시도해주세요.');
        }
        cache()->save($cdKey, 1, 30);

        // 중복 감지: 5분 이내 동일 제목+내용 차단
        $dupKey = 'board_dup_' . md5($ip . $title . $content);
        if (cache($dupKey)) {
            return redirect()->back()->withInput()
                ->with('error', '동일한 내용의 게시글이 최근에 등록되었습니다.');
        }
        cache()->save($dupKey, 1, 300);

        return null;
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

        if (! $this->validate($this->model->getValidationRules())) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $title   = $this->request->getPost('title');
        $content = $this->request->getPost('content');
        $author  = $this->request->getPost('author');

        if ($deny = $this->checkWriteRules($title, $content)) {
            return $deny;
        }

        $spamStatus = 'approved';
        if (self::SPAM_CHECK) {
            $spam = (new SpamChecker())->check($title, $content, $this->request->getIPAddress());

            if ($spam['status'] === 'spam') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', '스팸으로 감지된 게시글입니다. 내용을 확인해주세요.');
            }

            $spamStatus = $spam['status'];
        }

        $this->model->insert([
            'title'       => $title,
            'content'     => $content,
            'author'      => $author,
            'spam_status' => $spamStatus,
        ]);

        $message = $spamStatus === 'review'
            ? '게시글이 등록되었습니다. (검토 후 노출될 수 있습니다.)'
            : '게시글이 작성되었습니다.';

        return redirect()->to(base_url('examples/board'))
            ->with('success', $message);
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

        if (! $this->validate($this->model->getValidationRules())) {
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
