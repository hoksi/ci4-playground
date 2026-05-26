<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">RESTful API</li>
    </ol></nav>
    <h1><i class="bi bi-braces me-2"></i>RESTful API</h1>
    <p>CI4로 JSON 기반의 RESTful API를 개발하는 패턴을 알아봅니다.</p>
</div>

<!-- JSON 응답 -->
<div class="example-card">
    <div class="example-card-header"><span class="badge" style="background:#0dcaf0;color:#000;">1</span><h5>JSON 응답 기본</h5></div>
    <div class="example-card-body">
        <pre><code class="language-php">class Api extends BaseController
{
    public function users(): ResponseInterface
    {
        $users = [/* ... */];

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'success' => true,
                'count'   => count($users),
                'data'    => $users,
            ]);
    }
}</code></pre>
        <div class="mt-3 d-flex gap-2">
            <a href="<?= base_url('examples/api/users') ?>" target="_blank" class="demo-btn" style="background:#0dcaf0;color:#000;">
                <i class="bi bi-arrow-up-right-square"></i> GET /api/users
            </a>
            <a href="<?= base_url('examples/api/users/1') ?>" target="_blank" class="demo-btn" style="background:#0dcaf0;color:#000;">
                <i class="bi bi-arrow-up-right-square"></i> GET /api/users/1
            </a>
            <a href="<?= base_url('examples/api/users/99') ?>" target="_blank" class="demo-btn outline" style="color:#0dcaf0;border-color:#0dcaf0;">
                404 테스트 (ID=99)
            </a>
        </div>
    </div>
</div>

<!-- HTTP 상태 코드 -->
<div class="example-card">
    <div class="example-card-header"><span class="badge" style="background:#0dcaf0;color:#000;">2</span><h5>HTTP 상태 코드 처리</h5></div>
    <div class="example-card-body">
        <pre><code class="language-php">// 200 OK — 조회 성공
return $this->response->setStatusCode(200)->setJSON(['data' => $item]);

// 201 Created — 생성 성공
return $this->response->setStatusCode(201)->setJSON(['message' => '생성 완료', 'data' => $new]);

// 404 Not Found — 리소스 없음
return $this->response->setStatusCode(404)->setJSON(['error' => '존재하지 않습니다.']);

// 422 Unprocessable Entity — 유효성 검사 실패
return $this->response->setStatusCode(422)->setJSON(['errors' => $this->validator->getErrors()]);

// 401 Unauthorized — 인증 필요
return $this->response->setStatusCode(401)->setJSON(['error' => '토큰이 필요합니다.']);</code></pre>
    </div>
</div>

<!-- POST 테스트 -->
<div class="example-card">
    <div class="example-card-header"><span class="badge" style="background:#0dcaf0;color:#000;">3</span><h5>POST API 라이브 테스트</h5></div>
    <div class="example-card-body">
        <div class="row">
            <div class="col-lg-5">
                <div class="code-label">JSON Body로 POST 전송</div>
                <div class="p-3 border rounded bg-light">
                    <div class="mb-2"><input type="text" id="api-name" class="form-control form-control-sm" placeholder="이름" value="홍길동"></div>
                    <div class="mb-2"><input type="email" id="api-email" class="form-control form-control-sm" placeholder="이메일" value="hong@example.com"></div>
                    <button onclick="testApi()" class="demo-btn w-100 justify-content-center" style="background:#0dcaf0;color:#000;border:none;cursor:pointer;">
                        <i class="bi bi-send"></i> POST /api/users
                    </button>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="code-label">응답</div>
                <pre id="api-result" style="min-height:120px; background:#1e1e2e; color:#cdd6f4; border-radius:8px; padding:1rem; font-size:.82rem;"><code>// 버튼을 클릭하면 결과가 여기에 표시됩니다</code></pre>
            </div>
        </div>
        <div class="code-label mt-3">컨트롤러</div>
        <pre><code class="language-php">public function createUser(): ResponseInterface
{
    $json  = $this->request->getJSON(true); // JSON body 파싱
    $rules = ['name' => 'required', 'email' => 'required|valid_email'];

    if (! $this->validate($rules)) {
        return $this->response->setStatusCode(422)
                              ->setJSON(['errors' => $this->validator->getErrors()]);
    }

    return $this->response->setStatusCode(201)
                          ->setJSON(['success' => true, 'data' => $json]);
}</code></pre>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
async function testApi() {
    const name  = document.getElementById('api-name').value;
    const email = document.getElementById('api-email').value;
    const result = document.getElementById('api-result');

    result.innerHTML = '<code>// 요청 중...</code>';

    try {
        const res = await fetch('<?= base_url('examples/api/users') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ name, email }),
        });
        const data = await res.json();
        result.innerHTML = `<code class="language-json">${JSON.stringify(data, null, 2)}</code>`;
        document.querySelectorAll('#api-result code').forEach(el => hljs.highlightElement(el));
    } catch (e) {
        result.innerHTML = `<code>오류: ${e.message}</code>`;
    }
}
</script>
<?= $this->endSection() ?>
