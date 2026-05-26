<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-sliders me-2"></i>Config 환경 분리</h1>
    <p>BaseConfig 클래스 작성, .env 오버라이드, config() 헬퍼, env() 함수를 활용한 환경별 설정 분리 패턴을 학습합니다.</p>
</div>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active">Config 환경 분리</li>
    </ol>
</nav>

<ul class="nav nav-tabs mb-4" id="mainTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-code">코드 설명</a></li>
</ul>

<div class="tab-content">

    <!-- ── 라이브 데모 ──────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-demo">

        <!-- 현재 환경 배너 -->
        <div class="result-box <?= $environment === 'production' ? 'danger' : 'info' ?> mb-4 d-flex align-items-center gap-3">
            <i class="bi bi-globe fs-4"></i>
            <div>
                <strong>현재 환경(ENVIRONMENT):</strong>
                <span class="badge bg-<?= $environment === 'production' ? 'danger' : ($environment === 'testing' ? 'warning' : 'success') ?> ms-1 fs-6">
                    <?= esc($environment) ?>
                </span>
                <div class="text-muted small mt-1">.env 파일의 <code>CI_ENVIRONMENT</code> 값으로 제어됩니다.</div>
            </div>
        </div>

        <div class="row g-4">
            <!-- PlaygroundConfig 값 -->
            <div class="col-md-6">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-gear text-primary"></i>
                        <h5>PlaygroundConfig 현재 값</h5>
                    </div>
                    <div class="example-card-body">
                        <p class="text-muted small mb-3"><code>config('PlaygroundConfig')</code>로 읽은 값입니다. .env에 <code>playground.debugMode = true</code>를 추가하면 debugMode가 변경됩니다.</p>
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light"><tr><th>속성</th><th>값</th><th>타입</th></tr></thead>
                            <tbody>
                                <tr>
                                    <td><code>$appName</code></td>
                                    <td><?= esc($cfg->appName) ?></td>
                                    <td><span class="badge bg-light text-dark border">string</span></td>
                                </tr>
                                <tr>
                                    <td><code>$version</code></td>
                                    <td><?= esc($cfg->version) ?></td>
                                    <td><span class="badge bg-light text-dark border">string</span></td>
                                </tr>
                                <tr>
                                    <td><code>$debugMode</code></td>
                                    <td>
                                        <?php if ($cfg->debugMode): ?>
                                        <span class="badge bg-warning">true</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">false</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">bool</span></td>
                                </tr>
                                <tr>
                                    <td><code>$maxUpload</code></td>
                                    <td><?= esc($cfg->maxUpload) ?> MB</td>
                                    <td><span class="badge bg-light text-dark border">int</span></td>
                                </tr>
                                <tr>
                                    <td><code>$timezone</code></td>
                                    <td><?= esc($cfg->timezone) ?></td>
                                    <td><span class="badge bg-light text-dark border">string</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 시스템 환경 정보 -->
            <div class="col-md-6">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-info-circle text-success"></i>
                        <h5>시스템 환경 정보</h5>
                    </div>
                    <div class="example-card-body">
                        <p class="text-muted small mb-3">CI4 상수, <code>env()</code> 헬퍼, config() 헬퍼로 읽은 값들입니다.</p>
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light"><tr><th>항목</th><th>값</th></tr></thead>
                            <tbody>
                                <tr><td>ENVIRONMENT</td><td><code><?= esc($environment) ?></code></td></tr>
                                <tr><td>CI 버전</td><td><code><?= esc($ciVersion) ?></code></td></tr>
                                <tr><td>PHP 버전</td><td><code><?= esc($phpVersion) ?></code></td></tr>
                                <tr><td>Base URL</td><td><code><?= esc($baseUrl) ?></code></td></tr>
                                <tr><td>DB 드라이버</td><td><code><?= esc($dbDriver) ?></code></td></tr>
                                <tr><td>.env 파일</td><td><code><?= esc($envFile) ?></code></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ── 코드 설명 ────────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-code">

        <div class="example-card mb-4">
            <div class="example-card-header">
                <i class="bi bi-file-earmark-code text-primary"></i>
                <h5>BaseConfig 클래스 작성</h5>
            </div>
            <div class="example-card-body">
                <div class="code-label">app/Config/PlaygroundConfig.php</div>
                <pre><code class="language-php">namespace Config;

use CodeIgniter\Config\BaseConfig;

class PlaygroundConfig extends BaseConfig
{
    public string $appName   = 'CI4 Playground';
    public string $version   = '1.0.0';
    public bool   $debugMode = false;
    public int    $maxUpload = 5;       // MB
    public string $timezone  = 'Asia/Seoul';
}</code></pre>
                <div class="result-box info mt-3 small">
                    <strong>규칙:</strong> 클래스명은 대문자로 시작, <code>namespace Config</code> 사용, <code>BaseConfig</code> 상속.
                    파일 위치: <code>app/Config/</code>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-file-text text-warning"></i>
                        <h5>.env 파일로 오버라이드</h5>
                    </div>
                    <div class="example-card-body">
                        <div class="code-label">.env (프로젝트 루트)</div>
                        <pre><code class="language-bash"># CI4 환경 설정
CI_ENVIRONMENT = development

# PlaygroundConfig 오버라이드
# 클래스명(소문자).속성명 = 값
playground.debugMode = true
playground.maxUpload = 10
playground.timezone  = UTC</code></pre>
                        <div class="result-box warning mt-3 small">
                            <strong>주의:</strong> .env 파일은 절대 git에 커밋하지 마세요.
                            <code>.gitignore</code>에 반드시 추가하세요.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-code-slash text-success"></i>
                        <h5>config() vs new Config() vs env()</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">// 방법 1: config() 헬퍼 (권장)
// .env 오버라이드가 자동 적용됨
$cfg = config('PlaygroundConfig');
echo $cfg->debugMode;

// 방법 2: new 직접 생성
// .env 오버라이드가 자동 적용됨 (동일)
$cfg = new \Config\PlaygroundConfig();

// 방법 3: env() 헬퍼
// .env 값을 직접 읽음, 없으면 기본값 반환
$debug = env('playground.debugMode', false);
$env   = env('CI_ENVIRONMENT', 'production');

// ENVIRONMENT 상수
if (ENVIRONMENT === 'development') {
    // 개발 환경 전용 코드
}

// 내장 App 설정 읽기
$baseUrl = config('App')->baseURL;</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="example-card mt-4">
            <div class="example-card-header">
                <i class="bi bi-diagram-3 text-danger"></i>
                <h5>환경별 설정 분리 전략</h5>
            </div>
            <div class="example-card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr><th>환경</th><th>CI_ENVIRONMENT</th><th>에러 표시</th><th>디버그 툴바</th><th>캐시</th></tr>
                        </thead>
                        <tbody class="small">
                            <tr class="table-success">
                                <td><strong>개발</strong></td>
                                <td><code>development</code></td>
                                <td><span class="badge bg-success">상세 표시</span></td>
                                <td><span class="badge bg-success">활성화</span></td>
                                <td><span class="badge bg-secondary">비활성</span></td>
                            </tr>
                            <tr class="table-warning">
                                <td><strong>테스트</strong></td>
                                <td><code>testing</code></td>
                                <td><span class="badge bg-warning text-dark">부분 표시</span></td>
                                <td><span class="badge bg-secondary">비활성</span></td>
                                <td><span class="badge bg-secondary">비활성</span></td>
                            </tr>
                            <tr class="table-danger">
                                <td><strong>운영</strong></td>
                                <td><code>production</code></td>
                                <td><span class="badge bg-danger">숨김</span></td>
                                <td><span class="badge bg-secondary">비활성</span></td>
                                <td><span class="badge bg-success">활성화</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="result-box info mt-3 small">
                    CI4는 <code>app/Config/</code> 하위에 <strong>환경별 폴더</strong>를 만들어 설정을 오버라이드할 수도 있습니다.
                    예: <code>app/Config/development/Database.php</code>
                </div>
            </div>
        </div>

    </div>

</div>

<?= $this->endSection() ?>
