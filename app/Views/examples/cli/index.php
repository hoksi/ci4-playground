<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">CLI 커맨드</li>
    </ol></nav>
    <h1><i class="bi bi-terminal me-2"></i>CLI 커맨드</h1>
    <p>CI4의 BaseCommand를 사용하여 spark 커맨드를 만들고 실행하는 방법을 알아봅니다.</p>
</div>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-circle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php $tab = $tab ?? 'run'; ?>
<ul class="nav nav-tabs mb-3" id="cliTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'run' ? 'active' : '' ?>" href="#" onclick="showTab('run');return false;">명령어 실행</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- 명령어 실행 -->
<div id="tab-run" class="tab-content-pane" style="display:<?= $tab === 'run' ? 'block' : 'none' ?>">

    <!-- 등록된 명령어 목록 -->
    <?php if ($sparkList): ?>
    <div class="example-card mb-3">
        <div class="example-card-header"><h5><i class="bi bi-list-ul me-2"></i>등록된 Playground 명령어 <small class="text-muted fw-normal">(php spark list Playground)</small></h5></div>
        <div class="example-card-body">
            <pre style="background:#0d1117; color:#e6e6e6; border-radius:8px; padding:1rem; font-size:.8rem; margin:0;"><?= esc($sparkList) ?></pre>
        </div>
    </div>
    <?php endif; ?>

    <!-- 실행 버튼 -->
    <div class="example-card mb-3">
        <div class="example-card-header"><h5><i class="bi bi-play me-2"></i>명령어 실행 (웹에서 시뮬레이션)</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                실제로는 터미널에서 <code>php spark &lt;command&gt;</code>로 실행합니다. 여기서는 웹에서 결과를 미리 확인합니다.
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <?php
                $commands = [
                    'playground:stats'       => '통계 출력',
                    'playground:stats --json' => 'JSON 출력',
                    'playground:seed 3'      => '샘플 3개 추가',
                ];
                foreach ($commands as $cmd => $label): ?>
                <form method="post" action="<?= base_url('examples/cli/run') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="cmd" value="<?= esc($cmd) ?>">
                    <button type="submit" class="demo-btn <?= ($ran ?? '') === $cmd ? '' : 'outline' ?>"
                            style="<?= ($ran ?? '') === $cmd ? '' : 'border:1px solid #dd4814;' ?>;border:1px solid #dd4814;cursor:pointer;">
                        <i class="bi bi-terminal"></i> php spark <?= esc($cmd) ?>
                    </button>
                </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- 출력 결과 -->
    <?php if (isset($output)): ?>
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-terminal me-2"></i>실행 결과: <code>php spark <?= esc($ran) ?></code></h5></div>
        <div class="example-card-body">
            <pre style="background:#0d1117; color:#e6e6e6; border-radius:8px; padding:1rem; font-size:.83rem; margin:0; white-space:pre-wrap;"><?= esc($output) ?></pre>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>BaseCommand 클래스 작성</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// app/Commands/PlaygroundStats.php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class PlaygroundStats extends BaseCommand
{
    protected $group       = 'Playground';        // php spark list 그룹명
    protected $name        = 'playground:stats';  // 실행 이름
    protected $description = '게시물 통계 출력';
    protected $usage       = 'playground:stats [options]';
    protected $options     = [
        '--limit' => '최대 표시 수',
        '--json'  => 'JSON 형식 출력',
    ];
    protected $arguments   = [
        'name' => '인자 설명',  // 위치 인자
    ];

    public function run(array $params): void
    {
        // $params = 위치 인자 배열
        // CLI::getOption('limit') = --limit 값
        CLI::write('결과', 'green');
    }
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>CLI 출력 도구</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 텍스트 출력 (색상: black, dark_gray, blue, dark_blue,
//              light_blue, green, light_green, cyan, light_cyan,
//              red, light_red, purple, light_purple, light_yellow,
//              yellow, light_gray, white)
CLI::write('성공!', 'green');
CLI::error('오류 발생');    // 빨간색
CLI::newLine();             // 빈 줄

// 테이블
CLI::table($tbody, $thead);

// 진행 바
CLI::showProgress($current, $total);

// 사용자 입력
$name = CLI::prompt('이름을 입력하세요');
$yn   = CLI::prompt('계속하시겠습니까?', ['y', 'n']);</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>터미널에서 실행</h5></div>
        <div class="example-card-body">
            <pre><code class="language-bash"># 등록된 명령어 목록
php spark list
php spark list Playground

# 명령어 실행
php spark playground:stats
php spark playground:stats --limit=10
php spark playground:stats --json

# 위치 인자
php spark playground:seed 5

# 도움말
php spark playground:stats --help</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#cliTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
<?= $this->endSection() ?>
