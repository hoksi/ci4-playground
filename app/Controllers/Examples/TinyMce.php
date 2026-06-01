<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class TinyMce extends BaseController
{
    private string $uploadPath = WRITEPATH . 'uploads/tinymce/';

    public function index(): string
    {
        return view('examples/tinymce/index', [
            'title'   => 'TinyMCE 에디터',
            'content' => session()->getFlashdata('tinymce_content') ?? $this->defaultContent(),
            'saved'   => session()->getFlashdata('tinymce_saved') ?? false,
        ]);
    }

    public function save(): \CodeIgniter\HTTP\RedirectResponse
    {
        $content = $this->request->getPost('content') ?? '';

        session()->setFlashdata('tinymce_content', $content);
        session()->setFlashdata('tinymce_saved', true);

        return redirect()->to(base_url('examples/tinymce'));
    }

    public function upload(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }

        $file = $this->request->getFile('file');

        if (! $file || ! $file->isValid()) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => ['message' => '파일 업로드 실패']]);
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext     = strtolower($file->getClientExtension());

        if (! in_array($ext, $allowed)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => ['message' => '이미지 파일만 업로드 가능합니다.']]);
        }

        if ($file->getSize() > 3 * 1024 * 1024) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => ['message' => '파일 크기는 3MB 이하만 허용됩니다.']]);
        }

        $newName = $file->getRandomName();
        $file->move($this->uploadPath, $newName);

        // TinyMCE images_upload_handler 응답 형식
        return $this->response->setJSON([
            'location' => base_url('examples/tinymce/image/' . $newName),
        ]);
    }

    public function image(string $filename): void
    {
        $path = $this->uploadPath . basename($filename);
        if (! is_file($path)) {
            http_response_code(404);
            exit;
        }

        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'gif'         => 'image/gif',
            'webp'        => 'image/webp',
            default       => 'application/octet-stream',
        };

        header('Content-Type: ' . $mime);
        header('Cache-Control: max-age=3600');
        readfile($path);
        exit;
    }

    private function defaultContent(): string
    {
        return '<h2>TinyMCE 에디터 예제</h2>
<p>이 텍스트를 <strong>자유롭게 편집</strong>해보세요. TinyMCE는 다양한 서식 옵션을 제공합니다.</p>
<ul>
  <li>굵게, 기울임, 밑줄 서식</li>
  <li>글머리 기호 및 번호 목록</li>
  <li>이미지 삽입 및 링크</li>
  <li>표(Table) 삽입</li>
</ul>
<blockquote>
  <p>CodeIgniter 4와 TinyMCE를 함께 사용하면 강력한 콘텐츠 편집 기능을 쉽게 구현할 수 있습니다.</p>
</blockquote>';
    }
}
