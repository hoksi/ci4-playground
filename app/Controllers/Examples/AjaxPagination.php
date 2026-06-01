<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Entities\Post;
use App\Models\PostModel;

class AjaxPagination extends BaseController
{
    public function index(): string
    {
        return view('examples/ajax-pagination/index', [
            'title' => 'AJAX 페이지네이션',
        ]);
    }

    public function data(): \CodeIgniter\HTTP\ResponseInterface
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = max(5, min(50, (int) ($this->request->getGet('per_page') ?? 10)));
        $search  = trim((string) ($this->request->getGet('q') ?? ''));
        $sort    = (string) ($this->request->getGet('sort') ?? 'id');
        $dir     = strtolower((string) ($this->request->getGet('dir') ?? 'desc'));

        $allowed = ['id', 'title', 'author', 'views', 'created_at'];
        if (! in_array($sort, $allowed, true)) {
            $sort = 'id';
        }
        $dir = $dir === 'asc' ? 'asc' : 'desc';

        $model = new PostModel();

        if ($search !== '') {
            $model->groupStart()
                ->like('title', $search)
                ->orLike('author', $search)
                ->groupEnd();
        }

        $model->orderBy($sort, $dir);
        $posts = $model->paginate($perPage, 'default', $page);
        $pager = $model->pager;

        $total   = $pager->getTotal();
        $pages   = $pager->getPageCount();
        $curPage = $pager->getCurrentPage();

        $rows = array_map(static function (Post $p) {
            return [
                'id'      => $p->id,
                'title'   => $p->title,
                'author'  => $p->author,
                'views'   => $p->views,
                'excerpt' => $p->getExcerpt(80),
                'created' => $p->getFormattedDate(),
            ];
        }, $posts);

        return $this->response->setJSON([
            'data'      => $rows,
            'total'     => $total,
            'page'      => $curPage,
            'per_page'  => $pager->getPerPage(),
            'last_page' => $pages,
            'from'      => $total > 0 ? ($curPage - 1) * $perPage + 1 : 0,
            'to'        => min($curPage * $perPage, $total),
        ]);
    }
}
