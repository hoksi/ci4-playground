<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">컨트롤러</li>
    </ol></nav>
    <h1><i class="bi bi-cpu me-2"></i>컨트롤러</h1>
    <p>요청을 받아 처리하고 응답을 반환하는 CI4 컨트롤러의 핵심 기능을 알아봅니다.</p>
</div>

<!-- 1. 컨트롤러 기본 구조 -->
<div class="example-card">
    <div class="example-card-header">
        <span class="badge bg-primary">1</span>
        <h5>컨트롤러 기본 구조</h5>
    </div>
    <div class="example-card-body">
        <p class="text-muted">모든 컨트롤러는 <code>BaseController</code>를 상속하며 <code>app/Controllers/</code>에 위치합니다.</p>
        <div class="code-label">app/Controllers/Examples/Controllers.php</div>
        <pre><code class="language-php">&lt;?php namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Controllers extends BaseController
{
    public function index(): string
    {
        // view() 헬퍼로 뷰 렌더링, 배열로 데이터 전달
        return view('examples/controllers/index', [
            'title' => '컨트롤러',
        ]);
    }
}</code></pre>
        <div class="result-box info mt-3">
            <i class="bi bi-folder me-2 text-primary"></i>
            <strong>네임스페이스와 디렉토리 구조가 일치합니다.</strong><br>
            <code>App\Controllers\Examples\Controllers</code> → <code>app/Controllers/Examples/Controllers.php</code>
        </div>
    </div>
</div>

<!-- 2. Request 객체 -->
<div class="example-card">
    <div class="example-card-header">
        <span class="badge bg-primary">2</span>
        <h5>Request 객체 — 요청 정보 읽기</h5>
    </div>
    <div class="example-card-body">
        <p class="text-muted"><code>$this->request</code>로 HTTP 요청의 모든 정보에 접근할 수 있습니다.</p>
        <div class="code-label">컨트롤러</div>
        <pre><code class="language-php">public function request(): string
{
    $data = [
        'method'     => $this->request->getMethod(),           // 'get', 'post' 등
        'ipAddress'  => $this->request->getIPAddress(),        // 클라이언트 IP
        'userAgent'  => $this->request->getUserAgent()->getAgentString(),
        'uri'        => (string) $this->request->getUri(),     // 전체 URI
        'queryParams'=> $this->request->getGet(),              // GET 파라미터
        // POST 데이터: $this->request->getPost('name')
        // JSON 바디:   $this->request->getJSON()
        // 모든 입력:   $this->request->getVar('name')
    ];
    return view('examples/controllers/request', $data);
}</code></pre>
        <div class="mt-3">
            <a href="<?= base_url('examples/controllers/request') ?>?lang=ko&page=1" class="demo-btn" style="background:#0d6efd;">
                <i class="bi bi-play-fill"></i> Request 데모 (쿼리스트링 포함)
            </a>
        </div>
    </div>
</div>

<!-- 3. 폼 처리 & 유효성 검사 -->
<div class="example-card">
    <div class="example-card-header">
        <span class="badge bg-primary">3</span>
        <h5>폼 처리 & 유효성 검사 (AJAX)</h5>
    </div>
    <div class="example-card-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="code-label">컨트롤러</div>
                <pre><code class="language-php">public function store(): ResponseInterface
{
    $rules = [
        'name'  => 'required|min_length[2]',
        'email' => 'required|valid_email',
    ];

    if (! $this->validate($rules)) {
        return $this->response->setJSON([
            'success' => false,
            'errors'  => $this->validator->getErrors(),
        ]);
    }

    return $this->response->setJSON([
        'success' => true,
        'message' => '처리 완료!',
    ]);
}</code></pre>
            </div>
            <div class="col-lg-6">
                <p class="code-label">라이브 데모</p>
                <div class="p-3 border rounded bg-light">
                    <div class="mb-2">
                        <input type="text" id="demo-name" class="form-control form-control-sm" placeholder="이름 (2자 이상)">
                    </div>
                    <div class="mb-2">
                        <input type="email" id="demo-email" class="form-control form-control-sm" placeholder="이메일">
                    </div>
                    <button onclick="submitDemo()" class="demo-btn w-100 justify-content-center" style="background:#0d6efd;">
                        <i class="bi bi-send"></i> POST 전송
                    </button>
                    <div id="demo-result" class="mt-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4. 응답 타입 -->
<div class="example-card">
    <div class="example-card-header">
        <span class="badge bg-primary">4</span>
        <h5>다양한 응답 반환</h5>
    </div>
    <div class="example-card-body">
        <pre><code class="language-php">// 뷰 반환
return view('my/view', ['data' => $data]);

// JSON 응답
return $this->response->setJSON(['status' => 'ok']);

// HTTP 상태 코드 지정
return $this->response->setStatusCode(404)->setJSON(['error' => 'Not found']);

// 리다이렉트
return redirect()->to('/');

// 파일 다운로드
return $this->response->download('report.pdf', $fileContent);

// 뷰 문자열만 반환 (렌더링 후 가공)
$html = view('email/template', $data);
// $html을 이메일로 발송하는 등 활용 가능</code></pre>
        <div class="mt-3">
            <a href="<?= base_url('examples/controllers/response') ?>" class="demo-btn" style="background:#0d6efd;">
                <i class="bi bi-play-fill"></i> 응답 타입 데모
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
async function submitDemo() {
    const name  = document.getElementById('demo-name').value;
    const email = document.getElementById('demo-email').value;
    const result = document.getElementById('demo-result');

    const form = new FormData();
    form.append('name', name);
    form.append('email', email);

    try {
        const res = await fetch('<?= base_url('examples/controllers/store') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: form,
        });
        const data = await res.json();
        if (data.success) {
            result.innerHTML = `<div class="alert alert-success py-2 mb-0">${data.message}</div>`;
        } else {
            const errs = Object.values(data.errors).map(e => `<li>${e}</li>`).join('');
            result.innerHTML = `<div class="alert alert-danger py-2 mb-0"><ul class="mb-0">${errs}</ul></div>`;
        }
    } catch (e) {
        result.innerHTML = `<div class="alert alert-danger py-2 mb-0">오류 발생</div>`;
    }
}
</script>
<?= $this->endSection() ?>
