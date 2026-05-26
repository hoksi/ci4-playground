<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">유효성 검사</li>
    </ol></nav>
    <h1><i class="bi bi-shield-check me-2"></i>유효성 검사</h1>
    <p>CI4의 내장 유효성 검사 규칙, 커스텀 에러 메시지, 폼 재입력을 알아봅니다.</p>
</div>

<?php $tab = $tab ?? 'basic'; ?>
<?php if (isset($success)): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i><?= $success ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<ul class="nav nav-tabs mb-3" id="validationTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'basic' ? 'active' : '' ?>" href="#" onclick="showTab('basic');return false;">기본 유효성 검사</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'custom' ? 'active' : '' ?>" href="#" onclick="showTab('custom');return false;">커스텀 에러 메시지</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- 기본 유효성 검사 -->
<div id="tab-basic" class="tab-content-pane" style="display:<?= $tab === 'basic' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-ui-checks me-2"></i>기본 유효성 검사</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                <strong>적용 규칙:</strong> required, min_length, max_length, valid_email, integer, 범위 검사
            </div>
            <form method="post" action="<?= base_url('examples/validation/basic') ?>" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">이름 <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           value="<?= esc($old['name'] ?? '') ?>" placeholder="2~20자">
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">이메일 <span class="text-danger">*</span></label>
                    <input type="text" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                           value="<?= esc($old['email'] ?? '') ?>" placeholder="example@email.com">
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">나이 <span class="text-danger">*</span></label>
                    <input type="text" name="age" class="form-control <?= isset($errors['age']) ? 'is-invalid' : '' ?>"
                           value="<?= esc($old['age'] ?? '') ?>" placeholder="1~150">
                    <?php if (isset($errors['age'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['age']) ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-check-lg"></i> 검사 실행
                </button>
            </form>
        </div>
    </div>
</div>

<!-- 커스텀 에러 메시지 -->
<div id="tab-custom" class="tab-content-pane" style="display:<?= $tab === 'custom' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-person-plus me-2"></i>커스텀 에러 메시지 (회원가입 폼)</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                규칙별 한국어 에러 메시지, <code>matches</code> 규칙, <code>regex_match</code> 규칙 예제
            </div>
            <form method="post" action="<?= base_url('examples/validation/custom') ?>" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">사용자명 <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                           value="<?= esc($old['username'] ?? '') ?>" placeholder="영문+숫자 4~16자">
                    <?php if (isset($errors['username'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['username']) ?></div>
                    <?php else: ?>
                        <div class="form-text">영문자와 숫자만 사용 가능, 4~16자</div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">비밀번호 <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                           placeholder="8자 이상, 대문자+숫자 포함">
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                    <?php else: ?>
                        <div class="form-text">최소 8자, 대문자와 숫자 각 1개 이상 포함</div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">비밀번호 확인 <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirm" class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>">
                    <?php if (isset($errors['password_confirm'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['password_confirm']) ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-person-check"></i> 가입 확인
                </button>
            </form>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>기본 유효성 검사</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">$rules = [
    'name'  => 'required|min_length[2]|max_length[20]',
    'email' => 'required|valid_email',
    'age'   => 'required|integer|greater_than[0]|less_than[151]',
];

if (! $this->validate($rules)) {
    // $this->validator->getErrors() — 필드별 에러 배열
    return view('form', ['errors' => $this->validator->getErrors()]);
}
// 통과: $this->request->getPost('name') 으로 값 사용</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>커스텀 에러 메시지</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">$rules = [
    'username' => [
        'label' => '사용자명',
        'rules' => 'required|alpha_numeric|min_length[4]',
        'errors' => [
            'required'      => '{field}은(는) 필수입니다.',
            'alpha_numeric' => '{field}은(는) 영문자/숫자만 허용됩니다.',
            'min_length'    => '{field}은(는) 최소 {param}자 이상이어야 합니다.',
        ],
    ],
    'password_confirm' => [
        'label' => '비밀번호 확인',
        'rules' => 'required|matches[password]',  // password 필드와 일치해야 함
    ],
];</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>자주 쓰는 규칙 모음</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 문자열
'required'              // 필수
'min_length[n]'         // 최소 n자
'max_length[n]'         // 최대 n자
'exact_length[n]'       // 정확히 n자
'alpha'                 // 영문자만
'alpha_numeric'         // 영문자+숫자
'alpha_numeric_space'   // 영문자+숫자+공백

// 숫자
'integer'               // 정수
'decimal'               // 소수
'greater_than[n]'       // n 초과
'less_than[n]'          // n 미만
'greater_than_equal_to[n]'
'less_than_equal_to[n]'

// 형식
'valid_email'           // 이메일
'valid_url'             // URL
'valid_ip'              // IP 주소
'regex_match[/pattern/]'// 정규식

// 기타
'in_list[a,b,c]'        // 목록 중 하나
'matches[field]'        // 다른 필드와 일치
'differs[field]'        // 다른 필드와 달라야 함
'uploaded[file]'        // 파일 업로드됨
'max_size[file,2048]'   // 파일 최대 2MB</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#validationTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
<?= $this->endSection() ?>
