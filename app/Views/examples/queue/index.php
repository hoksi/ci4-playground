<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-collection-play me-2"></i>큐(Queue) 시스템</h1>
    <p>DB 기반 커스텀 큐 — 잡 추가(push) · 처리(process) · 실패 재시도(retry) 전체 흐름을 실습합니다.</p>
</div>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active">큐 시스템</li>
    </ol>
</nav>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<ul class="nav nav-tabs mb-4" id="mainTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-jobs">잡 목록</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-code">코드 설명</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-arch">아키텍처</a></li>
</ul>

<div class="tab-content">

    <!-- ── 라이브 데모 ──────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-demo">

        <!-- 통계 카드 -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card text-center border-warning">
                    <div class="card-body py-3">
                        <div class="fs-2 fw-bold text-warning" id="statPending"><?= $stats['pending'] ?></div>
                        <div class="small text-muted">대기(Pending)</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center border-primary">
                    <div class="card-body py-3">
                        <div class="fs-2 fw-bold text-primary" id="statProcessing"><?= $stats['processing'] ?></div>
                        <div class="small text-muted">처리 중</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center border-success">
                    <div class="card-body py-3">
                        <div class="fs-2 fw-bold text-success" id="statDone"><?= $stats['done'] ?></div>
                        <div class="small text-muted">완료(Done)</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center border-danger">
                    <div class="card-body py-3">
                        <div class="fs-2 fw-bold text-danger" id="statFailed"><?= $stats['failed'] ?></div>
                        <div class="small text-muted">실패(Failed)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            <!-- 잡 추가 -->
            <div class="col-md-5">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-plus-circle text-primary"></i>
                        <h5>잡 추가 (Push)</h5>
                    </div>
                    <div class="example-card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">큐 이름</label>
                            <select id="queueName" class="form-select form-select-sm">
                                <option value="default">default</option>
                                <option value="emails">emails</option>
                                <option value="reports">reports</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">잡 유형</label>
                            <select id="jobType" class="form-select form-select-sm">
                                <option value="email">이메일 발송 (EmailNotificationJob)</option>
                                <option value="data">데이터 처리 (DataProcessJob)</option>
                                <option value="report">보고서 생성 (ReportGenerateJob)</option>
                            </select>
                        </div>

                        <!-- 이메일 옵션 -->
                        <div id="optEmail" class="job-opts">
                            <div class="mb-2">
                                <label class="form-label small">수신자 이메일</label>
                                <input type="email" id="emailTo" class="form-control form-control-sm" value="user@example.com">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">제목</label>
                                <input type="text" id="emailSubject" class="form-control form-control-sm" value="알림 메일">
                            </div>
                        </div>

                        <!-- 데이터 처리 옵션 -->
                        <div id="optData" class="job-opts d-none">
                            <div class="mb-2">
                                <label class="form-label small">처리 건수</label>
                                <input type="number" id="dataItems" class="form-control form-control-sm" value="100">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">데이터 유형</label>
                                <select id="dataType" class="form-select form-select-sm">
                                    <option value="csv">CSV</option>
                                    <option value="json">JSON</option>
                                    <option value="xml">XML</option>
                                </select>
                            </div>
                        </div>

                        <!-- 보고서 옵션 -->
                        <div id="optReport" class="job-opts d-none">
                            <div class="mb-2">
                                <label class="form-label small">기간</label>
                                <select id="reportPeriod" class="form-select form-select-sm">
                                    <option value="daily">일간</option>
                                    <option value="weekly">주간</option>
                                    <option value="monthly" selected>월간</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">형식</label>
                                <select id="reportFormat" class="form-select form-select-sm">
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                </select>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="forceFail">
                                <label class="form-check-label small text-danger" for="forceFail">
                                    실패 시뮬레이션 (force_fail)
                                </label>
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label class="form-label small">지연 실행 (초)</label>
                            <input type="number" id="delay" class="form-control form-control-sm" value="0" min="0" max="60">
                        </div>

                        <button id="btnPush" class="btn btn-primary w-100">
                            <i class="bi bi-send me-1"></i> 큐에 추가
                        </button>

                        <div id="pushResult" class="mt-3 d-none"></div>
                    </div>
                </div>
            </div>

            <!-- 처리 & 로그 -->
            <div class="col-md-7">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-play-circle text-success"></i>
                        <h5>잡 처리 (Process)</h5>
                        <div class="ms-auto d-flex gap-2">
                            <button id="btnProcess" class="btn btn-sm btn-success">
                                <i class="bi bi-play me-1"></i>다음 잡 처리
                            </button>
                            <button id="btnProcessAll" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-fast-forward me-1"></i>전체 처리
                            </button>
                            <a href="<?= base_url('examples/queue/clear?queue=default') ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('큐를 초기화하시겠습니까?')">
                                <i class="bi bi-trash me-1"></i>초기화
                            </a>
                        </div>
                    </div>
                    <div class="example-card-body">
                        <div id="processLog" style="max-height:360px;overflow-y:auto;">
                            <div class="text-muted small text-center py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                                잡을 추가하고 "다음 잡 처리" 버튼을 눌러보세요.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 실패 잡 -->
        <div class="example-card mt-4" id="failedSection" <?= $stats['failed'] === 0 ? 'style="display:none"' : '' ?>>
            <div class="example-card-header">
                <i class="bi bi-exclamation-triangle text-danger"></i>
                <h5>실패한 잡</h5>
            </div>
            <div class="example-card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0" id="failedTable">
                        <thead class="table-light">
                            <tr><th>#</th><th>큐</th><th>잡 클래스</th><th>오류</th><th>시도</th><th>실패 시각</th><th></th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($failed as $f): ?>
                            <tr id="failed-<?= $f['id'] ?>">
                                <td><?= $f['id'] ?></td>
                                <td><span class="badge bg-secondary"><?= esc($f['queue']) ?></span></td>
                                <td class="small font-monospace"><?= esc(class_basename($f['job_class'])) ?></td>
                                <td class="small text-danger"><?= esc($f['exception']) ?></td>
                                <td><?= $f['attempts'] ?></td>
                                <td class="small"><?= date('H:i:s', (int)$f['failed_at']) ?></td>
                                <td>
                                    <button class="btn btn-xs btn-outline-warning btn-retry" data-id="<?= $f['id'] ?>">
                                        <i class="bi bi-arrow-clockwise"></i> 재시도
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- ── 잡 목록 ──────────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-jobs">
        <div class="row g-4">
            <div class="col-md-7">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-clock-history text-warning"></i>
                        <h5>대기/처리 중 잡</h5>
                    </div>
                    <div class="example-card-body">
                        <?php if (empty($pending)): ?>
                        <div class="result-box info small">대기 중인 잡이 없습니다.</div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr><th>#</th><th>큐</th><th>잡 클래스</th><th>상태</th><th>시도</th><th>등록 시각</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($pending as $j): ?>
                                <tr>
                                    <td><?= $j['id'] ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($j['queue']) ?></span></td>
                                    <td class="small font-monospace"><?= esc(class_basename($j['job_class'])) ?></td>
                                    <td>
                                        <?php if ($j['status'] === 'processing'): ?>
                                        <span class="badge bg-primary">처리 중</span>
                                        <?php else: ?>
                                        <span class="badge bg-warning text-dark">대기</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $j['attempts'] ?></td>
                                    <td class="small"><?= date('H:i:s', (int)$j['created_at']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-check-circle text-success"></i>
                        <h5>완료된 잡 (최근 10개)</h5>
                    </div>
                    <div class="example-card-body">
                        <?php if (empty($done)): ?>
                        <div class="result-box info small">완료된 잡이 없습니다.</div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr><th>#</th><th>잡 클래스</th><th>시도</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($done as $j): ?>
                                <tr>
                                    <td><?= $j['id'] ?></td>
                                    <td class="small font-monospace"><?= esc(class_basename($j['job_class'])) ?></td>
                                    <td><?= $j['attempts'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── 코드 설명 ────────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-code">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-box text-primary"></i>
                        <h5>잡 클래스 정의</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">// app/Jobs/BaseJob.php
abstract class BaseJob
{
    protected array $payload;

    public function __construct(array $payload = [])
    {
        $this->payload = $payload;
    }

    abstract public function handle(): array;
}

// app/Jobs/EmailNotificationJob.php
class EmailNotificationJob extends BaseJob
{
    public function handle(): array
    {
        $to      = $this->payload['to'];
        $subject = $this->payload['subject'];

        // 실제 이메일 발송 로직
        // mail($to, $subject, $body);

        return [
            'result'  => 'success',
            'message' => "이메일 발송 완료 → {$to}",
        ];
    }
}</code></pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-send text-success"></i>
                        <h5>잡 추가 & 처리</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">$queue = new \App\Libraries\QueueManager();

// ① 잡 추가 (즉시)
$id = $queue->push(
    EmailNotificationJob::class,
    ['to' => 'user@example.com', 'subject' => '알림'],
    'emails'       // 큐 이름
);

// ② 지연 실행 (30초 후)
$queue->push(
    ReportGenerateJob::class,
    ['period' => 'monthly'],
    'reports',
    30             // delaySeconds
);

// ③ 다음 잡 처리 (CLI 워커에서 루프)
$result = $queue->processNext('emails');
// $result: ['success'=>true, 'job'=>'...', 'result'=>[...]]

// ④ 실패 잡 재시도
$queue->retry($failedJobId);

// ⑤ 통계
$stats = $queue->stats('emails');
// ['pending'=>2, 'processing'=>1, 'done'=>15, 'failed'=>1]</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="example-card mt-4">
            <div class="example-card-header">
                <i class="bi bi-terminal text-dark"></i>
                <h5>CLI 워커 패턴 (실무에서는 이렇게 실행)</h5>
            </div>
            <div class="example-card-body">
                <pre><code class="language-php">// app/Commands/QueueWorker.php
class QueueWorker extends BaseCommand
{
    protected $name    = 'queue:work';
    protected $description = '큐 워커 실행';

    public function run(array $params): void
    {
        $queue   = $params[0] ?? 'default';
        $manager = new \App\Libraries\QueueManager();

        $this->write("워커 시작: {$queue} 큐");

        while (true) {
            $result = $manager->processNext($queue);

            if ($result['success']) {
                $this->write("[✓] {$result['job']} 처리 완료");
            } elseif (isset($result['failed']) && $result['failed']) {
                $this->write("[✗] {$result['job']} 최종 실패: {$result['exception']}");
            } else {
                // 잡 없음 → 1초 대기
                sleep(1);
            }
        }
    }
}

// 실행: php spark queue:work emails</code></pre>
                <div class="result-box warning mt-3 small">
                    <strong>운영 팁:</strong> 실무에서는 <code>Supervisor</code> 또는 <code>systemd</code>로 워커 프로세스를 관리합니다.
                    프로세스가 죽으면 자동 재시작되도록 설정하세요.
                </div>
            </div>
        </div>
    </div>

    <!-- ── 아키텍처 ─────────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-arch">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-diagram-3 text-primary"></i>
                        <h5>큐 처리 흐름</h5>
                    </div>
                    <div class="example-card-body">
                        <div class="result-box info">
                            <ol class="mb-0 ps-3">
                                <li class="mb-2"><strong>Push</strong> — 웹 요청에서 잡 데이터를 <code>queue_jobs</code> 테이블에 삽입</li>
                                <li class="mb-2"><strong>Worker</strong> — CLI 워커가 주기적으로 <code>processNext()</code> 호출</li>
                                <li class="mb-2"><strong>Processing</strong> — 잡 상태를 <code>processing</code>으로 변경 후 <code>handle()</code> 실행</li>
                                <li class="mb-2"><strong>Done / Retry</strong> — 성공 시 <code>done</code>, 실패 시 재시도 카운트 증가</li>
                                <li><strong>Failed</strong> — 최대 재시도 초과 시 <code>queue_failed_jobs</code>로 이동</li>
                            </ol>
                        </div>
                        <pre class="mt-3"><code class="language-text">웹 요청
  └── QueueManager::push()
        └── INSERT queue_jobs (status=pending)

CLI 워커 (루프)
  └── QueueManager::processNext()
        ├── SELECT pending job
        ├── UPDATE status=processing
        ├── Job::handle()
        │     ├── 성공 → UPDATE status=done
        │     └── 실패 → attempts++
        │           ├── attempts < max → status=pending (재시도 대기)
        │           └── attempts >= max → INSERT queue_failed_jobs
        │                                 DELETE queue_jobs</code></pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-table text-success"></i>
                        <h5>DB 스키마</h5>
                    </div>
                    <div class="example-card-body">
                        <div class="code-label mb-1">queue_jobs</div>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered mb-0 small">
                                <thead class="table-light"><tr><th>컬럼</th><th>타입</th><th>설명</th></tr></thead>
                                <tbody>
                                    <tr><td>id</td><td>INTEGER PK</td><td>잡 고유 ID</td></tr>
                                    <tr><td>queue</td><td>VARCHAR</td><td>큐 이름 (default/emails/…)</td></tr>
                                    <tr><td>job_class</td><td>VARCHAR</td><td>처리할 잡 클래스 FQCN</td></tr>
                                    <tr><td>payload</td><td>TEXT (JSON)</td><td>잡 파라미터</td></tr>
                                    <tr><td>status</td><td>VARCHAR</td><td>pending/processing/done</td></tr>
                                    <tr><td>attempts</td><td>INTEGER</td><td>시도 횟수</td></tr>
                                    <tr><td>max_attempts</td><td>INTEGER</td><td>최대 재시도 횟수</td></tr>
                                    <tr><td>available_at</td><td>INTEGER</td><td>실행 가능 시각 (Unix)</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="code-label mb-1">queue_failed_jobs</div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0 small">
                                <thead class="table-light"><tr><th>컬럼</th><th>설명</th></tr></thead>
                                <tbody>
                                    <tr><td>exception</td><td>실패 원인 메시지</td></tr>
                                    <tr><td>failed_at</td><td>실패 시각 (Unix)</td></tr>
                                    <tr><td>+ queue/job_class/payload/attempts</td><td>원본 잡 정보</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="example-card mt-4">
                    <div class="example-card-header">
                        <i class="bi bi-lightning-charge text-warning"></i>
                        <h5>실제 큐 라이브러리</h5>
                    </div>
                    <div class="example-card-body small">
                        <p>이 예제는 학습용 커스텀 구현입니다. 실무에서는 다음을 고려하세요:</p>
                        <ul class="mb-0">
                            <li><strong>Redis + Laravel Queues</strong> — 고성능, 실시간</li>
                            <li><strong>Amazon SQS</strong> — 클라우드 관리형</li>
                            <li><strong>RabbitMQ</strong> — AMQP 프로토콜</li>
                            <li><strong>codeigniter4/queue</strong> — CI4 공식 패키지 (DB/Redis 드라이버)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const CSRF_TOKEN = '<?= csrf_token() ?>';
const CSRF_HASH  = '<?= csrf_hash() ?>';
const BASE       = '<?= base_url('examples/queue') ?>';
let currentQueue = '<?= esc($queueName) ?>';

// ─── 잡 유형별 옵션 표시 ─────────────────────────────────
document.getElementById('jobType').addEventListener('change', function () {
    document.querySelectorAll('.job-opts').forEach(el => el.classList.add('d-none'));
    document.getElementById('opt' + this.value.charAt(0).toUpperCase() + this.value.slice(1)).classList.remove('d-none');
});

// ─── 잡 추가 ─────────────────────────────────────────────
document.getElementById('btnPush').addEventListener('click', async () => {
    const jobType = document.getElementById('jobType').value;
    const fd = new FormData();
    fd.append(CSRF_TOKEN, CSRF_HASH);
    fd.append('job_type', jobType);
    fd.append('queue', document.getElementById('queueName').value);
    fd.append('delay', document.getElementById('delay').value);

    if (jobType === 'email') {
        fd.append('to', document.getElementById('emailTo').value);
        fd.append('subject', document.getElementById('emailSubject').value);
    } else if (jobType === 'data') {
        fd.append('items', document.getElementById('dataItems').value);
        fd.append('data_type', document.getElementById('dataType').value);
    } else if (jobType === 'report') {
        fd.append('period', document.getElementById('reportPeriod').value);
        fd.append('format', document.getElementById('reportFormat').value);
        fd.append('force_fail', document.getElementById('forceFail').checked ? '1' : '');
    }

    const res  = await fetch(BASE + '/push', {method: 'POST', body: fd});
    const data = await res.json();

    const box = document.getElementById('pushResult');
    box.className = 'mt-3 result-box' + (data.success ? '' : ' danger');
    box.innerHTML = `<i class="bi bi-${data.success ? 'check-circle' : 'exclamation-circle'} me-1"></i>${escHtml(data.message)}`;
    box.classList.remove('d-none');

    if (data.stats) updateStats(data.stats);
});

// ─── 잡 처리 ─────────────────────────────────────────────
document.getElementById('btnProcess').addEventListener('click', () => processOne());

document.getElementById('btnProcessAll').addEventListener('click', async () => {
    const btn = document.getElementById('btnProcessAll');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>처리 중…';

    let hasJob = true;
    while (hasJob) {
        const result = await processOne(true);
        if (!result || result.message === '처리할 작업이 없습니다.') {
            hasJob = false;
        }
        await new Promise(r => setTimeout(r, 100));
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-fast-forward me-1"></i>전체 처리';
});

async function processOne(silent = false) {
    const fd = new FormData();
    fd.append(CSRF_TOKEN, CSRF_HASH);
    fd.append('queue', currentQueue);

    const res  = await fetch(BASE + '/process', {method: 'POST', body: fd});
    const data = await res.json();

    if (data.stats) updateStats(data.stats);
    appendLog(data);

    if (data.failed && data.job_id) {
        refreshFailedSection();
    }

    return data;
}

function appendLog(data) {
    const log = document.getElementById('processLog');
    const empty = log.querySelector('.text-muted');
    if (empty) empty.remove();

    const ts   = new Date().toLocaleTimeString('ko-KR');
    let cls    = 'border-success bg-success bg-opacity-10';
    let icon   = 'check-circle-fill text-success';
    let detail = '';

    if (!data.success && data.message === '처리할 작업이 없습니다.') {
        cls  = 'border-secondary bg-light';
        icon = 'inbox text-muted';
    } else if (!data.success) {
        cls  = data.failed ? 'border-danger bg-danger bg-opacity-10' : 'border-warning bg-warning bg-opacity-10';
        icon = data.failed ? 'x-circle-fill text-danger' : 'arrow-clockwise text-warning';
    }

    if (data.result) {
        detail = `<div class="mt-1 small text-muted font-monospace">${escHtml(JSON.stringify(data.result))}</div>`;
    }
    if (data.exception) {
        detail = `<div class="mt-1 small text-danger">${escHtml(data.exception)}</div>`;
    }

    const html = `
        <div class="border rounded p-2 mb-2 ${cls}">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-${icon} mt-1"></i>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold small">${escHtml(data.job || data.message)}</span>
                        <span class="text-muted small">${ts}</span>
                    </div>
                    <div class="small">${escHtml(data.message || '')}</div>
                    ${detail}
                </div>
            </div>
        </div>`;

    log.insertAdjacentHTML('afterbegin', html);
}

// ─── 실패 잡 재시도 ───────────────────────────────────────
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.btn-retry');
    if (!btn) return;

    const failedId = btn.dataset.id;
    const fd = new FormData();
    fd.append(CSRF_TOKEN, CSRF_HASH);
    fd.append('failed_id', failedId);
    fd.append('queue', currentQueue);

    const res  = await fetch(BASE + '/retry', {method: 'POST', body: fd});
    const data = await res.json();

    if (data.success) {
        document.getElementById('failed-' + failedId)?.remove();
        if (data.stats) updateStats(data.stats);
        appendLog({success: true, message: data.message, job: '재시도 등록'});
        checkFailedSection();
    }
});

function updateStats(s) {
    document.getElementById('statPending').textContent    = s.pending;
    document.getElementById('statProcessing').textContent = s.processing;
    document.getElementById('statDone').textContent       = s.done;
    document.getElementById('statFailed').textContent     = s.failed;
}

function checkFailedSection() {
    const tbody = document.querySelector('#failedTable tbody');
    const section = document.getElementById('failedSection');
    if (tbody && tbody.rows.length === 0) {
        section.style.display = 'none';
    }
}

async function refreshFailedSection() {
    const section = document.getElementById('failedSection');
    section.style.display = '';
    // 페이지 새로고침으로 실패 목록 갱신
    setTimeout(() => location.reload(), 1500);
}

function escHtml(str) {
    return String(str ?? '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
<?= $this->endSection() ?>
