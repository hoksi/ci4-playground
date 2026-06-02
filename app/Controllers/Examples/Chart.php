<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Chart extends BaseController
{
    public function index(): string
    {
        return view('examples/chart/index', ['title' => '차트 (Chart.js)']);
    }

    /** 월별 총 조회수 합계 — 꺾은선 차트 */
    public function lineData(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rows = db_connect()
            ->table('posts')
            ->select("strftime('%Y-%m', created_at) as month, SUM(views) as total_views")
            ->where('deleted_at', null)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'labels' => array_column($rows, 'month'),
            'data'   => array_map('intval', array_column($rows, 'total_views')),
        ]);
    }

    /** 월별 게시글 등록 수 — 막대 차트 */
    public function barData(): \CodeIgniter\HTTP\ResponseInterface
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
            'counts' => array_map('intval', array_column($rows, 'count')),
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

    /** 요일별 게시글 수 + 평균 조회수 — 복합(Bar+Line) 차트 */
    public function mixedData(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rows = db_connect()
            ->table('posts')
            ->select("CAST(strftime('%w', created_at) AS INTEGER) as dow_num,
                      COUNT(*) as count, ROUND(AVG(views),0) as avg_views")
            ->where('deleted_at', null)
            ->groupBy('dow_num')
            ->orderBy('dow_num', 'ASC')
            ->get()
            ->getResultArray();

        $dowMap = ['일', '월', '화', '수', '목', '금', '토'];

        return $this->response->setJSON([
            'labels'   => array_map(fn($r) => $dowMap[(int) $r['dow_num']] . '요일', $rows),
            'counts'   => array_map('intval', array_column($rows, 'count')),
            'avgViews' => array_map('intval', array_column($rows, 'avg_views')),
        ]);
    }
}
