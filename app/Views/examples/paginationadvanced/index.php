<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">Pagination 심화</li>
    </ol></nav>
    <h1><i class="bi bi-collection me-2"></i>Pagination 심화</h1>
    <p>기본 페이저, AJAX 페이지네이션, 무한 스크롤 세 가지 패턴을 비교합니다.</p>
</div>

<ul class="nav nav-tabs mb-3" id="pgTab">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="basic">기본 페이지네이션</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="ajax">AJAX 페이지네이션</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="infinite">무한 스크롤</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="code">코드 설명</a></li>
</ul>

<!-- 기본 페이지네이션 -->
<div id="tab-basic" class="tab-content-pane">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-grid-3x3-gap me-2"></i>$model->paginate() 기본 사용</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                서버 측 페이지네이션 — 페이지 변경 시 전체 페이지가 새로고침됩니다 (URL <code>?page=N</code>).
            </div>

            <?php if (empty($posts)): ?>
                <p class="text-muted">포스트가 없습니다. <a href="<?= base_url('examples/repository') ?>">Repository 예제</a>에서 생성해보세요.</p>
            <?php else: ?>
                <table class="table table-sm table-hover">
                    <thead class="table-dark">
                        <tr><th>#</th><th>제목</th><th>작성자</th><th>조회</th><th>작성일</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $p): ?>
                        <tr>
                            <td><?= esc($p->id) ?></td>
                            <td><?= esc($p->title) ?></td>
                            <td><?= esc($p->author) ?></td>
                            <td><?= esc($p->views) ?></td>
                            <td><small><?= $p->created_at ? esc($p->created_at->format('Y-m-d H:i')) : '' ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">
                        총 <?= $pager->getTotal() ?>건 · 페이지 <?= $pager->getCurrentPage() ?> / <?= $pager->getPageCount() ?>
                    </small>
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- AJAX 페이지네이션 -->
<div id="tab-ajax" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-arrow-clockwise me-2"></i>AJAX 페이지네이션 (fetch API)</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                페이지 버튼 클릭 시 <code>fetch('?page=N')</code>으로 JSON만 받아 테이블을 다시 그립니다.
            </div>

            <div id="ajax-area">
                <p class="text-muted">불러오는 중...</p>
            </div>
        </div>
    </div>
</div>

<!-- 무한 스크롤 -->
<div id="tab-infinite" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-arrow-down-circle me-2"></i>무한 스크롤 (Intersection Observer)</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                스크롤하여 보이는 영역에 들어오는 sentinel 요소를 감지하면 다음 페이지를 자동 로드합니다.
            </div>
            <button id="infinite-reset" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bi bi-arrow-counterclockwise"></i> 초기화
            </button>
            <div id="infinite-list"></div>
            <div id="infinite-sentinel" class="text-center py-3 text-muted">
                <i class="bi bi-arrow-down-circle me-1"></i>스크롤하여 더 불러오기
            </div>
            <div id="infinite-status" class="text-center py-2 text-muted small"></div>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>기본 paginate()</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// Controller
$model = new \App\Models\PostModel();
$posts = $model-&gt;orderBy('id', 'DESC')-&gt;paginate(10, 'default');
$pager = $model-&gt;pager;

return view('list', compact('posts', 'pager'));

// View — Bootstrap pager 자동 렌더
&lt;?= $pager-&gt;links() ?&gt;
&lt;?= $pager-&gt;links('default', 'simple') ?&gt;       // 단순 prev/next
&lt;?= $pager-&gt;links('default', 'default_full') ?&gt; // 풀버전</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>AJAX JSON 응답</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">public function data()
{
    $page    = (int) $this-&gt;request-&gt;getGet('page')     ?: 1;
    $perPage = (int) $this-&gt;request-&gt;getGet('per_page') ?: 10;

    $model = new PostModel();
    $rows  = $model-&gt;paginate($perPage, 'default', $page);
    $pager = $model-&gt;pager;

    return $this-&gt;response-&gt;setJSON([
        'data'      =&gt; $rows,
        'total'     =&gt; $pager-&gt;getTotal(),
        'page'      =&gt; $pager-&gt;getCurrentPage(),
        'per_page'  =&gt; $pager-&gt;getPerPage(),
        'last_page' =&gt; $pager-&gt;getPageCount(),
        'has_more'  =&gt; $page &lt; $pager-&gt;getPageCount(),
    ]);
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>Pager 객체 API</h5></div>
        <div class="example-card-body">
            <table class="table table-bordered table-sm">
                <thead class="table-dark"><tr><th>메서드</th><th>설명</th></tr></thead>
                <tbody>
                    <tr><td><code>getTotal($group)</code></td><td>전체 건수</td></tr>
                    <tr><td><code>getCurrentPage($group)</code></td><td>현재 페이지 번호</td></tr>
                    <tr><td><code>getPerPage($group)</code></td><td>페이지당 건수</td></tr>
                    <tr><td><code>getPageCount($group)</code></td><td>전체 페이지 수</td></tr>
                    <tr><td><code>getLastPage($group)</code></td><td>마지막 페이지 번호</td></tr>
                    <tr><td><code>hasPrevious($group)</code></td><td>이전 페이지 존재 여부</td></tr>
                    <tr><td><code>hasNext($group)</code></td><td>다음 페이지 존재 여부</td></tr>
                    <tr><td><code>links($group, $template)</code></td><td>HTML 페이저 렌더</td></tr>
                    <tr><td><code>only(['key'])</code></td><td>페이저 URL에 쿼리스트링 보존</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>커스텀 Pager 템플릿</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// app/Config/Pager.php
public array $templates = [
    'default_full'   =&gt; 'CodeIgniter\Pager\Views\default_full',
    'default_simple' =&gt; 'CodeIgniter\Pager\Views\default_simple',
    'my_custom'      =&gt; 'App\Views\pager\custom',   // 직접 작성한 뷰
];

// View
&lt;?= $pager-&gt;links('default', 'my_custom') ?&gt;</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('#pgTab .nav-link').forEach(el => {
    el.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('#pgTab .nav-link').forEach(n => n.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.tab-content-pane').forEach(p => p.style.display = 'none');
        document.getElementById('tab-' + el.dataset.tab).style.display = 'block';
        if (el.dataset.tab === 'ajax' && ! window._ajaxLoaded) {
            loadAjaxPage(1);
            window._ajaxLoaded = true;
        }
    });
});

// ─── AJAX 페이지네이션 ───────────────────────────────
async function loadAjaxPage(page) {
    const res  = await fetch('<?= base_url('examples/paginationadvanced/data') ?>?page=' + page + '&per_page=5');
    const data = await res.json();
    const area = document.getElementById('ajax-area');

    if (! data.data.length) {
        area.innerHTML = '<p class="text-muted">데이터가 없습니다.</p>';
        return;
    }

    let html = '<table class="table table-sm table-hover"><thead class="table-dark"><tr><th>#</th><th>제목</th><th>작성자</th><th>조회</th><th>작성일</th></tr></thead><tbody>';
    data.data.forEach(r => {
        html += `<tr><td>${r.id}</td><td>${r.title}</td><td>${r.author}</td><td>${r.views}</td><td><small>${r.created}</small></td></tr>`;
    });
    html += '</tbody></table>';

    // 스마트 페이지 번호: 현재 ±2 + 양끝, 사이 공백은 …
    const p = data.page, last = data.last_page;
    const visible = new Set([1, last]);
    for (let i = p - 2; i <= p + 2; i++) { if (i >= 1 && i <= last) visible.add(i); }
    const sorted = [...visible].sort((a, b) => a - b);

    html += '<nav aria-label="페이지 탐색"><ul class="pagination pagination-sm justify-content-center mb-1">';
    html += `<li class="page-item ${p <= 1 ? 'disabled' : ''}"><a class="page-link" data-page="1"><i class="bi bi-chevron-double-left"></i></a></li>`;
    html += `<li class="page-item ${p <= 1 ? 'disabled' : ''}"><a class="page-link" data-page="${p - 1}"><i class="bi bi-chevron-left"></i></a></li>`;
    let prev = 0;
    for (const n of sorted) {
        if (n - prev > 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        html += `<li class="page-item ${n === p ? 'active' : ''}"><a class="page-link" data-page="${n}">${n}</a></li>`;
        prev = n;
    }
    html += `<li class="page-item ${p >= last ? 'disabled' : ''}"><a class="page-link" data-page="${p + 1}"><i class="bi bi-chevron-right"></i></a></li>`;
    html += `<li class="page-item ${p >= last ? 'disabled' : ''}"><a class="page-link" data-page="${last}"><i class="bi bi-chevron-double-right"></i></a></li>`;
    html += '</ul></nav>';
    html += `<p class="text-center text-muted small">총 ${data.total}건 · ${p} / ${last} 페이지</p>`;

    area.innerHTML = html;

    // 이벤트 위임으로 페이지 버튼 처리
    area.querySelectorAll('[data-page]').forEach(el => {
        el.addEventListener('click', e => {
            e.preventDefault();
            const target = parseInt(el.dataset.page);
            if (target >= 1 && target <= last) loadAjaxPage(target);
        });
    });
}
window.loadAjaxPage = loadAjaxPage;

// ─── 무한 스크롤 ──────────────────────────────────────
let infinitePage = 0;
let infiniteLoading = false;
let infiniteDone   = false;

async function loadInfiniteNext() {
    if (infiniteLoading || infiniteDone) return;
    infiniteLoading = true;
    infinitePage++;

    const status = document.getElementById('infinite-status');
    status.textContent = '페이지 ' + infinitePage + ' 로딩 중...';

    const res  = await fetch('<?= base_url('examples/paginationadvanced/data') ?>?page=' + infinitePage + '&per_page=3');
    const data = await res.json();

    const list = document.getElementById('infinite-list');
    data.data.forEach(r => {
        const card = document.createElement('div');
        card.className = 'border rounded p-3 mb-2';
        card.innerHTML = `<div class="d-flex justify-content-between"><strong>${r.title}</strong><small class="text-muted">${r.created}</small></div>
                          <small class="text-muted">by ${r.author} · 조회 ${r.views}</small>
                          <p class="mb-0 mt-1">${r.excerpt}</p>`;
        list.appendChild(card);
    });

    if (! data.has_more) {
        infiniteDone = true;
        status.innerHTML = '<i class="bi bi-check-circle me-1"></i>모든 데이터를 불러왔습니다.';
        document.getElementById('infinite-sentinel').style.display = 'none';
    } else {
        status.textContent = '페이지 ' + infinitePage + ' 로드 완료 (총 ' + data.total + '건 중)';
    }
    infiniteLoading = false;
}

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => { if (entry.isIntersecting) loadInfiniteNext(); });
}, { rootMargin: '300px' });
observer.observe(document.getElementById('infinite-sentinel'));

document.getElementById('infinite-reset').addEventListener('click', () => {
    infinitePage = 0;
    infiniteLoading = false;
    infiniteDone = false;
    document.getElementById('infinite-list').innerHTML = '';
    document.getElementById('infinite-sentinel').style.display = 'block';
    document.getElementById('infinite-status').textContent = '';
    loadInfiniteNext();
});
</script>
<?= $this->endSection() ?>
