<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Chart extends BaseController
{
    public function index(): string
    {
        return view('examples/chart/index', ['title' => '차트 (Chart.js)']);
    }

    /** 게시글 조회수 순위 — 꺾은선 차트 */
    public function lineData(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rows = db_connect()
            ->table('posts')
            ->select('title, views')
            ->where('deleted_at', null)
            ->orderBy('views', 'DESC')
            ->get()
            ->getResultArray();

        // 제목이 길면 축약
        $labels = array_map(function ($r) {
            $t = $r['title'];
            return mb_strlen($t) > 10 ? mb_substr($t, 0, 10) . '…' : $t;
        }, $rows);

        return $this->response->setJSON([
            'labels' => $labels,
            'data'   => array_map('intval', array_column($rows, 'views')),
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

    /** 조회수 구간별 분포 — 도넛 차트 */
    public function pieData(): \CodeIgniter\HTTP\ResponseInterface
    {
        $db = db_connect();

        $ranges = [
            '1~100'    => [1,   100],
            '101~200'  => [101, 200],
            '201~300'  => [201, 300],
            '301~'     => [301, 999999],
        ];

        $data = [];
        foreach ($ranges as $label => [$min, $max]) {
            $count = $db->table('posts')
                ->where('deleted_at', null)
                ->where('views >=', $min)
                ->where('views <=', $max)
                ->countAllResults();
            if ($count > 0) {
                $data[] = ['label' => $label, 'count' => $count];
            }
        }

        return $this->response->setJSON([
            'labels' => array_column($data, 'label'),
            'data'   => array_map('intval', array_column($data, 'count')),
        ]);
    }

    /** 작성자별 게시글 수(Bar) + 총 조회수(Line) — 복합 차트 */
    public function mixedData(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rows = db_connect()
            ->table('posts')
            ->select('author, COUNT(*) as count, SUM(views) as total_views')
            ->where('deleted_at', null)
            ->groupBy('author')
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'labels'     => array_column($rows, 'author'),
            'counts'     => array_map('intval', array_column($rows, 'count')),
            'totalViews' => array_map('intval', array_column($rows, 'total_views')),
        ]);
    }
}
