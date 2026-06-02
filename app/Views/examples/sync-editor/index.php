<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">동기화 에디터</li>
    </ol></nav>
    <h1><i class="bi bi-pencil-square me-2"></i>동기화 에디터</h1>
    <p>SSE(Server-Sent Events) + AJAX를 활용한 실시간 동기화 텍스트 에디터입니다.
       여러 탭이나 브라우저에서 동시에 열면 변경 사항이 실시간으로 반영됩니다.</p>
</div>

<ul class="nav nav-tabs mb-3" id="editorTab">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="editor">에디터</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="code">코드 설명</a></li>
</ul>

<!-- 에디터 탭 -->
<div id="tab-editor" class="tab-content-pane">

    <!-- 상태 바 -->
    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
        <span class="badge bg-secondary" id="versionBadge">v<?= esc($version) ?></span>
        <span id="syncStatus" class="badge bg-success">
            <i class="bi bi-broadcast me-1"></i>연결 중...
        </span>
        <span id="editingStatus" class="text-muted small"></span>
        <span class="ms-auto text-muted small" id="lastSaved"></span>
    </div>

    <!-- 충돌 경고 -->
    <div id="conflictAlert" class="alert alert-warning d-none py-2 mb-2">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>다른 사용자가 편집했습니다.</strong>
        <button class="btn btn-sm btn-warning ms-2" id="applyRemote">최신 내용으로 덮어쓰기</button>
        <button class="btn btn-sm btn-outline-secondary ms-1" id="keepLocal">내 내용 유지 후 저장</button>
    </div>

    <div class="example-card">
        <div class="example-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>공유 문서</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" id="openNewTab">
                    <i class="bi bi-box-arrow-up-right me-1"></i>새 탭에서 열기
                </button>
                <button class="btn btn-sm btn-primary" id="saveBtn">
                    <i class="bi bi-floppy me-1"></i>저장 <kbd class="bg-white text-dark">Ctrl+S</kbd>
                </button>
            </div>
        </div>
        <div class="example-card-body p-0">
            <textarea id="editor"
                      class="form-control border-0 rounded-0 rounded-bottom font-monospace"
                      style="min-height:380px;resize:vertical;font-size:0.9rem;line-height:1.6;"
                      spellcheck="false"><?= esc($content) ?></textarea>
        </div>
    </div>

    <div class="result-box info mt-3">
        <i class="bi bi-info-circle me-2"></i>
        <strong>동작 방법:</strong>
        위 "새 탭에서 열기" 버튼을 클릭해 두 탭을 나란히 열고, 한쪽에서 텍스트를 수정하면
        약 2초 후 다른 탭에 자동으로 반영됩니다. 두 탭이 동시에 편집하면 충돌 경고가 표시됩니다.
    </div>
</div>

<!-- 코드 설명 탭 -->
<div id="tab-code" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>전체 동작 흐름</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <strong>SSE(서버→클라이언트)</strong> + <strong>AJAX(클라이언트→서버)</strong> 조합으로 양방향 실시간 동기화를 구현합니다.
            </div>
            <pre><code class="language-text">1. 페이지 로드  → SSE 연결 수립 (GET /sync-editor/stream)
2. 사용자 입력  → 1초 디바운스 후 자동 저장 (POST /sync-editor/save)
3. 서버 저장    → version++ 후 DB 업데이트
4. SSE 루프    → 2초마다 DB version 확인
5. version 변경 감지 → SSE 이벤트 전송 (event: update)
6. 다른 클라이언트 → SSE 수신 후 에디터 업데이트</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>Controller — save() 및 stream()</h5></div>
        <div class="example-card-body">
<pre><code class="language-php">// 저장: version 증가 후 DB 업데이트
public function save(): ResponseInterface
{
    $content  = $this->request->getPost('content') ?? '';
    $clientId = $this->request->getPost('client_id') ?? '';
    $doc      = db_connect()->table('sync_docs')->limit(1)->get()->getRowArray();

    db_connect()->table('sync_docs')->where('id', $doc['id'])->update([
        'content'    => $content,
        'version'    => (int) $doc['version'] + 1,
        'client_id'  => $clientId,
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    return $this->response->setJSON(['version' => $doc['version'] + 1]);
}

// SSE 스트림: version 변경 시 전체 내용 push
public function stream(): void
{
    header('Content-Type: text/event-stream; charset=UTF-8');
    $lastVersion = (int) ($this->request->getServer('HTTP_LAST_EVENT_ID') ?? 0);

    for ($i = 0; $i < 120; $i++) {
        if (connection_aborted()) break;
        $doc = $this->getDoc();
        if ((int) $doc['version'] > $lastVersion) {
            $lastVersion = (int) $doc['version'];
            echo "id: {$lastVersion}\n";
            echo "event: update\n";
            echo 'data: ' . json_encode($doc) . "\n\n";
            flush();
        }
        sleep(2);
    }
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>JavaScript — SSE 수신 및 충돌 처리</h5></div>
        <div class="example-card-body">
<pre><code class="language-js">const es = new EventSource('/examples/sync-editor/stream');

es.addEventListener('update', e => {
    const data = JSON.parse(e.data);

    // 내가 보낸 저장이면 무시
    if (data.client_id === clientId) return;

    if (isEditing) {
        // 편집 중 → 충돌 경고
        pendingRemote = data;
        showConflict(data);
    } else {
        // 편집 중 아님 → 즉시 반영
        applyRemoteContent(data);
    }
});

// 자동 저장: 1초 디바운스
editor.addEventListener('input', () => {
    isEditing = true;
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(saveContent, 1000);
});</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>충돌 처리 전략</h5></div>
        <div class="example-card-body">
            <table class="table table-bordered table-sm">
                <thead class="table-dark"><tr><th>상황</th><th>동작</th></tr></thead>
                <tbody>
                    <tr><td>내가 편집 중이 아닐 때 원격 변경</td><td>즉시 에디터에 반영</td></tr>
                    <tr><td>내가 편집 중일 때 원격 변경</td><td>충돌 경고 표시, 사용자 선택 대기</td></tr>
                    <tr><td>"최신 내용으로 덮어쓰기"</td><td>원격 내용으로 에디터 교체</td></tr>
                    <tr><td>"내 내용 유지 후 저장"</td><td>현재 에디터 내용으로 즉시 저장 (version 재덮기)</td></tr>
                    <tr><td>내가 저장한 내용의 SSE 수신</td><td><code>client_id</code> 비교로 자기 이벤트 무시</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">5</span><h5>Last-Event-ID로 재연결 처리</h5></div>
        <div class="example-card-body">
<pre><code class="language-js">// SSE 연결이 끊기면 브라우저가 자동 재연결
// Last-Event-ID 헤더로 마지막으로 받은 version을 서버에 전달
// → 서버는 해당 version 이후 변경분만 전송

// 서버 측: id: {version} 을 매 이벤트에 포함시키는 것이 핵심
echo "id: {$version}\n";
echo "event: update\n";
echo "data: " . json_encode($data) . "\n\n";</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
// ─── 탭 전환 ────────────────────────────────────────────
document.querySelectorAll('#editorTab .nav-link').forEach(el => {
    el.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('#editorTab .nav-link').forEach(n => n.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.tab-content-pane').forEach(p => p.style.display = 'none');
        document.getElementById('tab-' + el.dataset.tab).style.display = 'block';
    });
});

// ─── CSRF ────────────────────────────────────────────────
const CSRF_TOKEN = '<?= csrf_token() ?>';
let   csrfHash   = '<?= csrf_hash() ?>';

// ─── 상태 ────────────────────────────────────────────────
const editor        = document.getElementById('editor');
const versionBadge  = document.getElementById('versionBadge');
const syncStatus    = document.getElementById('syncStatus');
const editingStatus = document.getElementById('editingStatus');
const lastSavedEl   = document.getElementById('lastSaved');
const conflictAlert = document.getElementById('conflictAlert');

// 이 탭의 고유 ID (sessionStorage에 유지)
let clientId = sessionStorage.getItem('syncEditorClientId');
if (! clientId) {
    clientId = Math.random().toString(36).slice(2, 10);
    sessionStorage.setItem('syncEditorClientId', clientId);
}

let currentVersion = <?= (int) $version ?>;
let isEditing      = false;
let editingTimer   = null;
let autoSaveTimer  = null;
let pendingRemote  = null;
let isSaving       = false;

// ─── SSE 연결 ────────────────────────────────────────────
function connectSSE() {
    const es = new EventSource('<?= base_url('examples/sync-editor/stream') ?>', { withCredentials: false });

    es.onopen = () => {
        syncStatus.className = 'badge bg-success';
        syncStatus.innerHTML = '<i class="bi bi-broadcast me-1"></i>실시간 연결됨';
    };

    es.onerror = () => {
        syncStatus.className = 'badge bg-danger';
        syncStatus.innerHTML = '<i class="bi bi-broadcast-pin me-1"></i>재연결 중...';
    };

    es.addEventListener('update', e => {
        const data = JSON.parse(e.data);

        // 내가 저장한 결과면 무시
        if (data.client_id === clientId) {
            currentVersion = data.version;
            versionBadge.textContent = 'v' + data.version;
            return;
        }

        if (data.version <= currentVersion) return;

        if (isEditing) {
            pendingRemote = data;
            showConflict(data);
        } else {
            applyRemoteContent(data);
        }
    });

    es.addEventListener('reconnect', () => {
        es.close();
        setTimeout(connectSSE, 1000);
    });
}

connectSSE();

// ─── 원격 내용 적용 ──────────────────────────────────────
function applyRemoteContent(data) {
    editor.value     = data.content;
    currentVersion   = data.version;
    versionBadge.textContent = 'v' + data.version;
    lastSavedEl.textContent  = formatTime(data.updated_at) + ' · 원격 동기화됨';
    conflictAlert.classList.add('d-none');
    pendingRemote = null;
    flashEditor('success');
}

// ─── 충돌 표시 ───────────────────────────────────────────
function showConflict(data) {
    conflictAlert.classList.remove('d-none');
    editingStatus.innerHTML = `<span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>충돌 감지</span>`;
}

document.getElementById('applyRemote').addEventListener('click', () => {
    if (pendingRemote) applyRemoteContent(pendingRemote);
});

document.getElementById('keepLocal').addEventListener('click', () => {
    conflictAlert.classList.add('d-none');
    saveContent(true);
});

// ─── 저장 ────────────────────────────────────────────────
async function saveContent(force = false) {
    if (isSaving && ! force) return;
    isSaving = true;

    const content = editor.value;
    lastSavedEl.textContent = '저장 중...';

    try {
        const form = new URLSearchParams({ content, client_id: clientId });
        form.append(CSRF_TOKEN, csrfHash);

        const res  = await fetch('<?= base_url('examples/sync-editor/save') ?>', {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body:    form,
        });
        const json = await res.json();
        if (json.csrf_hash) csrfHash = json.csrf_hash;

        if (json.success) {
            currentVersion = json.version;
            versionBadge.textContent = 'v' + json.version;
            lastSavedEl.textContent  = formatTime(json.updated_at) + ' · 저장됨';
            isEditing = false;
            editingStatus.textContent = '';
            conflictAlert.classList.add('d-none');
        }
    } catch {
        lastSavedEl.textContent = '저장 실패';
    } finally {
        isSaving = false;
    }
}

// ─── 에디터 입력 이벤트 ──────────────────────────────────
editor.addEventListener('input', () => {
    isEditing = true;
    editingStatus.innerHTML = '<i class="bi bi-pencil me-1 text-primary"></i>편집 중...';

    clearTimeout(editingTimer);
    editingTimer = setTimeout(() => { isEditing = false; editingStatus.textContent = ''; }, 3000);

    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => saveContent(), 1000);
});

// Ctrl+S 단축키
editor.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        clearTimeout(autoSaveTimer);
        saveContent();
    }
});

document.getElementById('saveBtn').addEventListener('click', () => {
    clearTimeout(autoSaveTimer);
    saveContent();
});

// ─── 새 탭 열기 ──────────────────────────────────────────
document.getElementById('openNewTab').addEventListener('click', () => {
    window.open(location.href, '_blank');
});

// ─── 유틸 ────────────────────────────────────────────────
function formatTime(str) {
    if (! str) return '';
    const d = new Date(str.replace(' ', 'T'));
    return d.toLocaleTimeString('ko-KR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

function flashEditor(type) {
    const color = type === 'success' ? '#d1fae5' : '#fef3c7';
    editor.style.transition = 'background-color 0.3s';
    editor.style.backgroundColor = color;
    setTimeout(() => { editor.style.backgroundColor = ''; }, 800);
}
</script>
<?= $this->endSection() ?>
