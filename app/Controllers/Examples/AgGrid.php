<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class AgGrid extends BaseController
{
    public function index(): string
    {
        return view('examples/aggrid/index', ['title' => 'AG Grid']);
    }

    /** 클라이언트 사이드 — 전체 데이터 JSON 반환 */
    public function data(): \CodeIgniter\HTTP\Response
    {
        $rows = db_connect()
            ->table('posts')
            ->select('id, title, author, views, created_at')
            ->where('deleted_at', null)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($rows);
    }

    /** 서버 사이드 — 페이지네이션·정렬·검색 처리 후 JSON 반환 */
    public function serverData(): \CodeIgniter\HTTP\Response
    {
        $startRow  = (int) ($this->request->getGet('startRow') ?? 0);
        $endRow    = (int) ($this->request->getGet('endRow') ?? 20);
        $sortField = $this->request->getGet('sortField') ?? 'id';
        $sortDir   = $this->request->getGet('sortDir') ?? 'asc';
        $search    = trim($this->request->getGet('search') ?? '');

        $allowed = ['id', 'title', 'author', 'views', 'created_at'];
        if (! in_array($sortField, $allowed)) $sortField = 'id';
        $sortDir = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';

        $builder = db_connect()
            ->table('posts')
            ->select('id, title, author, views, created_at')
            ->where('deleted_at', null);

        if ($search !== '') {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('author', $search)
                ->groupEnd();
        }

        $total = $builder->countAllResults(false);

        $rows = $builder
            ->orderBy($sortField, $sortDir)
            ->limit($endRow - $startRow, $startRow)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'rows'  => $rows,
            'total' => $total,
        ]);
    }
}
