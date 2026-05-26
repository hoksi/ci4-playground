<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PostModel;

class PlaygroundSeed extends BaseCommand
{
    protected $group       = 'Playground';
    protected $name        = 'playground:seed';
    protected $description = '샘플 게시물을 데이터베이스에 추가합니다.';
    protected $usage       = 'playground:seed [count]';
    protected $arguments   = [
        'count' => '추가할 게시물 수 (기본값: 3)',
    ];

    public function run(array $params): void
    {
        $count = (int) ($params[0] ?? 3);
        if ($count < 1 || $count > 20) {
            CLI::error('count는 1~20 사이 값이어야 합니다.');
            return;
        }

        CLI::write("게시물 {$count}개 추가 중...", 'yellow');

        $model   = new PostModel();
        $titles  = ['PHP 기초', 'CI4 라우팅', '데이터베이스 활용', '뷰 템플릿', 'REST API', '필터 패턴', '캐싱 전략', '보안 best practice'];
        $authors = ['홍길동', '김철수', '이영희', '박민준'];

        for ($i = 0; $i < $count; $i++) {
            $model->insert([
                'title'   => $titles[array_rand($titles)] . ' #' . rand(100, 999),
                'content' => 'CLI 명령어로 생성된 샘플 게시물입니다.',
                'author'  => $authors[array_rand($authors)],
                'views'   => rand(0, 500),
            ]);
            CLI::showProgress($i + 1, $count);
        }

        CLI::newLine();
        CLI::write("{$count}개 게시물 추가 완료!", 'green');
    }
}
