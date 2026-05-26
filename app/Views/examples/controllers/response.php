<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/controllers') ?>">컨트롤러</a></li>
        <li class="breadcrumb-item active text-white">Response 활용</li>
    </ol></nav>
    <h1><i class="bi bi-send me-2"></i>Response 응답 타입</h1>
    <p>컨트롤러에서 다양한 형태의 응답을 반환하는 방법입니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header"><span class="badge bg-primary">1</span><h5>JSON 응답</h5></div>
    <div class="example-card-body">
        <pre><code class="language-php">public function jsonResponse(): ResponseInterface
{
    $data = ['status' => 'ok', 'count' => 42];

    return $this->response
        ->setStatusCode(200)
        ->setJSON($data);
    // Content-Type: application/json 자동 설정
}</code></pre>
        <div class="mt-3">
            <a href="<?= base_url('examples/api/users') ?>" target="_blank" class="demo-btn" style="background:#0d6efd;">
                <i class="bi bi-braces"></i> JSON API 데모 보기
            </a>
        </div>
    </div>
</div>

<div class="example-card">
    <div class="example-card-header"><span class="badge bg-primary">2</span><h5>HTTP 상태 코드</h5></div>
    <div class="example-card-body">
        <pre><code class="language-php">// 200 OK (기본값)
return $this->response->setJSON($data);

// 201 Created
return $this->response->setStatusCode(201)->setJSON($data);

// 400 Bad Request
return $this->response->setStatusCode(400)->setJSON(['error' => '잘못된 요청']);

// 401 Unauthorized
return $this->response->setStatusCode(401)->setJSON(['error' => '인증 필요']);

// 404 Not Found
return $this->response->setStatusCode(404)->setJSON(['error' => '데이터 없음']);

// 422 Validation Error
return $this->response->setStatusCode(422)->setJSON(['errors' => $this->validator->getErrors()]);</code></pre>
    </div>
</div>

<div class="example-card">
    <div class="example-card-header"><span class="badge bg-primary">3</span><h5>헤더 & 쿠키 설정</h5></div>
    <div class="example-card-body">
        <pre><code class="language-php">// 커스텀 헤더
return $this->response
    ->setHeader('X-Custom-Header', 'value')
    ->setJSON($data);

// 쿠키 설정
$this->response->setCookie('remember_me', $token, 30 * DAY);

// 쿠키 삭제
$this->response->deleteCookie('remember_me');

// Content-Type 직접 지정
return $this->response
    ->setContentType('text/csv')
    ->setBody($csvContent);</code></pre>
    </div>
</div>

<div class="mt-3">
    <a href="<?= base_url('examples/controllers') ?>" class="demo-btn" style="background:#0d6efd;">← 컨트롤러로</a>
</div>

<?= $this->endSection() ?>
