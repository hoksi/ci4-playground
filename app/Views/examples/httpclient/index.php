<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">HTTP 클라이언트</li>
    </ol></nav>
    <h1><i class="bi bi-globe me-2"></i>HTTP 클라이언트</h1>
    <p>CI4의 CURLRequest를 사용하여 외부 API를 호출하는 방법을 알아봅니다.</p>
</div>

<?php $tab = $tab ?? 'get'; ?>

<ul class="nav nav-tabs mb-3" id="httpTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'get' ? 'active' : '' ?>" href="#" onclick="showTab('get');return false;">GET 요청</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'post' ? 'active' : '' ?>" href="#" onclick="showTab('post');return false;">POST 요청</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'query' ? 'active' : '' ?>" href="#" onclick="showTab('query');return false;">쿼리 파라미터</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- GET 요청 -->
<div id="tab-get" class="tab-content-pane" style="display:<?= $tab === 'get' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-arrow-down-circle me-2"></i>GET 단건 조회</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                <code>jsonplaceholder.typicode.com/posts/1</code>에 GET 요청을 보냅니다.
            </div>
            <form method="post" action="<?= base_url('examples/httpclient/get') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-send"></i> GET 요청 실행
                </button>
            </form>
            <?php if (isset($result) && $tab === 'get'): ?>
            <div class="mt-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success"><?= $statusCode ?></span>
                    <code><?= esc($method) ?> <?= esc($url) ?></code>
                </div>
                <pre style="background:#0d1117; border-radius:8px; padding:1rem;"><code class="language-json"><?= esc(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></code></pre>
            </div>
            <?php elseif (isset($error) && $tab === 'get'): ?>
            <div class="alert alert-danger mt-3"><?= esc($error) ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- POST 요청 -->
<div id="tab-post" class="tab-content-pane" style="display:<?= $tab === 'post' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-arrow-up-circle me-2"></i>POST JSON 전송</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                JSON 바디를 포함한 POST 요청을 <code>jsonplaceholder.typicode.com/posts</code>에 전송합니다.
            </div>
            <form method="post" action="<?= base_url('examples/httpclient/post') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">제목</label>
                    <input type="text" name="title" class="form-control" value="<?= esc(($old['title'] ?? 'CI4 HTTP 클라이언트 테스트')) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">본문</label>
                    <textarea name="body" class="form-control" rows="3"><?= esc(($old['body'] ?? 'CURLRequest로 POST 요청을 보냅니다.')) ?></textarea>
                </div>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-send"></i> POST 요청 실행
                </button>
            </form>
            <?php if (isset($result) && $tab === 'post'): ?>
            <div class="mt-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary"><?= $statusCode ?></span>
                    <code><?= esc($method) ?> <?= esc($url) ?></code>
                </div>
                <pre style="background:#0d1117; border-radius:8px; padding:1rem;"><code class="language-json"><?= esc(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></code></pre>
            </div>
            <?php elseif (isset($error) && $tab === 'post'): ?>
            <div class="alert alert-danger mt-3"><?= esc($error) ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 쿼리 파라미터 -->
<div id="tab-query" class="tab-content-pane" style="display:<?= $tab === 'query' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-list-columns me-2"></i>쿼리 파라미터로 목록 조회</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                <code>query</code> 옵션으로 URL 쿼리 파라미터를 전달합니다. <code>?_limit=5</code>
            </div>
            <form method="post" action="<?= base_url('examples/httpclient/list') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-send"></i> 목록 5개 조회
                </button>
            </form>
            <?php if (isset($result) && $tab === 'query'): ?>
            <div class="mt-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success"><?= $statusCode ?></span>
                    <code><?= esc($method) ?> <?= esc($url) ?></code>
                </div>
                <pre style="background:#0d1117; border-radius:8px; padding:1rem;"><code class="language-json"><?= esc(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></code></pre>
            </div>
            <?php elseif (isset($error) && $tab === 'query'): ?>
            <div class="alert alert-danger mt-3"><?= esc($error) ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>기본 사용법</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 서비스로 인스턴스 생성
$client = \Config\Services::curlrequest();

// GET 요청
$response = $client->get('https://api.example.com/posts/1');
$data     = json_decode($response->getBody(), true);
$status   = $response->getStatusCode(); // 200

// 헤더와 함께 요청
$response = $client->get('https://api.example.com/data', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept'        => 'application/json',
    ],
]);</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>POST / JSON 전송</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// JSON 바디 전송
$response = $client->post('https://api.example.com/posts', [
    'json' => [
        'title'  => '제목',
        'body'   => '본문',
        'userId' => 1,
    ],
]);

// 폼 데이터 전송
$response = $client->post('https://api.example.com/form', [
    'form_params' => [
        'field1' => 'value1',
        'field2' => 'value2',
    ],
]);</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>쿼리 파라미터 & 에러 처리</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 쿼리 파라미터 전달
$response = $client->get('https://api.example.com/posts', [
    'query' => ['_limit' => 5, 'userId' => 1],
    // → /posts?_limit=5&userId=1
]);

// 에러 처리
use CodeIgniter\HTTP\Exceptions\HTTPException;
try {
    $response = $client->get('https://api.example.com/data', [
        'timeout' => 5,          // 5초 타임아웃
        'http_errors' => false,  // 4xx/5xx도 예외 대신 응답으로 받기
    ]);
    if ($response->getStatusCode() >= 400) {
        // 에러 응답 처리
    }
} catch (HTTPException $e) {
    log_message('error', $e->getMessage());
}</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#httpTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
<?= $this->endSection() ?>
