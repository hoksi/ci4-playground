<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class QueryBuilderAdvanced extends BaseController
{
    public function index(): string
    {
        return view('examples/querybuilderadvanced/index', [
            'title' => 'Query Builder 고급',
        ]);
    }

    public function joins()
    {
        $db = \Config\Database::connect();

        // INNER JOIN: posts + accounts (posts.id % 3 + 1 로 accounts.id 매핑 시뮬레이션)
        $innerJoin = $db->query("
            SELECT p.id, p.title, p.views, p.author, a.name AS account_name, a.balance
            FROM posts p
            INNER JOIN accounts a ON (p.id % 3 + 1) = a.id
            ORDER BY p.id
        ")->getResultArray();

        // LEFT JOIN: users_demo LEFT JOIN api_keys (없을 수 있음)
        $leftJoin = $db->table('users_demo u')
            ->select('u.id, u.username, u.email, u.role, k.api_key, k.is_active')
            ->join('api_keys k', 'u.id = k.id', 'left')
            ->orderBy('u.id')
            ->get()->getResultArray();

        // API 키 마스킹
        foreach ($leftJoin as &$row) {
            if (! empty($row['api_key'])) {
                $row['api_key'] = substr($row['api_key'], 0, 8) . '...';
            }
        }
        unset($row);

        return $this->response->setJSON([
            'inner_join' => $innerJoin,
            'left_join'  => $leftJoin,
        ]);
    }

    public function subquery()
    {
        $db = \Config\Database::connect();

        // 서브쿼리: 평균 views 이상인 posts 조회
        $result = $db->query("
            SELECT id, title, author, views, created_at
            FROM posts
            WHERE views >= (
                SELECT AVG(views) FROM posts
            )
            ORDER BY views DESC
        ")->getResultArray();

        $avgResult = $db->query("SELECT ROUND(AVG(views), 1) AS avg_views FROM posts")->getRowArray();

        return $this->response->setJSON([
            'avg_views' => $avgResult['avg_views'] ?? 0,
            'results'   => $result,
            'count'     => count($result),
        ]);
    }

    public function aggregate()
    {
        $db = \Config\Database::connect();

        // accounts: 잔액 기준 정렬 + 등급 계산
        $accountStats = $db->query("
            SELECT name, balance,
                CASE WHEN balance >= 1000 THEN '부자' ELSE '일반' END AS tier
            FROM accounts
            ORDER BY balance DESC
        ")->getResultArray();

        // posts: count per day, avg views
        $postStats = $db->query("
            SELECT
                DATE(created_at) AS post_date,
                COUNT(*) AS post_count,
                ROUND(AVG(views), 1) AS avg_views,
                SUM(views) AS total_views,
                MAX(views) AS max_views,
                MIN(views) AS min_views
            FROM posts
            GROUP BY DATE(created_at)
            HAVING post_count >= 1
            ORDER BY post_date DESC
        ")->getResultArray();

        // users: count per role
        $usersByRole = $db->table('users_demo')
            ->select('role, COUNT(*) AS cnt', false)
            ->groupBy('role')
            ->having('cnt >=', 1)
            ->get()->getResultArray();

        return $this->response->setJSON([
            'account_stats' => $accountStats,
            'post_stats'    => $postStats,
            'users_by_role' => $usersByRole,
        ]);
    }

    public function raw()
    {
        $db = \Config\Database::connect();

        // 파라미터 바인딩 직접 쿼리 실행
        $rawQuery1 = $db->query(
            "SELECT id, title, author, views FROM posts WHERE views > ? ORDER BY views DESC LIMIT 5",
            [0]
        )->getResultArray();

        // getLastQuery()로 마지막 실행 쿼리 확인
        $lastQuery = $db->getLastQuery();

        // DB 네이티브 함수 사용 (select 두 번째 인자 false = 이스케이프 없음)
        $rawQuery2 = $db->table('posts')
            ->select("id, title, views, LENGTH(title) AS title_len, UPPER(SUBSTR(title,1,1)) || SUBSTR(title,2) AS title_cap", false)
            ->orderBy('title_len', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // accounts 집계
        $rawQuery3 = $db->query(
            "SELECT COUNT(*) AS total, SUM(balance) AS total_balance, MAX(balance) AS max_balance, MIN(balance) AS min_balance FROM accounts"
        )->getRowArray();

        return $this->response->setJSON([
            'parameterized' => $rawQuery1,
            'last_query'    => (string) $lastQuery,
            'raw_functions' => $rawQuery2,
            'aggregation'   => $rawQuery3,
        ]);
    }
}
