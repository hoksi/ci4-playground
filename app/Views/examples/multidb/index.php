<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">다중 DB 연결</li>
    </ol></nav>
    <h1><i class="bi bi-database-add me-2"></i>다중 DB 연결</h1>
    <p>기본 DB와 보조 DB(다른 SQLite 파일)에 동시에 연결해 쿼리하는 패턴.</p>
</div>

<ul class="nav nav-tabs mb-3" id="mdTab">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="code">코드 설명</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="usecase">사용 사례</a></li>
</ul>

<!-- 라이브 데모 -->
<div id="tab-demo" class="tab-content-pane">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="example-card">
                <div class="example-card-header"><h5><i class="bi bi-database me-2"></i>기본 DB (default)</h5></div>
                <div class="example-card-body">
                    <div class="result-box info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        파일: <code>writable/database.db</code><br>
                        테이블: <strong><?= count($primaryTables) ?></strong>개
                    </div>
                    <?php if (empty($primaryTables)): ?>
                        <p class="text-muted">테이블이 없습니다.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($primaryTables as $t): ?>
                                <li class="list-group-item py-1"><i class="bi bi-table me-2"></i><?= esc($t) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="example-card">
                <div class="example-card-header"><h5><i class="bi bi-database-fill me-2"></i>보조 DB (secondary)</h5></div>
                <div class="example-card-body">
                    <div class="result-box warning mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        파일: <code><?= esc($secondaryPath) ?></code><br>
                        테이블: <strong><?= count($secondTables) ?></strong>개 (자동 초기화됨)
                    </div>
                    <?php if (empty($secondTables)): ?>
                        <p class="text-muted">테이블이 없습니다.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($secondTables as $t): ?>
                                <li class="list-group-item py-1"><i class="bi bi-table me-2"></i><?= esc($t) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="example-card mt-3">
        <div class="example-card-header"><h5><i class="bi bi-terminal me-2"></i>DB 선택 후 샘플 쿼리</h5></div>
        <div class="example-card-body">
            <form id="query-form" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">대상 DB</label>
                    <select name="target" class="form-select">
                        <option value="default">기본 DB (default)</option>
                        <option value="secondary">보조 DB (secondary)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                        <i class="bi bi-play"></i> 쿼리 실행
                    </button>
                </div>
            </form>

            <div id="query-result" class="mt-3" style="display:none;"></div>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>app/Config/Database.php — 그룹 설정</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">public string $defaultGroup = 'default';

public array $default = [
    'DSN'      =&gt; '',
    'database' =&gt; WRITEPATH . 'database.db',
    'DBDriver' =&gt; 'SQLite3',
    'DBPrefix' =&gt; '',
    'DBDebug'  =&gt; true,
    // ...
];

// 보조 DB 그룹 추가
public array $secondary = [
    'DSN'      =&gt; '',
    'database' =&gt; WRITEPATH . 'secondary.db',
    'DBDriver' =&gt; 'SQLite3',
    'DBPrefix' =&gt; '',
    'DBDebug'  =&gt; true,
    // ...
];</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>설정에 정의된 그룹으로 연결</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 기본 DB (default 그룹)
$db1 = \Config\Database::connect();

// 보조 DB (secondary 그룹) — 이름으로 호출
$db2 = \Config\Database::connect('secondary');

// 두 DB에 동시에 쿼리
$users    = $db1-&gt;table('users')-&gt;get()-&gt;getResultArray();
$products = $db2-&gt;table('products')-&gt;get()-&gt;getResultArray();</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>런타임 설정 배열로 연결</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 설정 파일을 수정하지 않고 동적으로 연결
$db = \Config\Database::connect([
    'DSN'      =&gt; '',
    'hostname' =&gt; '',
    'username' =&gt; '',
    'password' =&gt; '',
    'database' =&gt; WRITEPATH . 'secondary.db',
    'DBDriver' =&gt; 'SQLite3',
    'DBPrefix' =&gt; '',
    'pConnect' =&gt; false,
    'DBDebug'  =&gt; true,
    'charset'  =&gt; 'utf8',
    'DBCollat' =&gt; 'utf8_general_ci',
    'swapPre'  =&gt; '',
    'encrypt'  =&gt; false,
    'compress' =&gt; false,
    'strictOn' =&gt; false,
    'failover' =&gt; [],
    'port'     =&gt; 0,
]);

$tables = $db-&gt;listTables();
$rows   = $db-&gt;table('products')-&gt;get()-&gt;getResultArray();</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>Model에서 특정 DB 그룹 사용</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">class ProductModel extends \CodeIgniter\Model
{
    protected $DBGroup = 'secondary';  // ← 보조 DB 사용
    protected $table   = 'products';
}</code></pre>
        </div>
    </div>
</div>

<!-- 사용 사례 -->
<div id="tab-usecase" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-diagram-3 me-2"></i>다중 DB 사용 사례</h5></div>
        <div class="example-card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="result-box info">
                        <h6><i class="bi bi-arrow-left-right me-2"></i>읽기/쓰기 분리</h6>
                        <p class="mb-0">Master DB는 INSERT/UPDATE, Replica는 SELECT만 처리해 부하 분산. 트래픽 많은 서비스의 기본 패턴.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="result-box warning">
                        <h6><i class="bi bi-people me-2"></i>멀티 테넌트 (SaaS)</h6>
                        <p class="mb-0">테넌트별 DB를 분리해 데이터 격리. 도메인/세션으로 동적으로 DB 선택.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="result-box">
                        <h6><i class="bi bi-archive me-2"></i>아카이브 / 분석 DB</h6>
                        <p class="mb-0">실시간 DB와 분석/리포트 DB를 분리. OLTP/OLAP 패턴.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="result-box danger">
                        <h6><i class="bi bi-arrow-repeat me-2"></i>레거시 마이그레이션</h6>
                        <p class="mb-0">신규/구버전 DB를 동시에 사용하면서 점진적으로 데이터를 이관하는 단계.</p>
                    </div>
                </div>
            </div>

            <div class="result-box info mt-3">
                <strong><i class="bi bi-lightbulb me-2"></i>실무 팁</strong>
                <ul class="mb-0 mt-2">
                    <li>가능하면 그룹은 <code>Config/Database.php</code>에 정의하고, 코드는 <code>connect('group')</code>만 호출.</li>
                    <li>트랜잭션은 DB 인스턴스별로 처리됩니다. 2개 DB 동시 트랜잭션은 2-phase commit 필요.</li>
                    <li>같은 DB 그룹은 한 번 연결 후 캐싱됩니다 (싱글톤). 매번 새로 만드는 비용 없음.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('#mdTab .nav-link').forEach(el => {
    el.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('#mdTab .nav-link').forEach(n => n.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.tab-content-pane').forEach(p => p.style.display = 'none');
        document.getElementById('tab-' + el.dataset.tab).style.display = 'block';
    });
});

document.getElementById('query-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const res  = await fetch('<?= base_url('examples/multidb/query') ?>', { method: 'POST', body: formData });
    const data = await res.json();
    const area = document.getElementById('query-result');
    area.style.display = 'block';

    if (! data.success) {
        area.innerHTML = '<div class="result-box danger"><i class="bi bi-x-circle me-2"></i>' + data.message + '</div>';
        return;
    }

    let html = '<div class="result-box mb-3"><i class="bi bi-check-circle me-2"></i>대상: <strong>' + data.target + '</strong> · 드라이버: <code>' + data.driver + '</code> · DB: <code>' + data.database + '</code></div>';

    html += '<h6 class="mt-3">테이블 목록 (' + data.tables.length + ')</h6>';
    html += '<div class="mb-3">' + data.tables.map(t => '<span class="badge bg-secondary me-1">' + t + '</span>').join('') + '</div>';

    if (data.first_table) {
        html += '<h6>샘플 쿼리: <code>' + data.first_table + '</code> (총 ' + data.total_rows + '건, 상위 5건)</h6>';
        if (data.sample.length === 0) {
            html += '<p class="text-muted">데이터가 없습니다.</p>';
        } else {
            const keys = Object.keys(data.sample[0]);
            html += '<table class="table table-sm table-bordered"><thead class="table-dark"><tr>';
            keys.forEach(k => html += '<th>' + k + '</th>');
            html += '</tr></thead><tbody>';
            data.sample.forEach(row => {
                html += '<tr>';
                keys.forEach(k => html += '<td>' + (row[k] !== null ? row[k] : '<em>NULL</em>') + '</td>');
                html += '</tr>';
            });
            html += '</tbody></table>';
        }
    }
    area.innerHTML = html;
});
</script>
<?= $this->endSection() ?>
