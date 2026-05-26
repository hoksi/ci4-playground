<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">회원 인증</li>
    </ol></nav>
    <h1><i class="bi bi-person-lock me-2"></i>회원 인증 시스템</h1>
    <p>회원가입 / 로그인 / 로그아웃 / 보호된 페이지 / 비밀번호 변경 전체 흐름.</p>
</div>

<ul class="nav nav-tabs mb-3" id="authTab">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="demo">시연</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="code">코드 설명</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="flow">인증 흐름도</a></li>
</ul>

<!-- 시연 -->
<div id="tab-demo" class="tab-content-pane">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-info-circle me-2"></i>현재 상태</h5></div>
        <div class="example-card-body">
            <?php if ($authUser): ?>
                <div class="result-box mb-3">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong><?= esc($authUser['username']) ?></strong> 님으로 로그인된 상태입니다 (<?= esc($authUser['email']) ?>).
                </div>
                <a href="<?= base_url('examples/auth/dashboard') ?>" class="demo-btn me-2">
                    <i class="bi bi-speedometer2"></i> 대시보드 이동
                </a>
                <a href="<?= base_url('examples/auth/logout') ?>" class="demo-btn outline">
                    <i class="bi bi-box-arrow-right"></i> 로그아웃
                </a>
            <?php else: ?>
                <div class="result-box info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    현재 비로그인 상태입니다. 회원가입 또는 로그인을 진행하세요.<br>
                    <small>총 가입자 수: <strong><?= $totalUsers ?></strong>명</small>
                </div>
                <a href="<?= base_url('examples/auth/register') ?>" class="demo-btn me-2">
                    <i class="bi bi-person-plus"></i> 회원가입
                </a>
                <a href="<?= base_url('examples/auth/login') ?>" class="demo-btn outline">
                    <i class="bi bi-box-arrow-in-right"></i> 로그인
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="example-card mt-3">
        <div class="example-card-header"><h5><i class="bi bi-list-check me-2"></i>주요 라우트</h5></div>
        <div class="example-card-body">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-dark"><tr><th>메서드</th><th>경로</th><th>설명</th><th>인증</th></tr></thead>
                <tbody>
                    <tr><td><span class="badge bg-success">GET</span></td><td><code>/examples/auth/register</code></td><td>회원가입 폼</td><td>-</td></tr>
                    <tr><td><span class="badge bg-warning text-dark">POST</span></td><td><code>/examples/auth/register</code></td><td>가입 처리 + 자동 로그인</td><td>-</td></tr>
                    <tr><td><span class="badge bg-success">GET</span></td><td><code>/examples/auth/login</code></td><td>로그인 폼</td><td>-</td></tr>
                    <tr><td><span class="badge bg-warning text-dark">POST</span></td><td><code>/examples/auth/login</code></td><td>로그인 처리</td><td>-</td></tr>
                    <tr><td><span class="badge bg-success">GET</span></td><td><code>/examples/auth/logout</code></td><td>세션 제거</td><td>-</td></tr>
                    <tr><td><span class="badge bg-success">GET</span></td><td><code>/examples/auth/dashboard</code></td><td>보호된 대시보드</td><td><span class="badge bg-danger">필요</span></td></tr>
                    <tr><td><span class="badge bg-warning text-dark">POST</span></td><td><code>/examples/auth/profile</code></td><td>비밀번호 변경</td><td><span class="badge bg-danger">필요</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>auth_users 테이블 (마이그레이션)</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">$this-&gt;forge-&gt;addField([
    'id'             =&gt; ['type' =&gt; 'INTEGER', 'auto_increment' =&gt; true],
    'username'       =&gt; ['type' =&gt; 'VARCHAR', 'constraint' =&gt; 50],
    'email'          =&gt; ['type' =&gt; 'VARCHAR', 'constraint' =&gt; 255],
    'password'       =&gt; ['type' =&gt; 'VARCHAR', 'constraint' =&gt; 255],
    'remember_token' =&gt; ['type' =&gt; 'VARCHAR', 'constraint' =&gt; 100, 'null' =&gt; true],
    'created_at'     =&gt; ['type' =&gt; 'DATETIME', 'null' =&gt; true],
    'updated_at'     =&gt; ['type' =&gt; 'DATETIME', 'null' =&gt; true],
]);
$this-&gt;forge-&gt;addPrimaryKey('id');
$this-&gt;forge-&gt;addUniqueKey('email');
$this-&gt;forge-&gt;createTable('auth_users', true);</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>회원가입 처리</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">$rules = [
    'username'         =&gt; 'required|min_length[2]|max_length[50]',
    'email'            =&gt; 'required|valid_email|is_unique[auth_users.email]',
    'password'         =&gt; 'required|min_length[6]',
    'password_confirm' =&gt; 'required|matches[password]',
];

if (! $this-&gt;validate($rules)) {
    return redirect()-&gt;back()-&gt;withInput()-&gt;with('errors', $this-&gt;validator-&gt;getErrors());
}

$id = $this-&gt;users-&gt;insert([
    'username' =&gt; $this-&gt;request-&gt;getPost('username'),
    'email'    =&gt; strtolower($this-&gt;request-&gt;getPost('email')),
    'password' =&gt; password_hash($this-&gt;request-&gt;getPost('password'), PASSWORD_BCRYPT, ['cost' =&gt; 12]),
], true);</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>로그인 처리</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">$user = $this-&gt;users-&gt;findByEmail($email);
if (! $user || ! password_verify($password, $user-&gt;password)) {
    return redirect()-&gt;back()-&gt;withInput()
        -&gt;with('error', '이메일 또는 비밀번호가 올바르지 않습니다.');
}

$this-&gt;sess-&gt;set('auth_user', [
    'id'       =&gt; $user-&gt;id,
    'username' =&gt; $user-&gt;username,
    'email'    =&gt; $user-&gt;email,
]);

return redirect()-&gt;to(base_url('examples/auth/dashboard'));</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>보호된 페이지 (세션 체크)</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">public function dashboard()
{
    $auth = $this-&gt;sess-&gt;get('auth_user');
    if (! $auth) {
        return redirect()-&gt;to(base_url('examples/auth/login'))
            -&gt;with('error', '로그인이 필요합니다.');
    }
    // 보호된 콘텐츠
    return view('examples/auth/dashboard', ['auth' =&gt; $auth]);
}</code></pre>
            <div class="result-box info mt-3">
                <i class="bi bi-lightbulb me-2"></i>
                실무에서는 매번 컨트롤러에서 체크하지 않고 <strong>AuthFilter</strong>를 만들어 라우트 그룹에 적용합니다.<br>
                <code>$routes->group('mypage', ['filter' => 'auth'], ...)</code>
            </div>
        </div>
    </div>
</div>

<!-- 흐름도 -->
<div id="tab-flow" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-diagram-2 me-2"></i>회원 인증 흐름</h5></div>
        <div class="example-card-body">
            <pre style="background:#f8f9fa;border:1px solid #e9ecef;border-radius:8px;padding:1rem;"><code>┌──────────────────────┐
│   회원가입 폼        │
│   /auth/register     │
└──────────┬───────────┘
           ↓ POST username + email + password
┌──────────────────────┐
│  유효성 검사         │ ←─ valid_email, is_unique, min_length, matches
└──────────┬───────────┘
           ↓ 통과
┌──────────────────────┐
│  password_hash()     │ ←─ bcrypt cost 12
│  DB INSERT           │
└──────────┬───────────┘
           ↓ id 반환
┌──────────────────────┐
│  세션에 auth_user 저장 │
│  → 대시보드 리다이렉트  │
└──────────────────────┘

──────────────────────────────────────────

┌──────────────────────┐
│   로그인 폼          │
│   /auth/login        │
└──────────┬───────────┘
           ↓ POST email + password
┌──────────────────────┐
│  findByEmail()       │
└──────────┬───────────┘
           ↓ 사용자 있으면
┌──────────────────────┐
│  password_verify()   │ ←─ 평문 vs 해시 비교
└──────────┬───────────┘
           ↓ 매치 OK
┌──────────────────────┐
│  세션 설정 + 리다이렉트│
└──────────────────────┘

──────────────────────────────────────────

┌──────────────────────┐
│  보호된 페이지 요청   │
│  /auth/dashboard     │
└──────────┬───────────┘
           ↓
┌──────────────────────┐
│ $sess-&gt;get('auth_user')│
└──────────┬───────────┘
       있음 ↙ ↘ 없음
   ┌────────┐  ┌───────────────┐
   │ 뷰 렌더 │  │ /auth/login   │
   └────────┘  │ 리다이렉트     │
                └───────────────┘</code></pre>
            <div class="result-box warning mt-3">
                <strong><i class="bi bi-shield-exclamation me-2"></i>보안 체크리스트</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>password_hash</strong>로만 저장 (MD5/SHA1 금지).</li>
                    <li><strong>password_verify</strong>로 비교 (== 비교 금지, Timing attack).</li>
                    <li>이메일 정규화 (소문자 + trim)로 중복 가입 방지.</li>
                    <li>로그인 실패 메시지는 "이메일 또는 비밀번호" 공통으로 — 사용자 존재 여부 노출 금지.</li>
                    <li>로그인 후 세션 ID 재생성 (<code>$session-&gt;regenerate()</code>) 권장.</li>
                    <li>Rate Limiting (Throttler) 적용으로 brute-force 방어.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('#authTab .nav-link').forEach(el => {
    el.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('#authTab .nav-link').forEach(n => n.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.tab-content-pane').forEach(p => p.style.display = 'none');
        document.getElementById('tab-' + el.dataset.tab).style.display = 'block';
    });
});
</script>
<?= $this->endSection() ?>
