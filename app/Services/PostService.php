<?php

namespace App\Services;

use App\Models\PostModel;

/**
 * 서비스 레이어: 비즈니스 로직을 컨트롤러로부터 분리
 *
 * 컨트롤러는 HTTP 요청/응답만 담당하고,
 * 실제 비즈니스 로직은 이 서비스에서 처리합니다.
 */
class PostService
{
    public function __construct(private PostModel $model) {}

    /**
     * 최신 게시물 조회 (조회수 기준 상위 N개)
     */
    public function getTopPosts(int $limit = 5): array
    {
        return $this->model
            ->orderBy('views', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * 게시물 통계 요약
     */
    public function getSummary(): array
    {
        $total    = $this->model->countAll();
        $totalViews = $this->model->selectSum('views')->first();

        return [
            'total'       => $total,
            'total_views' => (int) ($totalViews->views ?? 0),
            'avg_views'   => $total > 0 ? round(($totalViews->views ?? 0) / $total, 1) : 0,
        ];
    }

    /**
     * 키워드로 게시물 검색
     */
    public function search(string $keyword): array
    {
        if (empty(trim($keyword))) {
            return [];
        }
        return $this->model
            ->like('title', $keyword)
            ->orLike('content', $keyword)
            ->findAll();
    }

    /**
     * 게시물 생성 (유효성 검사 포함)
     *
     * @return array{success: bool, id?: int, errors?: array}
     */
    public function create(array $data): array
    {
        $validator = \Config\Services::validation();
        $validator->setRules([
            'title'   => ['label' => '제목',  'rules' => 'required|max_length[200]'],
            'content' => ['label' => '본문',   'rules' => 'required'],
            'author'  => ['label' => '작성자', 'rules' => 'max_length[50]'],
        ]);

        if (! $validator->run($data)) {
            return ['success' => false, 'errors' => $validator->getErrors()];
        }

        $id = $this->model->insert($data, true);
        return ['success' => true, 'id' => $id];
    }
}
