<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">알림 시스템</li>
    </ol></nav>
    <h1><i class="bi bi-bell me-2"></i>알림 시스템</h1>
    <p>DB 기반 알림 저장·읽음 처리와 SSE 실시간 배지 카운트 업데이트를 구현합니다.</p>
</div>

<?php $tab = $tab ?? 'demo'; ?>
<ul class="nav nav-tabs mb-3" id="notifTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'demo' ? 'active' : '' ?>" href="#" onclick="showTab('demo');return false;">데모</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- 데모 탭 -->
<div id="tab-demo" class="tab-content-pane" style="display:<?= $tab === 'demo' ? 'block' : 'none' ?>">

    <!-- 상태 바 -->
    <div class="example-card mb-3">
        <div class="example-card-body">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <!-- 미읽음 배지 -->
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-5 fw-bold">미읽음</span>
                    <span id="unread-badge" class="badge rounded-pill fs-6"
                          style="background:<?= $unreadCount > 0 ? '#dd4814' : '#6c757d' ?>;">
                        <?= $unreadCount ?>
                    </span>
                </div>

                <!-- SSE 연결 상태 -->
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <span id="sse-dot" class="rounded-circle d-inline-block"
                          style="width:10px;height:10px;background:#adb5bd;"></span>
                    <small id="sse-status" class="text-muted">SSE 연결 대기</small>
                    <button class="btn btn-sm btn-outline-secondary" id="btn-sse-toggle" onclick="toggleSse()">
                        <i class="bi bi-broadcast me-1"></i>실시간 연결
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 액션 버튼 -->
    <div class="example-card mb-3">
        <div class="example-card-header"><h5><i class="bi bi-plus-circle me-2"></i>알림 생성</h5></div>
        <div class="example-card-body d-flex flex-wrap gap-2">
            <form method="post" action="<?= base_url('examples/notification/create') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-bell-fill"></i> 랜덤 알림 생성
                </button>
            </form>
            <?php if ($unreadCount > 0): ?>
            <form method="post" action="<?= base_url('examples/notification/read-all') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="demo-btn outline" style="cursor:pointer;">
                    <i class="bi bi-check-all"></i> 전체 읽음 처리
                </button>
            </form>
            <?php endif; ?>
            <?php if (!empty($notifications)): ?>
            <form method="post" action="<?= base_url('examples/notification/clear') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="demo-btn outline" style="cursor:pointer;border-color:#6c757d;color:#6c757d;"
                        onclick="return confirm('알림을 모두 삭제하시겠습니까?')">
                    <i class="bi bi-trash3"></i> 전체 삭제
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- 알림 목록 -->
    <div class="example-card">
        <div class="example-card-header">
            <h5><i class="bi bi-list-ul me-2"></i>알림 목록
                <small class="text-muted fw-normal ms-2">(총 <?= count($notifications) ?>개)</small>
            </h5>
        </div>
        <div class="example-card-body p-0">
            <?php if (empty($notifications)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-bell-slash fs-1 d-block mb-2 opacity-25"></i>
                알림이 없습니다. 위 버튼으로 알림을 생성해보세요.
            </div>
            <?php else: ?>
            <?php
            $typeConfig = [
                'info'    => ['icon' => 'info-circle-fill',    'color' => '#0d6efd', 'bg' => '#e7f0ff', 'badge' => 'primary'],
                'success' => ['icon' => 'check-circle-fill',   'color' => '#198754', 'bg' => '#e8f5e9', 'badge' => 'success'],
                'warning' => ['icon' => 'exclamation-triangle-fill', 'color' => '#fd7e14', 'bg' => '#fff3e0', 'badge' => 'warning'],
                'error'   => ['icon' => 'x-circle-fill',       'color' => '#dc3545', 'bg' => '#fdecea', 'badge' => 'danger'],
            ];
            foreach ($notifications as $n):
                $cfg = $typeConfig[$n['type']] ?? $typeConfig['info'];
            ?>
            <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom <?= $n['is_read'] ? 'opacity-60' : '' ?>"
                 style="background:<?= $n['is_read'] ? '#fff' : '#fffaf8' ?>;">
                <!-- 타입 아이콘 -->
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:40px;height:40px;background:<?= $cfg['bg'] ?>;">
                    <i class="bi bi-<?= $cfg['icon'] ?>" style="color:<?= $cfg['color'] ?>;font-size:1.2rem;"></i>
                </div>
                <!-- 내용 -->
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <?php if (!$n['is_read']): ?>
                        <span class="rounded-circle d-inline-block"
                              style="width:8px;height:8px;background:var(--ci-red);flex-shrink:0;"></span>
                        <?php endif; ?>
                        <strong class="<?= $n['is_read'] ? 'text-muted fw-normal' : '' ?>"><?= esc($n['title']) ?></strong>
                        <span class="badge bg-<?= $cfg['badge'] ?> bg-opacity-10 text-<?= $cfg['badge'] ?> border border-<?= $cfg['badge'] ?> border-opacity-25" style="font-size:.7rem;"><?= esc($n['type']) ?></span>
                    </div>
                    <p class="mb-1 small <?= $n['is_read'] ? 'text-muted' : '' ?>"><?= esc($n['message']) ?></p>
                    <small class="text-muted"><?= esc($n['created_at']) ?></small>
                </div>
                <!-- 읽음 처리 버튼 -->
                <?php if (!$n['is_read']): ?>
                <form method="post" action="<?= base_url('examples/notification/read/' . $n['id']) ?>" class="flex-shrink-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="읽음 처리">
                        <i class="bi bi-check2"></i>
                    </button>
                </form>
                <?php else: ?>
                <span class="flex-shrink-0 text-muted small" style="padding-top:.25rem;">
                    <i class="bi bi-check2-all"></i>
                </span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 코드 설명 탭 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>마이그레이션</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// app/Database/Migrations/CreateNotificationsTable.php
$this->forge->addField([
    'id'         => ['type' => 'INTEGER', 'auto_increment' => true],
    'type'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'info'],
    'title'      => ['type' => 'VARCHAR', 'constraint' => 200],
    'message'    => ['type' => 'TEXT'],
    'is_read'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
    'created_at' => ['type' => 'DATETIME', 'null' => true],
]);
$this->forge->createTable('notifications');</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>Model — 읽음 처리 메서드</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">class NotificationModel extends Model
{
    protected $table         = 'notifications';
    protected $allowedFields = ['type', 'title', 'message', 'is_read'];
    protected $useTimestamps = true;
    protected $updatedField  = '';   // updated_at 없음

    public function countUnread(): int
    {
        return $this->where('is_read', 0)->countAllResults();
    }

    public function markRead(int $id): void
    {
        $this->update($id, ['is_read' => 1]);
    }

    public function markAllRead(): void
    {
        $this->where('is_read', 0)->set(['is_read' => 1])->update();
    }
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>SSE 스트림 — 실시간 미읽음 카운트</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">public function stream(): void
{
    while (ob_get_level() > 0) ob_end_clean();
    ob_implicit_flush(true);

    header('Content-Type: text/event-stream; charset=UTF-8');
    header('Cache-Control: no-cache');
    header('X-Accel-Buffering: no');

    echo ": ping\n\n"; flush();

    for ($i = 0; $i &lt; 60; $i++) {
        if (connection_aborted()) break;

        $unread = $this->model->countUnread();
        echo "event: notification\n";
        echo 'data: ' . json_encode(['unread' => $unread]) . "\n\n";
        flush();

        sleep(3);
    }
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>클라이언트 — EventSource로 배지 업데이트</h5></div>
        <div class="example-card-body">
            <pre><code class="language-javascript">const es = new EventSource('/examples/notification/stream');

es.addEventListener('notification', e => {
    const { unread } = JSON.parse(e.data);
    const badge = document.getElementById('unread-badge');
    badge.textContent = unread;
    badge.style.background = unread > 0 ? '#dd4814' : '#6c757d';
});

es.addEventListener('reconnect', () => es.close());

es.onerror = () => {
    // EventSource가 자동 재연결 시도함
};</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">5</span><h5>알림 타입별 스타일 패턴</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 알림 타입: info | success | warning | error
$this->model->insert([
    'type'    => 'warning',
    'title'   => '시스템 점검 예정',
    'message' => '내일 새벽 2~4시 정기 점검이 예정되어 있습니다.',
]);

// 미읽음 수 조회
$count = $this->model->countUnread();

// 단건 읽음 처리
$this->model->markRead($id);

// 전체 읽음 처리
$this->model->markAllRead();</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#notifTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}

let es = null;

function toggleSse() {
    if (es) {
        es.close();
        es = null;
        setSseStatus(false);
        document.getElementById('btn-sse-toggle').innerHTML = '<i class="bi bi-broadcast me-1"></i>실시간 연결';
    } else {
        startSse();
        document.getElementById('btn-sse-toggle').innerHTML = '<i class="bi bi-broadcast-pin me-1"></i>연결 끊기';
    }
}

function startSse() {
    es = new EventSource('<?= base_url('examples/notification/stream') ?>');

    es.onopen = () => setSseStatus(true);

    es.addEventListener('notification', e => {
        const { unread } = JSON.parse(e.data);
        const badge = document.getElementById('unread-badge');
        badge.textContent = unread;
        badge.style.background = unread > 0 ? '#dd4814' : '#6c757d';
    });

    es.addEventListener('reconnect', () => {
        es.close(); es = null;
        setSseStatus(false);
        document.getElementById('btn-sse-toggle').innerHTML = '<i class="bi bi-broadcast me-1"></i>실시간 연결';
    });

    es.onerror = () => setSseStatus(false);
}

function setSseStatus(connected) {
    const dot    = document.getElementById('sse-dot');
    const status = document.getElementById('sse-status');
    dot.style.background    = connected ? '#198754' : '#adb5bd';
    status.textContent      = connected ? 'SSE 연결됨' : 'SSE 연결 끊김';
    status.style.color      = connected ? '#198754'   : '';
}
</script>
<?= $this->endSection() ?>
