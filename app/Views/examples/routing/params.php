<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/routing') ?>">라우팅</a></li>
        <li class="breadcrumb-item active text-white">URL 파라미터</li>
    </ol></nav>
    <h1><i class="bi bi-sign-turn-right me-2"></i>URL 파라미터 결과</h1>
    <p>URL에서 추출된 파라미터를 컨트롤러가 받아 뷰에 전달합니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header">
        <h5><i class="bi bi-check-circle text-success me-2"></i>파라미터 수신 성공</h5>
    </div>
    <div class="example-card-body">
        <div class="result-box">
            <p class="mb-1"><strong>요청 URL:</strong> <code><?= current_url() ?></code></p>
            <p class="mb-0"><strong>수신된 ID 값:</strong> <span class="badge bg-danger fs-5"><?= esc($id) ?></span></p>
        </div>
        <div class="code-label mt-3">실제 동작 코드</div>
        <pre><code class="language-php">// Routes.php: (:num) 플레이스홀더가 숫자만 허용
$routes->get('examples/routing/params/(:num)', 'Examples\Routing::params/$1');

// 컨트롤러: $id 파라미터로 자동 전달
public function params(int $id): string
{
    return view('examples/routing/params', ['id' => $id]);
}

// 뷰: 출력
echo esc($id); // → <?= esc($id) ?></code></pre>
        <div class="mt-3 d-flex gap-2">
            <a href="<?= base_url('examples/routing/params/1') ?>" class="demo-btn outline">ID = 1</a>
            <a href="<?= base_url('examples/routing/params/42') ?>" class="demo-btn outline">ID = 42</a>
            <a href="<?= base_url('examples/routing/params/100') ?>" class="demo-btn outline">ID = 100</a>
            <a href="<?= base_url('examples/routing') ?>" class="demo-btn">← 라우팅으로</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
