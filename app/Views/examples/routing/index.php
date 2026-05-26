<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">라우팅</li>
    </ol></nav>
    <h1><i class="bi bi-sign-turn-right me-2"></i>라우팅</h1>
    <p>URL과 컨트롤러 메서드를 연결하는 CI4의 라우팅 시스템을 알아봅니다.</p>
</div>

<!-- 1. 기본 라우팅 -->
<div class="example-card">
    <div class="example-card-header">
        <span class="badge bg-danger">1</span>
        <h5>기본 라우팅 — GET / POST</h5>
    </div>
    <div class="example-card-body">
        <p class="text-muted">가장 기본적인 형태로, HTTP 메서드와 URL을 컨트롤러 메서드에 매핑합니다.</p>
        <div class="code-label">app/Config/Routes.php</div>
        <pre><code class="language-php">// GET 요청: 홈 페이지
$routes->get('/', 'Home::index');

// POST 요청: 데이터 저장
$routes->post('examples/controllers/store', 'Examples\Controllers::store');

// GET + POST 동시 처리
$routes->match(['get', 'post'], 'examples/routing/method', 'Examples\Routing::method');</code></pre>
        <div class="mt-3 d-flex gap-2">
            <a href="<?= base_url('examples/routing/method') ?>" class="demo-btn">
                <i class="bi bi-play-fill"></i> GET 데모
            </a>
        </div>
    </div>
</div>

<!-- 2. URL 파라미터 -->
<div class="example-card">
    <div class="example-card-header">
        <span class="badge bg-danger">2</span>
        <h5>URL 파라미터</h5>
    </div>
    <div class="example-card-body">
        <p class="text-muted">URL에서 값을 추출해 컨트롤러에 전달합니다. <code>(:num)</code>, <code>(:alpha)</code>, <code>(:segment)</code> 등의 플레이스홀더를 사용합니다.</p>
        <div class="code-label">app/Config/Routes.php</div>
        <pre><code class="language-php">// (:num)  — 숫자만 허용
$routes->get('examples/routing/params/(:num)', 'Examples\Routing::params/$1');

// (:alpha) — 영문자만 허용
// (:alphanum) — 영문자+숫자
// (:segment) — / 를 제외한 모든 문자
// (:any) — 모든 문자 (슬래시 포함)</code></pre>
        <div class="code-label mt-3">app/Controllers/Examples/Routing.php</div>
        <pre><code class="language-php">public function params(int $id): string
{
    return view('examples/routing/params', ['id' => $id]);
}</code></pre>
        <div class="mt-3 d-flex flex-wrap gap-2">
            <a href="<?= base_url('examples/routing/params/1') ?>" class="demo-btn">ID = 1</a>
            <a href="<?= base_url('examples/routing/params/42') ?>" class="demo-btn">ID = 42</a>
            <a href="<?= base_url('examples/routing/params/999') ?>" class="demo-btn">ID = 999</a>
        </div>
    </div>
</div>

<!-- 3. Named Route -->
<div class="example-card">
    <div class="example-card-header">
        <span class="badge bg-danger">3</span>
        <h5>Named Route (이름 있는 라우트)</h5>
    </div>
    <div class="example-card-body">
        <p class="text-muted">라우트에 이름을 붙이면 URL이 변경되어도 코드를 수정할 필요 없이 <code>route_to()</code>로 URL을 생성할 수 있습니다.</p>
        <div class="code-label">app/Config/Routes.php</div>
        <pre><code class="language-php">$routes->get('examples/routing/named', 'Examples\Routing::named', ['as' => 'routing.named']);</code></pre>
        <div class="code-label mt-3">컨트롤러 / 뷰에서 사용</div>
        <pre><code class="language-php">// 컨트롤러에서
$url = route_to('routing.named'); // → /examples/routing/named

// 뷰에서
echo anchor(route_to('routing.named'), '이동하기');</code></pre>
        <div class="mt-3">
            <a href="<?= base_url('examples/routing/named') ?>" class="demo-btn">
                <i class="bi bi-play-fill"></i> Named Route 데모
            </a>
        </div>
    </div>
</div>

<!-- 4. 라우트 그룹 -->
<div class="example-card">
    <div class="example-card-header">
        <span class="badge bg-danger">4</span>
        <h5>라우트 그룹</h5>
    </div>
    <div class="example-card-body">
        <p class="text-muted">관련된 라우트를 그룹으로 묶어 공통 접두사, 네임스페이스, 필터를 한 번에 적용할 수 있습니다.</p>
        <div class="code-label">app/Config/Routes.php</div>
        <pre><code class="language-php">// 이 프로젝트의 모든 예제 라우트는 'examples' 그룹으로 관리됩니다
$routes->group('examples', ['namespace' => 'App\Controllers\Examples'], function ($routes) {
    $routes->get('routing', 'Routing::index');
    $routes->get('controllers', 'Controllers::index');
    $routes->get('models', 'Models::index');
    // ...
});

// 필터 적용 예시
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('users', 'Admin::users');
});</code></pre>
        <div class="result-box mt-3">
            <i class="bi bi-info-circle me-2 text-primary"></i>
            <strong>이 프로젝트 전체 라우트</strong>가 <code>examples</code> 그룹으로 구성되어 있습니다. <a href="<?= base_url() ?>">소스코드</a>에서 <code>app/Config/Routes.php</code>를 확인하세요.
        </div>
    </div>
</div>

<!-- 5. 리다이렉트 -->
<div class="example-card">
    <div class="example-card-header">
        <span class="badge bg-danger">5</span>
        <h5>리다이렉트</h5>
    </div>
    <div class="example-card-body">
        <p class="text-muted">컨트롤러에서 다른 URL로 리다이렉트하거나, 라우트 자체에 리다이렉트를 설정할 수 있습니다.</p>
        <div class="code-label">컨트롤러에서 리다이렉트</div>
        <pre><code class="language-php">// URL로 리다이렉트
return redirect()->to(base_url('examples/routing/redirected'));

// Named Route로 리다이렉트
return redirect()->route('routing.named');

// 이전 페이지로 돌아가기
return redirect()->back()->with('message', '처리 완료!');

// 라우트 설정에서 바로 리다이렉트
$routes->addRedirect('old-path', 'new-path', 301);</code></pre>
        <div class="mt-3">
            <a href="<?= base_url('examples/routing/redirect') ?>" class="demo-btn">
                <i class="bi bi-arrow-repeat"></i> 리다이렉트 데모
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
