<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class FileUploadAdvanced extends BaseController
{
    private string $uploadPath = WRITEPATH . 'uploads/advanced/';

    public function index(): string
    {
        return view('examples/fileupload-advanced/index', [
            'title' => '파일 업로드 심화',
            'files' => $this->getFiles(),
        ]);
    }

    public function upload(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }

        $file = $this->request->getFile('file');

        if (! $file || ! $file->isValid()) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => $file ? $file->getErrorString() : '파일 없음']);
        }

        $maxSize  = 5 * 1024 * 1024; // 5MB
        $allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'zip'];
        $ext      = strtolower($file->getClientExtension());

        if ($file->getSize() > $maxSize) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => '파일 크기가 5MB를 초과합니다.']);
        }

        if (! in_array($ext, $allowed)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => "허용되지 않는 확장자입니다: .{$ext}"]);
        }

        $newName = $file->getRandomName();
        $file->move($this->uploadPath, $newName);

        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

        return $this->response->setJSON([
            'success'  => true,
            'name'     => $newName,
            'original' => $file->getClientName(),
            'size'     => $file->getSize(),
            'ext'      => $ext,
            'isImage'  => $isImage,
            'url'      => $isImage ? base_url('examples/fileupload-advanced/thumb/' . $newName) : null,
        ]);
    }

    public function thumb(string $filename): void
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

    public function list(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setJSON($this->getFiles());
    }

    public function delete(string $filename): \CodeIgniter\HTTP\ResponseInterface
    {
        $path = $this->uploadPath . basename($filename);

        if (! is_file($path)) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => '파일을 찾을 수 없습니다.']);
        }

        unlink($path);
        return $this->response->setJSON(['success' => true]);
    }

    private function getFiles(): array
    {
        if (! is_dir($this->uploadPath)) {
            return [];
        }

        $files = [];
        foreach (glob($this->uploadPath . '*') as $path) {
            if (! is_file($path)) continue;
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $files[] = [
                'name'    => basename($path),
                'size'    => filesize($path),
                'ext'     => $ext,
                'isImage' => in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']),
                'time'    => filemtime($path),
            ];
        }

        usort($files, fn($a, $b) => $b['time'] - $a['time']);
        return $files;
    }
}
