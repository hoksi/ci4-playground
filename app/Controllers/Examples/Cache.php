<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Models\PostModel;

class Cache extends BaseController
{
    private string $cacheKey = 'playground_posts_summary';

    public function index(): string
    {
        $cache = \Config\Services::cache();

        $cached    = $cache->get($this->cacheKey);
        $cacheInfo = $cache->getMetaData($this->cacheKey);
        $isCached  = $cached !== null;

        if (! $isCached) {
            $start   = microtime(true);
            $data    = $this->fetchExpensiveData();
            $elapsed = round((microtime(true) - $start) * 1000, 2);

            $cache->save($this->cacheKey, $data, 60);
            $cached = $data;
        } else {
            $elapsed = 0;
        }

        return view('examples/cache/index', [
            'title'     => '캐싱',
            'data'      => $cached,
            'isCached'  => $isCached,
            'elapsed'   => $elapsed,
            'cacheInfo' => $cacheInfo ?: [],
            'cacheKey'  => $this->cacheKey,
        ]);
    }

    public function clear()
    {
        $cache = \Config\Services::cache();
        $cache->delete($this->cacheKey);
        return redirect()->to(base_url('examples/cache'))
            ->with('success', "캐시 키 [{$this->cacheKey}] 삭제 완료");
    }

    public function clearAll()
    {
        \Config\Services::cache()->clean();
        return redirect()->to(base_url('examples/cache'))
            ->with('success', '전체 캐시 삭제 완료');
    }

    private function fetchExpensiveData(): array
    {
        usleep(200000); // 0.2초 지연으로 "무거운 작업" 시뮬레이션
        $model = new PostModel();
        $posts = $model->orderBy('views', 'DESC')->limit(5)->findAll();
        return array_map(fn($p) => [
            'id'     => $p->id,
            'title'  => $p->title,
            'author' => $p->author,
            'views'  => $p->views,
        ], $posts);
    }
}
