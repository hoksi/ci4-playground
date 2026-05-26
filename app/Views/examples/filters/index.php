<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">필터</li>
    </ol></nav>
    <h1><i class="bi bi-funnel me-2"></i>필터 (Filters)</h1>
    <p>요청 전/후에 공통 로직을 처리하는 미들웨어 역할을 합니다.</p>
</div>

<!-- 필터 개념 -->
<div class="example-card">
    <div class="example-card-header"><span class="badge bg-warning text-dark">1</span><h5>필터 동작 방식</h5></div>
    <div class="example-card-body">
        <div class="result-box info mb-3">
            <strong>요청 흐름:</strong>
            <code>요청</code> → <strong>Before Filter</strong> → <code>Controller</code> → <strong>After Filter</strong> → <code>응답</code>
        </div>
        <pre><code class="language-php">// app/Filters/AuthFilter.php
class AuthFilter implements FilterInterface
{
    // 컨트롤러 실행 전 호출
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('filter_logged_in')) {
            // null이 아닌 값을 반환하면 컨트롤러 실행이 중단됩니다
            return redirect()->to(base_url('examples/filters/login'))
                             ->with('error', '로그인이 필요합니다.');
        }
        // null 반환 시 → 컨트롤러 계속 실행
    }

    // 컨트롤러 실행 후 호출
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // 응답 헤더 추가, 로깅 등
    }
}</code></pre>
    </div>
</div>

<!-- 필터 등록 -->
<div class="example-card">
    <div class="example-card-header"><span class="badge bg-warning text-dark">2</span><h5>필터 등록 & 적용</h5></div>
    <div class="example-card-body">
        <div class="code-label">app/Config/Filters.php — 별칭 등록</div>
        <pre><code class="language-php">public array $aliases = [
    // ... 기본 필터들 ...
    'auth-example' => \App\Filters\AuthFilter::class,
];</code></pre>
        <div class="code-label mt-3">app/Config/Routes.php — 특정 라우트에 적용</div>
        <pre><code class="language-php">// 단일 라우트에 필터 적용
$routes->get('examples/filters/protected', 'Examples\Filters::protectedPage',
    ['filter' => 'auth-example']
);

// 라우트 그룹 전체에 적용
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
});

// Filters.php에서 URI 패턴으로 전역 적용
public array $filters = [
    'auth' => ['before' => ['admin/*']],
];</code></pre>
    </div>
</div>

<!-- 라이브 데모 -->
<div class="example-card">
    <div class="example-card-header"><span class="badge bg-warning text-dark">3</span><h5>라이브 데모 — 인증 필터</h5></div>
    <div class="example-card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="result-box text-center">
                    <i class="bi bi-unlock-fill fs-3 text-success d-block mb-2"></i>
                    <strong>공개 페이지</strong>
                    <p class="text-muted small mt-1">필터 없음 — 누구나 접근 가능</p>
                    <a href="<?= base_url('examples/filters/public') ?>" class="demo-btn" style="background:#fd7e14;">접근 →</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="result-box text-center" style="background:#fff3ef;">
                    <i class="bi bi-lock-fill fs-3 text-danger d-block mb-2"></i>
                    <strong>보호된 페이지</strong>
                    <p class="text-muted small mt-1">auth-example 필터 적용<br>비로그인 시 리다이렉트</p>
                    <a href="<?= base_url('examples/filters/protected') ?>" class="demo-btn" style="background:#dc3545;">접근 시도 →</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="result-box text-center">
                    <i class="bi bi-person-check-fill fs-3 text-primary d-block mb-2"></i>
                    <strong>로그인</strong>
                    <p class="text-muted small mt-1">ID: <code>demo</code><br>PW: <code>1234</code></p>
                    <a href="<?= base_url('examples/filters/login') ?>" class="demo-btn" style="background:#0d6efd;">로그인 →</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
