<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Chart extends BaseController
{
    public function index(): string
    {
        return view('examples/chart/index', ['title' => '차트 (Chart.js)']);
    }

    /** 월별 게시글 수 추이 — 꺾은선 차트 */
    public function lineData(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rows = db_connect()
            ->table('posts')
            ->select("strftime('%Y-%m', created_at) as month, COUNT(*) as count")
            ->where('deleted_at', null)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'labels' => array_column($rows, 'month'),
            'data'   => array_map('intval', array_column($rows, 'count')),
        ]);
    }

    /** 작성자별 게시글 수 & 평균 조회수 — 막대 차트 */
    public function barData(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rows = db_connect()
            ->table('posts')
            ->select('author, COUNT(*) as count, ROUND(AVG(views),0) as avg_views')
            ->where('deleted_at', null)
            ->groupBy('author')
            ->orderBy('count', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'labels'   => array_column($rows, 'author'),
            'counts'   => array_map('intval', array_column($rows, 'count')),
            'avgViews' => array_map('intval', array_column($rows, 'avg_views')),
        ]);
    }

    /** 알림 타입별 분포 — 도넛 차트 */
    public function pieData(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rows = db_connect()
            ->table('notifications')
            ->select('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();

        $typeLabels = [
            'info'    => '정보',
            'success' => '성공',
            'warning' => '경고',
            'error'   => '오류',
        ];

        return $this->response->setJSON([
            'labels' => array_map(fn($r) => $typeLabels[$r['type']] ?? $r['type'], $rows),
            'data'   => array_map('intval', array_column($rows, 'count')),
        ]);
    }

    /** 월별 게시글 수(Bar) + 총 조회수(Line) — 복합 차트 */
    public function mixedData(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rows = db_connect()
            ->table('posts')
            ->select("strftime('%Y-%m', created_at) as month, COUNT(*) as count, SUM(views) as total_views")
            ->where('deleted_at', null)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'labels'      => array_column($rows, 'month'),
            'counts'      => array_map('intval', array_column($rows, 'count')),
            'totalViews'  => array_map('intval', array_column($rows, 'total_views')),
        ]);
    }
}
