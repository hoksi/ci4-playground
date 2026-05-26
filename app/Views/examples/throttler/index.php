<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-speedometer2 me-2"></i>Throttler (요청 속도 제한)</h1>
    <p>CI4 내장 Throttler로 IP 기반 API Rate Limiting을 구현하는 방법을 학습합니다.</p>
</div>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active">Throttler</li>
    </ol>
</nav>

<?php if ($success = session()->getFlashdata('success')): ?>
<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= esc($success) ?></div>
<?php endif; ?>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-basic">기본 사용법</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-filter">ThrottleFilter</a></li>
</ul>

<div class="tab-content">

    <!-- ── 라이브 데모 ──────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-demo">
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-play-circle text-primary"></i>
                <h5>Rate Limit 데모 — IP당 10초에 5회</h5>
            </div>
            <div class="example-card-body">
                <p class="text-muted small mb-3">버튼을 연속으로 눌러 한도를 초과하면 <strong>429 Too Many Requests</strong> 응답을 확인할 수 있습니다.</p>

                <div class="d-flex gap-2 mb-3">
                    <button id="btnHit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> 요청 보내기
                    </button>
                    <a href="<?= base_url('examples/throttler/reset') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> 카운터 초기화
                    </a>
                </div>

                <div id="result" class="d-none">
                    <div id="resultBadge" class="mb-2"></div>
                    <pre id="resultJson" style="background:#1e1e1e;color:#d4d4d4;padding:1rem;border-radius:8px;font-size:.85rem;"></pre>
                </div>

                <div class="result-box info mt-3">
                    <strong>설정:</strong> <code>CAPACITY=5</code> / <code>SECONDS=10</code> — 10초 윈도우에 최대 5회 허용.
                    한도 초과 시 <code>Retry-After</code> 헤더와 함께 429 반환.
                </div>
            </div>
        </div>
    </div>

    <!-- ── 기본 사용법 ─────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-basic">
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-code-slash text-primary"></i>
                <h5>Throttler::check()</h5>
            </div>
            <div class="example-card-body">
                <p class="mb-3"><code>check()</code>는 토큰 버킷 알고리즘 기반으로 요청을 제한합니다. <code>false</code>를 반환하면 한도 초과입니다.</p>
                <pre><code class="language-php">$throttler = service('throttler');

// check(키, 최대횟수, 시간(초), 비용=1)
// → IP당 1분에 60회 허용
if (! $throttler->check($request->getIPAddress(), 60, MINUTE)) {
    return $response
        ->setStatusCode(429)
        ->setHeader('Retry-After', (string) $throttler->getTokenTime())
        ->setJSON(['error' => 'Too Many Requests']);
}

// 남은 재시도 대기 시간(초)
$retryAfter = $throttler->getTokenTime();
</code></pre>

                <div class="code-label mt-4">토큰 버킷 알고리즘</div>
                <div class="result-box info">
                    <ul class="mb-0 small">
                        <li>버킷에 <strong>capacity</strong>개의 토큰이 있습니다.</li>
                        <li>요청마다 <strong>cost</strong>개의 토큰을 소비합니다.</li>
                        <li>토큰은 <code>seconds / capacity</code> 초마다 1개씩 자동 충전됩니다.</li>
                        <li>버킷이 비면 <code>false</code> 반환 → 429 응답.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- ── ThrottleFilter ─────────────────────────────── -->
    <div class="tab-pane fade" id="tab-filter">
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-funnel text-warning"></i>
                <h5>ThrottleFilter — 라우트 그룹에 적용</h5>
            </div>
            <div class="example-card-body">
                <div class="code-label">app/Filters/ThrottleFilter.php</div>
                <pre><code class="language-php">namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ThrottleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $throttler = service('throttler');

        // IP당 1분에 60회
        if (! $throttler->check($request->getIPAddress(), 60, MINUTE)) {
            return service('response')
                ->setStatusCode(429)
                ->setHeader('Retry-After', (string) $throttler->getTokenTime())
                ->setJSON(['error' => 'Too Many Requests']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
</code></pre>

                <div class="code-label mt-4">app/Config/Filters.php — 필터 등록</div>
                <pre><code class="language-php">public array $aliases = [
    'throttle' => \App\Filters\ThrottleFilter::class,
];
</code></pre>

                <div class="code-label mt-4">app/Config/Routes.php — API 라우트에 적용</div>
                <pre><code class="language-php">$routes->group('api', ['filter' => 'throttle'], function ($routes) {
    $routes->get('users',     'Api\Users::index');
    $routes->post('users',    'Api\Users::create');
    $routes->get('users/(:num)', 'Api\Users::show/$1');
});
</code></pre>
                <div class="result-box info mt-3">
                    필터 방식을 쓰면 컨트롤러 코드를 건드리지 않고 라우트 단위로 Rate Limit을 적용할 수 있어 API 보호에 적합합니다.
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('btnHit').addEventListener('click', async () => {
    const res  = await fetch('<?= base_url('examples/throttler/hit') ?>', {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': '<?= csrf_hash() ?>'}
    });
    const data = await res.json();
    const div  = document.getElementById('result');
    const badge = document.getElementById('resultBadge');
    const pre  = document.getElementById('resultJson');

    div.classList.remove('d-none');
    badge.innerHTML = res.status === 429
        ? '<span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i>429 Too Many Requests</span>'
        : '<span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>200 허용됨</span>';
    pre.textContent = JSON.stringify(data, null, 2);
});
</script>
<?= $this->endSection() ?>
