<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">고양이 키우기</li>
    </ol></nav>
    <h1>🐱 고양이 키우기</h1>
    <p>AI 고양이를 돌봐주세요. 방치하면 상태가 나빠집니다. Groq AI가 고양이 반응을 생성합니다.</p>
</div>

<style>
.cat-card { max-width: 480px; margin: 0 auto; }
.cat-emoji { font-size: 6rem; line-height: 1; transition: transform .3s; cursor: default; user-select: none; }
.cat-emoji.bounce { animation: bounce .5s ease 3; }
.cat-emoji.shake  { animation: shake .4s ease 2; }
.cat-emoji.pulse  { animation: pulse 2s ease infinite; }
@keyframes bounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-16px)} }
@keyframes shake  { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-8px)} 75%{transform:translateX(8px)} }
@keyframes pulse  { 0%,100%{transform:scale(1)} 50%{transform:scale(1.08)} }

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
.action-btn { width: 110px; height: 70px; font-size: .85rem; }
.action-btn:disabled { opacity: .5; }
.name-edit { display: inline-flex; align-items: center; gap: .4rem; }
</style>

<div class="cat-card">
    <!-- 고양이 카드 -->
    <div class="example-card text-center">
        <div class="example-card-body">

            <!-- 이름 -->
            <div class="name-edit mb-2">
                <h4 class="mb-0" id="catNameDisplay"><?= esc($cat['name']) ?></h4>
                <button class="btn btn-sm btn-link p-0 text-muted" id="editNameBtn" title="이름 변경">
                    <i class="bi bi-pencil"></i>
                </button>
            </div>
            <div id="renameForm" class="d-none input-group input-group-sm mb-2" style="max-width:200px;margin:0 auto;">
                <input type="text" id="nameInput" class="form-control" maxlength="32"
                       value="<?= esc($cat['name']) ?>" placeholder="고양이 이름">
                <button class="btn btn-primary" id="saveNameBtn">확인</button>
            </div>

            <!-- 고양이 이모지 -->
            <div class="cat-emoji <?= $mood === 'ecstatic' || $mood === 'happy' ? 'pulse' : '' ?>"
                 id="catEmoji"><?= esc($moodEmoji) ?></div>

            <!-- 말풍선 -->
            <div class="speech-bubble" id="speechBubble">
                <?= esc($defaultSpeech) ?>
            </div>

            <!-- 상태 바 -->
            <div class="px-3 mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <small>🍣 배고픔</small>
                    <small id="hungerVal"><?= (int) $cat['hunger'] ?>/100</small>
                </div>
                <div class="bg-light rounded" style="height:14px;">
                    <div class="stat-bar bg-warning" id="hungerBar"
                         style="width:<?= (int) $cat['hunger'] ?>%"></div>
                </div>

                <div class="d-flex justify-content-between mb-1 mt-2">
                    <small>😊 행복도</small>
                    <small id="happyVal"><?= (int) $cat['happiness'] ?>/100</small>
                </div>
                <div class="bg-light rounded" style="height:14px;">
                    <div class="stat-bar bg-danger" id="happyBar"
                         style="width:<?= (int) $cat['happiness'] ?>%"></div>
                </div>

                <div class="d-flex justify-content-between mb-1 mt-2">
                    <small>⚡ 에너지</small>
                    <small id="energyVal"><?= (int) $cat['energy'] ?>/100</small>
                </div>
                <div class="bg-light rounded" style="height:14px;">
                    <div class="stat-bar bg-success" id="energyBar"
                         style="width:<?= (int) $cat['energy'] ?>%"></div>
                </div>
            </div>

            <!-- 행동 버튼 -->
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                <button class="btn btn-outline-warning action-btn" data-action="feed">
                    🍣<br>먹이주기
                </button>
                <button class="btn btn-outline-primary action-btn" data-action="play">
                    🎾<br>놀기
                </button>
                <button class="btn btn-outline-secondary action-btn" data-action="sleep">
                    💤<br>재우기
                </button>
                <button class="btn btn-outline-success action-btn" data-action="pet">
                    🤚<br>쓰다듬기
                </button>
            </div>

            <!-- 초기화 -->
            <button class="btn btn-sm btn-link text-muted" id="resetBtn">
                <i class="bi bi-arrow-counterclockwise me-1"></i>상태 초기화
            </button>
        </div>
    </div>

    <!-- 안내 -->
    <div class="result-box info mt-3">
        <i class="bi bi-info-circle me-2"></i>
        고양이를 방치하면 배고픔·행복·에너지가 서서히 줄어듭니다.
        상태가 10 이하로 떨어지면 위험합니다!
        <?php if (! env('GROQ_API_KEY')): ?>
        <br><i class="bi bi-exclamation-triangle text-warning me-1"></i>
        <code>GROQ_API_KEY</code>가 설정되지 않아 AI 반응 없이 동작합니다.
        <?php endif ?>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
// ─── CSRF ─────────────────────────────────────────────────
const CSRF_TOKEN = '<?= csrf_token() ?>';
let   csrfHash   = '<?= csrf_hash() ?>';

// ─── 상태 ─────────────────────────────────────────────────
const MOODS = {
    ecstatic: { emoji: '😻', speech: '그르릉~ 너무 행복해요 냥! 🎉' },
    happy:    { emoji: '😺', speech: '냥~ 기분이 좋아요!' },
    neutral:  { emoji: '😼', speech: '..냥.' },
    hungry:   { emoji: '😿', speech: '배고파요... 밥 주세요 냥!' },
    tired:    { emoji: '😪', speech: '졸려요... 재워주세요 냥...' },
    sad:      { emoji: '😾', speech: '심심해요. 놀아주세요 냥!' },
    critical: { emoji: '🙀', speech: '도와주세요!! 냥!!' },
};

// ─── DOM ──────────────────────────────────────────────────
const catEmoji    = document.getElementById('catEmoji');
const speechBubble = document.getElementById('speechBubble');
const hungerBar   = document.getElementById('hungerBar');
const happyBar    = document.getElementById('happyBar');
const energyBar   = document.getElementById('energyBar');
const hungerVal   = document.getElementById('hungerVal');
const happyVal    = document.getElementById('happyVal');
const energyVal   = document.getElementById('energyVal');
const catNameDisplay = document.getElementById('catNameDisplay');
const actionBtns  = document.querySelectorAll('.action-btn');

// ─── 상태 업데이트 UI ─────────────────────────────────────
function updateUI(data) {
    hungerBar.style.width  = data.hunger    + '%';
    happyBar.style.width   = data.happiness + '%';
    energyBar.style.width  = data.energy    + '%';
    hungerVal.textContent  = data.hunger    + '/100';
    happyVal.textContent   = data.happiness + '/100';
    energyVal.textContent  = data.energy    + '/100';

    const mood = MOODS[data.mood] || MOODS.neutral;
    catEmoji.textContent = mood.emoji;
}

function showSpeech(text) {
    speechBubble.style.opacity = '0';
    setTimeout(() => {
        speechBubble.textContent = text;
        speechBubble.style.opacity = '1';
    }, 200);
}

function animateCat(type) {
    catEmoji.classList.remove('bounce', 'shake', 'pulse');
    void catEmoji.offsetWidth;
    catEmoji.classList.add(type);
    setTimeout(() => {
        catEmoji.classList.remove(type);
        const mood = catEmoji.textContent;
        if (mood === '😻' || mood === '😺') catEmoji.classList.add('pulse');
    }, 1500);
}

function setActionBtns(disabled) {
    actionBtns.forEach(b => b.disabled = disabled);
}

// ─── 행동 처리 ────────────────────────────────────────────
const ANIM_MAP = { feed: 'bounce', play: 'bounce', sleep: 'pulse', pet: 'bounce' };
const THINKING = { feed: '냠냠...', play: '신난다!...', sleep: 'Zzz...', pet: '그르릉...' };

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
                showSpeech(json.reaction || MOODS[json.mood]?.speech || '냥~');
            }
        } catch {
            showSpeech('오류가 발생했어요 냥...');
        } finally {
            setActionBtns(false);
        }
    });
});

// ─── 이름 변경 ────────────────────────────────────────────
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
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: form,
    });
    const json = await res.json();
    if (json.csrf_hash) csrfHash = json.csrf_hash;

    if (json.success) {
        catNameDisplay.textContent = json.name;
        document.getElementById('renameForm').classList.add('d-none');
    }
});

// ─── 초기화 ───────────────────────────────────────────────
document.getElementById('resetBtn').addEventListener('click', async () => {
    if (! confirm('고양이 상태를 초기화할까요?')) return;

    const form = new URLSearchParams();
    form.append(CSRF_TOKEN, csrfHash);

    const res  = await fetch('<?= base_url('examples/cat-game/reset') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: form,
    });
    const json = await res.json();
    if (json.csrf_hash) csrfHash = json.csrf_hash;

    if (json.success) {
        updateUI({ hunger: 70, happiness: 70, energy: 70, mood: 'happy' });
        showSpeech('냥~ 다시 시작해요!');
        animateCat('bounce');
    }
});

// ─── 30초마다 상태 폴링 ───────────────────────────────────
setInterval(async () => {
    try {
        const res  = await fetch('<?= base_url('examples/cat-game/status') ?>');
        const json = await res.json();
        updateUI(json);
        // 위험 상태면 말풍선 업데이트
        if (json.mood === 'critical' || json.mood === 'hungry' || json.mood === 'tired') {
            showSpeech(MOODS[json.mood]?.speech ?? '냥~');
        }
    } catch { /* silent */ }
}, 30000);
</script>

<?= $this->endSection() ?>
