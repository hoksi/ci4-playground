<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">차트 (Chart.js)</li>
    </ol></nav>
    <h1><i class="bi bi-bar-chart-line me-2"></i>차트 (Chart.js)</h1>
    <p>CI4 JSON API와 Chart.js를 연동하는 다양한 차트 패턴을 보여줍니다.</p>
</div>

<ul class="nav nav-tabs mb-3" id="chartTab">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="line">꺾은선</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="bar">막대</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="pie">원형</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="mixed">복합</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="code">코드 설명</a></li>
</ul>

<!-- 꺾은선 -->
<div id="tab-line" class="tab-content-pane">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-graph-up me-2"></i>월별 게시글 등록 수 (Line)</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                <code>posts</code> 테이블의 <code>created_at</code> 을 월 단위로 집계합니다.
            </div>
            <canvas id="lineChart" style="max-height:320px;"></canvas>
        </div>
    </div>
</div>

<!-- 막대 -->
<div id="tab-bar" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-bar-chart me-2"></i>작성자별 게시글 수 & 평균 조회수 (Bar)</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                작성자별 게시글 수(막대)와 평균 조회수(막대)를 나란히 비교합니다.
            </div>
            <canvas id="barChart" style="max-height:320px;"></canvas>
        </div>
    </div>
</div>

<!-- 원형 -->
<div id="tab-pie" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-pie-chart me-2"></i>조회수 구간별 분포 (Doughnut)</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                게시글 조회수를 구간(0–100, 101–500, 501–1000, 1001+)으로 나눠 분포를 표시합니다.
            </div>
            <div style="max-width:380px;margin:0 auto;">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- 복합 -->
<div id="tab-mixed" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-graph-up-arrow me-2"></i>월별 게시글 수 + 평균 조회수 (Mixed)</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                막대(게시글 수)와 꺾은선(평균 조회수)을 하나의 차트에 표시합니다.
            </div>
            <canvas id="mixedChart" style="max-height:320px;"></canvas>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>CI4 Controller — JSON 데이터 반환</h5></div>
        <div class="example-card-body">
<pre><code class="language-php">// 월별 게시글 수 집계
public function lineData(): ResponseInterface
{
    $rows = db_connect()
        ->table('posts')
        ->select("strftime('%Y-%m', created_at) as month, COUNT(*) as count")
        ->where('deleted_at', null)
        ->groupBy('month')
        ->orderBy('month', 'ASC')
        ->get()->getResultArray();

    return $this->response->setJSON([
        'labels' => array_column($rows, 'month'),
        'data'   => array_map('intval', array_column($rows, 'count')),
    ]);
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>Chart.js — fetch API로 데이터 로드</h5></div>
        <div class="example-card-body">
<pre><code class="language-js">const res  = await fetch('/examples/chart/line-data');
const json = await res.json();

new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: json.labels,
        datasets: [{
            label: '게시글 수',
            data: json.data,
            fill: true,
            tension: 0.4,   // 곡선 스무딩
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
    }
});</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>복합 차트 — type 혼용</h5></div>
        <div class="example-card-body">
<pre><code class="language-js">new Chart(ctx, {
    data: {
        labels: json.labels,
        datasets: [
            {
                type: 'bar',          // 막대
                label: '게시글 수',
                data: json.counts,
                yAxisID: 'y',
            },
            {
                type: 'line',         // 꺾은선 (같은 차트에 중첩)
                label: '평균 조회수',
                data: json.avgViews,
                yAxisID: 'y1',        // 별도 Y축
                tension: 0.4,
            }
        ]
    },
    options: {
        scales: {
            y:  { position: 'left' },
            y1: { position: 'right', grid: { drawOnChartArea: false } }
        }
    }
});</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>Chart.js CDN</h5></div>
        <div class="example-card-body">
<pre><code class="language-html">&lt;!-- jsDelivr CDN --&gt;
&lt;script src="https://cdn.jsdelivr.net/npm/chart.js"&gt;&lt;/script&gt;</code></pre>
            <p class="mt-2 text-muted small">버전을 고정하려면 <code>chart.js@4.4.4</code> 처럼 명시합니다.</p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ─── 탭 전환 + 차트 지연 로드 ─────────────────────────
const loaded = {};

document.querySelectorAll('#chartTab .nav-link').forEach(el => {
    el.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('#chartTab .nav-link').forEach(n => n.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.tab-content-pane').forEach(p => p.style.display = 'none');
        const tab = el.dataset.tab;
        document.getElementById('tab-' + tab).style.display = 'block';
        if (! loaded[tab] && tab !== 'code') {
            loaded[tab] = true;
            initChart(tab);
        }
    });
});

// ─── 팔레트 ───────────────────────────────────────────
const COLORS = [
    'rgba(99,102,241,.8)', 'rgba(244,63,94,.8)', 'rgba(16,185,129,.8)',
    'rgba(245,158,11,.8)', 'rgba(59,130,246,.8)', 'rgba(168,85,247,.8)',
    'rgba(20,184,166,.8)', 'rgba(249,115,22,.8)', 'rgba(236,72,153,.8)',
    'rgba(100,116,139,.8)',
];

async function initChart(tab) {
    const urls = {
        line:  '<?= base_url('examples/chart/line-data') ?>',
        bar:   '<?= base_url('examples/chart/bar-data') ?>',
        pie:   '<?= base_url('examples/chart/pie-data') ?>',
        mixed: '<?= base_url('examples/chart/mixed-data') ?>',
    };

    const json = await fetch(urls[tab]).then(r => r.json());

    if (tab === 'line')  buildLine(json);
    if (tab === 'bar')   buildBar(json);
    if (tab === 'pie')   buildPie(json);
    if (tab === 'mixed') buildMixed(json);
}

function buildLine(json) {
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: json.labels,
            datasets: [{
                label: '게시글 수',
                data:  json.data,
                fill: true,
                tension: 0.4,
                borderColor: COLORS[0],
                backgroundColor: COLORS[0].replace('.8)', '.15)'),
                pointRadius: 5,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
        }
    });
}

function buildBar(json) {
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: json.labels,
            datasets: [
                {
                    label: '게시글 수',
                    data: json.counts,
                    backgroundColor: COLORS[0],
                },
                {
                    label: '평균 조회수',
                    data: json.avgViews,
                    backgroundColor: COLORS[1],
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true } },
        }
    });
}

function buildPie(json) {
    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {
            labels: json.labels,
            datasets: [{
                data: json.data,
                backgroundColor: COLORS.slice(0, json.labels.length),
                hoverOffset: 12,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed}건 (${Math.round(ctx.parsed / ctx.dataset.data.reduce((a,b)=>a+b,0)*100)}%)`
                    }
                }
            }
        }
    });
}

function buildMixed(json) {
    new Chart(document.getElementById('mixedChart'), {
        data: {
            labels: json.labels,
            datasets: [
                {
                    type: 'bar',
                    label: '게시글 수',
                    data: json.counts,
                    backgroundColor: COLORS[0],
                    yAxisID: 'y',
                },
                {
                    type: 'line',
                    label: '평균 조회수',
                    data: json.avgViews,
                    borderColor: COLORS[1],
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    yAxisID: 'y1',
                    pointRadius: 5,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                y:  { beginAtZero: true, position: 'left',  title: { display: true, text: '게시글 수' } },
                y1: { beginAtZero: true, position: 'right', title: { display: true, text: '평균 조회수' },
                      grid: { drawOnChartArea: false } },
            }
        }
    });
}

// 첫 탭 로드
loaded['line'] = true;
initChart('line');
</script>
<?= $this->endSection() ?>
