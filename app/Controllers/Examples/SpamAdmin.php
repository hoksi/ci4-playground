<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Models\SpamKeywordModel;
use App\Services\SpamChecker;

class SpamAdmin extends BaseController
{
    protected SpamKeywordModel $model;

    public function __construct()
    {
        $this->model = new SpamKeywordModel();
    }

    public function index(): string
    {
        $keywords = $this->model->orderBy('frequency', 'DESC')->findAll();

        return view('examples/spam-admin/index', [
            'title'           => '스팸 키워드 관리',
            'keywords'        => $keywords,
            'builtinKeywords' => $this->getBuiltinKeywords(),
        ]);
    }

    public function toggle(int $id)
    {
        $this->model->toggle($id);

        return redirect()->to(base_url('examples/spam-admin'))
            ->with('success', '키워드 상태가 변경되었습니다.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id);
        cache()->delete('spam_keywords_active');

        return redirect()->to(base_url('examples/spam-admin'))
            ->with('success', '키워드가 삭제되었습니다.');
    }

    public function store()
    {
        $keyword = trim((string) $this->request->getPost('keyword'));

        if (mb_strlen($keyword) < 2) {
            return redirect()->back()->with('error', '키워드는 2자 이상 입력해주세요.');
        }

        $this->model->saveOrIncrement($keyword);

        return redirect()->to(base_url('examples/spam-admin'))
            ->with('success', "'{$keyword}' 키워드가 추가되었습니다.");
    }

    public function test()
    {
        $title   = (string) $this->request->getPost('title');
        $content = (string) $this->request->getPost('content');

        if (empty($title) || empty($content)) {
            return $this->response->setJSON(['error' => '제목과 내용을 입력해주세요.']);
        }

        $result = (new SpamChecker())->check($title, $content, $this->request->getIPAddress());

        return $this->response->setJSON($result);
    }

    private function getBuiltinKeywords(): array
    {
        return SpamChecker::builtinKeywords();
    }
}
