<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">실시간 채팅</li>
    </ol></nav>
    <h1><i class="bi bi-chat-dots me-2"></i>실시간 채팅</h1>
    <p>SSE(Server-Sent Events) + AJAX로 구현한 실시간 채팅입니다.
       여러 탭이나 브라우저에서 동시에 열어 채팅해보세요.</p>
</div>

<ul class="nav nav-tabs mb-3" id="chatTab">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="chat">채팅</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="code">코드 설명</a></li>
</ul>

<!-- 채팅 탭 -->
<div id="tab-chat" class="tab-content-pane">
    <div class="example-card">
        <div class="example-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <h5 class="mb-0"><i class="bi bi-chat-dots me-2"></i>채팅방</h5>
                <span id="connStatus" class="badge bg-secondary">
                    <i class="bi bi-broadcast me-1"></i>연결 중...
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary" id="openNewTab">
                    <i class="bi bi-box-arrow-up-right me-1"></i>새 탭
                </button>
                <button class="btn btn-sm btn-outline-danger" id="clearBtn">
                    <i class="bi bi-trash me-1"></i>전체 삭제
                </button>
            </div>
        </div>
        <div class="example-card-body p-0">

            <!-- 닉네임 설정 -->
            <div class="p-3 border-bottom bg-light d-flex align-items-center gap-2">
                <label class="text-muted small mb-0 text-nowrap">내 닉네임:</label>
                <input type="text" id="nicknameInput" class="form-control form-control-sm"
                       style="max-width:180px;" maxlength="32" placeholder="닉네임 입력">
                <button class="btn btn-sm btn-outline-primary" id="changeNickBtn">변경</button>
                <span id="myNickBadge" class="badge bg-primary ms-1"></span>
            </div>

            <!-- 봇 설정 -->
            <div class="p-3 border-bottom bg-light d-flex flex-wrap align-items-center gap-2">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="botToggle">
                    <label class="form-check-label text-muted small fw-semibold" for="botToggle">
                        <i class="bi bi-robot me-1"></i>AI 봇 자동응답
                    </label>
                </div>
                <?php if (env('GROQ_API_KEY')): ?>
                <span class="badge bg-success small">
                    <i class="bi bi-check-circle me-1"></i>서버 API 키 설정됨
                </span>
                <?php else: ?>
                <div id="apiKeyArea" class="d-flex align-items-center gap-2" style="display:none;">
                    <input type="password" id="apiKeyInput" class="form-control form-control-sm"
                           style="max-width:260px;" placeholder="Groq API 키 (gsk_...)">
                    <button class="btn btn-sm btn-outline-success" id="saveApiKeyBtn">저장</button>
                    <span id="apiKeyStatus" class="small"></span>
                </div>
                <?php endif ?>
            </div>

            <!-- 메시지 목록 -->
            <div id="chatMessages"
                 style="height:420px;overflow-y:auto;padding:1rem;display:flex;flex-direction:column;gap:0.75rem;">
                <?php foreach ($messages as $msg): ?>
                    <div class="chat-msg" data-id="<?= esc($msg['id']) ?>" data-nick="<?= esc($msg['nickname']) ?>">
                        <div class="msg-nick text-muted small mb-1"><?= esc($msg['nickname']) ?></div>
                        <div class="d-flex align-items-end gap-1">
                            <div class="msg-bubble bg-light border rounded-3 px-3 py-2"
                                 style="max-width:75%;word-break:break-word;"><?= nl2br(esc($msg['content'])) ?></div>
                            <small class="text-muted msg-time" style="white-space:nowrap;">
                                <?= date('H:i', strtotime($msg['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($messages)): ?>
                    <p class="text-center text-muted my-auto" id="emptyMsg">
                        <i class="bi bi-chat-square-dots d-block fs-2 mb-2"></i>
                        첫 메시지를 보내보세요!
                    </p>
                <?php endif; ?>
            </div>

            <!-- 입력 영역 -->
            <div class="p-3 border-top">
                <div class="input-group">
                    <textarea id="msgInput" class="form-control"
                              rows="2" style="resize:none;"
                              placeholder="메시지 입력 (Enter: 전송 / Shift+Enter: 줄바꿈)"
                              maxlength="500"></textarea>
                    <button class="btn btn-primary" id="sendBtn" style="width:72px;">
                        <i class="bi bi-send"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <small id="sendError" class="text-danger"></small>
                    <small id="charCount" class="text-muted">0 / 500</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 코드 설명 탭 -->
<div id="tab-code" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>동작 흐름</h5></div>
        <div class="example-card-body">
<pre><code class="language-text">1. 페이지 로드  → 최근 50개 메시지 렌더, SSE 연결 (GET /chat/stream)
2. 메시지 전송  → AJAX POST /chat/send → DB INSERT → JSON 응답
3. SSE 루프    → 2초마다 last_id 이후 신규 메시지 조회
4. 신규 발견   → SSE event: message 전송 (JSON 배열)
5. 클라이언트  → 메시지 버블 DOM 추가 + 자동 스크롤
6. 재연결      → Last-Event-ID 헤더로 끊긴 시점부터 이어받기</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>Controller — send() 및 stream()</h5></div>
        <div class="example-card-body">
<pre><code class="language-php">public function send(): ResponseInterface
{
    $id = db_connect()->table('chat_messages')->insert([
        'nickname'   => mb_substr($nickname, 0, 32),
        'content'    => $content,
        'created_at' => date('Y-m-d H:i:s'),
    ], true);  // true → insert ID 반환

    return $this->response->setJSON(['success' => true, 'id' => $id, ...]);
}

public function stream(): void
{
    header('Content-Type: text/event-stream; charset=UTF-8');
    $lastId = (int) ($this->request->getServer('HTTP_LAST_EVENT_ID') ?? 0);

    for ($i = 0; $i < 150; $i++) {
        if (connection_aborted()) break;

        $rows = db_connect()->table('chat_messages')
            ->where('id >', $lastId)
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        if (! empty($rows)) {
            $lastId = (int) end($rows)['id'];
            echo "id: {$lastId}\n";
            echo "event: message\n";
            echo 'data: ' . json_encode($rows) . "\n\n";
            flush();
        }
        sleep(2);
    }
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>JavaScript — SSE 수신 및 버블 렌더</h5></div>
        <div class="example-card-body">
<pre><code class="language-js">const es = new EventSource('/examples/chat/stream');

es.addEventListener('message', e => {
    const rows = JSON.parse(e.data);
    rows.forEach(row => appendMessage(row, row.nickname === myNickname));
    scrollToBottom();
});

function appendMessage(msg, isMine) {
    const wrap = document.createElement('div');
    wrap.className = 'chat-msg d-flex flex-column ' + (isMine ? 'align-items-end' : 'align-items-start');

    wrap.innerHTML = `
        ${! isMine ? `&lt;div class="msg-nick text-muted small mb-1"&gt;${escHtml(msg.nickname)}&lt;/div&gt;` : ''}
        &lt;div class="d-flex align-items-end gap-1 ${isMine ? 'flex-row-reverse' : ''}"&gt;
            &lt;div class="msg-bubble rounded-3 px-3 py-2 ${isMine ? 'bg-primary text-white' : 'bg-light border'}"
                 style="max-width:75%;word-break:break-word;"&gt;
                ${escHtml(msg.content).replace(/\n/g, '&lt;br&gt;')}
            &lt;/div&gt;
            &lt;small class="text-muted"&gt;${formatTime(msg.created_at)}&lt;/small&gt;
        &lt;/div&gt;`;

    chatMessages.appendChild(wrap);
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>SSE vs WebSocket 비교</h5></div>
        <div class="example-card-body">
            <table class="table table-bordered table-sm">
                <thead class="table-dark"><tr><th>항목</th><th>SSE</th><th>WebSocket</th></tr></thead>
                <tbody>
                    <tr><td>방향</td><td>서버 → 클라이언트 단방향</td><td>양방향</td></tr>
                    <tr><td>클라이언트→서버</td><td>별도 AJAX 필요</td><td>ws.send() 직접 전송</td></tr>
                    <tr><td>재연결</td><td>브라우저 자동 재연결</td><td>수동 구현 필요</td></tr>
                    <tr><td>CI4 지원</td><td>기본 PHP로 구현 가능</td><td>별도 서버/패키지 필요</td></tr>
                    <tr><td>적합한 용도</td><td>알림, 피드, 모니터링, 채팅</td><td>게임, 실시간 협업</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
// ─── 탭 전환 ────────────────────────────────────────────
document.querySelectorAll('#chatTab .nav-link').forEach(el => {
    el.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('#chatTab .nav-link').forEach(n => n.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.tab-content-pane').forEach(p => p.style.display = 'none');
        document.getElementById('tab-' + el.dataset.tab).style.display = 'block';
    });
});

// ─── CSRF ────────────────────────────────────────────────
const CSRF_TOKEN = '<?= csrf_token() ?>';
let   csrfHash   = '<?= csrf_hash() ?>';

// ─── 닉네임 초기화 ──────────────────────────────────────
const ADJECTIVES = ['빠른', '느린', '용감한', '조용한', '활발한', '신중한', '재빠른', '차분한'];
const NOUNS      = ['고양이', '강아지', '토끼', '여우', '곰', '호랑이', '펭귄', '다람쥐'];
function randomNick() {
    return ADJECTIVES[Math.floor(Math.random() * ADJECTIVES.length)]
         + NOUNS[Math.floor(Math.random() * NOUNS.length)];
}

let myNickname = sessionStorage.getItem('chatNickname');
if (! myNickname) {
    myNickname = randomNick();
    sessionStorage.setItem('chatNickname', myNickname);
}

const nicknameInput = document.getElementById('nicknameInput');
const myNickBadge   = document.getElementById('myNickBadge');
nicknameInput.value     = myNickname;
myNickBadge.textContent = myNickname;

document.getElementById('changeNickBtn').addEventListener('click', () => {
    const v = nicknameInput.value.trim();
    if (! v) return;
    myNickname = v;
    sessionStorage.setItem('chatNickname', myNickname);
    myNickBadge.textContent = myNickname;
});

// ─── DOM 참조 ────────────────────────────────────────────
const chatMessages = document.getElementById('chatMessages');
const msgInput     = document.getElementById('msgInput');
const sendBtn      = document.getElementById('sendBtn');
const connStatus   = document.getElementById('connStatus');
const sendError    = document.getElementById('sendError');
const charCount    = document.getElementById('charCount');

// ─── 기존 메시지 스타일 적용 ─────────────────────────────
document.querySelectorAll('.chat-msg').forEach(el => {
    const nick = el.dataset.nick;
    const isMine = (nick === myNickname);
    el.classList.add('d-flex', 'flex-column', isMine ? 'align-items-end' : 'align-items-start');

    const bubble  = el.querySelector('.msg-bubble');
    const nickEl  = el.querySelector('.msg-nick');
    const timeEl  = el.querySelector('.msg-time');
    const wrapper = el.querySelector('.d-flex.align-items-end');

    if (isMine) {
        bubble.classList.remove('bg-light', 'border');
        bubble.classList.add('bg-primary', 'text-white');
        if (nickEl) nickEl.style.display = 'none';
        if (wrapper) wrapper.classList.add('flex-row-reverse');
    }
});

scrollToBottom(false);

// ─── SSE 연결 ────────────────────────────────────────────
let lastId = <?= (int) $lastId ?>;

function connectSSE() {
    const es = new EventSource('<?= base_url('examples/chat/stream') ?>');

    es.onopen = () => {
        connStatus.className = 'badge bg-success';
        connStatus.innerHTML = '<i class="bi bi-broadcast me-1"></i>연결됨';
    };

    es.onerror = () => {
        connStatus.className = 'badge bg-danger';
        connStatus.innerHTML = '<i class="bi bi-broadcast-pin me-1"></i>재연결 중...';
    };

    es.addEventListener('message', e => {
        const rows = JSON.parse(e.data);
        const emptyMsg = document.getElementById('emptyMsg');
        if (emptyMsg) emptyMsg.remove();

        rows.forEach(row => {
            // 내가 방금 보낸 메시지는 이미 낙관적 렌더됨 → 중복 방지
            if (document.querySelector(`[data-id="${row.id}"]`)) return;
            appendMessage(row, row.nickname === myNickname);
        });
        scrollToBottom();
    });

    es.addEventListener('reconnect', () => {
        es.close();
        setTimeout(connectSSE, 1000);
    });
}

connectSSE();

// ─── 메시지 전송 ─────────────────────────────────────────
async function sendMessage() {
    const content = msgInput.value.trim();
    if (! content) return;

    const nickname = myNickname;
    sendBtn.disabled = true;
    sendError.textContent = '';

    // 낙관적 렌더 (즉시 표시)
    const tempId = 'temp-' + Date.now();
    appendMessage({ id: tempId, nickname, content, created_at: new Date().toISOString().replace('T', ' ').slice(0, 19) }, true);
    scrollToBottom();
    msgInput.value = '';
    charCount.textContent = '0 / 500';

    try {
        const form = new URLSearchParams({ nickname, content });
        form.append(CSRF_TOKEN, csrfHash);

        const res  = await fetch('<?= base_url('examples/chat/send') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: form,
        });
        const json = await res.json();
        if (json.csrf_hash) csrfHash = json.csrf_hash;

        if (json.success) {
            // 임시 ID → 실제 ID로 교체
            const tempEl = document.querySelector(`[data-id="${tempId}"]`);
            if (tempEl) tempEl.dataset.id = json.id;
            lastId = Math.max(lastId, json.id);
            // 봇 자동응답
            requestBotReply();
        } else {
            sendError.textContent = json.error ?? '전송 실패';
            document.querySelector(`[data-id="${tempId}"]`)?.remove();
            msgInput.value = content;
        }
    } catch {
        sendError.textContent = '서버 오류가 발생했습니다.';
        document.querySelector(`[data-id="${tempId}"]`)?.remove();
        msgInput.value = content;
    } finally {
        sendBtn.disabled = false;
        msgInput.focus();
    }
}

sendBtn.addEventListener('click', sendMessage);

msgInput.addEventListener('keydown', e => {
    if (e.key === 'Enter' && ! e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

msgInput.addEventListener('input', () => {
    charCount.textContent = msgInput.value.length + ' / 500';
});

// ─── 전체 삭제 ───────────────────────────────────────────
document.getElementById('clearBtn').addEventListener('click', async () => {
    if (! confirm('모든 채팅 내역을 삭제할까요?')) return;

    const form = new URLSearchParams();
    form.append(CSRF_TOKEN, csrfHash);

    const res  = await fetch('<?= base_url('examples/chat/clear') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: form,
    });
    const json = await res.json();
    if (json.csrf_hash) csrfHash = json.csrf_hash;

    if (json.success) {
        chatMessages.innerHTML = `<p class="text-center text-muted my-auto" id="emptyMsg">
            <i class="bi bi-chat-square-dots d-block fs-2 mb-2"></i>첫 메시지를 보내보세요!</p>`;
        lastId = 0;
    }
});

// ─── 봇 설정 ────────────────────────────────────────────
const botToggle    = document.getElementById('botToggle');
const apiKeyArea   = document.getElementById('apiKeyArea');
const apiKeyInput  = document.getElementById('apiKeyInput');
const apiKeyStatus = document.getElementById('apiKeyStatus');

let groqApiKey = localStorage.getItem('groqApiKey') ?? '';
if (groqApiKey) {
    apiKeyInput.value  = groqApiKey;
    apiKeyStatus.innerHTML = '<i class="bi bi-check-circle text-success"></i> 저장됨';
    apiKeyStatus.className = 'small text-success';
}

botToggle.addEventListener('change', () => {
    apiKeyArea.style.display = botToggle.checked ? 'flex' : 'none';
});

document.getElementById('saveApiKeyBtn').addEventListener('click', () => {
    const key = apiKeyInput.value.trim();
    if (! key.startsWith('gsk_')) {
        apiKeyStatus.textContent = 'gsk_ 로 시작하는 키를 입력하세요.';
        apiKeyStatus.className   = 'small text-danger';
        return;
    }
    groqApiKey = key;
    localStorage.setItem('groqApiKey', groqApiKey);
    apiKeyStatus.innerHTML = '<i class="bi bi-check-circle text-success"></i> 저장됨';
    apiKeyStatus.className = 'small text-success';
});

async function requestBotReply() {
    if (! botToggle.checked || ! groqApiKey) return;

    const indicator = document.createElement('div');
    indicator.id        = 'botTyping';
    indicator.className = 'chat-msg d-flex flex-column align-items-start';
    indicator.innerHTML = `<div class="msg-nick text-muted small mb-1">AI봇</div>
        <div class="d-flex align-items-end gap-1">
            <div class="msg-bubble bg-light border rounded-3 px-3 py-2 text-muted fst-italic">
                <span class="spinner-grow spinner-grow-sm me-1"></span>입력 중...
            </div>
        </div>`;
    chatMessages.appendChild(indicator);
    scrollToBottom();

    try {
        const form = new URLSearchParams({ api_key: groqApiKey });
        form.append(CSRF_TOKEN, csrfHash);

        const res  = await fetch('<?= base_url('examples/chat/bot-reply') ?>', {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body:    form,
        });
        const json = await res.json();
        if (json.csrf_hash) csrfHash = json.csrf_hash;

        indicator.remove();

        if (json.success) {
            appendMessage(json, false);
            scrollToBottom();
            lastId = Math.max(lastId, json.id);
        } else {
            sendError.textContent = json.error ?? '봇 응답 실패';
        }
    } catch {
        indicator.remove();
        sendError.textContent = '봇 API 호출 중 오류가 발생했습니다.';
    }
}

// ─── 새 탭 ───────────────────────────────────────────────
document.getElementById('openNewTab').addEventListener('click', () => {
    window.open(location.href, '_blank');
});

// ─── 유틸 ────────────────────────────────────────────────
function appendMessage(msg, isMine) {
    const wrap = document.createElement('div');
    wrap.className = 'chat-msg d-flex flex-column ' + (isMine ? 'align-items-end' : 'align-items-start');
    wrap.dataset.id   = msg.id;
    wrap.dataset.nick = msg.nickname;

    const bubbleClass = isMine ? 'bg-primary text-white' : 'bg-light border';
    const rowClass    = isMine ? 'flex-row-reverse' : '';
    const nickHtml    = isMine ? '' : `<div class="msg-nick text-muted small mb-1">${escHtml(msg.nickname)}</div>`;

    wrap.innerHTML = `
        ${nickHtml}
        <div class="d-flex align-items-end gap-1 ${rowClass}">
            <div class="msg-bubble rounded-3 px-3 py-2 ${bubbleClass}"
                 style="max-width:75%;word-break:break-word;">
                ${escHtml(msg.content).replace(/\n/g, '<br>')}
            </div>
            <small class="text-muted msg-time" style="white-space:nowrap;">${formatTime(msg.created_at)}</small>
        </div>`;

    chatMessages.appendChild(wrap);
}

function scrollToBottom(smooth = true) {
    chatMessages.scrollTo({ top: chatMessages.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
}

function formatTime(str) {
    if (! str) return '';
    const d = new Date(str.replace(' ', 'T'));
    return d.toLocaleTimeString('ko-KR', { hour: '2-digit', minute: '2-digit' });
}

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}
</script>
<?= $this->endSection() ?>
