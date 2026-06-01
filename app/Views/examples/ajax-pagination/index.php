<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">AJAX 페이지네이션</li>
    </ol></nav>
    <h1><i class="bi bi-ui-checks-grid me-2"></i>AJAX 페이지네이션</h1>
    <p>검색·정렬·페이지당 건수를 조합한 완전한 AJAX 테이블 패턴을 구현합니다. URL 상태(<code>pushState</code>)를 유지하여 뒤로가기/즐겨찾기가 동작합니다.</p>
</div>

<ul class="nav nav-tabs mb-3" id="pageTab">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="demo">데모</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="code">코드 설명</a></li>
</ul>

<!-- 데모 탭 -->
<div id="tab-demo" class="tab-content-pane">
    <div class="example-card">
        <div class="example-card-header">
            <h5><i class="bi bi-table me-2"></i>검색·정렬·페이지네이션 통합 AJAX 테이블</h5>
        </div>
        <div class="example-card-body">

            <!-- 컨트롤 영역 -->
            <div class="row g-2 mb-3 align-items-center">
                <div class="col-sm-6 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control"
                               placeholder="제목 또는 작성자 검색..." autocomplete="off">
                        <button class="btn btn-outline-secondary" id="clearSearch" type="button" title="검색 초기화">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
                <div class="col-sm-3 col-md-2">
                    <select id="perPageSelect" class="form-select">
                        <option value="5">5개씩</option>
                        <option value="10" selected>10개씩</option>
                        <option value="20">20개씩</option>
                        <option value="50">50개씩</option>
                    </select>
                </div>
                <div class="col-sm-3 col-md-5 text-sm-end">
                    <span id="resultInfo" class="text-muted small"></span>
                </div>
            </div>

            <!-- 테이블 -->
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" id="dataTable">
                    <thead class="table-dark">
                        <tr>
                            <th class="sortable" data-col="id" style="width:60px;cursor:pointer;">
                                # <i class="bi bi-arrow-down-up sort-icon" data-col="id"></i>
                            </th>
                            <th class="sortable" data-col="title" style="cursor:pointer;">
                                제목 <i class="bi bi-arrow-down-up sort-icon" data-col="title"></i>
                            </th>
                            <th class="sortable" data-col="author" style="width:120px;cursor:pointer;">
                                작성자 <i class="bi bi-arrow-down-up sort-icon" data-col="author"></i>
                            </th>
                            <th class="sortable" data-col="views" style="width:80px;cursor:pointer;">
                                조회 <i class="bi bi-arrow-down-up sort-icon" data-col="views"></i>
                            </th>
                            <th class="sortable" data-col="created_at" style="width:130px;cursor:pointer;">
                                작성일 <i class="bi bi-arrow-down-up sort-icon" data-col="created_at"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="5" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm me-2"></div>불러오는 중...
                        </td></tr>
                    </tbody>
                </table>
            </div>

            <!-- 로딩 오버레이 -->
            <div id="loadingOverlay" class="d-none">
                <div class="text-center py-3 text-muted small">
                    <div class="spinner-border spinner-border-sm me-1"></div> 데이터 로딩 중...
                </div>
            </div>

            <!-- 페이지네이션 -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
                <div id="pageInfo" class="text-muted small"></div>
                <nav id="pagination" aria-label="페이지 탐색"></nav>
            </div>

        </div>
    </div>

    <div class="example-card mt-3">
        <div class="example-card-header"><h5><i class="bi bi-link-45deg me-2"></i>URL 상태 유지</h5></div>
        <div class="example-card-body">
            <div class="result-box info">
                <i class="bi bi-info-circle me-2"></i>
                아래 URL이 현재 테이블 상태를 반영합니다. 복사해서 붙여넣으면 동일한 상태로 복원됩니다.
            </div>
            <code id="currentUrl" class="d-block mt-2 text-break small bg-light p-2 rounded"></code>
        </div>
    </div>
</div>

<!-- 코드 설명 탭 -->
<div id="tab-code" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>Controller — JSON 엔드포인트</h5></div>
        <div class="example-card-body">
<pre><code class="language-php">public function data(): ResponseInterface
{
    $page    = max(1, (int) ($this->request->getGet('page')     ?? 1));
    $perPage = max(5, min(50, (int) ($this->request->getGet('per_page') ?? 10)));
    $search  = trim($this->request->getGet('q') ?? '');
    $sort    = $this->request->getGet('sort') ?? 'id';
    $dir     = $this->request->getGet('dir')  ?? 'desc';

    // 허용 컬럼만 정렬 (SQL Injection 방지)
    $allowed = ['id', 'title', 'author', 'views', 'created_at'];
    if (! in_array($sort, $allowed, true)) $sort = 'id';
    $dir = $dir === 'asc' ? 'asc' : 'desc';

    $model = new PostModel();

    if ($search !== '') {
        $model->groupStart()
              ->like('title', $search)
              ->orLike('author', $search)
              ->groupEnd();
    }

    $model->orderBy($sort, $dir);
    $posts = $model->paginate($perPage, 'default', $page);
    $pager = $model->pager;

    return $this->response->setJSON([
        'data'      => $rows,       // 현재 페이지 행
        'total'     => $pager->getTotal(),
        'page'      => $pager->getCurrentPage(),
        'per_page'  => $pager->getPerPage(),
        'last_page' => $pager->getPageCount(),
        'from'      => ($page-1)*$perPage + 1,
        'to'        => min($page*$perPage, $total),
    ]);
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>JavaScript — 상태 관리</h5></div>
        <div class="example-card-body">
<pre><code class="language-js">// 상태 객체 하나로 모든 파라미터 관리
const state = { page: 1, perPage: 10, q: '', sort: 'id', dir: 'desc' };

async function loadData() {
    const params = new URLSearchParams(state);
    const res    = await fetch('/examples/ajax-pagination/data?' + params);
    const json   = await res.json();

    renderTable(json.data);
    renderPagination(json);

    // URL 업데이트 (뒤로가기·즐겨찾기 지원)
    history.pushState(state, '', location.pathname + '?' + params);
}

// 뒤로가기 버튼 처리
window.addEventListener('popstate', e => {
    if (e.state) Object.assign(state, e.state);
    loadData();
});</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>JavaScript — 페이지네이션 렌더 (스마트 번호)</h5></div>
        <div class="example-card-body">
<pre><code class="language-js">function renderPagination({ page, last_page }) {
    // 현재 페이지 주변 ±2, 양끝 1·last_page 항상 표시
    const pages = new Set([1, last_page]);
    for (let i = page - 2; i <= page + 2; i++) {
        if (i >= 1 && i <= last_page) pages.add(i);
    }
    const sorted = [...pages].sort((a, b) => a - b);

    let html = '&lt;ul class="pagination pagination-sm mb-0"&gt;';
    html += `&lt;li class="page-item ${page===1?'disabled':''}"&gt;
                &lt;a class="page-link" href="#" data-page="${page-1}"&gt;&amp;laquo;&lt;/a&gt;&lt;/li&gt;`;

    let prev = 0;
    for (const p of sorted) {
        if (p - prev > 1) html += `&lt;li class="page-item disabled"&gt;&lt;span class="page-link"&gt;…&lt;/span&gt;&lt;/li&gt;`;
        html += `&lt;li class="page-item ${p===page?'active':''}"&gt;
                    &lt;a class="page-link" href="#" data-page="${p}"&gt;${p}&lt;/a&gt;&lt;/li&gt;`;
        prev = p;
    }

    html += `&lt;li class="page-item ${page===last_page?'disabled':''}"&gt;
                &lt;a class="page-link" href="#" data-page="${page+1}"&gt;&amp;raquo;&lt;/a&gt;&lt;/li&gt;`;
    html += '&lt;/ul&gt;';
    paginationEl.innerHTML = html;
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>검색 디바운스 처리</h5></div>
        <div class="example-card-body">
<pre><code class="language-js">let debounceTimer;

searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        state.q    = searchInput.value.trim();
        state.page = 1;   // 검색 시 1페이지로 리셋
        loadData();
    }, 350);   // 350ms 후 요청 (타이핑 중 과도한 요청 방지)
});</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">5</span><h5>URL에서 초기 상태 복원</h5></div>
        <div class="example-card-body">
<pre><code class="language-js">// 페이지 로드 시 URL 쿼리스트링으로 상태 복원
const qs = new URLSearchParams(location.search);
state.page    = parseInt(qs.get('page'))     || 1;
state.perPage = parseInt(qs.get('per_page')) || 10;
state.q       = qs.get('q')    || '';
state.sort    = qs.get('sort') || 'id';
state.dir     = qs.get('dir')  || 'desc';

// UI에 반영
searchInput.value          = state.q;
perPageSelect.value        = state.perPage;

loadData();   // 복원된 상태로 최초 로드</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
// ─── 탭 전환 ────────────────────────────────────────────
document.querySelectorAll('#pageTab .nav-link').forEach(el => {
    el.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('#pageTab .nav-link').forEach(n => n.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.tab-content-pane').forEach(p => p.style.display = 'none');
        document.getElementById('tab-' + el.dataset.tab).style.display = 'block';
    });
});

// ─── 요소 참조 ───────────────────────────────────────────
const tableBody     = document.getElementById('tableBody');
const resultInfo    = document.getElementById('resultInfo');
const pageInfo      = document.getElementById('pageInfo');
const paginationEl  = document.getElementById('pagination');
const searchInput   = document.getElementById('searchInput');
const perPageSelect = document.getElementById('perPageSelect');
const clearSearchBtn = document.getElementById('clearSearch');
const currentUrlEl  = document.getElementById('currentUrl');

// ─── 상태 초기화 (URL 쿼리스트링 반영) ───────────────────
const qs = new URLSearchParams(location.search);
const state = {
    page:    parseInt(qs.get('page'))     || 1,
    per_page: parseInt(qs.get('per_page')) || 10,
    q:       qs.get('q')    || '',
    sort:    qs.get('sort') || 'id',
    dir:     qs.get('dir')  || 'desc',
};

searchInput.value   = state.q;
perPageSelect.value = state.per_page;

// ─── 데이터 로드 ─────────────────────────────────────────
let loadingTimer = null;

async function loadData(pushHistory = true) {
    // 로딩 표시 (300ms 이상 걸릴 때만 스피너 표시)
    loadingTimer = setTimeout(() => {
        tableBody.style.opacity = '0.4';
    }, 300);

    const params = new URLSearchParams({
        page:     state.page,
        per_page: state.per_page,
        q:        state.q,
        sort:     state.sort,
        dir:      state.dir,
    });

    try {
        const res  = await fetch('<?= base_url('examples/ajax-pagination/data') ?>?' + params);
        const json = await res.json();

        renderTable(json);
        renderPagination(json);
        renderInfo(json);
        renderSortIcons();

        if (pushHistory) {
            history.pushState({ ...state }, '', location.pathname + '?' + params);
        }
        currentUrlEl.textContent = location.href;
    } catch (err) {
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-3">
            <i class="bi bi-exclamation-triangle me-2"></i>데이터를 불러오지 못했습니다.
        </td></tr>`;
    } finally {
        clearTimeout(loadingTimer);
        tableBody.style.opacity = '1';
    }
}

// ─── 테이블 렌더 ─────────────────────────────────────────
function renderTable({ data }) {
    if (! data.length) {
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">
            <i class="bi bi-inbox me-2"></i>검색 결과가 없습니다.
        </td></tr>`;
        return;
    }

    tableBody.innerHTML = data.map(r => `
        <tr>
            <td class="text-muted">${r.id}</td>
            <td>
                <div class="fw-medium">${escHtml(r.title)}</div>
                <small class="text-muted">${escHtml(r.excerpt)}</small>
            </td>
            <td><span class="badge bg-secondary">${escHtml(r.author)}</span></td>
            <td class="text-end">${r.views.toLocaleString()}</td>
            <td><small class="text-muted">${escHtml(r.created)}</small></td>
        </tr>
    `).join('');
}

// ─── 페이지네이션 렌더 (스마트 번호 표시) ────────────────
function renderPagination({ page, last_page }) {
    if (last_page <= 1) {
        paginationEl.innerHTML = '';
        return;
    }

    // 현재 페이지 주변 ±2, 양끝 항상 표시
    const visible = new Set([1, last_page]);
    for (let i = page - 2; i <= page + 2; i++) {
        if (i >= 1 && i <= last_page) visible.add(i);
    }
    const sorted = [...visible].sort((a, b) => a - b);

    let html = '<ul class="pagination pagination-sm mb-0">';
    html += `<li class="page-item ${page <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${page - 1}" aria-label="이전">
                    <i class="bi bi-chevron-left"></i>
                </a>
             </li>`;

    let prev = 0;
    for (const p of sorted) {
        if (p - prev > 1) {
            html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        html += `<li class="page-item ${p === page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${p}">${p}</a>
                 </li>`;
        prev = p;
    }

    html += `<li class="page-item ${page >= last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${page + 1}" aria-label="다음">
                    <i class="bi bi-chevron-right"></i>
                </a>
             </li>`;
    html += '</ul>';

    paginationEl.innerHTML = html;

    paginationEl.querySelectorAll('a[data-page]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const p = parseInt(a.dataset.page);
            if (p < 1 || p > last_page) return;
            state.page = p;
            loadData();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
}

// ─── 결과 정보 렌더 ──────────────────────────────────────
function renderInfo({ total, from, to, page, last_page }) {
    if (total === 0) {
        resultInfo.textContent = '결과 없음';
        pageInfo.textContent   = '';
        return;
    }
    resultInfo.textContent = `총 ${total.toLocaleString()}건`;
    pageInfo.textContent   = `${from}–${to} / ${total.toLocaleString()}건  ·  ${page} / ${last_page} 페이지`;
}

// ─── 정렬 아이콘 렌더 ────────────────────────────────────
function renderSortIcons() {
    document.querySelectorAll('.sort-icon').forEach(icon => {
        const col = icon.dataset.col;
        icon.className = 'bi sort-icon ' + (
            col === state.sort
                ? (state.dir === 'asc' ? 'bi-sort-up text-warning' : 'bi-sort-down text-warning')
                : 'bi-arrow-down-up text-secondary'
        );
    });
}

// ─── 이벤트 : 컬럼 정렬 ─────────────────────────────────
document.querySelectorAll('th.sortable').forEach(th => {
    th.addEventListener('click', () => {
        const col = th.dataset.col;
        if (state.sort === col) {
            state.dir = state.dir === 'asc' ? 'desc' : 'asc';
        } else {
            state.sort = col;
            state.dir  = 'desc';
        }
        state.page = 1;
        loadData();
    });
});

// ─── 이벤트 : 검색 (디바운스 350ms) ─────────────────────
let debounceTimer;
searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        state.q    = searchInput.value.trim();
        state.page = 1;
        loadData();
    }, 350);
});

// ─── 이벤트 : 검색 초기화 ────────────────────────────────
clearSearchBtn.addEventListener('click', () => {
    searchInput.value = '';
    state.q    = '';
    state.page = 1;
    loadData();
    searchInput.focus();
});

// ─── 이벤트 : 페이지당 건수 ──────────────────────────────
perPageSelect.addEventListener('change', () => {
    state.per_page = parseInt(perPageSelect.value);
    state.page     = 1;
    loadData();
});

// ─── 뒤로가기/앞으로가기 ─────────────────────────────────
window.addEventListener('popstate', e => {
    if (e.state) {
        Object.assign(state, e.state);
        searchInput.value   = state.q;
        perPageSelect.value = state.per_page;
    }
    loadData(false);
});

// ─── XSS 방지 헬퍼 ───────────────────────────────────────
function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// ─── 최초 로드 ───────────────────────────────────────────
loadData(false);
</script>
<?= $this->endSection() ?>
