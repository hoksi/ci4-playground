<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <div class="rounded-circle d-flex align-items-center justify-content-center"
         style="width:52px;height:52px;background:#ede7f6;">
        <i class="bi bi-clock-history fs-3" style="color:#6610f2;"></i>
    </div>
    <div>
        <h2 class="mb-0">Task Scheduler</h2>
        <p class="text-muted mb-0">codeigniter4/tasks — 클로저·커맨드·셸 스케줄 등록 및 수동 실행</p>
    </div>
</div>

<!-- 탭 -->
<ul class="nav nav-tabs mb-4" id="mainTab">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-code">코드</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-cron">Cron 설정</a></li>
</ul>

<div class="tab-content">

<!-- ══════════════════════════════════════════════════════
     탭 1 — 라이브 데모
══════════════════════════════════════════════════════ -->
<div class="tab-pane fade show active" id="tab-demo">

    <div class="alert alert-info d-flex gap-2 mb-4">
        <i class="bi bi-info-circle-fill mt-1"></i>
        <div>
            <strong>운영 방법:</strong> cron에 <code>* * * * * cd /path &amp;&amp; php spark tasks:run &gt;&gt; /dev/null 2&gt;&amp;1</code>을 등록하면
            매분 실행되어 스케줄에 맞는 태스크가 자동으로 처리됩니다.
            아래 <strong>▶ 실행</strong> 버튼으로 태스크를 즉시 수동 실행할 수 있습니다.
        </div>
    </div>

    <!-- 태스크 목록 -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-task me-2"></i>등록된 태스크 (<?= count($tasks) ?>개)</span>
            <span class="badge bg-secondary">현재 시각: <span id="nowTime"></span></span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>이름</th>
                        <th>타입</th>
                        <th>액션</th>
                        <th>스케줄</th>
                        <th>Cron 표현식</th>
                        <th class="text-center">지금 실행?</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($tasks as $t): ?>
                <tr id="row-<?= esc($t['name']) ?>">
                    <td><code class="text-purple fw-semibold"><?= esc($t['name']) ?></code></td>
                    <td><?= typeBadge($t['type']) ?></td>
                    <td><small class="text-muted font-monospace"><?= esc($t['action']) ?></small></td>
                    <td><span class="badge bg-light text-dark border"><?= esc($t['schedule']) ?></span></td>
                    <td><code class="small"><?= esc($t['expression']) ?></code></td>
                    <td class="text-center">
                        <?php if ($t['shouldRun']): ?>
                            <span class="badge bg-success">✓ 대기 중</span>
                        <?php else: ?>
                            <span class="badge bg-light text-secondary border">–</span>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if ($t['runnable']): ?>
                        <button class="btn btn-sm btn-outline-purple run-btn"
                                data-name="<?= esc($t['name']) ?>"
                                style="color:#6610f2;border-color:#6610f2;">
                            <i class="bi bi-play-fill"></i> 실행
                        </button>
                        <?php else: ?>
                        <span class="text-muted small">—</span>
                        <?php endif ?>
                    </td>
                </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 실행 결과 -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-terminal me-2"></i>실행 결과
        </div>
        <div class="card-body p-0">
            <div id="outputArea" class="p-3 font-monospace small"
                 style="background:#1e1e2e;color:#cdd6f4;min-height:120px;border-radius:0 0 .375rem .375rem;">
                <span class="text-secondary">태스크 실행 버튼을 클릭하면 결과가 여기에 표시됩니다.</span>
            </div>
        </div>
    </div>

    <!-- spark tasks:list 출력 -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-terminal-fill me-2"></i><code>php spark tasks:list</code> 출력 예시
        </div>
        <div class="card-body p-0">
            <pre class="p-3 mb-0 small" style="background:#1e1e2e;color:#cdd6f4;border-radius:0 0 .375rem .375rem;">+------------------+---------+---------------+--------------+
| Task             | Type    | Expression    | Next Run     |
+------------------+---------+---------------+--------------+
| health-check     | closure | * * * * *     | (every min)  |
| queue-monitor    | closure | */5 * * * *   | (every 5min) |
| cache-clear      | command | 0 * * * *     | (every hour) |
| daily-stats      | closure | 0 0 * * *     | (midnight)   |
| playground-reset | command | 0 3 * * *     | (3am daily)  |
+------------------+---------+---------------+--------------+</pre>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════════════════════
     탭 2 — 코드
══════════════════════════════════════════════════════ -->
<div class="tab-pane fade" id="tab-code">

    <div class="row g-4">
        <!-- 태스크 유형 -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">태스크 유형과 스케줄 메서드</div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ([
                            ['closure', 'primary', 'bi-braces', '클로저', 'PHP 익명 함수를 직접 실행. 간단한 로직에 적합.'],
                            ['command', 'dark',    'bi-terminal-fill', 'Spark 커맨드', 'php spark {name} 형태의 CLI 커맨드 실행.'],
                            ['shell',   'secondary','bi-terminal', '셸 커맨드', '운영체제 셸 명령 실행. cp, rm 등 시스템 명령.'],
                            ['url',     'info',    'bi-globe2', 'URL 호출', 'HTTP GET으로 지정 URL 호출. 웹훅·핑 용도.'],
                            ['queue',   'warning', 'bi-collection-play', '큐 잡 푸시', 'codeigniter4/queue 잡을 스케줄에 맞춰 자동 push.'],
                        ] as [$type, $color, $icon, $label, $desc]): ?>
                        <div class="col-md-4">
                            <div class="d-flex gap-2 p-3 rounded border h-100">
                                <i class="bi <?= $icon ?> text-<?= $color ?> fs-5 mt-1"></i>
                                <div>
                                    <div class="fw-semibold"><?= $label ?> <span class="badge bg-<?= $color ?> ms-1"><?= $type ?></span></div>
                                    <small class="text-muted"><?= $desc ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Config/Tasks.php -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-file-code me-2"></i>app/Config/Tasks.php
                </div>
                <div class="card-body p-0">
<pre><code class="language-php">&lt;?php

namespace Config;

use CodeIgniter\Tasks\Config\Tasks as BaseTasks;
use CodeIgniter\Tasks\Scheduler;

class Tasks extends BaseTasks
{
    public bool $logPerformance = false; // true 시 settings 테이블 필요
    public int  $maxLogsPerTask  = 20;

    public function init(Scheduler $schedule): void
    {
        // ① 클로저 태스크 — 매분 실행
        $schedule-&gt;call(static function () {
            $db     = \Config\Database::connect();
            $tables = count($db-&gt;listTables());
            log_message('info', "[Task] 테이블 {$tables}개 정상");
            return "DB 연결 정상 · 테이블 {$tables}개";
        })-&gt;everyMinute()-&gt;named('health-check');

        // ② 클로저 태스크 — 5분마다
        $schedule-&gt;call(static function () {
            $db      = \Config\Database::connect();
            $pending = $db-&gt;table('queue_jobs')-&gt;where('status', 0)-&gt;countAllResults();
            return "대기 잡 {$pending}개";
        })-&gt;everyFiveMinutes()-&gt;named('queue-monitor');

        // ③ Spark 커맨드 — 매시간
        $schedule-&gt;command('cache:clear')-&gt;hourly()-&gt;named('cache-clear');

        // ④ 클로저 태스크 — 매일 자정
        $schedule-&gt;call(static function () {
            $posts = \Config\Database::connect()-&gt;table('posts')-&gt;countAllResults();
            log_message('info', "[Task] 게시글 {$posts}개");
        })-&gt;daily()-&gt;named('daily-stats');

        // ⑤ 커맨드 + 환경 제한 — 매일 새벽 3시, production만
        $schedule-&gt;command('playground:reset --db-only --quiet')
            -&gt;daily('3:00 am')
            -&gt;environments('production')  // production 환경에서만 실행
            -&gt;named('playground-reset');
    }
}</code></pre>
                </div>
            </div>
        </div>

        <!-- 스케줄 메서드 레퍼런스 -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">주요 스케줄 메서드</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>메서드</th><th>Cron</th><th>설명</th></tr></thead>
                        <tbody>
                        <?php foreach ([
                            ['everyMinute()',         '* * * * *',   '매분'],
                            ['everyFiveMinutes()',    '*/5 * * * *', '5분마다'],
                            ['everyFifteenMinutes()', '*/15 * * * *','15분마다'],
                            ['hourly()',              '0 * * * *',   '매시간 정각'],
                            ['daily()',               '0 0 * * *',   '매일 자정'],
                            ['daily(\'3:00 am\')',    '0 3 * * *',   '매일 새벽 3시'],
                            ['weekdays()',            '0 0 * * 1-5', '평일 자정'],
                            ['mondays()',             '0 0 * * 1',   '매주 월요일'],
                            ['monthly()',             '0 0 1 * *',   '매월 1일'],
                            ['cron(\'*/10 * * * *\')', '*/10 * * * *','커스텀 표현식'],
                        ] as [$m, $c, $d]): ?>
                        <tr>
                            <td><code class="small"><?= $m ?></code></td>
                            <td><code class="small text-muted"><?= $c ?></code></td>
                            <td><small><?= $d ?></small></td>
                        </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 고급 옵션 -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">고급 옵션</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>옵션</th><th>설명</th></tr></thead>
                        <tbody>
                        <?php foreach ([
                            ['->named(\'name\')',                '태스크 이름 지정. tasks:run --task=name 으로 단독 실행 가능'],
                            ['->environments(\'production\')',   '특정 CI_ENVIRONMENT에서만 실행'],
                            ['->singleInstance()',               '동시 실행 방지 (잠금 파일 사용)'],
                            ['->singleInstance(1800)',           'TTL 1800초 잠금'],
                        ] as [$opt, $desc]): ?>
                        <tr>
                            <td><code class="small"><?= $opt ?></code></td>
                            <td><small class="text-muted"><?= $desc ?></small></td>
                        </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>

                    <hr class="mx-3">
                    <div class="px-3 pb-3">
                        <p class="fw-semibold mb-2 small">특정 태스크만 실행</p>
<pre class="mb-0 small"><code class="language-bash"># 이름으로 단독 실행
php spark tasks:run --task=health-check

# 등록된 태스크 목록 조회
php spark tasks:list

# 태스크 실행 일시 중지 / 재개
php spark tasks:disable
php spark tasks:enable</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     탭 3 — Cron 설정
══════════════════════════════════════════════════════ -->
<div class="tab-pane fade" id="tab-cron">
    <div class="row g-4">

        <div class="col-12">
            <div class="alert alert-warning d-flex gap-2">
                <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                <div>
                    Task Scheduler는 <strong>cron이 매분 <code>php spark tasks:run</code>을 실행</strong>해야
                    스케줄대로 동작합니다. 아래 한 줄만 등록하면 됩니다.
                </div>
            </div>
        </div>

        <!-- cron 등록 -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">1. crontab 등록</div>
                <div class="card-body">
<pre><code class="language-bash"># crontab 편집
crontab -e

# 아래 한 줄 추가
* * * * * cd /var/www/playground &amp;&amp; php spark tasks:run &gt;&gt; /dev/null 2&gt;&amp;1</code></pre>
                    <p class="text-muted small mt-3 mb-0">
                        웹 서버 사용자(<code>www-data</code>)로 등록하려면:
                    </p>
<pre class="mt-2"><code class="language-bash">sudo crontab -u www-data -e</code></pre>
                </div>
            </div>
        </div>

        <!-- 로그 확인 -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">2. 실행 로그 확인</div>
                <div class="card-body">
<pre><code class="language-bash"># CI4 로그 파일 실시간 확인
tail -f writable/logs/log-<?= date('Y-m-d') ?>.log

# tasks:run 결과만 필터링
grep "\[Task\]" writable/logs/log-<?= date('Y-m-d') ?>.log</code></pre>
                    <p class="text-muted small mt-3 mb-0">
                        <code>logPerformance = true</code>로 설정하면 DB에 실행 시간·출력을 저장합니다.
                        (<code>codeigniter4/settings</code> 마이그레이션 필요)
                    </p>
                </div>
            </div>
        </div>

        <!-- 전체 흐름 -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">전체 실행 흐름</div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center py-2">
                        <?php foreach ([
                            ['bi-clock',          '#6610f2', 'cron (매분)'],
                            ['bi-arrow-right',    '#adb5bd', ''],
                            ['bi-terminal',       '#198754', 'spark tasks:run'],
                            ['bi-arrow-right',    '#adb5bd', ''],
                            ['bi-gear',           '#0d6efd', 'Config/Tasks.php'],
                            ['bi-arrow-right',    '#adb5bd', ''],
                            ['bi-check2-circle',  '#dc3545', 'shouldRun() 체크'],
                            ['bi-arrow-right',    '#adb5bd', ''],
                            ['bi-play-circle',    '#fd7e14', 'task->run()'],
                            ['bi-arrow-right',    '#adb5bd', ''],
                            ['bi-journal-text',   '#6c757d', 'log_message()'],
                        ] as [$icon, $color, $label]): ?>
                            <?php if ($label): ?>
                            <div class="text-center">
                                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-1"
                                     style="width:44px;height:44px;background:<?= $color ?>22;">
                                    <i class="bi <?= $icon ?>" style="color:<?= $color ?>;font-size:1.2rem;"></i>
                                </div>
                                <small style="color:<?= $color ?>;font-size:.7rem;"><?= $label ?></small>
                            </div>
                            <?php else: ?>
                            <i class="bi <?= $icon ?> text-muted fs-5 mb-3"></i>
                            <?php endif ?>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

</div><!-- /tab-content -->

<style>
.text-purple { color: #6610f2; }
pre code { font-size: .82rem; }
</style>

<script>
// 현재 시각 표시
function updateClock() {
    const now = new Date();
    document.getElementById('nowTime').textContent =
        now.toLocaleTimeString('ko-KR', {hour12: false});
}
updateClock();
setInterval(updateClock, 1000);

// 태스크 수동 실행
document.querySelectorAll('.run-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const name = btn.dataset.name;
        const out  = document.getElementById('outputArea');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> 실행 중...';
        out.innerHTML = `<span class="text-yellow-400" style="color:#f9e2af;">[${name}] 실행 중...</span>`;

        fetch('<?= base_url('examples/taskscheduler/run') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded',
                      'X-Requested-With': 'XMLHttpRequest'},
            body: new URLSearchParams({
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                name
            })
        })
        .then(r => r.json())
        .then(data => {
            const ts = new Date().toLocaleTimeString('ko-KR', {hour12: false});
            if (data.ok) {
                out.innerHTML =
                    `<span style="color:#a6e3a1;">[${ts}] ✓ ${name}</span>\n` +
                    `<span style="color:#cdd6f4;">${escHtml(data.output)}</span>`;
            } else {
                out.innerHTML =
                    `<span style="color:#f38ba8;">[${ts}] ✗ ${name}</span>\n` +
                    `<span style="color:#fab387;">${escHtml(data.error)}</span>`;
            }
        })
        .catch(e => {
            out.innerHTML = `<span style="color:#f38ba8;">네트워크 오류: ${e.message}</span>`;
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-play-fill"></i> 실행';
        });
    });
});

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>

<?php
// 뷰 헬퍼: 타입 배지
function typeBadge(string $type): string {
    $map = [
        'closure' => ['primary', '클로저'],
        'command' => ['dark',    'Spark커맨드'],
        'shell'   => ['secondary','셸'],
        'url'     => ['info',    'URL'],
        'queue'   => ['warning', '큐'],
    ];
    [$color, $label] = $map[$type] ?? ['light', $type];
    return "<span class=\"badge bg-{$color}\">{$label}</span>";
}
?>

<?= $this->endSection() ?>
