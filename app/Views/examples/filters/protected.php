<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/filters') ?>">필터</a></li>
        <li class="breadcrumb-item active text-white">보호된 페이지</li>
    </ol></nav>
    <h1><i class="bi bi-lock-fill me-2"></i>보호된 페이지</h1>
    <p>auth-example 필터를 통과하여 접근한 페이지입니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header">
        <h5><i class="bi bi-shield-check text-success me-2"></i>인증 필터 통과 — 접근 허용</h5>
    </div>
    <div class="example-card-body">
        <div class="result-box">
            <i class="bi bi-person-check-fill text-success me-2"></i>
            <strong><?= esc($user) ?></strong>님, 환영합니다! 필터를 통과하여 이 페이지에 접근하셨습니다.
        </div>

        <div class="code-label mt-3">필터 동작 확인</div>
        <pre><code class="language-php">// AuthFilter::before() 가 실행된 결과:
// session()->get('filter_logged_in') === true  →  통과!

// 만약 로그인하지 않았다면:
// session()->get('filter_logged_in') === false →  login 페이지로 리다이렉트</code></pre>

        <div class="result-box info mt-3">
            <i class="bi bi-info-circle me-2"></i>
            <strong>테스트 방법:</strong>
            <a href="<?= base_url('examples/filters/logout') ?>" class="text-danger">로그아웃</a> 후
            이 페이지 URL(<code>/examples/filters/protected</code>)에 다시 접근하면
            로그인 페이지로 자동 리다이렉트됩니다.
        </div>

        <div class="mt-3 d-flex gap-2">
            <a href="<?= base_url('examples/filters/logout') ?>" class="demo-btn" style="background:#dc3545;">
                <i class="bi bi-box-arrow-right"></i> 로그아웃
            </a>
            <a href="<?= base_url('examples/filters') ?>" class="demo-btn" style="background:#fd7e14;">← 필터로</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
