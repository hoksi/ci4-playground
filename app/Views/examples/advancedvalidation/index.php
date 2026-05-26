<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-shield-check me-2"></i>유효성 검사 고급</h1>
    <p>커스텀 규칙 클래스, 규칙 그룹, 조건부 규칙(permit_empty, if_exist)을 활용한 고급 유효성 검사를 학습합니다.</p>
</div>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active">유효성 검사 고급</li>
    </ol>
</nav>

<ul class="nav nav-tabs mb-4" id="mainTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-custom">커스텀 규칙</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-conditional">조건부 규칙</a></li>
</ul>

<div class="tab-content">

    <!-- ── 라이브 데모 ──────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-demo">

        <ul class="nav nav-pills mb-3" id="demoTabs">
            <li class="nav-item"><a class="nav-pill nav-link active" data-bs-toggle="pill" href="#demo-basic">기본 + 커스텀 규칙</a></li>
            <li class="nav-item"><a class="nav-pill nav-link" data-bs-toggle="pill" href="#demo-group">규칙 그룹</a></li>
            <li class="nav-item"><a class="nav-pill nav-link" data-bs-toggle="pill" href="#demo-conditional">조건부 규칙</a></li>
        </ul>

        <div class="tab-content">

            <!-- 기본 + 커스텀 -->
            <div class="tab-pane fade show active" id="demo-basic">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-person-check text-primary"></i>
                        <h5>기본 규칙 + 커스텀 규칙 (korean_phone, not_reserved)</h5>
                    </div>
                    <div class="example-card-body">
                        <div class="row g-4">
                            <div class="col-md-5">
                                <form id="formBasic">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">사용자명 <span class="text-muted small">(금지어: admin, root, system)</span></label>
                                        <input type="text" name="username" class="form-control" placeholder="예: honggildong">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">전화번호 <span class="text-muted small">(010-XXXX-XXXX)</span></label>
                                        <input type="text" name="phone" class="form-control" placeholder="010-1234-5678">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">이메일</label>
                                        <input type="text" name="email" class="form-control" placeholder="user@example.com">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">검증하기</button>
                                </form>
                            </div>
                            <div class="col-md-7">
                                <div id="resultBasic" class="d-none">
                                    <div id="resultBasicBadge" class="mb-2"></div>
                                    <pre id="resultBasicJson" style="background:#1e1e1e;color:#d4d4d4;padding:1rem;border-radius:8px;font-size:.84rem;"></pre>
                                </div>
                                <div class="result-box info small">
                                    <strong>테스트 케이스:</strong>
                                    <ul class="mb-0 mt-1">
                                        <li>사용자명에 <code>admin</code> 입력 → not_reserved 오류</li>
                                        <li>전화번호에 <code>02-123-4567</code> 입력 → korean_phone 오류</li>
                                        <li>올바른 값: <code>honggil</code> / <code>010-1234-5678</code> / 이메일</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 규칙 그룹 -->
            <div class="tab-pane fade" id="demo-group">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-collection text-success"></i>
                        <h5>규칙 그룹 — 상품 등록 폼</h5>
                    </div>
                    <div class="example-card-body">
                        <div class="row g-4">
                            <div class="col-md-5">
                                <form id="formGroup">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">상품명</label>
                                        <input type="text" name="product_name" class="form-control" placeholder="예: 무선 이어폰">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">가격 (원)</label>
                                        <input type="text" name="price" class="form-control" placeholder="예: 29000">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">수량</label>
                                        <input type="text" name="quantity" class="form-control" placeholder="예: 10">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">카테고리</label>
                                        <select name="category" class="form-select">
                                            <option value="">선택하세요</option>
                                            <option value="electronics">전자제품</option>
                                            <option value="clothing">의류</option>
                                            <option value="food">식품</option>
                                            <option value="books">도서</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">검증하기</button>
                                </form>
                            </div>
                            <div class="col-md-7">
                                <div id="resultGroup" class="d-none">
                                    <div id="resultGroupBadge" class="mb-2"></div>
                                    <pre id="resultGroupJson" style="background:#1e1e1e;color:#d4d4d4;padding:1rem;border-radius:8px;font-size:.84rem;"></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 조건부 규칙 -->
            <div class="tab-pane fade" id="demo-conditional">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-toggle-on text-warning"></i>
                        <h5>조건부 규칙 — permit_empty / if_exist</h5>
                    </div>
                    <div class="example-card-body">
                        <div class="row g-4">
                            <div class="col-md-5">
                                <form id="formCond">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">이름 <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" placeholder="필수 입력">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">닉네임 <span class="text-muted small">(선택, 2~20자)</span></label>
                                        <input type="text" name="nickname" class="form-control" placeholder="비워도 됩니다">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">나이 <span class="text-muted small">(선택)</span></label>
                                        <input type="text" name="age" class="form-control" placeholder="비워도 됩니다">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">웹사이트 <span class="text-muted small">(선택, https://)</span></label>
                                        <input type="text" name="website" class="form-control" placeholder="https://example.com">
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100">검증하기</button>
                                </form>
                            </div>
                            <div class="col-md-7">
                                <div id="resultCond" class="d-none">
                                    <div id="resultCondBadge" class="mb-2"></div>
                                    <pre id="resultCondJson" style="background:#1e1e1e;color:#d4d4d4;padding:1rem;border-radius:8px;font-size:.84rem;"></pre>
                                </div>
                                <div class="result-box info small">
                                    <strong>테스트 케이스:</strong>
                                    <ul class="mb-0 mt-1">
                                        <li>닉네임/나이/웹사이트 비우고 제출 → 통과 (permit_empty)</li>
                                        <li>닉네임에 <code>a</code> 1자 입력 → 최소 2자 오류</li>
                                        <li>웹사이트에 <code>example.com</code> → URL 형식 오류</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ── 커스텀 규칙 코드 ────────────────────────────── -->
    <div class="tab-pane fade" id="tab-custom">

        <div class="example-card mb-4">
            <div class="example-card-header">
                <i class="bi bi-code-slash text-primary"></i>
                <h5>커스텀 규칙 클래스 작성</h5>
            </div>
            <div class="example-card-body">
                <div class="code-label">app/Validation/PlaygroundRules.php</div>
                <pre><code class="language-php">namespace App\Validation;

class PlaygroundRules
{
    // 한국 전화번호 형식 검사 (010-XXXX-XXXX)
    public function korean_phone(string $value, string &$error = null): bool
    {
        if (preg_match('/^01[016789]-\d{3,4}-\d{4}$/', $value)) {
            return true;
        }
        $error = '올바른 한국 전화번호 형식이 아닙니다. (예: 010-1234-5678)';
        return false;
    }

    // 사용자명 금지어 체크
    // 사용법: not_reserved[admin,root,system]
    public function not_reserved(
        string $value,
        string $params,
        array $data,
        string &$error = null
    ): bool {
        $reserved = array_map('trim', explode(',', $params));
        if (in_array(strtolower($value), array_map('strtolower', $reserved))) {
            $error = '"' . $value . '"은(는) 사용할 수 없는 예약어입니다.';
            return false;
        }
        return true;
    }
}</code></pre>
            </div>
        </div>

        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-gear text-success"></i>
                <h5>Config/Validation.php — 규칙 클래스 등록</h5>
            </div>
            <div class="example-card-body">
                <pre><code class="language-php">// app/Config/Validation.php
public array $ruleSets = [
    Rules::class,
    FormatRules::class,
    FileRules::class,
    CreditCardRules::class,
    \App\Validation\PlaygroundRules::class,  // 커스텀 규칙 추가
];</code></pre>
                <div class="result-box info mt-3 small">
                    등록 후 컨트롤러에서 <code>'rules' => 'required|korean_phone'</code>처럼 바로 사용할 수 있습니다.
                    파라미터가 있는 규칙은 <code>not_reserved[admin,root,system]</code> 형식으로 전달합니다.
                </div>
            </div>
        </div>

    </div>

    <!-- ── 조건부 규칙 코드 ────────────────────────────── -->
    <div class="tab-pane fade" id="tab-conditional">

        <div class="row g-4">
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-toggle-off text-warning"></i>
                        <h5>permit_empty</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">// 빈 값을 허용. 값이 있으면 나머지 규칙 적용
$rules = [
    'nickname' => 'permit_empty|min_length[2]|max_length[20]',
    'website'  => 'permit_empty|valid_url_strict',
];

// 빈 문자열('')로 제출 → 통과
// 값이 있으면 min_length, valid_url 검사 실행</code></pre>
                        <div class="result-box info mt-3 small">
                            <strong>permit_empty vs required:</strong><br>
                            <code>required</code>는 빈 값 거부.<br>
                            <code>permit_empty</code>는 빈 값 허용, 값 있을 때만 검사.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-question-circle text-danger"></i>
                        <h5>if_exist</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">// 필드 자체가 POST 데이터에 없으면 검사 생략
// PUT/PATCH API에서 부분 업데이트 시 유용
$rules = [
    'age' => 'if_exist|integer|greater_than[0]',
];

// age 필드가 전송 안 됨 → 규칙 전체 생략
// age='' 로 전송 됨   → 정수 검사 실행
// age=25 로 전송 됨   → 통과</code></pre>
                        <div class="result-box info mt-3 small">
                            <strong>API 부분 업데이트:</strong><br>
                            PUT 요청에서 변경할 필드만 보낼 때 <code>if_exist</code>를 쓰면 없는 필드를 오류 처리하지 않습니다.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="example-card mt-4">
            <div class="example-card-header">
                <i class="bi bi-file-earmark-code text-primary"></i>
                <h5>컨트롤러에서 규칙 배열 + 커스텀 에러 메시지</h5>
            </div>
            <div class="example-card-body">
                <pre><code class="language-php">$rules = [
    'username' => [
        'label' => '사용자명',
        'rules' => 'required|min_length[3]|alpha_numeric|not_reserved[admin,root]',
        'errors' => [
            'required'      => '{field}은(는) 필수 입력 항목입니다.',
            'min_length'    => '{field}은(는) 최소 {param}자 이상이어야 합니다.',
            'alpha_numeric' => '{field}은(는) 영문자와 숫자만 허용됩니다.',
        ],
    ],
];

if ($this->validate($rules)) {
    // 통과
} else {
    $errors = $this->validator->getErrors();
    // ['username' => '오류 메시지', ...]
}</code></pre>
            </div>
        </div>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function handleFormSubmit(formId, url, resultId, badgeId, jsonId) {
    document.getElementById(formId).addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const res  = await fetch(url, {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: formData
        });
        const data = await res.json();
        document.getElementById(resultId).classList.remove('d-none');
        document.getElementById(badgeId).innerHTML = data.passed
            ? '<span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>검증 통과</span>'
            : '<span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i>검증 실패</span>';
        document.getElementById(jsonId).textContent = JSON.stringify(data, null, 2);
    });
}

handleFormSubmit('formBasic', '<?= base_url('examples/advancedvalidation/basic') ?>',
    'resultBasic', 'resultBasicBadge', 'resultBasicJson');
handleFormSubmit('formGroup', '<?= base_url('examples/advancedvalidation/group') ?>',
    'resultGroup', 'resultGroupBadge', 'resultGroupJson');
handleFormSubmit('formCond', '<?= base_url('examples/advancedvalidation/conditional') ?>',
    'resultCond', 'resultCondBadge', 'resultCondJson');
</script>
<?= $this->endSection() ?>
