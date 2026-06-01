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
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-5 fw-bold">미읽음</span>
                    <span id="unread-badge" class="badge rounded-pill fs-6"
                          style="background:<?= $unreadCount > 0 ? '#dd4814' : '#6c757d' ?>;">
                        <?= $unreadCount ?>
                    </span>
                </div>
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
        <div class="example-card-body d-flex flex-wrap gap-2" id="action-buttons">
            <button class="demo-btn" onclick="createNotification(this)">
                <i class="bi bi-bell-fill"></i> 랜덤 알림 생성
            </button>
            <button class="demo-btn outline" id="btn-read-all"
                    style="<?= $unreadCount === 0 ? 'display:none;' : '' ?>"
                    onclick="readAllNotifications(this)">
                <i class="bi bi-check-all"></i> 전체 읽음 처리
            </button>
            <button class="demo-btn outline" id="btn-clear"
                    style="border-color:#6c757d;color:#6c757d;<?= empty($notifications) ? 'display:none;' : '' ?>"
                    onclick="clearNotifications(this)">
                <i class="bi bi-trash3"></i> 전체 삭제
            </button>
        </div>
    </div>

    <!-- 알림 목록 -->
    <div class="example-card">
        <div class="example-card-header">
            <h5><i class="bi bi-list-ul me-2"></i>알림 목록
                <small class="text-muted fw-normal ms-2" id="notif-count">(총 <?= count($notifications) ?>개)</small>
            </h5>
        </div>
        <div class="example-card-body p-0" id="notif-list">
            <?php if (empty($notifications)): ?>
            <div id="empty-msg" class="text-center text-muted py-5">
                <i class="bi bi-bell-slash fs-1 d-block mb-2 opacity-25"></i>
                알림이 없습니다. 위 버튼으로 알림을 생성해보세요.
            </div>
            <?php else: ?>
            <?php
            $typeConfig = [
                'info'    => ['icon' => 'info-circle-fill',          'color' => '#0d6efd', 'bg' => '#e7f0ff', 'badge' => 'primary'],
                'success' => ['icon' => 'check-circle-fill',         'color' => '#198754', 'bg' => '#e8f5e9', 'badge' => 'success'],
                'warning' => ['icon' => 'exclamation-triangle-fill', 'color' => '#fd7e14', 'bg' => '#fff3e0', 'badge' => 'warning'],
                'error'   => ['icon' => 'x-circle-fill',             'color' => '#dc3545', 'bg' => '#fdecea', 'badge' => 'danger'],
            ];
            foreach ($notifications as $n):
                $cfg = $typeConfig[$n['type']] ?? $typeConfig['info'];
            ?>
            <div class="notif-row d-flex align-items-start gap-3 px-4 py-3 border-bottom"
                 data-id="<?= $n['id'] ?>"
                 data-read="<?= $n['is_read'] ?>"
                 style="background:<?= $n['is_read'] ? '#fff' : '#fffaf8' ?>;<?= $n['is_read'] ? 'opacity:.7;' : '' ?>">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:40px;height:40px;background:<?= $cfg['bg'] ?>;">
                    <i class="bi bi-<?= $cfg['icon'] ?>" style="color:<?= $cfg['color'] ?>;font-size:1.2rem;"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <?php if (!$n['is_read']): ?>
                        <span class="unread-dot rounded-circle d-inline-block"
                              style="width:8px;height:8px;background:var(--ci-red);flex-shrink:0;"></span>
                        <?php endif; ?>
                        <strong class="notif-title <?= $n['is_read'] ? 'text-muted fw-normal' : '' ?>"><?= esc($n['title']) ?></strong>
                        <span class="badge bg-<?= $cfg['badge'] ?> bg-opacity-10 text-<?= $cfg['badge'] ?> border border-<?= $cfg['badge'] ?> border-opacity-25"
                              style="font-size:.7rem;"><?= esc($n['type']) ?></span>
                    </div>
                    <p class="mb-1 small notif-message <?= $n['is_read'] ? 'text-muted' : '' ?>"><?= esc($n['message']) ?></p>
                    <small class="text-muted"><?= esc($n['created_at']) ?></small>
                </div>
                <div class="flex-shrink-0 read-action" style="padding-top:.1rem;">
                    <?php if (!$n['is_read']): ?>
                    <button class="btn btn-sm btn-outline-secondary" title="읽음 처리"
                            onclick="markRead(<?= $n['id'] ?>, this.closest('.notif-row'))">
                        <i class="bi bi-check2"></i>
                    </button>
                    <?php else: ?>
                    <span class="text-muted small"><i class="bi bi-check2-all"></i></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 코드 설명 탭 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>AJAX 공통 헬퍼 — CSRF 자동 갱신</h5></div>
        <div class="example-card-body">
            <pre><code class="language-javascript">let csrfHash = '<?= csrf_hash() ?>';

async function ajaxPost(url) {
    const form = new FormData();
    form.append('<?= csrf_token() ?>', csrfHash);

    const res  = await fetch(url, { method: 'POST', body: form });
    const data = await res.json();

    // CI4는 응답 JSON에 새 CSRF 해시를 포함하지 않으므로
    // 각 요청 후 갱신이 필요한 경우 별도 처리
    return data;
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>알림 생성 — AJAX + DOM 추가</h5></div>
        <div class="example-card-body">
            <pre><code class="language-javascript">async function createNotification() {
    const data = await ajaxPost('/examples/notification/create');
    if (!data.success) return;

    // 새 알림 행을 목록 맨 위에 추가
    const row = renderNotifRow(data.notification);
    row.style.opacity = '0';
    list.prepend(row);
    requestAnimationFrame(() => row.style.opacity = '1');

    updateBadge(data.unreadCount);
    updateCount(+1);
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>단건 읽음 처리 — DOM 업데이트</h5></div>
        <div class="example-card-body">
            <pre><code class="language-javascript">async function markRead(id, rowEl) {
    const data = await ajaxPost(`/examples/notification/read/${id}`);
    if (!data.success) return;

    // 행 스타일을 "읽음" 상태로 변경
    rowEl.style.background = '#fff';
    rowEl.style.opacity    = '.7';
    rowEl.dataset.read     = '1';
    rowEl.querySelector('.unread-dot')?.remove();
    rowEl.querySelector('.notif-title')?.classList.add('text-muted', 'fw-normal');
    rowEl.querySelector('.notif-message')?.classList.add('text-muted');
    rowEl.querySelector('.read-action').innerHTML =
        '&lt;span class="text-muted small"&gt;&lt;i class="bi bi-check2-all"&gt;&lt;/i&gt;&lt;/span&gt;';

    updateBadge(data.unreadCount);
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>CI4 컨트롤러 — JSON 응답</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// redirect() 대신 JSON 응답 반환
public function create(): Response
{
    $this->model->insert($data);
    return $this->response->setJSON([
        'success'      => true,
        'notification' => $this->model->find($id),
        'unreadCount'  => $this->model->countUnread(),
    ]);
}

public function read(int $id): Response
{
    $this->model->markRead($id);
    return $this->response->setJSON([
        'success'    => true,
        'unreadCount' => $this->model->countUnread(),
    ]);
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">5</span><h5>SSE — 실시간 미읽음 배지</h5></div>
        <div class="example-card-body">
            <pre><code class="language-javascript">const es = new EventSource('/examples/notification/stream');

es.addEventListener('notification', e => {
    const { unread } = JSON.parse(e.data);
    updateBadge(unread);
});

function updateBadge(count) {
    const badge = document.getElementById('unread-badge');
    badge.textContent      = count;
    badge.style.background = count > 0 ? '#dd4814' : '#6c757d';
}</code></pre>
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

// ── CSRF ─────────────────────────────────────────────────
const CSRF_TOKEN = '<?= csrf_token() ?>';
let   csrfHash   = '<?= csrf_hash() ?>';

async function ajaxPost(url) {
    const form = new FormData();
    form.append(CSRF_TOKEN, csrfHash);
    const res  = await fetch(url, { method: 'POST', body: form });
    const data = await res.json();
    if (data.csrf_hash) csrfHash = data.csrf_hash;
    return data;
}

// ── 알림 생성 ─────────────────────────────────────────────
async function createNotification(btn) {
    btn.disabled = true;
    const data = await ajaxPost('<?= base_url('examples/notification/create') ?>');
    btn.disabled = false;
    if (!data.success) return;

    const list = document.getElementById('notif-list');
    const empty = document.getElementById('empty-msg');
    if (empty) empty.remove();

    const row = buildRow(data.notification);
    row.style.transition = 'opacity .3s';
    row.style.opacity    = '0';
    list.prepend(row);
    requestAnimationFrame(() => { row.style.opacity = '1'; });

    updateBadge(data.unreadCount);
    updateCount(+1);
    document.getElementById('btn-clear').style.display = '';
    document.getElementById('btn-read-all').style.display = '';
}

// ── 단건 읽음 ─────────────────────────────────────────────
async function markRead(id, rowEl) {
    const data = await ajaxPost('<?= base_url('examples/notification/read/') ?>' + id);
    if (!data.success) return;

    rowEl.style.background = '#fff';
    rowEl.style.opacity    = '.7';
    rowEl.dataset.read     = '1';
    rowEl.querySelector('.unread-dot')?.remove();
    rowEl.querySelector('.notif-title')?.classList.add('text-muted', 'fw-normal');
    rowEl.querySelector('.notif-message')?.classList.add('text-muted');
    rowEl.querySelector('.read-action').innerHTML =
        '<span class="text-muted small"><i class="bi bi-check2-all"></i></span>';

    updateBadge(data.unreadCount);
    if (data.unreadCount === 0) document.getElementById('btn-read-all').style.display = 'none';
}

// ── 전체 읽음 ─────────────────────────────────────────────
async function readAllNotifications(btn) {
    btn.disabled = true;
    const data = await ajaxPost('<?= base_url('examples/notification/read-all') ?>');
    btn.disabled = false;
    if (!data.success) return;

    document.querySelectorAll('.notif-row[data-read="0"]').forEach(row => {
        row.style.background = '#fff';
        row.style.opacity    = '.7';
        row.dataset.read     = '1';
        row.querySelector('.unread-dot')?.remove();
        row.querySelector('.notif-title')?.classList.add('text-muted', 'fw-normal');
        row.querySelector('.notif-message')?.classList.add('text-muted');
        row.querySelector('.read-action').innerHTML =
            '<span class="text-muted small"><i class="bi bi-check2-all"></i></span>';
    });

    updateBadge(0);
    btn.style.display = 'none';
}

// ── 전체 삭제 ─────────────────────────────────────────────
async function clearNotifications(btn) {
    if (!confirm('알림을 모두 삭제하시겠습니까?')) return;
    btn.disabled = true;
    const data = await ajaxPost('<?= base_url('examples/notification/clear') ?>');
    btn.disabled = false;
    if (!data.success) return;

    document.getElementById('notif-list').innerHTML =
        '<div id="empty-msg" class="text-center text-muted py-5">' +
        '<i class="bi bi-bell-slash fs-1 d-block mb-2 opacity-25"></i>' +
        '알림이 없습니다. 위 버튼으로 알림을 생성해보세요.</div>';

    updateBadge(0);
    document.getElementById('notif-count').textContent = '(총 0개)';
    document.getElementById('btn-read-all').style.display = 'none';
    btn.style.display = 'none';
}

// ── 공통 유틸 ─────────────────────────────────────────────
function updateBadge(count) {
    const badge = document.getElementById('unread-badge');
    badge.textContent      = count;
    badge.style.background = count > 0 ? '#dd4814' : '#6c757d';
}

function updateCount(delta) {
    const el  = document.getElementById('notif-count');
    const cur = parseInt(el.textContent.replace(/\D/g, '')) || 0;
    el.textContent = `(총 ${cur + delta}개)`;
}

const TYPE_CFG = {
    info:    { icon: 'info-circle-fill',          color: '#0d6efd', bg: '#e7f0ff', badge: 'primary' },
    success: { icon: 'check-circle-fill',         color: '#198754', bg: '#e8f5e9', badge: 'success' },
    warning: { icon: 'exclamation-triangle-fill', color: '#fd7e14', bg: '#fff3e0', badge: 'warning' },
    error:   { icon: 'x-circle-fill',             color: '#dc3545', bg: '#fdecea', badge: 'danger'  },
};

function buildRow(n) {
    const cfg  = TYPE_CFG[n.type] ?? TYPE_CFG.info;
    const div  = document.createElement('div');
    div.className = 'notif-row d-flex align-items-start gap-3 px-4 py-3 border-bottom';
    div.dataset.id   = n.id;
    div.dataset.read = '0';
    div.style.background = '#fffaf8';
    div.innerHTML = `
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:40px;height:40px;background:${cfg.bg};">
            <i class="bi bi-${cfg.icon}" style="color:${cfg.color};font-size:1.2rem;"></i>
        </div>
        <div class="flex-grow-1 min-w-0">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="unread-dot rounded-circle d-inline-block"
                      style="width:8px;height:8px;background:var(--ci-red);flex-shrink:0;"></span>
                <strong class="notif-title">${esc(n.title)}</strong>
                <span class="badge bg-${cfg.badge} bg-opacity-10 text-${cfg.badge} border border-${cfg.badge} border-opacity-25"
                      style="font-size:.7rem;">${esc(n.type)}</span>
            </div>
            <p class="mb-1 small notif-message">${esc(n.message)}</p>
            <small class="text-muted">${esc(n.created_at ?? '')}</small>
        </div>
        <div class="flex-shrink-0 read-action" style="padding-top:.1rem;">
            <button class="btn btn-sm btn-outline-secondary" title="읽음 처리"
                    onclick="markRead(${n.id}, this.closest('.notif-row'))">
                <i class="bi bi-check2"></i>
            </button>
        </div>`;
    return div;
}

function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── SSE ───────────────────────────────────────────────────
let es = null;

function toggleSse() {
    if (es) {
        es.close(); es = null;
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
        updateBadge(unread);
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
    dot.style.background = connected ? '#198754' : '#adb5bd';
    status.textContent   = connected ? 'SSE 연결됨' : 'SSE 연결 끊김';
    status.style.color   = connected ? '#198754'   : '';
}
</script>
<?= $this->endSection() ?>
