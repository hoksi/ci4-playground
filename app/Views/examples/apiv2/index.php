<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">RESTful API v2 (JWT)</li>
    </ol></nav>
    <h1><i class="bi bi-braces-asterisk me-2"></i>RESTful API v2 + JWT</h1>
    <p>JSON Web Token으로 인증되는 RESTful API. 토큰 발급 → Bearer 헤더 인증 → 보호된 리소스 접근.</p>
</div>

<ul class="nav nav-tabs mb-3" id="v2Tab">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="jwt">JWT 구조</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="code">코드 설명</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="compare">v1 vs v2</a></li>
</ul>

<!-- 라이브 데모 -->
<div id="tab-demo" class="tab-content-pane">
    <div class="result-box warning mb-3">
        <i class="bi bi-info-circle me-2"></i>
        먼저 <a href="<?= base_url('examples/auth/register') ?>" class="alert-link">회원 인증 예제</a>에서 계정을 만들어 두세요.
        같은 <code>auth_users</code> 테이블을 사용합니다.
    </div>

    <div class="row g-3">
        <div class="col-md-5">
            <div class="example-card">
                <div class="example-card-header"><h5><i class="bi bi-key me-2"></i>1) 토큰 발급</h5></div>
                <div class="example-card-body">
                    <form id="token-form">
                        <div class="mb-2">
                            <label class="form-label fw-bold">이메일</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">비밀번호</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                            <i class="bi bi-shield-check"></i> 토큰 발급
                        </button>
                    </form>
                    <div id="token-result" class="mt-3" style="display:none;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="example-card">
                <div class="example-card-header">
                    <h5><i class="bi bi-cloud-arrow-down me-2"></i>2) 보호된 API 호출</h5>
                </div>
                <div class="example-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">발급된 JWT 토큰 <small class="text-muted">(자동 채워짐)</small></label>
                        <textarea id="jwt-input" class="form-control" rows="2" placeholder="발급된 토큰이 자동으로 입력됩니다."></textarea>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button class="btn btn-sm btn-primary" onclick="callApi('GET', '<?= base_url('examples/apiv2/users') ?>')">
                            <i class="bi bi-people"></i> GET /users
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="callApi('GET', '<?= base_url('examples/apiv2/users/1') ?>')">
                            <i class="bi bi-person"></i> GET /users/1
                        </button>
                        <button class="btn btn-sm btn-success" onclick="createUserApi()">
                            <i class="bi bi-plus-circle"></i> POST /users
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="callApiNoAuth()">
                            <i class="bi bi-shield-x"></i> 토큰 없이 호출 (401 테스트)
                        </button>
                    </div>

                    <div class="mb-2">
                        <span class="code-label">응답 (Status: <span id="api-status">-</span>)</span>
                        <pre style="background:#0d1117;color:#f8f8f2;border-radius:8px;padding:1rem;max-height:400px;overflow:auto;margin:0;"><code id="api-result">API 호출 결과가 여기에 표시됩니다.</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JWT 구조 -->
<div id="tab-jwt" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-puzzle me-2"></i>JWT 구조 (Header.Payload.Signature)</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                JWT는 <strong>마침표(.)로 구분된 세 부분</strong>으로 구성되며, 각 부분은 Base64URL로 인코딩됩니다.
            </div>

            <div class="mb-4">
                <h6><span class="badge bg-danger">Header</span> 헤더 — 알고리즘과 타입</h6>
                <pre style="background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:.8rem;margin:0;"><code class="language-json">{
  "alg": "HS256",
  "typ": "JWT"
}</code></pre>
                <p class="small text-muted mt-1 mb-0">Base64URL → <code>eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9</code></p>
            </div>

            <div class="mb-4">
                <h6><span class="badge bg-success">Payload</span> 페이로드 — 클레임</h6>
                <pre style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;padding:.8rem;margin:0;"><code class="language-json">{
  "sub":      1,                  // Subject — 사용자 ID
  "username": "alice",
  "email":    "alice@example.com",
  "iat":      1779562800,         // Issued At — 발급 시각
  "exp":      1779566400          // Expiration — 만료 시각
}</code></pre>
                <p class="small text-muted mt-1 mb-0">
                    <strong>표준 클레임</strong>: <code>iss</code>(발급자), <code>sub</code>(주체), <code>aud</code>(대상),
                    <code>exp</code>(만료), <code>iat</code>(발급), <code>jti</code>(JWT ID).
                </p>
            </div>

            <div class="mb-4">
                <h6><span class="badge bg-primary">Signature</span> 시그니처 — 위변조 방지</h6>
                <pre style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;padding:.8rem;margin:0;"><code class="language-php">HMACSHA256(
  base64UrlEncode(header) + "." + base64UrlEncode(payload),
  SECRET_KEY
)</code></pre>
                <p class="small text-muted mt-1 mb-0">
                    서버만 아는 SECRET_KEY로 서명하여 위변조 방지. 클라이언트는 페이로드를 읽을 수는 있지만 <strong>수정할 수 없음</strong>.
                </p>
            </div>

            <div class="result-box warning">
                <strong><i class="bi bi-exclamation-triangle me-2"></i>JWT 보안 주의사항</strong>
                <ul class="mb-0 mt-2">
                    <li>Payload는 <strong>암호화되지 않음</strong>. 비밀번호 등 민감 정보 절대 넣지 말 것.</li>
                    <li>SECRET_KEY는 충분히 긴 랜덤값을 사용 (32+ 바이트 권장).</li>
                    <li><code>exp</code> 만료 시간은 짧게 (15분~1시간). 갱신은 Refresh Token으로.</li>
                    <li><code>alg: none</code> 공격 방지 — 헤더의 alg를 신뢰하지 말고 서버에서 강제.</li>
                    <li>토큰 무효화가 어려움 — 로그아웃은 클라이언트 측 삭제, 서버는 blacklist 또는 짧은 TTL.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>JWT 생성 (라이브러리 없이)</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">private function generateJwt(array $payload): string
{
    $header  = $this-&gt;b64UrlEncode(json_encode(['alg' =&gt; 'HS256', 'typ' =&gt; 'JWT']));
    $payload = $this-&gt;b64UrlEncode(json_encode(array_merge($payload, [
        'iat' =&gt; time(),
        'exp' =&gt; time() + 3600,  // 1시간 후 만료
    ])));
    $sig = $this-&gt;b64UrlEncode(
        hash_hmac('sha256', "{$header}.{$payload}", $this-&gt;jwtSecret, true)
    );
    return "{$header}.{$payload}.{$sig}";
}

private function b64UrlEncode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>JWT 검증</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">private function verifyJwt(string $token): ?array
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;
    [$header, $payload, $sig] = $parts;

    $expected = $this-&gt;b64UrlEncode(
        hash_hmac('sha256', "{$header}.{$payload}", $this-&gt;jwtSecret, true)
    );
    // Timing-safe 비교!
    if (! hash_equals($expected, $sig)) return null;

    $data = json_decode($this-&gt;b64UrlDecode($payload), true);
    if ($data['exp'] &lt; time()) return null;  // 만료 체크
    return $data;
}

private function getAuthenticatedUser(): ?array
{
    $auth = $this-&gt;request-&gt;getHeaderLine('Authorization');
    if (! str_starts_with($auth, 'Bearer ')) return null;
    return $this-&gt;verifyJwt(substr($auth, 7));
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>토큰 발급 엔드포인트</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">public function token()
{
    $user = $this-&gt;users-&gt;findByEmail($email);
    if (! $user || ! password_verify($password, $user-&gt;password)) {
        return $this-&gt;response-&gt;setStatusCode(401)-&gt;setJSON([
            'success' =&gt; false,
            'message' =&gt; '이메일 또는 비밀번호가 올바르지 않습니다.',
        ]);
    }

    $token = $this-&gt;generateJwt([
        'sub'      =&gt; $user-&gt;id,
        'username' =&gt; $user-&gt;username,
        'email'    =&gt; $user-&gt;email,
    ]);

    return $this-&gt;response-&gt;setJSON([
        'token_type' =&gt; 'Bearer',
        'token'      =&gt; $token,
        'expires_in' =&gt; 3600,
    ]);
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>클라이언트에서 토큰 사용</h5></div>
        <div class="example-card-body">
            <pre><code class="language-javascript">// fetch API
const res = await fetch('/examples/apiv2/users', {
    headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type':  'application/json',
    },
});
const data = await res.json();

// 또는 axios
axios.get('/examples/apiv2/users', {
    headers: { Authorization: `Bearer ${token}` }
});

// 또는 curl
// curl -H "Authorization: Bearer eyJhbGciOi..." /examples/apiv2/users</code></pre>
            <div class="result-box info mt-3">
                <i class="bi bi-lightbulb me-2"></i>
                실무에서는 <strong>firebase/php-jwt</strong> 같은 검증된 라이브러리를 사용하는 것을 권장합니다.<br>
                <code>composer require firebase/php-jwt</code>
            </div>
        </div>
    </div>
</div>

<!-- 비교표 -->
<div id="tab-compare" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-arrow-left-right me-2"></i>API v1 (API Key) vs API v2 (JWT)</h5></div>
        <div class="example-card-body">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th></th><th>v1 — API Key</th><th>v2 — JWT</th></tr>
                </thead>
                <tbody>
                    <tr><th>토큰 형태</th><td>임의 문자열 (UUID/random)</td><td>3-Part Base64URL (Header.Payload.Sig)</td></tr>
                    <tr><th>상태</th><td><span class="badge bg-warning text-dark">Stateful</span> (DB 조회 필수)</td><td><span class="badge bg-success">Stateless</span> (서명으로만 검증)</td></tr>
                    <tr><th>유효기간</th><td>발급 시 영구 / 수동 만료</td><td><code>exp</code> 클레임으로 자동 만료</td></tr>
                    <tr><th>사용자 정보</th><td>DB 조인 필요</td><td>Payload에 포함 (즉시 사용)</td></tr>
                    <tr><th>무효화</th><td>DB에서 즉시 삭제 가능</td><td>Blacklist 또는 만료 대기</td></tr>
                    <tr><th>적합 상황</th><td>서드파티 API 키, 머신간 통신</td><td>SPA/모바일, 마이크로서비스</td></tr>
                    <tr><th>구현 복잡도</th><td>낮음</td><td>중간 (서명/만료 처리 필요)</td></tr>
                    <tr><th>확장성</th><td>중앙 DB 의존</td><td>분산 시스템에서 유리</td></tr>
                </tbody>
            </table>
            <div class="result-box info mt-3">
                <strong><i class="bi bi-lightbulb me-2"></i>선택 가이드</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>외부 개발자에게 키를 발급</strong> → API Key</li>
                    <li><strong>사용자 로그인 후 SPA에서 API 호출</strong> → JWT</li>
                    <li><strong>마이크로서비스간 인증</strong> → JWT (Stateless 장점)</li>
                    <li><strong>최고 보안 필요</strong> → OAuth 2.0 + JWT (Bearer Token)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('#v2Tab .nav-link').forEach(el => {
    el.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('#v2Tab .nav-link').forEach(n => n.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.tab-content-pane').forEach(p => p.style.display = 'none');
        document.getElementById('tab-' + el.dataset.tab).style.display = 'block';
    });
});

document.getElementById('token-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const res  = await fetch('<?= base_url('examples/apiv2/auth/token') ?>', { method: 'POST', body: formData });
    const data = await res.json();
    const area = document.getElementById('token-result');
    area.style.display = 'block';

    if (! data.success) {
        area.innerHTML = '<div class="result-box danger"><i class="bi bi-x-circle me-2"></i>' + data.message + '</div>';
        return;
    }
    document.getElementById('jwt-input').value = data.token;
    area.innerHTML = `<div class="result-box">
        <i class="bi bi-check-circle me-2"></i>토큰 발급 성공!
        <p class="mb-1 mt-2"><small>type: <code>${data.token_type}</code> · expires_in: <code>${data.expires_in}s</code></small></p>
        <p class="mb-0"><small>user: <code>${data.user.username}</code> (${data.user.email})</small></p>
    </div>`;
});

async function callApi(method, url) {
    const token = document.getElementById('jwt-input').value.trim();
    try {
        const res = await fetch(url, {
            method,
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const data = await res.json().catch(() => ({ raw: 'JSON 파싱 실패' }));
        document.getElementById('api-status').textContent = res.status + ' ' + res.statusText;
        document.getElementById('api-result').textContent = JSON.stringify(data, null, 2);
    } catch (e) {
        document.getElementById('api-result').textContent = '오류: ' + e.message;
    }
}
window.callApi = callApi;

async function callApiNoAuth() {
    try {
        const res = await fetch('<?= base_url('examples/apiv2/users') ?>');
        const data = await res.json();
        document.getElementById('api-status').textContent = res.status + ' ' + res.statusText;
        document.getElementById('api-result').textContent = JSON.stringify(data, null, 2);
    } catch (e) {
        document.getElementById('api-result').textContent = '오류: ' + e.message;
    }
}
window.callApiNoAuth = callApiNoAuth;

async function createUserApi() {
    const token = document.getElementById('jwt-input').value.trim();
    const username = prompt('사용자명?', 'newuser_' + Math.floor(Math.random() * 1000));
    if (! username) return;
    const email = prompt('이메일?', username + '@example.com');
    if (! email) return;
    const password = prompt('비밀번호?(6자 이상)', 'secret123');
    if (! password) return;

    const body = new URLSearchParams();
    body.append('username', username);
    body.append('email',    email);
    body.append('password', password);

    const res = await fetch('<?= base_url('examples/apiv2/users') ?>', {
        method:  'POST',
        headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/x-www-form-urlencoded' },
        body,
    });
    const data = await res.json().catch(() => ({}));
    document.getElementById('api-status').textContent = res.status + ' ' + res.statusText;
    document.getElementById('api-result').textContent = JSON.stringify(data, null, 2);
}
window.createUserApi = createUserApi;
</script>
<?= $this->endSection() ?>
