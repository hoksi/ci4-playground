<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Views extends BaseController
{
    public function index(): string
    {
        return view('examples/views/index', ['title' => '뷰']);
    }

    public function layout(): string
    {
        return view('examples/views/layout', ['title' => '뷰 — 레이아웃 시스템']);
    }

    public function partial(): string
    {
        $items = ['사과', '바나나', '오렌지', '포도', '딸기'];
        return view('examples/views/partial', [
            'title' => '뷰 — 파셜 & include',
            'items' => $items,
        ]);
    }

    public function cell(): string
    {
        return view('examples/views/cell', ['title' => '뷰 — View Cell']);
    }
}
