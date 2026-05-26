<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-arrow-left-right me-2"></i>DB 트랜잭션</h1>
    <p>transStart/transComplete, 수동 롤백, 예외 기반 자동 롤백 등 CI4 트랜잭션 패턴을 계좌 이체 시나리오로 학습합니다.</p>
</div>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active">DB 트랜잭션</li>
    </ol>
</nav>

<?php $result = session()->getFlashdata('result'); ?>
<?php $success = session()->getFlashdata('success'); ?>

<?php if ($success): ?>
<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= esc($success) ?></div>
<?php endif; ?>

<?php if ($result): ?>
<div class="alert <?= $result['success'] ? 'alert-success' : 'alert-danger' ?> d-flex align-items-start gap-2">
    <i class="bi <?= $result['success'] ? 'bi-check-circle' : 'bi-x-circle' ?> mt-1"></i>
    <div>
        <strong><?= $result['success'] ? '커밋 완료' : ($result['scene'] === 'rollback' ? '롤백 (잔액부족)' : '롤백 (예외 발생)') ?></strong><br>
        <?= esc($result['message']) ?>
    </div>
</div>
<?php endif; ?>

<!-- 탭 -->
<ul class="nav nav-tabs mb-4" id="txTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-basic">기본 트랜잭션</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-rollback">수동 롤백</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-exception">예외 기반 롤백</a></li>
</ul>

<div class="tab-content">

    <!-- ── 라이브 데모 ──────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-demo">

        <!-- 계좌 현황 -->
        <div class="example-card mb-4">
            <div class="example-card-header">
                <i class="bi bi-wallet2 text-success"></i>
                <h5>현재 계좌 잔액</h5>
                <a href="<?= base_url('examples/transaction/reset') ?>"
                   class="btn btn-sm btn-outline-secondary ms-auto"
                   onclick="return confirm('잔액을 초기화하시겠습니까?')">
                    <i class="bi bi-arrow-counterclockwise"></i> 초기화
                </a>
            </div>
            <div class="example-card-body">
                <div class="row g-3">
                    <?php foreach ($accounts as $acc): ?>
                    <div class="col-md-4">
                        <div class="result-box info text-center">
                            <div class="fw-bold fs-5"><?= esc($acc->name) ?></div>
                            <div class="fs-4 mt-1" style="color:#0d6efd;">
                                <?= number_format($acc->balance) ?> <small class="fs-6">원</small>
                            </div>
                            <small class="text-muted">계좌 #<?= $acc->id ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 이체 폼 -->
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-play-circle text-primary"></i>
                <h5>트랜잭션 시나리오 실행</h5>
            </div>
            <div class="example-card-body">
                <form method="post" action="<?= base_url('examples/transaction/transfer') ?>">
                    <?= csrf_field() ?>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">보내는 계좌</label>
                            <select name="from_id" class="form-select">
                                <?php foreach ($accounts as $acc): ?>
                                <option value="<?= $acc->id ?>"><?= esc($acc->name) ?> (<?= number_format($acc->balance) ?>원)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">받는 계좌</label>
                            <select name="to_id" class="form-select">
                                <?php foreach ($accounts as $acc): ?>
                                <option value="<?= $acc->id ?>" <?= $acc->id === 2 ? 'selected' : '' ?>><?= esc($acc->name) ?> (<?= number_format($acc->balance) ?>원)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">이체 금액 (원)</label>
                            <input type="number" name="amount" class="form-control" value="30000" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">시나리오</label>
                            <select name="scene" class="form-select">
                                <option value="success">✅ 정상 이체 (커밋)</option>
                                <option value="rollback">⚠️ 잔액 부족 (수동 롤백)</option>
                                <option value="error">❌ 강제 오류 (예외 롤백)</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> 이체 실행
                    </button>
                </form>
            </div>
        </div>

    </div><!-- /tab-demo -->

    <!-- ── 기본 트랜잭션 ───────────────────────────────── -->
    <div class="tab-pane fade" id="tab-basic">
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-check2-all text-success"></i>
                <h5>transStart / transComplete</h5>
            </div>
            <div class="example-card-body">
                <p class="mb-3">가장 기본적인 트랜잭션 패턴입니다. <code>transStart()</code>와 <code>transComplete()</code> 사이의 모든 쿼리가 하나의 트랜잭션으로 묶입니다. 오류 발생 시 자동으로 롤백됩니다.</p>
                <pre><code class="language-php">$db = db_connect();

$db->transStart();

// 출금
$db->table('accounts')
   ->where('id', $fromId)
   ->update(['balance' => $from->balance - $amount]);

// 입금
$db->table('accounts')
   ->where('id', $toId)
   ->update(['balance' => $to->balance + $amount]);

$db->transComplete();

// 트랜잭션 성공 여부 확인
if ($db->transStatus() === false) {
    // 실패 처리
}
</code></pre>
                <div class="result-box info mt-3">
                    <strong>transStatus()</strong> — <code>transComplete()</code> 호출 후 <code>false</code>면 내부 오류로 롤백된 것입니다.
                </div>
            </div>
        </div>
    </div>

    <!-- ── 수동 롤백 ───────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-rollback">
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-arrow-counterclockwise text-warning"></i>
                <h5>transRollback — 수동 롤백</h5>
            </div>
            <div class="example-card-body">
                <p class="mb-3">비즈니스 로직 조건(잔액 부족 등)으로 직접 롤백이 필요할 때 <code>transRollback()</code>을 호출합니다.</p>
                <pre><code class="language-php">$db->transStart();

$from = $db->table('accounts')->where('id', $fromId)->get()->getRowObject();

// 잔액 부족 → 수동 롤백
if ($from->balance < $amount) {
    $db->transRollback();
    return redirect()->back()->with('error', '잔액이 부족합니다.');
}

$db->table('accounts')->where('id', $fromId)
   ->update(['balance' => $from->balance - $amount]);

$db->table('accounts')->where('id', $toId)
   ->update(['balance' => $to->balance + $amount]);

$db->transComplete();
</code></pre>
                <div class="result-box warning mt-3">
                    <code>transRollback()</code> 호출 후에는 이후 쿼리가 트랜잭션 밖에서 실행될 수 있으니 즉시 함수를 종료하세요.
                </div>
            </div>
        </div>
    </div>

    <!-- ── 예외 기반 롤백 ─────────────────────────────── -->
    <div class="tab-pane fade" id="tab-exception">
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-exclamation-triangle text-danger"></i>
                <h5>try/catch + transRollback — 예외 기반 롤백</h5>
            </div>
            <div class="example-card-body">
                <p class="mb-3">외부 서비스 호출이나 예측 불가한 오류를 대비한 패턴입니다. <code>try/catch</code>로 예외를 잡아 명시적으로 롤백합니다.</p>
                <pre><code class="language-php">$db->transStart();

try {
    $db->table('accounts')->where('id', $fromId)
       ->update(['balance' => $from->balance - $amount]);

    // 외부 API 호출 등 오류 발생 가능한 작업
    externalApiCall(); // RuntimeException 발생

    $db->table('accounts')->where('id', $toId)
       ->update(['balance' => $to->balance + $amount]);

    $db->transComplete(); // 정상 완료 시 커밋

} catch (\RuntimeException $e) {
    $db->transRollback(); // 예외 발생 시 전체 롤백
    log_message('error', '이체 실패: ' . $e->getMessage());
    return redirect()->back()->with('error', '처리 중 오류가 발생했습니다.');
}
</code></pre>
                <div class="result-box danger mt-3">
                    <strong>출금 후 예외 발생 시나리오</strong><br>
                    출금 쿼리는 실행됐지만 예외 → <code>transRollback()</code> → 출금도 취소됩니다. DB는 트랜잭션 시작 전 상태로 복원됩니다.
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
