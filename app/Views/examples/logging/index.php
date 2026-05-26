<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-journal-text me-2"></i>로깅</h1>
    <p>CI4 내장 Logger의 PSR-3 레벨별 로그 기록, 로그 파일 확인, 설정 방법을 학습합니다.</p>
</div>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active">로깅</li>
    </ol>
</nav>

<?php if ($success = session()->getFlashdata('success')): ?>
<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= esc($success) ?></div>
<?php endif; ?>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-basic">기본 사용법</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-config">설정</a></li>
</ul>

<div class="tab-content">

    <!-- ── 라이브 데모 ──────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-demo">

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-pencil-square text-primary"></i>
                        <h5>로그 기록</h5>
                    </div>
                    <div class="example-card-body">
                        <form method="post" action="<?= base_url('examples/logging/write') ?>">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">로그 레벨</label>
                                <select name="level" class="form-select">
                                    <?php foreach (['debug'=>'#6c757d','info'=>'#0d6efd','notice'=>'#0dcaf0','warning'=>'#ffc107','error'=>'#fd7e14','critical'=>'#dc3545','alert'=>'#6f42c1','emergency'=>'#000'] as $lvl => $color): ?>
                                    <option value="<?= $lvl ?>"><?= strtoupper($lvl) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">메시지</label>
                                <input type="text" name="message" class="form-control" value="테스트 로그 메시지입니다.">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-journal-plus me-1"></i> 로그 기록
                            </button>
                        </form>

                        <?php if (! empty($logFiles)): ?>
                        <hr>
                        <div class="code-label">로그 파일 목록 (최근 5개)</div>
                        <?php foreach ($logFiles as $f): ?>
                        <div class="d-flex justify-content-between small text-muted py-1 border-bottom">
                            <span><i class="bi bi-file-text me-1"></i><?= esc($f['name']) ?></span>
                            <span><?= number_format($f['size']) ?> B</span>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-list-ul text-success"></i>
                        <h5>최근 로그 (최대 30줄)</h5>
                    </div>
                    <div class="example-card-body p-0">
                        <?php
                        $levelColors = [
                            'DEBUG'=>'secondary','INFO'=>'primary','NOTICE'=>'info',
                            'WARNING'=>'warning','ERROR'=>'danger','CRITICAL'=>'danger',
                            'ALERT'=>'purple','EMERGENCY'=>'dark',
                        ];
                        ?>
                        <div style="max-height:420px;overflow-y:auto;">
                        <?php if (empty($recentLogs)): ?>
                            <p class="text-muted p-3 mb-0">기록된 로그가 없습니다. 위에서 로그를 기록해보세요.</p>
                        <?php else: ?>
                            <table class="table table-sm table-hover mb-0 small">
                                <thead class="table-dark sticky-top">
                                    <tr><th width="90">레벨</th><th width="155">시각</th><th>메시지</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($recentLogs as $log): ?>
                                <tr>
                                    <td>
                                        <?php if ($log['level']): ?>
                                        <span class="badge bg-<?= $levelColors[$log['level']] ?? 'secondary' ?>">
                                            <?= $log['level'] ?>
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted"><?= esc($log['time']) ?></td>
                                    <td style="word-break:break-all;"><?= esc($log['message']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /tab-demo -->

    <!-- ── 기본 사용법 ─────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-basic">
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-code-slash text-primary"></i>
                <h5>log_message() — PSR-3 레벨</h5>
            </div>
            <div class="example-card-body">
                <p class="mb-3">CI4는 PSR-3 표준을 따르는 8단계 로그 레벨을 지원합니다. 낮을수록 상세, 높을수록 심각합니다.</p>

                <div class="row g-2 mb-4">
                    <?php foreach ([
                        ['debug',     'secondary', '상세 디버그 정보. 개발 중에만 사용.'],
                        ['info',      'primary',   '일반 정보성 메시지.'],
                        ['notice',    'info',      '정상이지만 주목할 만한 이벤트.'],
                        ['warning',   'warning',   '오류는 아니지만 주의 필요.'],
                        ['error',     'danger',    '즉각 조치가 필요한 런타임 오류.'],
                        ['critical',  'danger',    '심각한 조건 (컴포넌트 사용 불가 등).'],
                        ['alert',     'dark',      '즉각 행동이 필요 (DB 다운 등).'],
                        ['emergency', 'dark',      '시스템 전체 사용 불가 상태.'],
                    ] as [$lvl, $color, $desc]): ?>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start gap-2 p-2 border rounded">
                            <span class="badge bg-<?= $color ?> mt-1" style="min-width:80px;"><?= strtoupper($lvl) ?></span>
                            <small class="text-muted"><?= $desc ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="code-label">기본 사용 예시</div>
                <pre><code class="language-php">// 헬퍼 함수로 간단하게 기록
log_message('info',  '사용자 로그인: user_id=42');
log_message('error', '결제 실패: ' . $e->getMessage());

// 플레이스홀더 사용 (PSR-3 스타일)
log_message('warning', '잔액 부족: user={user}, balance={balance}', [
    'user'    => $user->name,
    'balance' => $user->balance,
]);

// Logger 서비스 직접 사용
$logger = service('logger');
$logger->info('주문 완료', ['order_id' => $orderId]);
$logger->critical('DB 연결 실패');
</code></pre>

                <div class="result-box info mt-3">
                    로그 파일 위치: <code>writable/logs/log-YYYY-MM-DD.log</code><br>
                    로그 임계값 설정보다 낮은 레벨은 기록되지 않습니다 (<code>Config/Logger.php</code>의 <code>$threshold</code>).
                </div>
            </div>
        </div>
    </div>

    <!-- ── 설정 ───────────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-config">
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-gear text-warning"></i>
                <h5>app/Config/Logger.php 설정</h5>
            </div>
            <div class="example-card-body">
                <pre><code class="language-php">// app/Config/Logger.php

public int $threshold = 4; // 4 이상 레벨만 기록 (WARNING~EMERGENCY)
                            // 0 = 모두 기록, 9 = 기록 안 함

// 핸들러 설정
public array $handlers = [
    // 파일 핸들러 (기본)
    'CodeIgniter\Log\Handlers\FileHandler' => [
        'handles' => ['critical', 'alert', 'emergency', 'debug',
                      'error', 'info', 'notice', 'warning'],
        'path'    => WRITEPATH . 'logs/',
        'fileExtension' => 'log',
        'filePermissions' => 0644,
        'dateFormat'      => 'Y-m-d H:i:s',
    ],

    // ChromeLogger (개발 도구 콘솔 출력)
    // 'CodeIgniter\Log\Handlers\ChromeLoggerHandler' => [
    //     'handles' => ['debug', 'info'],
    // ],
];
</code></pre>

                <div class="code-label mt-4">개발 환경에서 모든 로그 기록 (.env)</div>
                <pre><code class="language-bash"># .env
logger.threshold = 0  # 모든 레벨 기록
</code></pre>

                <div class="result-box warning mt-3">
                    운영 환경에서는 <code>threshold = 4</code> (WARNING 이상)를 권장합니다.
                    DEBUG 레벨을 운영에 두면 민감한 정보가 로그에 남을 수 있습니다.
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
