<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/filters') ?>">필터</a></li>
        <li class="breadcrumb-item active text-white">공개 페이지</li>
    </ol></nav>
    <h1><i class="bi bi-unlock-fill me-2"></i>공개 페이지</h1>
    <p>필터가 적용되지 않아 누구나 접근할 수 있는 페이지입니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header">
        <h5><i class="bi bi-check-circle text-success me-2"></i>접근 성공 — 필터 없음</h5>
    </div>
    <div class="example-card-body">
        <div class="result-box">
            <i class="bi bi-unlock-fill text-success me-2"></i>
            이 페이지는 로그인 없이 누구나 접근할 수 있습니다.
        </div>
        <div class="code-label mt-3">Routes.php — 필터 없음</div>
        <pre><code class="language-php">// 필터 미적용 — 자유롭게 접근 가능
$routes->get('examples/filters/public', 'Examples\Filters::publicPage');</code></pre>
        <div class="mt-3 d-flex gap-2">
            <a href="<?= base_url('examples/filters/protected') ?>" class="demo-btn" style="background:#dc3545;">보호된 페이지 접근 시도</a>
            <a href="<?= base_url('examples/filters') ?>" class="demo-btn" style="background:#fd7e14;">← 필터로</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
