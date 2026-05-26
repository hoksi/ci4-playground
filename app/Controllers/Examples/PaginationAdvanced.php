<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Models\PostModel;

class PaginationAdvanced extends BaseController
{
    public function index(): string
    {
        $model = new PostModel();
        $posts = $model->orderBy('id', 'DESC')->paginate(5, 'default');
        $pager = $model->pager;

        return view('examples/paginationadvanced/index', [
            'title' => 'Pagination 심화',
            'posts' => $posts,
            'pager' => $pager,
        ]);
    }

    public function data()
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = max(1, min(50, (int) ($this->request->getGet('per_page') ?? 5)));

        $model = new PostModel();
        $data  = $model->orderBy('id', 'DESC')->paginate($perPage, 'default', $page);
        $pager = $model->pager;

        $rows = array_map(static function ($p) {
            return [
                'id'      => $p->id,
                'title'   => $p->title,
                'author'  => $p->author,
                'views'   => $p->views,
                'excerpt' => method_exists($p, 'getExcerpt') ? $p->getExcerpt(60) : mb_substr($p->content ?? '', 0, 60),
                'created' => $p->created_at ? $p->created_at->format('Y-m-d H:i') : '',
            ];
        }, $data);

        return $this->response->setJSON([
            'success'   => true,
            'data'      => $rows,
            'total'     => $pager->getTotal(),
            'page'      => $pager->getCurrentPage(),
            'per_page'  => $pager->getPerPage(),
            'last_page' => $pager->getPageCount(),
            'has_more'  => $page < $pager->getPageCount(),
        ]);
    }
}
