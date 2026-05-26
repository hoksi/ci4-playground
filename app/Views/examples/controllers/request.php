<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/controllers') ?>">컨트롤러</a></li>
        <li class="breadcrumb-item active text-white">Request 객체</li>
    </ol></nav>
    <h1><i class="bi bi-inbox me-2"></i>Request 객체 정보</h1>
    <p>현재 HTTP 요청에서 추출한 실제 데이터입니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header">
        <h5><i class="bi bi-check-circle text-success me-2"></i>현재 요청 분석 결과</h5>
    </div>
    <div class="example-card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="result-box">
                    <div class="code-label">HTTP 메서드</div>
                    <code class="fs-6"><?= esc(strtoupper($method)) ?></code>
                </div>
            </div>
            <div class="col-md-6">
                <div class="result-box">
                    <div class="code-label">클라이언트 IP</div>
                    <code class="fs-6"><?= esc($ipAddress) ?></code>
                </div>
            </div>
            <div class="col-12">
                <div class="result-box info">
                    <div class="code-label">요청 URI</div>
                    <code><?= esc($uri) ?></code>
                </div>
            </div>
            <?php if (! empty($queryParams)): ?>
            <div class="col-12">
                <div class="result-box warning">
                    <div class="code-label">GET 파라미터 (?lang=ko&page=1)</div>
                    <pre style="background:transparent; padding:0; margin:0;"><code class="language-php"><?= esc(print_r($queryParams, true)) ?></code></pre>
                </div>
            </div>
            <?php endif; ?>
            <div class="col-12">
                <div class="result-box">
                    <div class="code-label">User-Agent</div>
                    <small><?= esc($userAgent) ?></small>
                </div>
            </div>
        </div>

        <div class="code-label mt-4">Request 메서드 참고</div>
        <pre><code class="language-php">$this->request->getMethod()           // 'get', 'post' 등
$this->request->getIPAddress()        // 클라이언트 IP
$this->request->getUri()              // URI 객체
$this->request->getGet('key')         // GET 파라미터
$this->request->getPost('key')        // POST 파라미터
$this->request->getVar('key')         // GET + POST 모두
$this->request->getJSON()             // JSON 요청 바디
$this->request->getFile('file_input') // 업로드 파일
$this->request->getUserAgent()        // UA 객체
$this->request->isAJAX()             // AJAX 요청 여부</code></pre>

        <div class="mt-3 d-flex gap-2">
            <a href="<?= base_url('examples/controllers/request') ?>?lang=ko&page=1" class="demo-btn outline" style="color:#0d6efd;border-color:#0d6efd;">다시 로드 (쿼리스트링)</a>
            <a href="<?= base_url('examples/controllers') ?>" class="demo-btn" style="background:#0d6efd;">← 컨트롤러로</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
