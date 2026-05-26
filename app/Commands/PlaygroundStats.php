<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PostModel;

class PlaygroundStats extends BaseCommand
{
    protected $group       = 'Playground';
    protected $name        = 'playground:stats';
    protected $description = '게시물 통계를 출력합니다.';
    protected $usage       = 'playground:stats [options]';
    protected $options     = [
        '--limit' => '표시할 최대 게시물 수 (기본값: 5)',
        '--json'  => 'JSON 형식으로 출력',
    ];

    public function run(array $params): void
    {
        $limit  = (int) CLI::getOption('limit') ?: 5;
        $asJson = (bool) CLI::getOption('json');

        CLI::write('');
        CLI::write('CI4 Playground — 게시물 통계', 'green');
        CLI::write(str_repeat('─', 50));

        $model   = new PostModel();
        $total   = $model->countAll();
        $posts   = $model->orderBy('views', 'DESC')->limit($limit)->findAll();

        if ($asJson) {
            CLI::write(json_encode([
                'total' => $total,
                'top'   => array_map(fn($p) => ['title' => $p->title, 'views' => $p->views], $posts),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return;
        }

        CLI::write("총 게시물: {$total}개", 'yellow');
        CLI::write('');
        CLI::write("인기 게시물 Top {$limit}:", 'cyan');

        $thead = ['#', '제목', '조회수', '작성자'];
        $tbody = [];
        foreach ($posts as $i => $post) {
            $tbody[] = [$i + 1, $post->title, number_format($post->views), $post->author];
        }
        CLI::table($tbody, $thead);

        CLI::write('');
        CLI::write('완료!', 'green');
    }
}
