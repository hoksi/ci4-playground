<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/routing') ?>">라우팅</a></li>
        <li class="breadcrumb-item active text-white">Named Route</li>
    </ol></nav>
    <h1><i class="bi bi-tag me-2"></i>Named Route 데모</h1>
    <p>이름으로 라우트 URL을 동적으로 생성합니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header">
        <h5><i class="bi bi-check-circle text-success me-2"></i>route_to() 결과</h5>
    </div>
    <div class="example-card-body">
        <div class="result-box">
            <p class="mb-1"><strong>라우트 이름:</strong> <code>routing.named</code></p>
            <p class="mb-0"><strong>생성된 URL:</strong> <code><?= esc($generatedUrl) ?></code></p>
        </div>
        <div class="code-label mt-3">컨트롤러 코드</div>
        <pre><code class="language-php">// Named Route 등록 (Routes.php)
$routes->get('examples/routing/named', 'Examples\Routing::named', ['as' => 'routing.named']);

// 컨트롤러에서 URL 생성
public function named(): string
{
    $url = route_to('routing.named'); // → /examples/routing/named
    return view('examples/routing/named', ['generatedUrl' => $url]);
}</code></pre>
        <div class="result-box info mt-3">
            <i class="bi bi-lightbulb me-2 text-primary"></i>
            <strong>장점:</strong> URL 구조가 바뀌어도 <code>route_to('routing.named')</code>만 쓰면 자동으로 올바른 URL을 생성합니다.
        </div>
        <div class="mt-3">
            <a href="<?= base_url('examples/routing') ?>" class="demo-btn">← 라우팅으로</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
