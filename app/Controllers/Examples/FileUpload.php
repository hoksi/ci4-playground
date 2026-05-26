<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class FileUpload extends BaseController
{
    private string $uploadPath = WRITEPATH . 'uploads/playground/';

    public function index(): string
    {
        $files = $this->getUploadedFiles();
        return view('examples/fileupload/index', [
            'title' => '파일 업로드',
            'files' => $files,
        ]);
    }

    public function upload()
    {
        $file = $this->request->getFile('userfile');

        $rules = [
            'userfile' => [
                'label' => '파일',
                'rules' => [
                    'uploaded[userfile]',
                    'max_size[userfile,2048]',
                    'ext_in[userfile,jpg,jpeg,png,gif,pdf,txt,zip]',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('error', $this->validator->getError('userfile'))
                ->with('tab', 'single');
        }

        if ($file->isValid() && ! $file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move($this->uploadPath, $newName);

            return redirect()->to(base_url('examples/fileupload'))
                ->with('success', "'{$file->getClientName()}' 업로드 완료! → {$newName}");
        }

        return redirect()->back()->with('error', '업로드 실패: ' . $file->getErrorString());
    }

    public function multi()
    {
        $files = $this->request->getFileMultiple('multifiles');

        if (empty($files) || (count($files) === 1 && ! $files[0]->isValid())) {
            return redirect()->back()
                ->with('error', '파일을 선택해주세요.')
                ->with('tab', 'multi');
        }

        $uploaded = [];
        $errors   = [];

        foreach ($files as $file) {
            if (! $file->isValid()) {
                $errors[] = $file->getErrorString();
                continue;
            }
            if ($file->getSize() > 2 * 1024 * 1024) {
                $errors[] = "{$file->getClientName()}: 2MB 초과";
                continue;
            }
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'zip'];
            if (! in_array(strtolower($file->getClientExtension()), $allowed)) {
                $errors[] = "{$file->getClientName()}: 허용되지 않는 확장자";
                continue;
            }
            $newName = $file->getRandomName();
            $file->move($this->uploadPath, $newName);
            $uploaded[] = $file->getClientName();
        }

        if ($uploaded) {
            $msg = count($uploaded) . '개 파일 업로드 완료: ' . implode(', ', $uploaded);
            return redirect()->to(base_url('examples/fileupload'))
                ->with('success', $msg);
        }

        return redirect()->back()
            ->with('error', implode(' / ', $errors))
            ->with('tab', 'multi');
    }

    public function delete(string $filename)
    {
        $path = $this->uploadPath . basename($filename);
        if (is_file($path)) {
            unlink($path);
            return redirect()->to(base_url('examples/fileupload'))
                ->with('success', "'{$filename}' 삭제 완료");
        }
        return redirect()->to(base_url('examples/fileupload'))
            ->with('error', '파일을 찾을 수 없습니다.');
    }

    private function getUploadedFiles(): array
    {
        if (! is_dir($this->uploadPath)) {
            return [];
        }
        $files = [];
        foreach (glob($this->uploadPath . '*') as $path) {
            if (is_file($path)) {
                $files[] = [
                    'name'    => basename($path),
                    'size'    => filesize($path),
                    'ext'     => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                    'time'    => filemtime($path),
                ];
            }
        }
        usort($files, fn($a, $b) => $b['time'] - $a['time']);
        return $files;
    }
}
