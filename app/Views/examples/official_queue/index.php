<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center mb-4">
    <div>
        <h2 class="mb-1">CI4 공식 Queue 시스템</h2>
        <p class="text-muted mb-0"><code>codeigniter4/queue</code> 패키지 — Database 핸들러(SQLite)로 잡 추가·처리·재시도·실패 관리</p>
    </div>
</div>

<!-- 통계 카드 -->
<div class="row g-3 mb-4" id="statsRow">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #0d6efd !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#e7f0ff;">
                    <i class="bi bi-hourglass-split fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold text-primary" id="statPending"><?= $pending ?></div>
                    <div class="text-muted small">대기(Pending)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #ffc107 !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#fff8e1;">
                    <i class="bi bi-gear-wide-connected fs-4 text-warning"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold text-warning" id="statProcessing"><?= $processing ?></div>
                    <div class="text-muted small">처리 중</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #dc3545 !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#fdecea;">
                    <i class="bi bi-x-circle fs-4 text-danger"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold text-danger" id="statFailed"><?= $failed ?></div>
                    <div class="text-muted small">실패(Failed)</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 탭 -->
<ul class="nav nav-tabs mb-4" id="mainTab">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo"><i class="bi bi-play-circle me-1"></i>라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-jobs"><i class="bi bi-list-ul me-1"></i>잡 목록</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-code"><i class="bi bi-code-slash me-1"></i>코드 설명</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-compare"><i class="bi bi-arrow-left-right me-1"></i>DIY vs 공식</a></li>
</ul>

<div class="tab-content">

    <!-- ── 라이브 데모 ───────────────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-demo">
        <div class="row g-4">

            <!-- 잡 추가 -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white"><i class="bi bi-plus-circle me-2"></i><strong>잡 추가 (Push)</strong></div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label fw-bold">잡 유형</label>
                            <select id="jobType" class="form-select" onchange="toggleJobOptions()">
                                <option value="send-email">이메일 발송 (SendEmailJob)</option>
                                <option value="process-data">데이터 처리 (ProcessDataJob)</option>
                                <option value="generate-report">보고서 생성 (GenerateReportJob)</option>
                            </select>
                        </div>

                        <!-- 이메일 옵션 -->
                        <div id="opt-email" class="mb-3">
                            <label class="form-label text-muted small">수신자 이메일</label>
                            <input type="email" id="emailTo" class="form-control form-control-sm" value="user@example.com">
                        </div>

                        <!-- 보고서 옵션 -->
                        <div id="opt-report" class="mb-3 d-none">
                            <label class="form-label text-muted small">보고서 유형</label>
                            <select id="reportType" class="form-select form-select-sm mb-2">
                                <option value="daily">일간 보고서</option>
                                <option value="weekly">주간 보고서</option>
                                <option value="monthly">월간 보고서</option>
                            </select>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="forceFail">
                                <label class="form-check-label text-danger small" for="forceFail">실패 시뮬레이션 (force_fail)</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small">지연 실행 (초, 0=즉시)</label>
                            <input type="number" id="delay" class="form-control form-control-sm" value="0" min="0" max="300">
                        </div>

                        <button class="btn btn-primary w-100" onclick="pushJob()">
                            <i class="bi bi-send me-1"></i>큐에 추가
                        </button>
                    </div>
                </div>

                <!-- 처리 컨트롤 -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-success text-white"><i class="bi bi-play me-2"></i><strong>잡 처리</strong></div>
                    <div class="card-body d-grid gap-2">
                        <button class="btn btn-success" onclick="processNext()">
                            <i class="bi bi-skip-forward me-1"></i>다음 잡 1건 처리
                        </button>
                        <button class="btn btn-warning" onclick="retryFailed()">
                            <i class="bi bi-arrow-repeat me-1"></i>실패 잡 전체 재시도
                        </button>
                        <button class="btn btn-outline-danger btn-sm" onclick="clearQueue()">
                            <i class="bi bi-trash me-1"></i>큐 초기화
                        </button>
                    </div>
                </div>
            </div>

            <!-- 처리 로그 -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                        <span><i class="bi bi-terminal me-2"></i><strong>처리 로그</strong></span>
                        <button class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('log').innerHTML=''">지우기</button>
                    </div>
                    <div class="card-body p-0">
                        <div id="log" class="p-3 font-monospace small" style="height:380px;overflow-y:auto;background:#1e1e1e;color:#d4d4d4;border-radius:0 0 .375rem .375rem;">
                            <span class="text-muted">// 잡을 추가하고 처리 버튼을 눌러보세요</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ── 잡 목록 탭 ────────────────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-jobs">
        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-hourglass-split me-2"></i><strong>대기 중인 잡</strong> <span class="badge bg-primary" id="pendingBadge"><?= $pending ?></span></span>
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshJobs()"><i class="bi bi-arrow-clockwise"></i> 새로고침</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0 small" id="pendingTable">
                            <thead class="table-dark">
                                <tr><th>ID</th><th>잡 유형</th><th>데이터</th><th>상태</th><th>시도</th><th>등록시간</th></tr>
                            </thead>
                            <tbody id="pendingBody">
                                <?php foreach ($pendingJobs as $j): ?>
                                <?php $p = json_decode($j['payload'], true); ?>
                                <tr>
                                    <td class="text-muted"><?= $j['id'] ?></td>
                                    <td><span class="badge bg-primary"><?= esc($p['job'] ?? '') ?></span></td>
                                    <td class="text-muted"><?= esc(json_encode($p['data'] ?? [], JSON_UNESCAPED_UNICODE)) ?></td>
                                    <td><?php
                                        $s = (int)$j['status'];
                                        echo match($s) {
                                            0 => '<span class="badge bg-secondary">대기</span>',
                                            1 => '<span class="badge bg-warning text-dark">처리중</span>',
                                            2 => '<span class="badge bg-success">완료</span>',
                                            default => '<span class="badge bg-light text-dark">'.$s.'</span>'
                                        };
                                    ?></td>
                                    <td><?= $j['attempts'] ?></td>
                                    <td class="text-muted"><?= date('H:i:s', $j['created_at']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($pendingJobs)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-3">대기 중인 잡이 없습니다.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <i class="bi bi-x-circle me-2"></i><strong>실패한 잡</strong> <span class="badge bg-light text-danger" id="failedBadge"><?= $failed ?></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0 small" id="failedTable">
                            <thead class="table-dark">
                                <tr><th>ID</th><th>잡 유형</th><th>예외 메시지</th><th>실패시간</th><th>액션</th></tr>
                            </thead>
                            <tbody id="failedBody">
                                <?php foreach ($failedJobs as $j): ?>
                                <?php $p = json_decode($j['payload'], true); ?>
                                <tr>
                                    <td class="text-muted"><?= $j['id'] ?></td>
                                    <td><span class="badge bg-danger"><?= esc($p['job'] ?? '') ?></span></td>
                                    <td class="text-muted small"><?= esc(substr($j['exception'], 0, 80)) ?>...</td>
                                    <td class="text-muted"><?= date('H:i:s', $j['failed_at']) ?></td>
                                    <td>
                                        <button class="btn btn-xs btn-outline-warning btn-sm py-0" onclick="retryFailed(<?= $j['id'] ?>)">
                                            <i class="bi bi-arrow-repeat"></i> 재시도
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($failedJobs)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">실패한 잡이 없습니다.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── 코드 설명 탭 ──────────────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-code">
        <div class="row g-4">

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white"><strong>1. 잡 클래스 작성</strong></div>
                    <div class="card-body p-0">
                        <pre class="mb-0"><code class="language-php">// app/Jobs/Queue/SendEmailJob.php
namespace App\Jobs\Queue;

use CodeIgniter\Queue\BaseJob;
use CodeIgniter\Queue\Interfaces\JobInterface;

class SendEmailJob extends BaseJob implements JobInterface
{
    protected int $retryAfter = 30; // 재시도 대기 (초)
    protected int $tries      = 3;  // 최대 시도 횟수

    public function process(): void
    {
        $to = $this->data['to'];

        // 실제 이메일 발송 로직
        // service('email')->send(...);

        log_message('info', "이메일 발송: {$to}");
    }
}</code></pre>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white"><strong>2. 잡 핸들러 등록</strong></div>
                    <div class="card-body p-0">
                        <pre class="mb-0"><code class="language-php">// app/Config/Queue.php
public array $jobHandlers = [
    'send-email'      => \App\Jobs\Queue\SendEmailJob::class,
    'process-data'    => \App\Jobs\Queue\ProcessDataJob::class,
    'generate-report' => \App\Jobs\Queue\GenerateReportJob::class,
];</code></pre>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-dark text-white"><strong>3. 잡 추가 (Push)</strong></div>
                    <div class="card-body p-0">
                        <pre class="mb-0"><code class="language-php">// 즉시 실행
service('queue')->push(
    'playground',   // 큐 이름
    'send-email',   // 잡 핸들러 키
    ['to' => 'user@example.com', 'subject' => '안녕']
);

// 30초 지연 후 실행
service('queue')
    ->setDelay(30)
    ->push('playground', 'send-email', $data);

// 체이닝 (순서 보장)
service('queue')->chain(function($chain) {
    $chain->push('process-data',    ['batch_id' => 'A']);
    $chain->push('generate-report', ['type'     => 'daily']);
});</code></pre>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white"><strong>4. 워커 실행 (CLI)</strong></div>
                    <div class="card-body p-0">
                        <pre class="mb-0"><code class="language-bash"># 큐 워커 시작 (데몬으로 실행)
php spark queue:work playground

# 옵션 지정
php spark queue:work playground \
    --sleep 5 \       # 잡 없을 때 대기 초
    --tries 3 \       # 재시도 횟수
    --retry-after 60  # 재시도 대기 초

# 잡 1건만 처리하고 종료
php spark queue:work playground --max-jobs 1

# 실패 잡 목록 확인
php spark queue:failed

# 실패 잡 재시도
php spark queue:retry --id 5
php spark queue:retry --queue playground</code></pre>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white"><strong>5. 설치 및 설정</strong></div>
                    <div class="card-body p-0">
                        <pre class="mb-0"><code class="language-bash"># 패키지 설치
composer require codeigniter4/queue

# 설정 파일 생성
php spark queue:publish

# 마이그레이션 실행 (queue_jobs, queue_jobs_failed 테이블 생성)
php spark migrate --all</code></pre>
                    </div>

                    <div class="card-header bg-light border-top"><strong class="small">지원 핸들러</strong></div>
                    <div class="card-body p-3">
                        <ul class="small mb-0">
                            <li><strong>Database</strong> — MySQL, SQLite, PostgreSQL (이 예제)</li>
                            <li><strong>Redis</strong> — 고성능 인메모리 큐</li>
                            <li><strong>Predis</strong> — PHP Redis 클라이언트</li>
                            <li><strong>RabbitMQ</strong> — 엔터프라이즈 메시지 브로커</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ── DIY vs 공식 비교 탭 ───────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-compare">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light"><i class="bi bi-arrow-left-right me-2"></i><strong>#40 DIY 큐 vs #42 공식 패키지 비교</strong></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:22%">항목</th>
                                <th><i class="bi bi-tools me-1"></i>#40 DIY 커스텀 큐</th>
                                <th><i class="bi bi-box-seam me-1"></i>#42 codeigniter4/queue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">구현 방식</td>
                                <td>직접 구현 (QueueManager 라이브러리)</td>
                                <td>공식 패키지 (Composer 설치)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">테이블</td>
                                <td><code>queue_jobs</code>, <code>queue_failed_jobs</code></td>
                                <td><code>queue_jobs</code>, <code>queue_jobs_failed</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">잡 클래스</td>
                                <td>커스텀 <code>BaseJob</code> 추상 클래스</td>
                                <td>공식 <code>CodeIgniter\Queue\BaseJob</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">핸들러</td>
                                <td>Database only (SQLite)</td>
                                <td>Database / Redis / Predis / RabbitMQ</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">워커 CLI</td>
                                <td>없음 (웹에서 직접 처리)</td>
                                <td><code>php spark queue:work</code> 데몬</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">우선순위</td>
                                <td>없음</td>
                                <td>지원 (<code>setPriority()</code>)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">지연 실행</td>
                                <td>available_at 필드</td>
                                <td><code>setDelay(seconds)</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">체이닝</td>
                                <td>없음</td>
                                <td><code>chain(callback)</code> 지원</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">재시도</td>
                                <td>수동 retry 구현</td>
                                <td>자동 (<code>$tries</code>, <code>$retryAfter</code>)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">이벤트</td>
                                <td>없음</td>
                                <td><code>QueueEventManager</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">추천 상황</td>
                                <td class="text-muted small">큐 시스템 동작 원리 이해, 경량 프로젝트</td>
                                <td class="text-success small fw-bold">실제 프로젝트, 프로덕션 환경</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
const csrfToken = '<?= csrf_token() ?>';
const csrfHash  = '<?= csrf_hash() ?>';

function toggleJobOptions() {
    const type = document.getElementById('jobType').value;
    document.getElementById('opt-email').classList.toggle('d-none', type !== 'send-email');
    document.getElementById('opt-report').classList.toggle('d-none', type !== 'generate-report');
}

function appendLog(msg, type = 'info') {
    const log = document.getElementById('log');
    const colors = { success: '#4ec9b0', error: '#f48771', warning: '#dcdcaa', info: '#9cdcfe', empty: '#808080' };
    const now = new Date().toLocaleTimeString();
    log.innerHTML += `<div style="color:${colors[type] || '#d4d4d4'}">[${now}] ${msg}</div>`;
    log.scrollTop = log.scrollHeight;
}

function updateStats(stats) {
    if (!stats) return;
    document.getElementById('statPending').textContent    = stats.pending;
    document.getElementById('statProcessing').textContent = stats.processing;
    document.getElementById('statFailed').textContent     = stats.failed;
    document.getElementById('pendingBadge').textContent   = stats.pending;
    document.getElementById('failedBadge').textContent    = stats.failed;
}

async function post(url, body) {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams({ ...body, [csrfToken]: csrfHash }),
    });
    return res.json();
}

async function pushJob() {
    const jobType = document.getElementById('jobType').value;
    const body = {
        job:         jobType,
        delay:       document.getElementById('delay').value,
        email:       document.getElementById('emailTo')?.value,
        report_type: document.getElementById('reportType')?.value,
        force_fail:  document.getElementById('forceFail')?.checked ? '1' : '0',
    };
    const data = await post('<?= base_url('examples/official-queue/push') ?>', body);
    appendLog(data.message, data.ok ? 'success' : 'error');
    if (data.stats) updateStats(data.stats);
}

async function processNext() {
    appendLog('잡 처리 중...', 'warning');
    const data = await post('<?= base_url('examples/official-queue/process') ?>', {});
    appendLog(data.message, data.status === 'success' ? 'success' : data.status === 'empty' ? 'empty' : 'error');
    if (data.stats) updateStats(data.stats);
}

async function retryFailed(id = null) {
    const body = id ? { id } : {};
    const data = await post('<?= base_url('examples/official-queue/retry') ?>', body);
    appendLog(data.message, 'warning');
    if (data.stats) updateStats(data.stats);
}

async function clearQueue() {
    if (!confirm('큐를 초기화하시겠습니까?')) return;
    const data = await post('<?= base_url('examples/official-queue/clear') ?>', {});
    appendLog(data.message, 'info');
    if (data.stats) updateStats(data.stats);
}

function refreshJobs() { location.reload(); }
</script>

<?= $this->endSection() ?>
