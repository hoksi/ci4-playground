<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">챗봇</li>
    </ol></nav>
    <h1><i class="bi bi-robot me-2"></i>챗봇</h1>
    <p>Groq API(LLaMA · Mixtral · Gemma)를 활용한 AI 챗봇입니다.
       대화 내용은 DB에 저장되며 컨텍스트로 활용됩니다.</p>
</div>

<ul class="nav nav-tabs mb-3" id="chatTab">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="chat">챗봇</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="code">코드 설명</a></li>
</ul>

<!-- 챗봇 탭 -->
<div id="tab-chat" class="tab-content-pane">
    <div class="example-card">
        <div class="example-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-chat-dots me-2"></i>AI 챗봇</h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary">Llama 3.1 8B</span>
                <button class="btn btn-sm btn-outline-danger" id="clearBtn">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="example-card-body p-0">

            <?php if (! env('GROQ_API_KEY')): ?>
            <!-- API 키 입력 (서버 키 없을 때만 표시) -->
            <div class="p-3 border-bottom bg-light d-flex align-items-center gap-2">
                <i class="bi bi-key text-muted"></i>
                <input type="password" id="apiKeyInput" class="form-control form-control-sm"
                       style="max-width:280px;" placeholder="Groq API 키 (gsk_...)">
                <button class="btn btn-sm btn-outline-success" id="saveApiKeyBtn">저장</button>
                <span id="apiKeyStatus" class="small"></span>
            </div>
            <?php else: ?>
            <div class="px-3 py-2 border-bottom bg-light">
                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>서버 API 키 설정됨</span>
            </div>
            <?php endif ?>

            <!-- 메시지 목록 -->
            <div id="chatMessages"
                 style="height:420px;overflow-y:auto;padding:1rem;display:flex;flex-direction:column;gap:0.75rem;">
                <?php if (empty($messages)): ?>
                <p class="text-center text-muted my-auto" id="emptyMsg">
                    <i class="bi bi-chat-square-dots d-block fs-2 mb-2"></i>
                    안녕하세요! 무엇이든 물어보세요.
                </p>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                    <?php $isMine = $msg['nickname'] === '나'; ?>
                    <div class="chat-msg d-flex flex-column <?= $isMine ? 'align-items-end' : 'align-items-start' ?>">
                        <?php if (! $isMine): ?>
                        <div class="text-muted small mb-1">
                            <i class="bi bi-robot me-1"></i><?= esc($msg['nickname']) ?>
                        </div>
                        <?php endif ?>
                        <div class="d-flex align-items-end gap-1 <?= $isMine ? 'flex-row-reverse' : '' ?>">
                            <div class="rounded-3 px-3 py-2 <?= $isMine ? 'bg-primary text-white' : 'bg-light border' ?>"
                                 style="max-width:75%;word-break:break-word;">
                                <?= nl2br(esc($msg['content'])) ?>
                            </div>
                            <small class="text-muted" style="white-space:nowrap;">
                                <?= date('H:i', strtotime($msg['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach ?>
                <?php endif ?>
            </div>

            <!-- 입력 영역 -->
            <div class="p-3 border-top">
                <div class="input-group">
                    <textarea id="msgInput" class="form-control" rows="2"
                              style="resize:none;"
                              placeholder="메시지 입력 (Enter: 전송 / Shift+Enter: 줄바꿈)"
                              maxlength="500"></textarea>
                    <button class="btn btn-primary" id="sendBtn" style="width:72px;">
                        <i class="bi bi-send" id="sendIcon"></i>
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
<pre><code class="language-text">1. 사용자 입력  → POST /chat/send (content, model, api_key)
2. 서버         → 유저 메시지 DB 저장
3. 서버         → 최근 20개 메시지로 Groq API 호출
4. Groq 응답   → 봇 메시지 DB 저장
5. JSON 반환   → { user: {...}, bot: {...} }
6. 클라이언트  → 두 말풍선 동시 렌더</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>Controller — send()</h5></div>
        <div class="example-card-body">
<pre><code class="language-php">public function send(): ResponseInterface
{
    // 1. 유저 메시지 DB 저장
    $userId = $db->table('chat_messages')->insert([
        'nickname' => '나', 'content' => $content, ...
    ], true);

    // 2. 최근 20개 대화 → Groq 컨텍스트 구성
    $history = $db->table('chat_messages')->orderBy('id','DESC')->limit(20)->get();
    $groqMessages = [
        ['role' => 'system', 'content' => '...'],
        // history: nickname === 'AI봇' → assistant, 나머지 → user
    ];

    // 3. Groq API 호출
    $response = Services::curlrequest()->post(
        'https://api.groq.com/openai/v1/chat/completions',
        ['body' => json_encode(['model' => $model, 'messages' => $groqMessages])]
    );

    // 4. 봇 메시지 저장 후 둘 다 반환
    return $this->response->setJSON([
        'user' => $userMsg,
        'bot'  => $botMsg,
    ]);
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>Groq 무료 티어 제한</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                무료 제한은 <strong>모델마다 다르며</strong> RPM(분당 요청) · TPM(분당 토큰) · RPD(일 요청) 세 가지가 적용됩니다.
                정확한 수치는 <strong>console.groq.com → Settings → Limits</strong> 에서 확인하세요.
            </div>
            <table class="table table-bordered table-sm">
                <thead class="table-dark"><tr><th>모델</th><th>특징</th></tr></thead>
                <tbody>
                    <tr><td><code>llama-3.3-70b-versatile</code></td><td>최고 품질, 70B 파라미터</td></tr>
                    <tr><td><code>llama-3.1-8b-instant</code></td><td>가장 빠름, 경량</td></tr>
                    <tr><td><code>mixtral-8x7b-32768</code></td><td>긴 컨텍스트(32K)</td></tr>
                    <tr><td><code>gemma2-9b-it</code></td><td>Google Gemma 경량</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
// ─── 탭 전환 ─────────────────────────────────────────────
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

// ─── DOM 참조 ────────────────────────────────────────────
const chatMessages = document.getElementById('chatMessages');
const msgInput     = document.getElementById('msgInput');
const sendBtn      = document.getElementById('sendBtn');
const sendIcon     = document.getElementById('sendIcon');
const sendError    = document.getElementById('sendError');
const charCount    = document.getElementById('charCount');

// ─── API 키 (서버 키 없을 때) ────────────────────────────
const serverHasKey  = <?= env('GROQ_API_KEY') ? 'true' : 'false' ?>;
const apiKeyInput   = document.getElementById('apiKeyInput');
const apiKeyStatus  = document.getElementById('apiKeyStatus');
const saveApiKeyBtn = document.getElementById('saveApiKeyBtn');

let groqApiKey = serverHasKey ? '__server__' : (localStorage.getItem('groqApiKey') ?? '');

if (! serverHasKey && groqApiKey && apiKeyInput) {
    apiKeyInput.value      = groqApiKey;
    apiKeyStatus.innerHTML = '<i class="bi bi-check-circle text-success"></i> 저장됨';
    apiKeyStatus.className = 'small text-success';
}

if (saveApiKeyBtn) {
    saveApiKeyBtn.addEventListener('click', () => {
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
}

// ─── 초기 스크롤 ─────────────────────────────────────────
scrollToBottom(false);

// ─── 메시지 전송 ─────────────────────────────────────────
async function sendMessage() {
    const content = msgInput.value.trim();
    if (! content) return;

    sendError.textContent = '';
    setLoading(true);
    msgInput.value = '';
    charCount.textContent = '0 / 500';

    // 유저 말풍선 즉시 렌더
    appendMessage({ nickname: '나', content, created_at: nowStr() }, true);
    // 검색 여부 사전 감지 → 인디케이터 문구 변경
    const searching = willSearch(content);
    const typingEl  = appendTyping(searching ? '🔍 웹 검색 중...' : '입력 중...');
    scrollToBottom();

    try {
        const keyParam = groqApiKey === '__server__' ? '' : groqApiKey;
        const form = new URLSearchParams({ content, api_key: keyParam });
        form.append(CSRF_TOKEN, csrfHash);

        const res  = await fetch('<?= base_url('examples/chat/send') ?>', {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body:    form,
        });
        const json = await res.json();
        if (json.csrf_hash) csrfHash = json.csrf_hash;

        typingEl.remove();

        if (json.success) {
            const botEl = appendMessage(json.bot, false);
            // 검색 출처 표시
            if (json.searched && json.sources && json.sources.length) {
                const sources = document.createElement('div');
                sources.className = 'mt-1 ms-1';
                sources.innerHTML = `<small class="text-muted"><i class="bi bi-search me-1"></i>검색 출처: </small>`
                    + json.sources.map((url, i) =>
                        `<a href="${escHtml(url)}" target="_blank" rel="noopener"
                            class="badge bg-light text-dark border me-1">${i + 1}</a>`
                    ).join('');
                botEl.appendChild(sources);
            }
            scrollToBottom();
            document.getElementById('emptyMsg')?.remove();
        } else {
            sendError.textContent = json.error ?? '오류가 발생했습니다.';
        }
    } catch {
        typingEl.remove();
        sendError.textContent = '서버 오류가 발생했습니다.';
    } finally {
        setLoading(false);
        msgInput.focus();
    }
}

sendBtn.addEventListener('click', sendMessage);
msgInput.addEventListener('keydown', e => {
    if (e.key === 'Enter' && ! e.shiftKey) { e.preventDefault(); sendMessage(); }
});
msgInput.addEventListener('input', () => {
    charCount.textContent = msgInput.value.length + ' / 500';
});

// ─── 전체 삭제 ───────────────────────────────────────────
document.getElementById('clearBtn').addEventListener('click', async () => {
    if (! confirm('대화 내역을 모두 삭제할까요?')) return;

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
            <i class="bi bi-chat-square-dots d-block fs-2 mb-2"></i>안녕하세요! 무엇이든 물어보세요.</p>`;
    }
});

// ─── 유틸 ────────────────────────────────────────────────
function appendMessage(msg, isMine) {
    const wrap = document.createElement('div');
    wrap.className = 'chat-msg d-flex flex-column ' + (isMine ? 'align-items-end' : 'align-items-start');

    const nickHtml = isMine ? '' :
        `<div class="text-muted small mb-1"><i class="bi bi-robot me-1"></i>${escHtml(msg.nickname)}</div>`;
    const rowClass    = isMine ? 'flex-row-reverse' : '';
    const bubbleClass = isMine ? 'bg-primary text-white' : 'bg-light border';

    wrap.innerHTML = `${nickHtml}
        <div class="d-flex align-items-end gap-1 ${rowClass}">
            <div class="rounded-3 px-3 py-2 ${bubbleClass}" style="max-width:75%;word-break:break-word;">
                ${escHtml(msg.content).replace(/\n/g, '<br>')}
            </div>
            <small class="text-muted" style="white-space:nowrap;">${formatTime(msg.created_at)}</small>
        </div>`;
    chatMessages.appendChild(wrap);
    return wrap;
}

// 검색 키워드 (서버와 동일)
const SEARCH_KEYWORDS = ['최신', '오늘', '현재', '지금', '어제', '이번 주', '이번 달',
                         '뉴스', '날씨', '주가', '환율', '최근', '요즘'];

function willSearch(content) {
    if (SEARCH_KEYWORDS.some(kw => content.includes(kw))) return true;
    return /20\d{2}년?/.test(content);
}

function appendTyping(label = '입력 중...') {
    const wrap = document.createElement('div');
    wrap.className = 'chat-msg d-flex flex-column align-items-start';
    wrap.innerHTML = `<div class="text-muted small mb-1"><i class="bi bi-robot me-1"></i>AI봇</div>
        <div class="d-flex align-items-end gap-1">
            <div class="rounded-3 px-3 py-2 bg-light border text-muted fst-italic">
                <span class="spinner-grow spinner-grow-sm me-1"></span>${escHtml(label)}
            </div>
        </div>`;
    chatMessages.appendChild(wrap);
    return wrap;
}

function setLoading(on) {
    sendBtn.disabled = on;
    sendIcon.className = on ? 'bi bi-hourglass-split' : 'bi bi-send';
}

function scrollToBottom(smooth = true) {
    chatMessages.scrollTo({ top: chatMessages.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
}

function nowStr() {
    return new Date().toISOString().replace('T', ' ').slice(0, 19);
}

function formatTime(str) {
    if (! str) return '';
    return new Date(str.replace(' ', 'T')).toLocaleTimeString('ko-KR', { hour: '2-digit', minute: '2-digit' });
}

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}
</script>
<?= $this->endSection() ?>
