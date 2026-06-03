<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">고양이 키우기</li>
    </ol></nav>
    <h1>🐱 고양이 키우기</h1>
    <p>AI 고양이를 돌봐주세요. 돌볼수록 경험치가 쌓이고 성장합니다. Groq AI가 고양이 반응을 생성합니다.</p>
</div>

<style>
.cat-card { max-width: 520px; margin: 0 auto; }
.cat-emoji { font-size: 6rem; line-height: 1; transition: transform .3s; cursor: default; user-select: none; }
.cat-emoji.bounce { animation: bounce .5s ease 3; }
.cat-emoji.shake  { animation: shake .4s ease 2; }
.cat-emoji.pulse  { animation: pulse 2s ease infinite; }
.cat-emoji.levelup { animation: levelup .8s ease; }
@keyframes bounce  { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-16px)} }
@keyframes shake   { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-8px)} 75%{transform:translateX(8px)} }
@keyframes pulse   { 0%,100%{transform:scale(1)} 50%{transform:scale(1.08)} }
@keyframes levelup { 0%{transform:scale(1)} 30%{transform:scale(1.4) rotate(-5deg)} 60%{transform:scale(1.4) rotate(5deg)} 100%{transform:scale(1)} }

.speech-bubble {
    position: relative; background: #f8f9fa; border: 2px solid #dee2e6;
    border-radius: 18px; padding: .6rem 1rem; margin: .75rem auto;
    max-width: 340px; min-height: 2.5rem; font-size: .95rem;
    transition: opacity .3s;
}
.speech-bubble::before {
    content: ''; position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
    border: 6px solid transparent; border-bottom-color: #dee2e6;
}
.speech-bubble::after {
    content: ''; position: absolute; top: -9px; left: 50%; transform: translateX(-50%);
    border: 5px solid transparent; border-bottom-color: #f8f9fa;
}

.stat-bar { height: 14px; border-radius: 7px; transition: width .5s ease; }
.exp-bar  { height: 8px;  border-radius: 4px; transition: width .6s ease; background: linear-gradient(90deg,#f59e0b,#fbbf24); }
.action-btn { width: 110px; height: 70px; font-size: .85rem; }
.action-btn:disabled { opacity: .5; }
.name-edit { display: inline-flex; align-items: center; gap: .4rem; }
.levelup-toast {
    position: fixed; top: 80px; left: 50%; transform: translateX(-50%);
    background: linear-gradient(135deg,#f59e0b,#ef4444);
    color: #fff; padding: .75rem 2rem; border-radius: 50px;
    font-weight: 700; font-size: 1.1rem; z-index: 9999;
    box-shadow: 0 4px 20px rgba(0,0,0,.3);
    animation: toastIn .4s ease;
    display: none;
}
@keyframes toastIn { from{opacity:0;top:60px} to{opacity:1;top:80px} }
</style>

<!-- 레벨업 토스트 -->
<div class="levelup-toast" id="levelupToast">✨ 레벨 업!</div>

<div class="cat-card">
    <div class="example-card text-center">
        <div class="example-card-body">

            <!-- 이름 + 성장 단계 -->
            <div class="name-edit mb-1">
                <h4 class="mb-0" id="catNameDisplay"><?= esc($cat['name']) ?></h4>
                <button class="btn btn-sm btn-link p-0 text-muted" id="editNameBtn" title="이름 변경">
                    <i class="bi bi-pencil"></i>
                </button>
            </div>
            <div id="renameForm" class="d-none input-group input-group-sm mb-2" style="max-width:200px;margin:0 auto;">
                <input type="text" id="nameInput" class="form-control" maxlength="32" value="<?= esc($cat['name']) ?>">
                <button class="btn btn-primary" id="saveNameBtn">확인</button>
            </div>

            <!-- 성장 단계 + 레벨 배지 -->
            <?php
            $stageLabels = ['baby' => '🐱 아기', 'adult' => '😺 성묘', 'elder' => '🐈 노령묘'];
            $stageColors = ['baby' => 'info', 'adult' => 'primary', 'elder' => 'secondary'];
            ?>
            <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                <span id="stageBadge" class="badge bg-<?= $stageColors[$stage] ?>"><?= $stageLabels[$stage] ?></span>
                <span id="levelBadge" class="badge bg-warning text-dark">Lv.<?= (int) $cat['level'] ?></span>
            </div>

            <!-- 경험치 바 -->
            <div class="px-4 mb-2">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">경험치</small>
                    <small id="expInfo" class="text-muted">
                        <?php if ((int)$cat['level'] >= 20): ?>최대 레벨<?php else: ?>다음 레벨까지 <?= $expToNext ?>exp<?php endif ?>
                    </small>
                </div>
                <div class="bg-light rounded" style="height:8px;">
                    <div class="exp-bar" id="expBar" style="width:<?= $expProgress ?>%"></div>
                </div>
            </div>

            <!-- 고양이 이모지 -->
            <div class="cat-emoji <?= in_array($mood, ['ecstatic','happy']) ? 'pulse' : '' ?>"
                 id="catEmoji"><?= esc($moodEmoji) ?></div>

            <!-- 말풍선 -->
            <div class="speech-bubble" id="speechBubble"><?= esc($defaultSpeech) ?></div>

            <!-- 상태 바 -->
            <div class="px-3 mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <small>🍣 배고픔</small><small id="hungerVal"><?= (int)$cat['hunger'] ?>/100</small>
                </div>
                <div class="bg-light rounded" style="height:14px;">
                    <div class="stat-bar bg-warning" id="hungerBar" style="width:<?= (int)$cat['hunger'] ?>%"></div>
                </div>
                <div class="d-flex justify-content-between mb-1 mt-2">
                    <small>😊 행복도</small><small id="happyVal"><?= (int)$cat['happiness'] ?>/100</small>
                </div>
                <div class="bg-light rounded" style="height:14px;">
                    <div class="stat-bar bg-danger" id="happyBar" style="width:<?= (int)$cat['happiness'] ?>%"></div>
                </div>
                <div class="d-flex justify-content-between mb-1 mt-2">
                    <small>⚡ 에너지</small><small id="energyVal"><?= (int)$cat['energy'] ?>/100</small>
                </div>
                <div class="bg-light rounded" style="height:14px;">
                    <div class="stat-bar bg-success" id="energyBar" style="width:<?= (int)$cat['energy'] ?>%"></div>
                </div>
            </div>

            <!-- 행동 버튼 -->
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                <button class="btn btn-outline-warning action-btn" data-action="feed">🍣<br>먹이주기</button>
                <button class="btn btn-outline-primary action-btn" data-action="play">🎾<br>놀기</button>
                <button class="btn btn-outline-secondary action-btn" data-action="sleep">💤<br>재우기</button>
                <button class="btn btn-outline-success action-btn" data-action="pet">🤚<br>쓰다듬기</button>
            </div>

            <button class="btn btn-sm btn-link text-muted" id="resetBtn">
                <i class="bi bi-arrow-counterclockwise me-1"></i>상태 초기화
            </button>
        </div>
    </div>

    <!-- 히스토리 차트 -->
    <div class="example-card mt-3">
        <div class="example-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>상태 히스토리</h5>
            <button class="btn btn-sm btn-outline-secondary" id="refreshHistory">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
        <div class="example-card-body">
            <canvas id="historyChart" style="max-height:220px;"></canvas>
            <p class="text-center text-muted small mt-2" id="historyEmpty" style="display:none;">
                아직 기록이 없습니다. 행동 버튼을 눌러보세요!
            </p>
        </div>
    </div>

    <div class="result-box info mt-3">
        <i class="bi bi-info-circle me-2"></i>
        방치하면 상태가 감소합니다. 행동마다 경험치가 쌓여 레벨업!
        <strong>Lv.1~5 아기 → Lv.6~15 성묘 → Lv.16+ 노령묘</strong>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ─── CSRF ─────────────────────────────────────────────
const CSRF_TOKEN = '<?= csrf_token() ?>';
let   csrfHash   = '<?= csrf_hash() ?>';

// ─── 상수 ─────────────────────────────────────────────
const MOODS = {
    ecstatic: { baby:'🐱', adult:'😻', elder:'🐈',  speech:'그르릉~ 너무 행복해요 냥! 🎉' },
    happy:    { baby:'🐱', adult:'😺', elder:'🐈',  speech:'냥~ 기분이 좋아요!' },
    neutral:  { baby:'🐱', adult:'😼', elder:'🐈‍⬛', speech:'..냥.' },
    hungry:   { baby:'😿', adult:'😿', elder:'😿',  speech:'배고파요... 냥!' },
    tired:    { baby:'😪', adult:'😪', elder:'😪',  speech:'졸려요... 냥...' },
    sad:      { baby:'😾', adult:'😾', elder:'😾',  speech:'심심해요. 냥!' },
    critical: { baby:'🙀', adult:'🙀', elder:'🙀',  speech:'도와주세요!! 냥!!' },
};
const STAGE_LABELS = { baby:'🐱 아기', adult:'😺 성묘', elder:'🐈 노령묘' };
const STAGE_COLORS = { baby:'info',    adult:'primary',  elder:'secondary' };

// ─── DOM ──────────────────────────────────────────────
const catEmoji    = document.getElementById('catEmoji');
const speechBubble = document.getElementById('speechBubble');
const hungerBar   = document.getElementById('hungerBar');
const happyBar    = document.getElementById('happyBar');
const energyBar   = document.getElementById('energyBar');
const hungerVal   = document.getElementById('hungerVal');
const happyVal    = document.getElementById('happyVal');
const energyVal   = document.getElementById('energyVal');
const levelBadge  = document.getElementById('levelBadge');
const stageBadge  = document.getElementById('stageBadge');
const expBar      = document.getElementById('expBar');
const expInfo     = document.getElementById('expInfo');
const actionBtns  = document.querySelectorAll('.action-btn');
const levelupToast = document.getElementById('levelupToast');

// ─── UI 업데이트 ───────────────────────────────────────
function updateUI(data) {
    hungerBar.style.width  = data.hunger    + '%';
    happyBar.style.width   = data.happiness + '%';
    energyBar.style.width  = data.energy    + '%';
    hungerVal.textContent  = data.hunger    + '/100';
    happyVal.textContent   = data.happiness + '/100';
    energyVal.textContent  = data.energy    + '/100';

    if (data.level !== undefined) {
        levelBadge.textContent = 'Lv.' + data.level;
        expBar.style.width     = data.expProgress + '%';
        expInfo.textContent    = data.level >= 20 ? '최대 레벨' : '다음 레벨까지 ' + data.expToNext + 'exp';
        stageBadge.textContent = STAGE_LABELS[data.stage] || '';
        stageBadge.className   = 'badge bg-' + (STAGE_COLORS[data.stage] || 'secondary');
    }

    const stage = data.stage || 'adult';
    const mood  = MOODS[data.mood] || MOODS.neutral;
    catEmoji.textContent = mood[stage] || mood.adult;
}

function showSpeech(text) {
    speechBubble.style.opacity = '0';
    setTimeout(() => { speechBubble.textContent = text; speechBubble.style.opacity = '1'; }, 200);
}

function animateCat(type) {
    catEmoji.classList.remove('bounce', 'shake', 'pulse', 'levelup');
    void catEmoji.offsetWidth;
    catEmoji.classList.add(type);
    setTimeout(() => {
        catEmoji.classList.remove(type);
        const s = document.getElementById('stageBadge').textContent;
        if (s.includes('아기') || s.includes('성묘') || s.includes('노령묘')) {
            // restore pulse if happy
        }
    }, 1500);
}

function showLevelUp(level) {
    levelupToast.textContent = '✨ Lv.' + level + ' 레벨 업!';
    levelupToast.style.display = 'block';
    animateCat('levelup');
    setTimeout(() => { levelupToast.style.display = 'none'; }, 2500);
}

function setActionBtns(disabled) {
    actionBtns.forEach(b => b.disabled = disabled);
}

// ─── 행동 처리 ────────────────────────────────────────
const ANIM_MAP  = { feed:'bounce', play:'bounce', sleep:'pulse', pet:'bounce' };
const THINKING  = { feed:'냠냠...', play:'신난다!...', sleep:'Zzz...', pet:'그르릉...' };

actionBtns.forEach(btn => {
    btn.addEventListener('click', async () => {
        const act = btn.dataset.action;
        setActionBtns(true);
        showSpeech(THINKING[act]);

        const form = new URLSearchParams({ action: act });
        form.append(CSRF_TOKEN, csrfHash);

        try {
            const res  = await fetch('<?= base_url('examples/cat-game/action') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: form,
            });
            const json = await res.json();
            if (json.csrf_hash) csrfHash = json.csrf_hash;

            if (json.success) {
                updateUI(json);
                animateCat(ANIM_MAP[act]);
                if (json.leveledUp) showLevelUp(json.level);
                showSpeech(json.reaction || MOODS[json.mood]?.[json.stage] || '냥~');
                loadHistory();
            }
        } catch {
            showSpeech('오류가 발생했어요 냥...');
        } finally {
            setActionBtns(false);
        }
    });
});

// ─── 이름 변경 ────────────────────────────────────────
document.getElementById('editNameBtn').addEventListener('click', () => {
    document.getElementById('renameForm').classList.toggle('d-none');
    document.getElementById('nameInput').focus();
});

document.getElementById('saveNameBtn').addEventListener('click', async () => {
    const name = document.getElementById('nameInput').value.trim();
    if (! name) return;
    const form = new URLSearchParams({ name });
    form.append(CSRF_TOKEN, csrfHash);
    const res  = await fetch('<?= base_url('examples/cat-game/rename') ?>', {
        method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' }, body: form,
    });
    const json = await res.json();
    if (json.csrf_hash) csrfHash = json.csrf_hash;
    if (json.success) {
        document.getElementById('catNameDisplay').textContent = json.name;
        document.getElementById('renameForm').classList.add('d-none');
    }
});

// ─── 초기화 ───────────────────────────────────────────
document.getElementById('resetBtn').addEventListener('click', async () => {
    if (! confirm('고양이 상태와 레벨을 초기화할까요?')) return;
    const form = new URLSearchParams();
    form.append(CSRF_TOKEN, csrfHash);
    const res  = await fetch('<?= base_url('examples/cat-game/reset') ?>', {
        method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' }, body: form,
    });
    const json = await res.json();
    if (json.csrf_hash) csrfHash = json.csrf_hash;
    if (json.success) {
        updateUI({ hunger:70, happiness:70, energy:70, mood:'happy', stage:'baby', level:1, exp:0, expProgress:0, expToNext:50 });
        showSpeech('냥~ 다시 시작해요!');
        animateCat('bounce');
        loadHistory();
    }
});

// ─── 히스토리 차트 ────────────────────────────────────
let historyChart = null;

async function loadHistory() {
    const res  = await fetch('<?= base_url('examples/cat-game/history') ?>');
    const json = await res.json();
    const empty = document.getElementById('historyEmpty');

    if (! json.labels.length) {
        empty.style.display = 'block';
        if (historyChart) { historyChart.destroy(); historyChart = null; }
        return;
    }
    empty.style.display = 'none';

    const ctx = document.getElementById('historyChart').getContext('2d');
    const cfg = {
        type: 'line',
        data: {
            labels: json.labels,
            datasets: [
                { label:'배고픔',  data: json.hunger,    borderColor:'#f59e0b', backgroundColor:'rgba(245,158,11,.1)', tension:.4, fill:true },
                { label:'행복도',  data: json.happiness, borderColor:'#ef4444', backgroundColor:'rgba(239,68,68,.1)',  tension:.4, fill:true },
                { label:'에너지',  data: json.energy,    borderColor:'#10b981', backgroundColor:'rgba(16,185,129,.1)', tension:.4, fill:true },
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { min:0, max:100, ticks:{ stepSize:25 } } },
        }
    };

    if (historyChart) {
        historyChart.data = cfg.data;
        historyChart.update();
    } else {
        historyChart = new Chart(ctx, cfg);
    }
}

document.getElementById('refreshHistory').addEventListener('click', loadHistory);

// ─── 30초 폴링 + 최초 히스토리 로드 ──────────────────
setInterval(async () => {
    const res  = await fetch('<?= base_url('examples/cat-game/status') ?>');
    const json = await res.json();
    updateUI(json);
    if (json.mood === 'critical' || json.mood === 'hungry' || json.mood === 'tired') {
        showSpeech(MOODS[json.mood]?.adult ?? '냥~');
    }
}, 30000);

loadHistory();
</script>
<?= $this->endSection() ?>
