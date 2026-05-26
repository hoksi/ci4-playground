<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">암호화 & 해싱</li>
    </ol></nav>
    <h1><i class="bi bi-lock me-2"></i>암호화 & 해싱</h1>
    <p>비밀번호 해싱(<code>password_hash</code>), 검증(<code>password_verify</code>), 그리고 암호화 vs 해싱 차이.</p>
</div>

<ul class="nav nav-tabs mb-3" id="encTab">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="hash">비밀번호 해싱</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="verify">해시 검증</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="code">코드 설명</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="compare">암호화 vs 해싱</a></li>
</ul>

<!-- 비밀번호 해싱 -->
<div id="tab-hash" class="tab-content-pane">
    <div class="example-card">
        <div class="example-card-header">
            <h5><i class="bi bi-key me-2"></i>password_hash() 데모</h5>
        </div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                bcrypt 알고리즘으로 cost 12를 사용합니다. 같은 평문도 매번 다른 해시가 생성됩니다(salt 자동).
            </div>
            <form id="hash-form">
                <div class="mb-3">
                    <label class="form-label fw-bold">평문 비밀번호</label>
                    <input type="text" name="password" class="form-control" placeholder="예: secret123" required>
                </div>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-hash"></i> 해시 생성
                </button>
            </form>

            <div id="hash-result" class="mt-4" style="display:none;">
                <div class="result-box mb-3" id="hash-message"></div>
                <table class="table table-bordered table-sm">
                    <tbody id="hash-rows"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 해시 검증 -->
<div id="tab-verify" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header">
            <h5><i class="bi bi-shield-check me-2"></i>password_verify() 데모</h5>
        </div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                해시 탭에서 생성한 해시를 복사해 붙여 넣고 평문과 비교해 보세요.
            </div>
            <form id="verify-form">
                <div class="mb-3">
                    <label class="form-label fw-bold">평문</label>
                    <input type="text" name="password" class="form-control" placeholder="예: secret123" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">해시</label>
                    <textarea name="hash" class="form-control" rows="2" placeholder="$2y$12$..." required></textarea>
                </div>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-check2-circle"></i> 검증
                </button>
            </form>

            <div id="verify-result" class="mt-4" style="display:none;">
                <div class="result-box mb-3" id="verify-message"></div>
                <table class="table table-bordered table-sm">
                    <tbody id="verify-rows"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>비밀번호 해싱</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// PHP 내장 함수 (CI4에서 그대로 사용)
$plain = $this->request->getPost('password');

// bcrypt 알고리즘, cost 12 (2^12 = 4096회 반복)
$hash = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);

// 반환 예: $2y$12$KIXgKf...kj9R6 (60자)
// DB 컬럼은 VARCHAR(255) 이상 권장 (알고리즘 변경 대비)

return $this->response->setJSON([
    'plain'     => $plain,
    'hash'      => $hash,
    'algorithm' => 'bcrypt',
    'cost'      => 12,
    'length'    => strlen($hash),
]);</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>해시 검증</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">$plain = $this->request->getPost('password');
$hash  = $this->request->getPost('hash');

// 평문과 해시 비교 (Timing-safe)
if (password_verify($plain, $hash)) {
    // 로그인 성공
}

// 해시 알고리즘/옵션 정보
$info = password_get_info($hash);
// ['algo' => 2, 'algoName' => 'bcrypt', 'options' => ['cost' => 12]]

// 알고리즘/cost 변경 시 자동 재해싱 필요 여부 체크
if (password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 13])) {
    $newHash = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 13]);
    // DB 업데이트
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>CI4 Encryption 서비스 (양방향)</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// .env 또는 Encryption Config에서 encryption.key 설정 필요
// > php spark key:generate

$encrypter = \Config\Services::encrypter();

$cipher = $encrypter->encrypt('민감한 데이터');  // 암호화
$plain  = $encrypter->decrypt($cipher);          // 복호화

// 기본 드라이버: OpenSSL (AES-256-CTR + HMAC-SHA512)</code></pre>
        </div>
    </div>
    <div class="result-box warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>주의사항</strong>
        <ul class="mb-0 mt-2">
            <li>DB 컬럼 길이는 <code>VARCHAR(255)</code> 이상 권장 (알고리즘 변경 대비).</li>
            <li>비밀번호는 <strong>절대 평문/MD5/SHA1으로 저장하지 마세요.</strong> 반드시 <code>password_hash</code>.</li>
            <li><code>password_verify()</code>는 Timing-attack에 안전합니다. <code>==</code> 비교는 절대 사용 금지.</li>
            <li>cost 값이 높을수록 안전하지만 느려집니다 (서버 200ms 이내 권장).</li>
        </ul>
    </div>
</div>

<!-- 비교표 -->
<div id="tab-compare" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-bar-chart me-2"></i>암호화 vs 해싱 비교</h5></div>
        <div class="example-card-body">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th></th><th>해싱 (Hashing)</th><th>암호화 (Encryption)</th></tr>
                </thead>
                <tbody>
                    <tr><th>복호화 가능 여부</th><td><span class="badge bg-danger">불가능</span> (단방향)</td><td><span class="badge bg-success">가능</span> (양방향)</td></tr>
                    <tr><th>키 필요 여부</th><td>불필요</td><td>키(secret)와 IV 필요</td></tr>
                    <tr><th>주요 용도</th><td>비밀번호, 무결성 검증, 토큰</td><td>민감 데이터 저장, 통신 보호</td></tr>
                    <tr><th>대표 알고리즘</th><td>bcrypt, Argon2, SHA-256</td><td>AES-256, RSA, ChaCha20</td></tr>
                    <tr><th>PHP 함수</th><td><code>password_hash()</code><br><code>hash_hmac()</code></td><td><code>openssl_encrypt()</code><br>CI4 <code>encrypter()</code></td></tr>
                    <tr><th>같은 입력 → 같은 출력?</th><td>해싱은 같음. <strong>password_hash는 salt로 매번 다름</strong></td><td>키+IV 같으면 같음</td></tr>
                    <tr><th>대표 예시</th><td>로그인 비밀번호, API 토큰, ETag</td><td>주민번호, 카드번호, 메시지</td></tr>
                </tbody>
            </table>

            <div class="result-box info mt-3">
                <strong><i class="bi bi-lightbulb me-2"></i>실무 가이드</strong>
                <ul class="mb-0 mt-2">
                    <li>비밀번호 → 반드시 <strong>해싱</strong> (bcrypt/Argon2).</li>
                    <li>주민번호/카드번호 등 복원 필요한 민감 정보 → <strong>암호화</strong>.</li>
                    <li>파일 무결성 검증 → <code>hash_file('sha256', $file)</code>.</li>
                    <li>API 서명 → <code>hash_hmac('sha256', $payload, $secret)</code>.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('#encTab .nav-link').forEach(el => {
    el.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('#encTab .nav-link').forEach(n => n.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.tab-content-pane').forEach(p => p.style.display = 'none');
        document.getElementById('tab-' + el.dataset.tab).style.display = 'block';
    });
});

document.getElementById('hash-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const res  = await fetch('<?= base_url('examples/encryption/hash') ?>', { method: 'POST', body: formData });
    const data = await res.json();
    const area = document.getElementById('hash-result');
    const msg  = document.getElementById('hash-message');
    const rows = document.getElementById('hash-rows');
    area.style.display = 'block';

    if (! data.success) {
        msg.className = 'result-box danger mb-3';
        msg.innerHTML = '<i class="bi bi-x-circle me-2"></i>' + data.message;
        rows.innerHTML = '';
        return;
    }
    msg.className = 'result-box mb-3';
    msg.innerHTML = '<i class="bi bi-check-circle me-2"></i>해시 생성 완료 (처리 시간: ' + data.elapsed_ms + ' ms)';
    rows.innerHTML = `
        <tr><th style="width:140px;">평문</th><td><code>${data.plain}</code></td></tr>
        <tr><th>알고리즘</th><td><span class="badge bg-secondary">${data.algorithm}</span></td></tr>
        <tr><th>Cost</th><td>${data.cost}</td></tr>
        <tr><th>해시 길이</th><td>${data.length} 문자</td></tr>
        <tr><th>해시 결과</th><td><code style="word-break:break-all;">${data.hash}</code></td></tr>
    `;
});

document.getElementById('verify-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const res  = await fetch('<?= base_url('examples/encryption/verify') ?>', { method: 'POST', body: formData });
    const data = await res.json();
    const area = document.getElementById('verify-result');
    const msg  = document.getElementById('verify-message');
    const rows = document.getElementById('verify-rows');
    area.style.display = 'block';

    if (! data.success) {
        msg.className = 'result-box danger mb-3';
        msg.innerHTML = '<i class="bi bi-x-circle me-2"></i>' + data.message;
        rows.innerHTML = '';
        return;
    }
    if (data.match) {
        msg.className = 'result-box mb-3';
        msg.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i><strong>일치!</strong> 비밀번호가 정확합니다. (검증 시간: ' + data.elapsed_ms + ' ms)';
    } else {
        msg.className = 'result-box danger mb-3';
        msg.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i><strong>불일치</strong> 비밀번호가 다릅니다.';
    }
    rows.innerHTML = `
        <tr><th style="width:140px;">매치 결과</th><td>${data.match ? '<span class="badge bg-success">TRUE</span>' : '<span class="badge bg-danger">FALSE</span>'}</td></tr>
        <tr><th>알고리즘</th><td><span class="badge bg-secondary">${data.info.algoName ?? 'unknown'}</span></td></tr>
        <tr><th>옵션</th><td><code>${JSON.stringify(data.info.options ?? {})}</code></td></tr>
        <tr><th>재해싱 필요?</th><td>${data.needs_rehash ? '<span class="badge bg-warning text-dark">YES</span>' : '<span class="badge bg-light text-dark">NO</span>'}</td></tr>
    `;
});
</script>
<?= $this->endSection() ?>
