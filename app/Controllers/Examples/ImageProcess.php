<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class ImageProcess extends BaseController
{
    private string $uploadPath = WRITEPATH . 'uploads/images/';

    public function index(): string
    {
        $files = $this->getProcessedImages();

        return view('examples/imageprocess/index', [
            'title' => '이미지 처리',
            'files' => $files,
        ]);
    }

    public function upload()
    {
        $file = $this->request->getFile('image');

        if ($file === null || ! $file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '이미지 파일을 선택하세요.',
            ]);
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext     = strtolower($file->getClientExtension());
        if (! in_array($ext, $allowed, true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "허용되지 않는 확장자입니다. ({$ext})",
            ]);
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '파일 크기는 5MB 이하만 가능합니다.',
            ]);
        }

        // 디렉토리 자동 생성
        if (! is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }

        $started = microtime(true);

        // 원본 저장
        $originalName = $file->getRandomName();
        $file->move($this->uploadPath, $originalName);
        $originalPath = $this->uploadPath . $originalName;
        $originalSize = filesize($originalPath);

        // 원본 크기 계산
        [$origWidth, $origHeight] = getimagesize($originalPath);

        // 썸네일 (150x150 crop)
        $thumbName = 'thumb_' . $originalName;
        $thumbPath = $this->uploadPath . $thumbName;
        try {
            $image = \Config\Services::image();
            $image->withFile($originalPath)
                  ->fit(150, 150, 'center')
                  ->save($thumbPath);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '썸네일 생성 실패: ' . $e->getMessage(),
            ]);
        }
        $thumbSize = filesize($thumbPath);

        // 리사이즈 (가로 800px 이하)
        $resizedName = 'resized_' . $originalName;
        $resizedPath = $this->uploadPath . $resizedName;
        try {
            $image2 = \Config\Services::image();
            $image2->withFile($originalPath)
                   ->resize(800, 600, true, 'width')
                   ->save($resizedPath);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '리사이즈 실패: ' . $e->getMessage(),
            ]);
        }
        [$resWidth, $resHeight] = getimagesize($resizedPath);
        $resizedSize = filesize($resizedPath);

        $elapsed = round((microtime(true) - $started) * 1000, 2);

        return $this->response->setJSON([
            'success' => true,
            'message' => '이미지 처리 완료',
            'elapsed_ms' => $elapsed,
            'original' => [
                'name'   => $originalName,
                'size'   => $originalSize,
                'width'  => $origWidth,
                'height' => $origHeight,
            ],
            'thumb' => [
                'name'   => $thumbName,
                'size'   => $thumbSize,
                'width'  => 150,
                'height' => 150,
            ],
            'resized' => [
                'name'   => $resizedName,
                'size'   => $resizedSize,
                'width'  => $resWidth,
                'height' => $resHeight,
            ],
        ]);
    }

    private function getProcessedImages(): array
    {
        if (! is_dir($this->uploadPath)) {
            return [];
        }
        $files = [];
        foreach (glob($this->uploadPath . '*') as $path) {
            if (is_file($path)) {
                $files[] = [
                    'name' => basename($path),
                    'size' => filesize($path),
                    'time' => filemtime($path),
                ];
            }
        }
        usort($files, fn($a, $b) => $b['time'] - $a['time']);

        return array_slice($files, 0, 12);
    }
}
