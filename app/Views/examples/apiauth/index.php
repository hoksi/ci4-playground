<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-key me-2"></i>API 인증 (API Key)</h1>
    <p>API Key 발급, Filter를 통한 Bearer 토큰 인증, 키 활성화/비활성화 관리를 학습합니다.</p>
</div>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active">API 인증</li>
    </ol>
</nav>

<?php if ($success = session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible">
    <i class="bi bi-check-circle me-2"></i>
    <strong>발급된 키:</strong> <code class="user-select-all"><?= esc($success) ?></code>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($error = session()->getFlashdata('error')): ?>
<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i><?= esc($error) ?></div>
<?php endif; ?>

<ul class="nav nav-tabs mb-4" id="mainTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-filter">ApiKeyFilter 코드</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-usage">사용법 & 등록</a></li>
</ul>

<div class="tab-content">

    <!-- ── 라이브 데모 ──────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-demo">
        <div class="row g-4">

            <!-- API 키 발급 -->
            <div class="col-md-4">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-plus-circle text-success"></i>
                        <h5>API 키 발급</h5>
                    </div>
                    <div class="example-card-body">
                        <form action="<?= base_url('examples/apiauth/generate') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">앱/서비스 이름</label>
                                <input type="text" name="name" class="form-control" placeholder="예: 모바일 앱" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-key me-1"></i> API 키 생성
                            </button>
                        </form>
                        <div class="result-box info mt-3 small">
                            <code>bin2hex(random_bytes(32))</code>로 64자 랜덤 키를 생성합니다.
                        </div>
                    </div>
                </div>
            </div>

            <!-- 보호된 엔드포인트 테스트 -->
            <div class="col-md-8">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-shield-lock text-primary"></i>
                        <h5>보호된 엔드포인트 테스트</h5>
                    </div>
                    <div class="example-card-body">
                        <p class="text-muted small mb-3">
                            <code>GET /examples/apiauth/protected</code> 는 <code>api-key</code> 필터가 적용된 보호 엔드포인트입니다.
                            아래에 발급된 키를 붙여넣고 테스트하세요.
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">API 키</label>
                            <input type="text" id="testApiKey" class="form-control font-monospace" placeholder="발급된 64자 API 키를 붙여넣으세요">
                        </div>
                        <div class="d-flex gap-2 mb-3">
                            <button id="btnTestValid" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> 유효한 키로 요청
                            </button>
                            <button id="btnTestInvalid" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle me-1"></i> 잘못된 키로 요청
                            </button>
                            <button id="btnTestNoKey" class="btn btn-outline-secondary">
                                <i class="bi bi-slash-circle me-1"></i> 키 없이 요청
                            </button>
                        </div>
                        <div id="testResult" class="d-none">
                            <div id="testResultBadge" class="mb-2"></div>
                            <pre id="testResultJson" style="background:#1e1e1e;color:#d4d4d4;padding:1rem;border-radius:8px;font-size:.84rem;"></pre>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- 발급된 키 목록 -->
        <div class="example-card mt-4">
            <div class="example-card-header">
                <i class="bi bi-list-ul text-primary"></i>
                <h5>발급된 API 키 목록</h5>
            </div>
            <div class="example-card-body">
                <?php if (empty($keys)): ?>
                <div class="result-box warning text-center">
                    <i class="bi bi-info-circle me-1"></i> 발급된 API 키가 없습니다.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>이름</th>
                                <th>API 키</th>
                                <th>상태</th>
                                <th>마지막 사용</th>
                                <th>발급일</th>
                                <th>관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($keys as $key): ?>
                            <tr>
                                <td><?= esc($key['id']) ?></td>
                                <td><?= esc($key['name']) ?></td>
                                <td>
                                    <code class="small user-select-all" style="font-size:.75rem;">
                                        <?= esc(substr($key['api_key'], 0, 16)) ?>...
                                    </code>
                                    <button class="btn btn-xs btn-outline-secondary btn-sm ms-1 py-0 px-1"
                                            onclick="copyKey('<?= esc($key['api_key']) ?>')" title="복사">
                                        <i class="bi bi-clipboard" style="font-size:.75rem;"></i>
                                    </button>
                                </td>
                                <td>
                                    <?php if ($key['is_active']): ?>
                                    <span class="badge bg-success">활성</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">비활성</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= $key['last_used_at'] ? esc($key['last_used_at']) : '-' ?></td>
                                <td class="small text-muted"><?= esc($key['created_at']) ?></td>
                                <td>
                                    <?php if ($key['is_active']): ?>
                                    <button class="btn btn-sm btn-outline-danger py-0"
                                            onclick="revokeKey(<?= $key['id'] ?>)">
                                        <i class="bi bi-slash-circle me-1"></i>비활성화
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- ── ApiKeyFilter 코드 ──────────────────────────── -->
    <div class="tab-pane fade" id="tab-filter">
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-code-slash text-primary"></i>
                <h5>app/Filters/ApiKeyFilter.php</h5>
            </div>
            <div class="example-card-body">
                <pre><code class="language-php">namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiKeyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        // Authorization: Bearer {api_key} 헤더 검증
        if (empty($authHeader) || ! str_starts_with($authHeader, 'Bearer ')) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Unauthorized', 'message' => '헤더가 필요합니다.']);
        }

        $apiKey = trim(substr($authHeader, 7));

        $db  = \Config\Database::connect();
        $row = $db->table('api_keys')->where('api_key', $apiKey)->get()->getRowArray();

        if (! $row) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Invalid API Key']);
        }

        if (! $row['is_active']) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON(['error' => 'Forbidden', 'message' => '비활성화된 키']);
        }

        // 통과 시 last_used_at 업데이트
        $db->table('api_keys')
            ->where('id', $row['id'])
            ->update(['last_used_at' => date('Y-m-d H:i:s')]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}</code></pre>
            </div>
        </div>
    </div>

    <!-- ── 사용법 & 등록 ─────────────────────────────── -->
    <div class="tab-pane fade" id="tab-usage">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-gear text-success"></i>
                        <h5>필터 등록 (Config/Filters.php)</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">// app/Config/Filters.php
public array $aliases = [
    // ...기존 필터들
    'api-key' => \App\Filters\ApiKeyFilter::class,
];</code></pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-signpost text-primary"></i>
                        <h5>라우트에 필터 적용 (Routes.php)</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">// 단일 라우트에 적용
$routes->get('apiauth/protected',
    'Examples\ApiAuth::protected',
    ['filter' => 'api-key']
);

// 그룹 전체에 적용
$routes->group('api/v1', ['filter' => 'api-key'],
    function($routes) {
        $routes->get('users',  'Api\Users::index');
        $routes->post('users', 'Api\Users::create');
    }
);</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="example-card mt-4">
            <div class="example-card-header">
                <i class="bi bi-terminal text-warning"></i>
                <h5>클라이언트에서 사용하는 방법</h5>
            </div>
            <div class="example-card-body">
                <div class="code-label">curl</div>
                <pre><code class="language-bash">curl -H "Authorization: Bearer YOUR_API_KEY" \
     https://example.com/api/v1/users</code></pre>

                <div class="code-label mt-3">JavaScript fetch</div>
                <pre><code class="language-javascript">const response = await fetch('/api/v1/users', {
    headers: {
        'Authorization': 'Bearer ' + apiKey,
        'Content-Type':  'application/json',
    }
});</code></pre>

                <div class="code-label mt-3">PHP (CI4 CURLRequest)</div>
                <pre><code class="language-php">$client   = \Config\Services::curlrequest();
$response = $client->get('https://api.example.com/users', [
    'headers' => [
        'Authorization' => 'Bearer ' . $apiKey,
    ],
]);</code></pre>
                <div class="result-box info mt-3 small">
                    <strong>응답 코드 규칙:</strong>
                    <code>200</code> 인증 성공 /
                    <code>401</code> 키 없음·유효하지 않음 /
                    <code>403</code> 키 비활성화
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function copyKey(key) {
    navigator.clipboard.writeText(key).then(() => {
        document.getElementById('testApiKey').value = key;
        alert('키가 복사되어 테스트 입력란에 붙여넣어졌습니다.');
    });
}

async function testProtected(apiKey) {
    const headers = {'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>'};
    if (apiKey !== null) headers['Authorization'] = 'Bearer ' + apiKey;

    const res  = await fetch('<?= base_url('examples/apiauth/protected') ?>', { headers });
    const data = await res.json();

    document.getElementById('testResult').classList.remove('d-none');
    const statusColors = {200: 'success', 401: 'danger', 403: 'warning'};
    const color = statusColors[res.status] || 'secondary';
    document.getElementById('testResultBadge').innerHTML =
        `<span class="badge bg-${color} fs-6"><i class="bi bi-circle-fill me-1"></i>HTTP ${res.status}</span>`;
    document.getElementById('testResultJson').textContent = JSON.stringify(data, null, 2);
}

document.getElementById('btnTestValid').addEventListener('click', () => {
    const key = document.getElementById('testApiKey').value.trim();
    if (!key) { alert('API 키를 입력하세요.'); return; }
    testProtected(key);
});

document.getElementById('btnTestInvalid').addEventListener('click', () => {
    testProtected('invalid_key_1234567890abcdef');
});

document.getElementById('btnTestNoKey').addEventListener('click', () => {
    testProtected(null);
});

async function revokeKey(id) {
    if (!confirm('이 API 키를 비활성화하시겠습니까?')) return;
    const formData = new FormData();
    formData.append('id', id);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    const res  = await fetch('<?= base_url('examples/apiauth/revoke') ?>', {
        method: 'POST',
        body: formData
    });
    const data = await res.json();
    if (data.success) {
        location.reload();
    } else {
        alert(data.message);
    }
}
</script>
<?= $this->endSection() ?>
