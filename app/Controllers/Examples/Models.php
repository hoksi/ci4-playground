<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Models\PostModel;

class Models extends BaseController
{
    public function index(): string
    {
        $model = new PostModel();
        $posts = $model->orderBy('created_at', 'DESC')->findAll(5);

        return view('examples/models/index', [
            'title' => '모델 & 데이터베이스',
            'posts' => $posts,
        ]);
    }

    public function queryBuilder(): string
    {
        $model = new PostModel();
        $db    = \Config\Database::connect();

        // Query Builder 예시들
        $topPosts   = $model->where('views >', 50)->orderBy('views', 'DESC')->findAll(3);
        $totalViews = $model->selectSum('views')->first()->views ?? 0;
        $countAll   = $model->countAllResults();

        // Raw Query Builder
        $rawResult = $db->table('posts')
            ->select('author, SUM(views) as total_views, COUNT(*) as post_count')
            ->groupBy('author')
            ->orderBy('total_views', 'DESC')
            ->get()->getResultArray();

        return view('examples/models/querybuilder', [
            'title'      => '모델 — Query Builder',
            'topPosts'   => $topPosts,
            'totalViews' => $totalViews,
            'countAll'   => $countAll,
            'rawResult'  => $rawResult,
        ]);
    }

    public function pagination(): string
    {
        $model = new PostModel();
        $posts = $model->orderBy('created_at', 'DESC')->paginate(3, 'default');
        $pager = $model->pager;

        return view('examples/models/pagination', [
            'title' => '모델 — 페이지네이션',
            'posts' => $posts,
            'pager' => $pager,
        ]);
    }

    public function entity(): string
    {
        $model = new PostModel();
        $post  = $model->first();

        return view('examples/models/entity', [
            'title' => '모델 — Entity 클래스',
            'post'  => $post,
        ]);
    }
}
