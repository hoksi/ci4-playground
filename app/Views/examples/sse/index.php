<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <div class="rounded-circle d-flex align-items-center justify-content-center"
         style="width:52px;height:52px;background:#e8f5e9;">
        <i class="bi bi-broadcast fs-3" style="color:#198754;"></i>
    </div>
    <div>
        <h2 class="mb-0">Server-Sent Events (SSE)</h2>
        <p class="text-muted mb-0">서버 → 클라이언트 단방향 실시간 스트림 · EventSource API</p>
    </div>
</div>

<ul class="nav nav-tabs mb-4" id="mainTab">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-code">코드</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-vs">SSE vs WebSocket</a></li>
</ul>

<div class="tab-content">

<!-- ══════════════════════════════════════════════════════
     탭 1 — 라이브 데모
══════════════════════════════════════════════════════ -->
<div class="tab-pane fade show active" id="tab-demo">

    <!-- 컨트롤 바 -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex align-items-center gap-3 flex-wrap">
            <button id="btnStart" class="btn btn-success">
                <i class="bi bi-broadcast me-1"></i>연결 시작
            </button>
            <button id="btnStop" class="btn btn-outline-danger" disabled>
                <i class="bi bi-stop-circle me-1"></i>연결 종료
            </button>
            <div class="d-flex align-items-center gap-2 ms-2">
                <span id="statusDot" class="rounded-circle d-inline-block"
                      style="width:10px;height:10px;background:#adb5bd;"></span>
                <span id="statusText" class="small text-muted">연결 안 됨</span>
            </div>
            <div class="ms-auto small text-muted">
                수신 이벤트: <strong id="eventCount">0</strong>개
                &nbsp;|&nbsp; 재연결: <strong id="reconnectCount">0</strong>회
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- 시스템 정보 -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-cpu me-2 text-primary"></i>시스템 정보
                    <span class="badge bg-primary ms-1 small" id="sysBadge">대기</span>
                </div>
                <div class="card-body">
                    <dl class="mb-0 small">
                        <dt class="text-muted">서버 시각</dt>
                        <dd id="sysTime" class="fw-bold font-monospace fs-6">--:--:--</dd>
                        <dt class="text-muted mt-2">PHP 버전</dt>
                        <dd id="sysPhp" class="fw-semibold">—</dd>
                        <dt class="text-muted mt-2">메모리 사용량</dt>
                        <dd id="sysMem" class="fw-semibold">—</dd>
                        <dt class="text-muted mt-2">누적 틱</dt>
                        <dd id="sysTick" class="fw-semibold">—</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- 큐 현황 -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-collection-play me-2 text-warning"></i>큐 현황
                    <span class="badge bg-warning text-dark ms-1 small" id="queueBadge">대기</span>
                </div>
                <div class="card-body">
                    <div class="row text-center g-3">
                        <div class="col-6">
                            <div class="p-3 rounded" style="background:#fff9e6;">
                                <div id="qPending" class="display-6 fw-bold text-warning">—</div>
                                <small class="text-muted">대기 중</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded" style="background:#fef2f2;">
                                <div id="qFailed" class="display-6 fw-bold text-danger">—</div>
                                <small class="text-muted">실패</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 알림 -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-bell me-2 text-success"></i>실시간 알림
                    <span class="badge bg-success ms-1 small" id="notifyBadge">대기</span>
                </div>
                <div class="card-body p-0" style="max-height:160px;overflow-y:auto;" id="notifyList">
                    <div class="text-center text-muted small py-4">
                        연결 후 알림이 여기에 표시됩니다.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 이벤트 로그 -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-terminal me-2"></i>이벤트 로그</span>
            <button class="btn btn-sm btn-outline-secondary" id="btnClear">지우기</button>
        </div>
        <div class="card-body p-0">
            <div id="logArea" class="p-3 font-monospace small"
                 style="background:#1e1e2e;color:#cdd6f4;min-height:200px;max-height:350px;overflow-y:auto;border-radius:0 0 .375rem .375rem;">
                <span class="text-secondary">연결 시작 버튼을 클릭하면 이벤트 로그가 여기에 표시됩니다.</span>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════════════════════
     탭 2 — 코드
══════════════════════════════════════════════════════ -->
<div class="tab-pane fade" id="tab-code">
    <div class="row g-4">

        <!-- 서버 측 -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-server me-2"></i>서버 측 — app/Controllers/Examples/Sse.php
                </div>
                <div class="card-body p-0">
<pre><code class="language-php">public function stream(): void
{
    @set_time_limit(120);
    @ini_set('output_buffering', 'off');

    // ① SSE 필수 헤더
    header('Content-Type: text/event-stream; charset=UTF-8');
    header('Cache-Control: no-cache');
    header('X-Accel-Buffering: no');    // nginx 버퍼링 비활성화

    if (ob_get_level()) ob_end_flush();

    $lastId = (int)($_SERVER['HTTP_LAST_EVENT_ID'] ?? 0);
    $db     = \Config\Database::connect();

    for ($tick = 0; $tick &lt; 30; $tick++) {
        if (connection_aborted()) break;

        $id = $lastId + $tick + 1;

        // ② 이벤트 전송 형식
        // id: {고유 ID}
        // event: {이벤트 타입}
        // data: {JSON 문자열}
        // (빈 줄 2개로 이벤트 종료)
        echo "id: {$id}\n";
        echo "event: system\n";
        echo 'data: ' . json_encode([
            'time'   =&gt; date('H:i:s'),
            'memory' =&gt; round(memory_get_usage(true) / 1024 / 1024, 1),
        ]) . "\n\n";

        flush();   // ③ 즉시 전송
        sleep(2);  // ④ 2초 대기 후 다음 이벤트
    }

    // ⑤ 재연결 유도 (max ticks 후 연결 종료)
    echo "event: reconnect\ndata: {}\n\n";
    flush();
}</code></pre>
                </div>
            </div>
        </div>

        <!-- 클라이언트 측 -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-browser-chrome me-2"></i>클라이언트 측 — EventSource API
                </div>
                <div class="card-body p-0">
<pre><code class="language-javascript">// ① EventSource 생성
const es = new EventSource('/examples/sse/stream');

// ② 기본 message 이벤트
es.onmessage = (e) => {
    const data = JSON.parse(e.data);
    console.log('message:', data);
};

// ③ 커스텀 이벤트 타입 수신
es.addEventListener('system', (e) => {
    const data = JSON.parse(e.data);
    document.getElementById('sysTime').textContent = data.time;
});

es.addEventListener('queue', (e) => {
    const data = JSON.parse(e.data);
    document.getElementById('qPending').textContent = data.pending;
});

es.addEventListener('notify', (e) => {
    const data = JSON.parse(e.data);
    showNotification(data.message);
});

// ④ 재연결 이벤트 (서버가 연결을 끊으면 자동 재연결)
es.addEventListener('reconnect', () => {
    es.close();
    setTimeout(() => reconnect(), 1000);
});

// ⑤ 에러 처리 (자동 재연결 내장)
es.onerror = (e) => {
    console.warn('SSE 오류 — 자동 재연결 시도 중...');
};

// ⑥ 연결 종료
function stopSSE() {
    es.close();
}</code></pre>
                </div>
            </div>
        </div>

        <!-- SSE 이벤트 형식 -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">SSE 이벤트 형식</div>
                <div class="card-body p-0">
<pre class="m-0"><code class="language-text"># 기본 message 이벤트
data: Hello World

# 이벤트 ID 포함
id: 42
data: {"key": "value"}

# 커스텀 이벤트 타입
id: 43
event: system
data: {"time": "12:34:56"}

# 재연결 간격 설정 (밀리초)
retry: 3000
data: reconnecting...

# 주석 (keepalive용)
: ping</code></pre>
                </div>
            </div>
        </div>

        <!-- nginx 설정 -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">nginx 프록시 설정</div>
                <div class="card-body">
<pre><code class="language-nginx">location /examples/sse/stream {
    proxy_pass         http://127.0.0.1:8080;
    proxy_http_version 1.1;

    # SSE 필수 설정
    proxy_buffering    off;
    proxy_cache        off;
    proxy_read_timeout 3600s;

    # 헤더 전달
    proxy_set_header   Connection '';
    proxy_set_header   X-Accel-Buffering no;
}</code></pre>
                    <p class="small text-muted mt-2 mb-0">
                        <strong>주의:</strong> nginx 기본 버퍼링을 끄지 않으면 이벤트가 묶음으로 전달됩니다.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     탭 3 — SSE vs WebSocket
══════════════════════════════════════════════════════ -->
<div class="tab-pane fade" id="tab-vs">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width:25%">항목</th>
                        <th class="text-center">SSE</th>
                        <th class="text-center">WebSocket</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ([
                    ['통신 방향',     '서버 → 클라이언트 (단방향)',        '양방향'],
                    ['프로토콜',      'HTTP/1.1, HTTP/2',                   'ws:// / wss://'],
                    ['구현 난이도',   '낮음 (표준 HTTP)',                   '높음 (별도 서버 필요)'],
                    ['자동 재연결',   '브라우저 내장',                       '직접 구현 필요'],
                    ['Last-Event-ID', '내장 지원',                          '없음'],
                    ['바이너리 전송', '불가 (텍스트 전용)',                  '가능'],
                    ['서버 부하',     '연결당 HTTP 스레드/커넥션 유지',     '경량 WebSocket 서버'],
                    ['적합한 용도',   '실시간 피드, 알림, 로그 스트리밍',   '채팅, 게임, 양방향 통신'],
                    ['CI4 지원',      '기본 PHP로 구현 가능',                '별도 패키지 필요 (Ratchet 등)'],
                ] as [$item, $sse, $ws]): ?>
                <tr>
                    <td class="fw-semibold text-muted"><?= $item ?></td>
                    <td><?= $sse ?></td>
                    <td><?= $ws ?></td>
                </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-success d-flex gap-2 mt-4">
        <i class="bi bi-lightbulb-fill mt-1"></i>
        <div>
            <strong>언제 SSE를 선택할까?</strong><br>
            실시간 <em>읽기</em>만 필요하다면 SSE가 훨씬 간단합니다.
            대시보드 갱신, 알림 푸시, 로그 스트리밍, Queue 상태 모니터링 등
            서버 → 클라이언트 단방향 시나리오에 적합합니다.
        </div>
    </div>
</div>

</div><!-- /tab-content -->

<script>
let es = null;
let eventCount    = 0;
let reconnectCount = 0;

const btnStart  = document.getElementById('btnStart');
const btnStop   = document.getElementById('btnStop');
const statusDot = document.getElementById('statusDot');
const statusTxt = document.getElementById('statusText');
const logArea   = document.getElementById('logArea');

function setStatus(state) {
    const map = {
        connecting: ['#ffc107', '연결 중...'],
        connected:  ['#198754', '연결됨'],
        closed:     ['#adb5bd', '연결 안 됨'],
        error:      ['#dc3545', '오류'],
    };
    const [color, text] = map[state] || map.closed;
    statusDot.style.background = color;
    statusTxt.textContent = text;
    statusTxt.style.color = color;
}

function log(msg, color = '#cdd6f4') {
    const ts = new Date().toLocaleTimeString('ko-KR', {hour12: false});
    const line = document.createElement('div');
    line.innerHTML = `<span style="color:#6c7086">[${ts}]</span> <span style="color:${color}">${escHtml(msg)}</span>`;
    if (logArea.querySelector('span.text-secondary')) logArea.innerHTML = '';
    logArea.appendChild(line);
    logArea.scrollTop = logArea.scrollHeight;
    document.getElementById('eventCount').textContent = ++eventCount;
}

function startSSE() {
    if (es) es.close();
    setStatus('connecting');
    btnStart.disabled = true;
    btnStop.disabled  = false;
    log('EventSource 연결 시작...', '#89b4fa');

    es = new EventSource('<?= base_url('examples/sse/stream') ?>');

    es.addEventListener('system', e => {
        const d = JSON.parse(e.data);
        document.getElementById('sysTime').textContent = d.time;
        document.getElementById('sysPhp').textContent  = 'PHP ' + d.php;
        document.getElementById('sysMem').textContent  = d.memory_mb + ' MB';
        document.getElementById('sysTick').textContent = d.tick + ' / 30';
        document.getElementById('sysBadge').textContent = '실시간';
        log(`[system] time=${d.time} mem=${d.memory_mb}MB tick=${d.tick}`, '#a6e3a1');
        setStatus('connected');
    });

    es.addEventListener('queue', e => {
        const d = JSON.parse(e.data);
        document.getElementById('qPending').textContent = d.pending;
        document.getElementById('qFailed').textContent  = d.failed;
        document.getElementById('queueBadge').textContent = '실시간';
        log(`[queue] pending=${d.pending} failed=${d.failed}`, '#f9e2af');
    });

    es.addEventListener('notify', e => {
        const d = JSON.parse(e.data);
        const list = document.getElementById('notifyList');
        if (list.querySelector('div.text-center')) list.innerHTML = '';
        const item = document.createElement('div');
        item.className = 'px-3 py-2 border-bottom small d-flex align-items-center gap-2';
        item.innerHTML = `<i class="bi bi-bell-fill text-success"></i><span>${escHtml(d.message)}</span>`;
        list.prepend(item);
        document.getElementById('notifyBadge').textContent = '실시간';
        log(`[notify] ${d.message}`, '#89dceb');
    });

    es.addEventListener('reconnect', () => {
        log('[reconnect] 서버가 연결을 종료했습니다. 재연결 중...', '#f38ba8');
        es.close();
        document.getElementById('reconnectCount').textContent = ++reconnectCount;
        setTimeout(startSSE, 1500);
    });

    es.onerror = () => {
        if (es.readyState === EventSource.CLOSED) {
            setStatus('closed');
        } else {
            setStatus('error');
            log('[error] 연결 오류 — 자동 재연결 대기 중', '#f38ba8');
        }
    };
}

function stopSSE() {
    if (es) { es.close(); es = null; }
    setStatus('closed');
    btnStart.disabled = false;
    btnStop.disabled  = true;
    log('연결을 종료했습니다.', '#6c7086');
}

btnStart.addEventListener('click', startSSE);
btnStop.addEventListener('click', stopSSE);
document.getElementById('btnClear').addEventListener('click', () => {
    logArea.innerHTML = '<span class="text-secondary">로그가 지워졌습니다.</span>';
    eventCount = 0;
    document.getElementById('eventCount').textContent = 0;
});

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>

<?= $this->endSection() ?>
