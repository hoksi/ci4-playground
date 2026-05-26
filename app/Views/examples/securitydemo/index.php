<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-shield-lock me-2"></i>Security 클래스</h1>
    <p>CI4의 esc(), sanitizeFilename(), CSRF 보호 등 내장 보안 기능으로 XSS 및 파일명 인젝션을 방어합니다.</p>
</div>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active">Security 클래스</li>
    </ol>
</nav>

<ul class="nav nav-tabs mb-4" id="mainTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-esc">esc() / sanitizeFilename</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-csrf">CSRF 설정</a></li>
</ul>

<div class="tab-content">

    <!-- ── 라이브 데모 ──────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-demo">

        <!-- 입력 새니타이즈 -->
        <div class="example-card mb-4">
            <div class="example-card-header">
                <i class="bi bi-input-cursor-text text-primary"></i>
                <h5>입력 새니타이즈 비교 — esc() vs strip_tags() vs htmlspecialchars() vs sanitizeFilename()</h5>
            </div>
            <div class="example-card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">입력 문자열</label>
                        <textarea id="sanitizeInput" class="form-control font-monospace" rows="4"
                                  placeholder="예: &lt;script&gt;alert('xss')&lt;/script&gt; 또는 ../../../etc/passwd"></textarea>
                        <button id="btnSanitize" class="btn btn-primary mt-2 w-100">
                            <i class="bi bi-funnel me-1"></i> 새니타이즈 실행
                        </button>
                        <div class="mt-2 d-flex flex-wrap gap-1">
                            <button class="btn btn-sm btn-outline-secondary" onclick="setInput('&lt;script&gt;alert(1)&lt;/script&gt;')">XSS 스크립트</button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="setInput('../../../etc/passwd')">경로 탐색</button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="setInput('&lt;b&gt;굵게&lt;/b&gt; &amp; &quot;따옴표&quot;')">HTML 태그</button>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div id="sanitizeResult" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light"><tr><th>함수</th><th>결과</th></tr></thead>
                                    <tbody id="sanitizeTable"></tbody>
                                </table>
                            </div>
                        </div>
                        <div id="sanitizePlaceholder" class="result-box info">
                            <i class="bi bi-info-circle me-1"></i> 왼쪽에 문자열을 입력하고 버튼을 누르세요.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- XSS 패턴 비교 -->
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-bug text-danger"></i>
                <h5>XSS 패턴 → esc() 처리 결과 시각적 비교</h5>
            </div>
            <div class="example-card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">XSS 페이로드 입력</label>
                        <textarea id="xssInput" class="form-control font-monospace" rows="4"
                                  placeholder="XSS 패턴을 입력하세요"></textarea>
                        <button id="btnXss" class="btn btn-danger mt-2 w-100">
                            <i class="bi bi-shield-x me-1"></i> XSS 분석
                        </button>
                        <div class="mt-2 d-flex flex-wrap gap-1">
                            <button class="btn btn-sm btn-outline-danger" onclick="setXssInput('&lt;script&gt;document.cookie&lt;/script&gt;')">쿠키 탈취</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="setXssInput('&lt;img src=x onerror=alert(1)&gt;')">이미지 XSS</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="setXssInput('javascript:alert(document.domain)')">JS 프로토콜</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="setXssInput('&lt;a onclick=alert(1)&gt;클릭&lt;/a&gt;')">이벤트 핸들러</button>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div id="xssResult" class="d-none">
                            <div id="xssDangerBadge" class="mb-3"></div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="code-label">원본 입력 (위험!)</div>
                                    <div id="xssOrigPanel" class="p-3 rounded border"
                                         style="background:#fef2f2;border-color:#fecaca!important;font-family:monospace;font-size:.85rem;word-break:break-all;"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="code-label">esc($input, 'html') — 안전</div>
                                    <div id="xssHtmlPanel" class="p-3 rounded border"
                                         style="background:#f0fdf4;border-color:#bbf7d0!important;font-family:monospace;font-size:.85rem;word-break:break-all;"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="code-label">esc($input, 'js') — JS 컨텍스트</div>
                                    <div id="xssJsPanel" class="p-3 rounded border"
                                         style="background:#f0fdf4;border-color:#bbf7d0!important;font-family:monospace;font-size:.85rem;word-break:break-all;"></div>
                                </div>
                            </div>
                        </div>
                        <div id="xssPlaceholder" class="result-box danger">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            XSS 페이로드를 입력하면 <code>esc()</code> 처리 전/후를 비교할 수 있습니다.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ── esc() / sanitizeFilename ──────────────────── -->
    <div class="tab-pane fade" id="tab-esc">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-code-slash text-primary"></i>
                        <h5>esc() — 컨텍스트별 이스케이프</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">$input = '&lt;script&gt;alert("xss")&lt;/script&gt;';

// HTML 컨텍스트 (기본값)
esc($input);           // &lt;script&gt;...
esc($input, 'html');   // &lt;script&gt;...

// HTML 속성 컨텍스트
// &lt;input value="&lt;?= esc($val, 'attr') ?&gt;"&gt;
esc($input, 'attr');

// JavaScript 컨텍스트
// var x = '&lt;?= esc($val, 'js') ?&gt;';
esc($input, 'js');

// URL 컨텍스트
// &lt;a href="&lt;?= esc($url, 'url') ?&gt;"&gt;
esc($input, 'url');

// CSS 컨텍스트
esc($input, 'css');</code></pre>
                        <div class="result-box warning mt-3 small">
                            <strong>주의:</strong> <code>esc()</code>는 출력 시 이스케이프합니다.
                            DB 저장 전에 적용하면 이중 인코딩이 발생합니다.
                            <strong>항상 출력 직전에 사용하세요.</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-file-earmark-x text-danger"></i>
                        <h5>sanitizeFilename() — 파일명 보안</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">$security = \Config\Services::security();

// 경로 탐색 공격 방어
$filename = '../../../etc/passwd';
$safe = $security->sanitizeFilename($filename);
// 결과: 'etcpasswd'

// 특수문자 제거
$filename = 'my file &lt;script&gt;.php';
$safe = $security->sanitizeFilename($filename);
// 결과: 'my file script.php'

// 파일 업로드 시 실제 사용 예
public function upload()
{
    $file = $this->request->getFile('upload');
    $name = $security->sanitizeFilename(
        $file->getClientName()
    );
    $file->move(WRITEPATH . 'uploads', $name);
}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="example-card mt-4">
            <div class="example-card-header">
                <i class="bi bi-table text-success"></i>
                <h5>함수별 특성 비교</h5>
            </div>
            <div class="example-card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr><th>함수</th><th>용도</th><th>특징</th><th>권장 컨텍스트</th></tr>
                        </thead>
                        <tbody class="small">
                            <tr>
                                <td><code>esc($v, 'html')</code></td>
                                <td>HTML 출력</td>
                                <td><code>&lt;&gt;&amp;"'</code> 변환</td>
                                <td>HTML 본문</td>
                            </tr>
                            <tr>
                                <td><code>esc($v, 'attr')</code></td>
                                <td>HTML 속성</td>
                                <td>속성 값 내 특수문자 변환</td>
                                <td>태그 속성값</td>
                            </tr>
                            <tr>
                                <td><code>esc($v, 'js')</code></td>
                                <td>JS 인라인</td>
                                <td>JS 특수문자 이스케이프</td>
                                <td>script 태그 내</td>
                            </tr>
                            <tr>
                                <td><code>esc($v, 'url')</code></td>
                                <td>URL</td>
                                <td>URL 인코딩</td>
                                <td>href, src 속성</td>
                            </tr>
                            <tr>
                                <td><code>strip_tags($v)</code></td>
                                <td>태그 제거</td>
                                <td>HTML 태그 완전 제거</td>
                                <td>평문 저장</td>
                            </tr>
                            <tr>
                                <td><code>sanitizeFilename()</code></td>
                                <td>파일명</td>
                                <td>경로 탐색, 특수문자 제거</td>
                                <td>파일 업로드</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ── CSRF 설정 코드 ────────────────────────────── -->
    <div class="tab-pane fade" id="tab-csrf">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-shield-fill-check text-success"></i>
                        <h5>CSRF 활성화 (Config/Filters.php)</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">// app/Config/Filters.php
public array $globals = [
    'before' => [
        'csrf',   // 전역 CSRF 활성화
    ],
];

// 특정 라우트만 제외 (예: webhook)
public array $globals = [
    'before' => [
        'csrf' => ['except' => ['webhook/*']],
    ],
];</code></pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-code-slash text-primary"></i>
                        <h5>뷰에서 CSRF 토큰 삽입</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">// HTML 폼 — 히든 필드 자동 생성
&lt;form method="post"&gt;
    &lt;?= csrf_field() ?&gt;
    &lt;!-- &lt;input type="hidden" name="csrf_test_name"
              value="abc123..."&gt; --&gt;
    ...
&lt;/form&gt;

// AJAX 요청 — 헤더에 토큰 포함
fetch('/endpoint', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': '&lt;?= csrf_hash() ?&gt;',
    },
    body: formData
});</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="example-card mt-4">
            <div class="example-card-header">
                <i class="bi bi-gear text-warning"></i>
                <h5>CSRF 설정 (Config/Security.php)</h5>
            </div>
            <div class="example-card-body">
                <pre><code class="language-php">// app/Config/Security.php
class Security extends BaseConfig
{
    // 토큰 재생성 전략
    // 'cookie' : 요청마다 토큰 재생성 (기본값)
    // 'session': 세션에 토큰 저장
    public string $tokenRandomize = 'cookie';

    // CSRF 토큰 이름 (히든 필드 name)
    public string $tokenName = 'csrf_test_name';

    // CSRF 쿠키 이름
    public string $cookieName = 'csrf_cookie_name';

    // 토큰 만료 시간 (초, 0=세션과 동일)
    public int $expires = 7200;

    // SameSite 속성 (Strict/Lax/None/'')
    public string $samesite = 'Lax';
}</code></pre>
                <div class="result-box info mt-3 small">
                    <strong>현재 상태:</strong> 이 프로젝트는 학습용이라 CSRF 전역 필터가 비활성화되어 있습니다.
                    운영 환경에서는 반드시 <code>'csrf'</code> 전역 필터를 활성화하세요.
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function setInput(val) {
    document.getElementById('sanitizeInput').value = val;
}
function setXssInput(val) {
    document.getElementById('xssInput').value = val;
}

document.getElementById('btnSanitize').addEventListener('click', async () => {
    const input = document.getElementById('sanitizeInput').value;
    const formData = new FormData();
    formData.append('input', input);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const res  = await fetch('<?= base_url('examples/securitydemo/sanitize') ?>', {
        method: 'POST', body: formData
    });
    const data = await res.json();

    document.getElementById('sanitizeResult').classList.remove('d-none');
    document.getElementById('sanitizePlaceholder').classList.add('d-none');

    const labels = {
        esc_html: 'esc($input, \'html\')',
        esc_js:   'esc($input, \'js\')',
        esc_attr: 'esc($input, \'attr\')',
        strip_tags: 'strip_tags($input)',
        htmlspecialchars: 'htmlspecialchars($input)',
        sanitizeFilename: 'sanitizeFilename($input)',
    };

    let rows = '';
    for (const [key, label] of Object.entries(labels)) {
        rows += `<tr><td><code>${label}</code></td><td class="font-monospace small" style="word-break:break-all;">${escapeHtml(data[key])}</td></tr>`;
    }
    document.getElementById('sanitizeTable').innerHTML = rows;
});

document.getElementById('btnXss').addEventListener('click', async () => {
    const input = document.getElementById('xssInput').value;
    const formData = new FormData();
    formData.append('input', input);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const res  = await fetch('<?= base_url('examples/securitydemo/xss') ?>', {
        method: 'POST', body: formData
    });
    const data = await res.json();

    document.getElementById('xssResult').classList.remove('d-none');
    document.getElementById('xssPlaceholder').classList.add('d-none');

    document.getElementById('xssDangerBadge').innerHTML = data.is_dangerous
        ? '<span class="badge bg-danger fs-6"><i class="bi bi-exclamation-triangle me-1"></i>위험한 XSS 패턴 감지됨!</span>'
        : '<span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>일반 입력</span>';

    document.getElementById('xssOrigPanel').textContent = data.original;
    document.getElementById('xssHtmlPanel').textContent = data.esc_html;
    document.getElementById('xssJsPanel').textContent   = data.esc_js;
});

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
</script>
<?= $this->endSection() ?>
