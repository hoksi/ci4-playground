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
            'labels'    => array_column($rows, 'author'),
            'counts'    => array_map('intval',  array_column($rows, 'count')),
            'avgViews'  => array_map('intval',  array_column($rows, 'avg_views')),
        ]);
    }

    /** 조회수 구간별 분포 — 도넛 차트 */
    public function pieData(): \CodeIgniter\HTTP\ResponseInterface
    {
        $db = db_connect();

        $ranges = [
            '0–100'    => [0,   100],
            '101–500'  => [101, 500],
            '501–1000' => [501, 1000],
            '1001+'    => [1001, 999999],
        ];

        $data = [];
        foreach ($ranges as $label => [$min, $max]) {
            $count = $db->table('posts')
                ->where('deleted_at', null)
                ->where('views >=', $min)
                ->where('views <=', $max)
                ->countAllResults();
            $data[] = ['label' => $label, 'count' => $count];
        }

        return $this->response->setJSON([
            'labels' => array_column($data, 'label'),
            'data'   => array_map('intval', array_column($data, 'count')),
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
