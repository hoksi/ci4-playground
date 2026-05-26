<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-database-gear me-2"></i>Query Builder 고급</h1>
    <p>JOIN, 서브쿼리, GROUP BY / HAVING 집계, Raw SQL 직접 실행 등 Query Builder의 고급 기능을 학습합니다.</p>
</div>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active">Query Builder 고급</li>
    </ol>
</nav>

<ul class="nav nav-tabs mb-4" id="mainTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-join">JOIN</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-subquery">서브쿼리</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-aggregate">집계</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-raw">Raw SQL</a></li>
</ul>

<div class="tab-content">

    <!-- ── JOIN ──────────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-join">
        <div class="example-card mb-4">
            <div class="example-card-header">
                <i class="bi bi-diagram-2 text-primary"></i>
                <h5>INNER JOIN / LEFT JOIN 실행 결과</h5>
                <button id="btnJoin" class="btn btn-sm btn-primary ms-auto">
                    <i class="bi bi-play-circle me-1"></i> 쿼리 실행
                </button>
            </div>
            <div class="example-card-body">
                <div id="joinResult">
                    <div class="result-box info">쿼리 실행 버튼을 눌러 결과를 확인하세요.</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-code-slash text-primary"></i>
                        <h5>INNER JOIN — Query Builder</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">$db = \Config\Database::connect();

// INNER JOIN: 양쪽 테이블에 모두 존재하는 행만 반환
$result = $db->table('posts p')
    ->select('p.id, p.title, p.view_count, a.name AS author')
    ->join('accounts a', 'p.author_id = a.id', 'inner')
    ->orderBy('p.id')
    ->get()->getResultArray();</code></pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-code-slash text-success"></i>
                        <h5>LEFT JOIN — Query Builder</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">// LEFT JOIN: 왼쪽 테이블 전체 + 오른쪽 일치 행
// 오른쪽에 없으면 NULL 반환
$result = $db->table('users_demo u')
    ->select('u.id, u.username, u.role, k.api_key')
    ->join('api_keys k', 'u.id = k.id', 'left')
    ->orderBy('u.id')
    ->get()->getResultArray();

// join() 세 번째 파라미터
// 'inner' / 'left' / 'right' / 'outer'
// 'left outer' / 'right outer' / 'cross'</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── 서브쿼리 ──────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-subquery">
        <div class="example-card mb-4">
            <div class="example-card-header">
                <i class="bi bi-layers text-warning"></i>
                <h5>서브쿼리 — 평균 조회수 이상인 게시글</h5>
                <button id="btnSubquery" class="btn btn-sm btn-warning ms-auto">
                    <i class="bi bi-play-circle me-1"></i> 쿼리 실행
                </button>
            </div>
            <div class="example-card-body">
                <div id="subqueryResult">
                    <div class="result-box info">쿼리 실행 버튼을 눌러 결과를 확인하세요.</div>
                </div>
            </div>
        </div>

        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-code-slash text-warning"></i>
                <h5>서브쿼리 코드 예제</h5>
            </div>
            <div class="example-card-body">
                <div class="code-label">방법 1: 직접 SQL 서브쿼리</div>
                <pre><code class="language-php">$result = $db->query("
    SELECT id, title, view_count
    FROM posts
    WHERE view_count >= (
        SELECT AVG(view_count) FROM posts
    )
    ORDER BY view_count DESC
")->getResultArray();</code></pre>

                <div class="code-label mt-4">방법 2: Query Builder subquery() (CI4 4.3+)</div>
                <pre><code class="language-php">// 서브쿼리 빌더 생성
$subQuery = $db->table('posts')
    ->selectAvg('view_count', 'avg_views');

// 메인 쿼리에서 서브쿼리 사용
$result = $db->table('posts')
    ->where('view_count >=', $subQuery->getCompiledSelect(), false)
    ->orderBy('view_count', 'DESC')
    ->get()->getResultArray();</code></pre>

                <div class="code-label mt-4">방법 3: WHERE IN 서브쿼리</div>
                <pre><code class="language-php">// WHERE IN (서브쿼리)
$activeIds = $db->table('api_keys')
    ->select('id')
    ->where('is_active', 1)
    ->getCompiledSelect();

$result = $db->table('users_demo')
    ->whereIn('id', $activeIds, false)
    ->get()->getResultArray();</code></pre>
            </div>
        </div>
    </div>

    <!-- ── 집계 ──────────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-aggregate">
        <div class="example-card mb-4">
            <div class="example-card-header">
                <i class="bi bi-bar-chart text-success"></i>
                <h5>GROUP BY / HAVING / 집계 함수 결과</h5>
                <button id="btnAggregate" class="btn btn-sm btn-success ms-auto">
                    <i class="bi bi-play-circle me-1"></i> 쿼리 실행
                </button>
            </div>
            <div class="example-card-body">
                <div id="aggregateResult">
                    <div class="result-box info">쿼리 실행 버튼을 눌러 결과를 확인하세요.</div>
                </div>
            </div>
        </div>

        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-code-slash text-success"></i>
                <h5>집계 코드 예제</h5>
            </div>
            <div class="example-card-body">
                <pre><code class="language-php">// COUNT, SUM, AVG, MAX, MIN + GROUP BY + HAVING
$result = $db->table('posts')
    ->select('DATE(created_at) AS post_date')
    ->selectCount('*', 'post_count')
    ->selectAvg('view_count', 'avg_views')
    ->selectSum('view_count', 'total_views')
    ->selectMax('view_count', 'max_views')
    ->selectMin('view_count', 'min_views')
    ->groupBy('DATE(created_at)')
    ->having('post_count >=', 1)
    ->orderBy('post_date', 'DESC')
    ->get()->getResultArray();

// 역할별 유저 수
$byRole = $db->table('users_demo')
    ->select('role')
    ->selectCount('*', 'cnt')
    ->groupBy('role')
    ->having('cnt >=', 1)
    ->get()->getResultArray();</code></pre>
            </div>
        </div>
    </div>

    <!-- ── Raw SQL ───────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-raw">
        <div class="example-card mb-4">
            <div class="example-card-header">
                <i class="bi bi-terminal text-danger"></i>
                <h5>Raw SQL 실행 결과</h5>
                <button id="btnRaw" class="btn btn-sm btn-danger ms-auto">
                    <i class="bi bi-play-circle me-1"></i> 쿼리 실행
                </button>
            </div>
            <div class="example-card-body">
                <div id="rawResult">
                    <div class="result-box info">쿼리 실행 버튼을 눌러 결과를 확인하세요.</div>
                </div>
            </div>
        </div>

        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-code-slash text-danger"></i>
                <h5>Raw SQL / query() 코드 예제</h5>
            </div>
            <div class="example-card-body">
                <div class="code-label">파라미터 바인딩 쿼리</div>
                <pre><code class="language-php">$db = \Config\Database::connect();

// ? 플레이스홀더 사용 (SQL 인젝션 방지)
$result = $db->query(
    "SELECT id, title, view_count FROM posts
     WHERE view_count > ? ORDER BY view_count DESC LIMIT 5",
    [100]
)->getResultArray();

// 마지막 실행된 쿼리 확인 (디버깅용)
$lastQuery = $db->getLastQuery();
echo $lastQuery; // 완성된 쿼리 문자열 출력</code></pre>

                <div class="code-label mt-4">DB 네이티브 함수 사용</div>
                <pre><code class="language-php">// select()에 false를 전달하면 이스케이프 없이 SQL 표현식 사용 가능
$result = $db->table('posts')
    ->select("id, title, LENGTH(title) AS title_len,
              UPPER(title) AS title_upper", false)
    ->orderBy('title_len', 'DESC')
    ->limit(5)
    ->get()->getResultArray();</code></pre>

                <div class="code-label mt-4">트랜잭션과 함께 사용</div>
                <pre><code class="language-php">$db->transStart();

$db->query("UPDATE accounts SET balance = balance - ? WHERE id = ?", [1000, 1]);
$db->query("UPDATE accounts SET balance = balance + ? WHERE id = ?", [1000, 2]);

$db->transComplete();

if ($db->transStatus() === false) {
    // 롤백됨
}</code></pre>
                <div class="result-box warning mt-3 small">
                    <strong>주의:</strong> <code>$db->query()</code>를 쓸 때 항상 파라미터 바인딩(<code>?</code>)을 사용해 SQL 인젝션을 방지하세요.
                    사용자 입력을 쿼리 문자열에 직접 삽입하면 안 됩니다.
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
async function runQuery(url, resultDivId) {
    const res  = await fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
    const data = await res.json();
    return {res, data};
}

function renderTable(columns, rows) {
    if (!rows || rows.length === 0) {
        return '<div class="result-box warning">결과가 없습니다.</div>';
    }
    let html = '<div class="table-responsive"><table class="table table-sm table-hover table-bordered mb-0"><thead class="table-light"><tr>';
    columns.forEach(c => { html += `<th>${c}</th>`; });
    html += '</tr></thead><tbody>';
    rows.forEach(row => {
        html += '<tr>';
        columns.forEach(c => {
            const val = row[c] ?? '<span class="text-muted">NULL</span>';
            html += `<td class="small">${val}</td>`;
        });
        html += '</tr>';
    });
    html += '</tbody></table></div>';
    return html;
}

document.getElementById('btnJoin').addEventListener('click', async () => {
    const {data} = await runQuery('<?= base_url('examples/querybuilderadvanced/joins') ?>');
    let html = '<div class="row g-3">';

    html += '<div class="col-12"><div class="code-label">INNER JOIN — posts × accounts</div>';
    const iCols = data.inner_join.length > 0 ? Object.keys(data.inner_join[0]) : [];
    html += renderTable(iCols, data.inner_join) + '</div>';

    html += '<div class="col-12 mt-2"><div class="code-label">LEFT JOIN — users_demo × api_keys</div>';
    const lCols = data.left_join.length > 0 ? Object.keys(data.left_join[0]) : [];
    html += renderTable(lCols, data.left_join) + '</div>';

    html += '</div>';
    document.getElementById('joinResult').innerHTML = html;
});

document.getElementById('btnSubquery').addEventListener('click', async () => {
    const {data} = await runQuery('<?= base_url('examples/querybuilderadvanced/subquery') ?>');
    let html = `<div class="result-box info mb-3">
        <strong>평균 조회수:</strong> <code>${data.avg_views}</code> &nbsp;|&nbsp;
        <strong>기준 이상 게시글:</strong> <code>${data.count}건</code>
    </div>`;
    const cols = data.results.length > 0 ? Object.keys(data.results[0]) : [];
    html += renderTable(cols, data.results);
    document.getElementById('subqueryResult').innerHTML = html;
});

document.getElementById('btnAggregate').addEventListener('click', async () => {
    const {data} = await runQuery('<?= base_url('examples/querybuilderadvanced/aggregate') ?>');
    let html = '<div class="row g-3">';

    html += '<div class="col-md-4"><div class="code-label">계좌 목록 (잔액 기준)</div>';
    const aCols = data.account_stats.length > 0 ? Object.keys(data.account_stats[0]) : [];
    html += renderTable(aCols, data.account_stats) + '</div>';

    html += '<div class="col-md-4"><div class="code-label">일별 게시글 집계</div>';
    const pCols = data.post_stats.length > 0 ? Object.keys(data.post_stats[0]) : [];
    html += renderTable(pCols, data.post_stats) + '</div>';

    html += '<div class="col-md-4"><div class="code-label">역할별 유저 수</div>';
    const uCols = data.users_by_role.length > 0 ? Object.keys(data.users_by_role[0]) : [];
    html += renderTable(uCols, data.users_by_role) + '</div>';

    html += '</div>';
    document.getElementById('aggregateResult').innerHTML = html;
});

document.getElementById('btnRaw').addEventListener('click', async () => {
    const {data} = await runQuery('<?= base_url('examples/querybuilderadvanced/raw') ?>');
    let html = '';

    html += '<div class="code-label">파라미터 바인딩 결과 (view_count > 0, 상위 5개)</div>';
    const p1Cols = data.parameterized.length > 0 ? Object.keys(data.parameterized[0]) : [];
    html += renderTable(p1Cols, data.parameterized);

    html += '<div class="code-label mt-3">마지막 실행된 쿼리 (getLastQuery)</div>';
    html += `<pre style="background:#1e1e1e;color:#d4d4d4;padding:.75rem 1rem;border-radius:8px;font-size:.84rem;">${escapeHtml(data.last_query)}</pre>`;

    html += '<div class="code-label mt-3">DB 네이티브 함수 (title 길이, 대문자)</div>';
    const p2Cols = data.raw_functions.length > 0 ? Object.keys(data.raw_functions[0]) : [];
    html += renderTable(p2Cols, data.raw_functions);

    html += '<div class="code-label mt-3">accounts 집계 (COUNT/SUM/MAX/MIN)</div>';
    const agg = data.aggregation;
    html += `<div class="result-box info small">
        <strong>총 계좌수:</strong> ${agg.total} &nbsp;|&nbsp;
        <strong>잔액 합계:</strong> ${Number(agg.total_balance).toLocaleString()}원 &nbsp;|&nbsp;
        <strong>최대:</strong> ${Number(agg.max_balance).toLocaleString()}원 &nbsp;|&nbsp;
        <strong>최소:</strong> ${Number(agg.min_balance).toLocaleString()}원
    </div>`;

    document.getElementById('rawResult').innerHTML = html;
});

function escapeHtml(str) {
    return String(str || '')
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
</script>
<?= $this->endSection() ?>
