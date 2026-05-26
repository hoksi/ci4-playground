<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/routing') ?>">라우팅</a></li>
        <li class="breadcrumb-item active text-white">HTTP 메서드</li>
    </ol></nav>
    <h1><i class="bi bi-arrow-left-right me-2"></i>HTTP 메서드 라우팅</h1>
    <p>같은 URL이라도 GET/POST에 따라 다른 처리를 할 수 있습니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header">
        <h5><i class="bi bi-check-circle text-success me-2"></i>현재 요청 정보</h5>
    </div>
    <div class="example-card-body">
        <div class="result-box <?= $httpMethod === 'POST' ? '' : 'info' ?>">
            <p class="mb-0">
                <strong>HTTP 메서드:</strong>
                <span class="badge <?= $httpMethod === 'POST' ? 'bg-warning text-dark' : 'bg-primary' ?> ms-2 fs-6">
                    <?= esc($httpMethod) ?>
                </span>
            </p>
        </div>

        <div class="code-label mt-3">Routes.php — match()로 여러 메서드 허용</div>
        <pre><code class="language-php">// GET과 POST 모두 허용
$routes->match(['get', 'post'], 'examples/routing/method', 'Examples\Routing::method');

// 이 라우트에 다른 메서드(PUT, DELETE 등)로 접근하면 404 오류</code></pre>

        <div class="code-label mt-3">컨트롤러 — 메서드 분기 처리</div>
        <pre><code class="language-php">public function method(): string
{
    $httpMethod = $this->request->getMethod(); // 'get' 또는 'post'
    return view('examples/routing/method', [
        'httpMethod' => strtoupper($httpMethod),
    ]);
}</code></pre>

        <div class="mt-3 d-flex gap-3 align-items-center flex-wrap">
            <a href="<?= base_url('examples/routing/method') ?>" class="demo-btn outline">GET 요청</a>
            <form method="post" action="<?= base_url('examples/routing/method') ?>" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="demo-btn" style="border:none; cursor:pointer;">
                    <i class="bi bi-send"></i> POST 요청 보내기
                </button>
            </form>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="<?= base_url('examples/routing') ?>" class="demo-btn outline">← 라우팅으로</a>
</div>

<?= $this->endSection() ?>
