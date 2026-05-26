<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">이벤트 시스템</li>
    </ol></nav>
    <h1><i class="bi bi-bell me-2"></i>이벤트 시스템</h1>
    <p>CI4의 Events 클래스로 이벤트를 발행하고 구독하는 방법을 알아봅니다.</p>
</div>

<?php $tab = $tab ?? 'demo'; ?>
<ul class="nav nav-tabs mb-3" id="eventTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'demo' ? 'active' : '' ?>" href="#" onclick="showTab('demo');return false;">이벤트 데모</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- 이벤트 데모 -->
<div id="tab-demo" class="tab-content-pane" style="display:<?= $tab === 'demo' ? 'block' : 'none' ?>">

    <div class="example-card mb-3">
        <div class="example-card-header"><h5><i class="bi bi-play-circle me-2"></i>시나리오 선택 & 실행</h5></div>
        <div class="example-card-body">
            <form method="post" action="<?= base_url('examples/events/trigger') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">시나리오</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check">
                            <input type="radio" name="scenario" value="basic" id="s1" class="form-check-input"
                                <?= ($scenario ?? 'basic') === 'basic' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="s1">
                                <strong>기본</strong> — 복수 리스너
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="scenario" value="priority" id="s2" class="form-check-input"
                                <?= ($scenario ?? '') === 'priority' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="s2">
                                <strong>우선순위</strong> — 실행 순서 제어
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="scenario" value="halt" id="s3" class="form-check-input"
                                <?= ($scenario ?? '') === 'halt' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="s3">
                                <strong>중단</strong> — false 반환으로 체인 중지
                            </label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-lightning"></i> 이벤트 발생 (trigger)
                </button>
            </form>
        </div>
    </div>

    <?php if (isset($log)): ?>
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-terminal me-2"></i>이벤트 실행 로그</h5></div>
        <div class="example-card-body">
            <?php if (empty($log)): ?>
                <p class="text-muted mb-0">실행된 리스너 없음</p>
            <?php else: ?>
                <div style="background:#0d1117; border-radius:8px; padding:1rem;">
                    <?php foreach ($log as $i => $entry): ?>
                    <div style="color:#e6e6e6; font-family:monospace; font-size:.85rem; padding:.15rem 0;">
                        <span style="color:#6c757d;"><?= $i + 1 ?>.</span>
                        <?= esc($entry) ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>이벤트 리스너 등록 & 발생</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">use CodeIgniter\Events\Events;

// 리스너 등록 (클로저)
Events::on('user_registered', function(array $user) {
    // 이메일 발송, 로그 기록 등
    log_message('info', '신규 가입: ' . $user['email']);
});

// 이벤트 발생 (데이터 전달)
Events::trigger('user_registered', ['email' => 'user@example.com', 'name' => '홍길동']);

// 여러 인자 전달은 배열로 — 리스너가 배열을 받음
Events::trigger('my_event', $arg1, $arg2);</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>우선순위 & 실행 중단</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 우선순위 상수 (낮은 숫자 = 먼저 실행)
// EVENT_PRIORITY_HIGH = 10
// EVENT_PRIORITY_NORMAL = 100 (기본값)
// EVENT_PRIORITY_LOW = 200

Events::on('my_event', $listener1, EVENT_PRIORITY_HIGH);   // 먼저
Events::on('my_event', $listener2, EVENT_PRIORITY_NORMAL); // 다음
Events::on('my_event', $listener3, EVENT_PRIORITY_LOW);    // 나중

// 리스너에서 false 반환 → 이후 리스너 실행 중단
Events::on('my_event', function() {
    return false; // 체인 중단
});

$result = Events::trigger('my_event'); // false = 중단됨</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>app/Config/Events.php에 전역 등록</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// app/Config/Events.php — 애플리케이션 시작 시 자동 등록
use CodeIgniter\Events\Events;

// 모델이 데이터를 저장할 때 발생하는 CI4 내장 이벤트
Events::on('post_model_insert', function(\App\Models\PostModel $model) {
    // 캐시 무효화, 알림 발송 등
});

// 커스텀 이벤트 전역 리스너
Events::on('user_login', [App\Listeners\UserLoginListener::class, 'handle']);</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#eventTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
<?= $this->endSection() ?>
