<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">AG Grid</li>
    </ol></nav>
    <h1><i class="bi bi-table me-2"></i>AG Grid Community</h1>
    <p>CI4 JSON API와 AG Grid Community를 연동하여 정렬·필터·페이지네이션·서버사이드 처리를 구현합니다.</p>
</div>

<?php $tab = $tab ?? 'client'; ?>
<ul class="nav nav-tabs mb-3" id="gridTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'client' ? 'active' : '' ?>" href="#" onclick="showTab('client');return false;">클라이언트 사이드</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'server' ? 'active' : '' ?>" href="#" onclick="showTab('server');return false;">서버 사이드</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- ── 클라이언트 사이드 탭 ── -->
<div id="tab-client" class="tab-content-pane" style="display:<?= $tab === 'client' ? 'block' : 'none' ?>">
    <div class="example-card mb-3">
        <div class="example-card-header">
            <h5><i class="bi bi-grid me-2"></i>클라이언트 사이드 모드</h5>
        </div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                전체 데이터를 한 번에 로드합니다. 정렬·필터·페이지네이션은 브라우저에서 처리됩니다.
            </div>

            <!-- 퀵 필터 -->
            <div class="d-flex gap-2 mb-3">
                <input type="text" id="quick-filter" class="form-control"
                       placeholder="검색어 입력 (제목, 작성자...)" style="max-width:320px;"
                       oninput="clientGridApi?.setGridOption('quickFilterText', this.value)">
                <button class="btn btn-outline-secondary" onclick="clientGridApi?.setGridOption('quickFilterText','');document.getElementById('quick-filter').value=''">
                    <i class="bi bi-x-circle"></i>
                </button>
                <button class="btn btn-outline-secondary ms-auto" onclick="clientGridApi?.exportDataAsCsv()">
                    <i class="bi bi-download me-1"></i>CSV 내보내기
                </button>
            </div>

            <!-- 클라이언트 그리드 -->
            <div id="client-grid" class="ag-theme-quartz" style="height:420px;"></div>

            <!-- 행 클릭 상세 -->
            <div id="client-detail" class="mt-3" style="display:none;">
                <div class="example-card">
                    <div class="example-card-header"><h5><i class="bi bi-card-text me-2"></i>선택된 행 상세</h5></div>
                    <div class="example-card-body" id="client-detail-body"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── 서버 사이드 탭 ── -->
<div id="tab-server" class="tab-content-pane" style="display:<?= $tab === 'server' ? 'block' : 'none' ?>">
    <div class="example-card mb-3">
        <div class="example-card-header">
            <h5><i class="bi bi-server me-2"></i>서버 사이드 모드 (Infinite Row Model)</h5>
        </div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                데이터를 청크 단위로 서버에서 요청합니다. 대용량 데이터에 적합하며 정렬도 서버에서 처리됩니다.
            </div>

            <!-- 서버 검색 -->
            <div class="d-flex gap-2 mb-3">
                <input type="text" id="server-search" class="form-control"
                       placeholder="서버 검색 (제목, 작성자...)" style="max-width:320px;">
                <button class="btn" style="background:var(--ci-red);color:#fff;" onclick="applyServerSearch()">
                    <i class="bi bi-search me-1"></i>검색
                </button>
                <button class="btn btn-outline-secondary" onclick="clearServerSearch()">초기화</button>
                <small class="text-muted ms-auto align-self-center" id="server-total"></small>
            </div>

            <!-- 서버 그리드 -->
            <div id="server-grid" class="ag-theme-quartz" style="height:420px;"></div>
        </div>
    </div>
</div>

<!-- ── 코드 설명 탭 ── -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>CDN 설정 (API 키 불필요)</h5></div>
        <div class="example-card-body">
            <pre><code class="language-html">&lt;!-- AG Grid Community — jsDelivr CDN, 무료 오픈소스 --&gt;
&lt;link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@33/styles/ag-grid.min.css"&gt;
&lt;link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@33/styles/ag-theme-quartz.min.css"&gt;
&lt;script src="https://cdn.jsdelivr.net/npm/ag-grid-community@33/dist/ag-grid-community.min.js"&gt;&lt;/script&gt;</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>클라이언트 사이드 — 전체 데이터 로드</h5></div>
        <div class="example-card-body">
            <pre><code class="language-javascript">// 컬럼 정의
const colDefs = [
    { field: 'id',         headerName: '#',    width: 70 },
    { field: 'title',      headerName: '제목', flex: 2, filter: true },
    { field: 'author',     headerName: '작성자', width: 120 },
    { field: 'views',      headerName: '조회수', width: 100,
      cellRenderer: p => `&lt;span class="badge"&gt;${p.value}&lt;/span&gt;` },
    { field: 'created_at', headerName: '작성일', width: 150 },
];

// 그리드 생성
const gridApi = agGrid.createGrid(document.getElementById('my-grid'), {
    columnDefs: colDefs,
    defaultColDef: { sortable: true, resizable: true },
    pagination: true,
    paginationPageSize: 10,
    rowData: [],  // 초기 빈 데이터
});

// CI4 API에서 데이터 로드
fetch('/examples/aggrid/data')
    .then(r => r.json())
    .then(rows => gridApi.setGridOption('rowData', rows));</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>CI4 — 클라이언트 데이터 API</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">public function data(): Response
{
    $rows = db_connect()
        ->table('posts')
        ->select('id, title, author, views, created_at')
        ->where('deleted_at', null)
        ->orderBy('id', 'ASC')
        ->get()
        ->getResultArray();

    return $this->response->setJSON($rows);
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>서버 사이드 — Infinite Row Model</h5></div>
        <div class="example-card-body">
            <pre><code class="language-javascript">let serverSearch = '';

const gridApi = agGrid.createGrid(document.getElementById('server-grid'), {
    columnDefs: colDefs,
    rowModelType: 'infinite',    // 서버 사이드 무한 스크롤
    cacheBlockSize: 20,          // 한 번에 요청할 행 수
    maxBlocksInCache: 5,
    datasource: {
        getRows(params) {
            const url = new URL('/examples/aggrid/server-data', location.origin);
            url.searchParams.set('startRow', params.startRow);
            url.searchParams.set('endRow',   params.endRow);
            url.searchParams.set('search',   serverSearch);

            if (params.sortModel.length) {
                url.searchParams.set('sortField', params.sortModel[0].colId);
                url.searchParams.set('sortDir',   params.sortModel[0].sort);
            }

            fetch(url)
                .then(r => r.json())
                .then(data => {
                    params.successCallback(data.rows, data.total);
                })
                .catch(() => params.failCallback());
        },
    },
});</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">5</span><h5>CI4 — 서버 사이드 API</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">public function serverData(): Response
{
    $startRow  = (int) $this->request->getGet('startRow');
    $endRow    = (int) $this->request->getGet('endRow');
    $sortField = $this->request->getGet('sortField') ?? 'id';
    $sortDir   = $this->request->getGet('sortDir')   ?? 'asc';
    $search    = trim($this->request->getGet('search') ?? '');

    $builder = db_connect()->table('posts')
        ->where('deleted_at', null);

    if ($search) {
        $builder->groupStart()
            ->like('title', $search)
            ->orLike('author', $search)
            ->groupEnd();
    }

    $total = $builder->countAllResults(false);  // 전체 수 (페이지 적용 전)

    $rows = $builder
        ->orderBy($sortField, $sortDir)
        ->limit($endRow - $startRow, $startRow)
        ->get()->getResultArray();

    return $this->response->setJSON([
        'rows'  => $rows,
        'total' => $total,
    ]);
}</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@33/styles/ag-grid.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@33/styles/ag-theme-quartz.min.css">
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@33/dist/ag-grid-community.min.js"></script>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#gridTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');

    if (name === 'server' && !serverGridApi) initServerGrid();
}

// ── 공통 컬럼 정의 ─────────────────────────────────────
const viewsCellRenderer = p => {
    if (p.value == null) return '';
    const color = p.value >= 200 ? '#198754' : p.value >= 100 ? '#0d6efd' : '#6c757d';
    return `<span style="background:${color};color:#fff;padding:2px 8px;border-radius:12px;font-size:.75rem;">${p.value}</span>`;
};

const colDefs = [
    { field: 'id',         headerName: '#',    width: 70,  sortable: true },
    { field: 'title',      headerName: '제목', flex: 2,    sortable: true, filter: true },
    { field: 'author',     headerName: '작성자', width: 120, sortable: true, filter: true },
    { field: 'views',      headerName: '조회수', width: 110, sortable: true,
      cellRenderer: viewsCellRenderer },
    { field: 'created_at', headerName: '작성일', width: 165, sortable: true,
      valueFormatter: p => p.value ? p.value.slice(0, 16) : '' },
];

// ── 클라이언트 사이드 그리드 ───────────────────────────
let clientGridApi = null;

(function initClientGrid() {
    clientGridApi = agGrid.createGrid(
        document.getElementById('client-grid'),
        {
            columnDefs: colDefs,
            defaultColDef: { resizable: true },
            rowData: [],
            pagination: true,
            paginationPageSize: 5,
            paginationPageSizeSelector: [5, 10, 20],
            animateRows: true,
            onRowClicked(e) {
                const d = e.data;
                document.getElementById('client-detail').style.display = 'block';
                document.getElementById('client-detail-body').innerHTML = `
                    <table class="table table-sm mb-0">
                        <tr><th style="width:100px">ID</th><td>${d.id}</td></tr>
                        <tr><th>제목</th><td>${esc(d.title)}</td></tr>
                        <tr><th>작성자</th><td>${esc(d.author)}</td></tr>
                        <tr><th>조회수</th><td>${d.views}</td></tr>
                        <tr><th>작성일</th><td>${d.created_at}</td></tr>
                    </table>`;
            },
        }
    );

    fetch('<?= base_url('examples/aggrid/data') ?>')
        .then(r => r.json())
        .then(rows => clientGridApi.setGridOption('rowData', rows));
})();

// ── 서버 사이드 그리드 ────────────────────────────────
let serverGridApi = null;
let serverSearch  = '';

function initServerGrid() {
    serverGridApi = agGrid.createGrid(
        document.getElementById('server-grid'),
        {
            columnDefs: colDefs,
            defaultColDef: { resizable: true },
            rowModelType: 'infinite',
            cacheBlockSize: 10,
            maxBlocksInCache: 5,
            animateRows: true,
            datasource: {
                getRows(params) {
                    const url = new URL('<?= base_url('examples/aggrid/server-data') ?>', location.origin);
                    url.searchParams.set('startRow', params.startRow);
                    url.searchParams.set('endRow',   params.endRow);
                    url.searchParams.set('search',   serverSearch);

                    if (params.sortModel.length) {
                        url.searchParams.set('sortField', params.sortModel[0].colId);
                        url.searchParams.set('sortDir',   params.sortModel[0].sort);
                    }

                    fetch(url)
                        .then(r => r.json())
                        .then(data => {
                            document.getElementById('server-total').textContent =
                                `총 ${data.total}개`;
                            params.successCallback(data.rows, data.total);
                        })
                        .catch(() => params.failCallback());
                },
            },
        }
    );
}

function applyServerSearch() {
    serverSearch = document.getElementById('server-search').value.trim();
    serverGridApi?.refreshInfiniteCache();
}

function clearServerSearch() {
    serverSearch = '';
    document.getElementById('server-search').value = '';
    serverGridApi?.refreshInfiniteCache();
}

document.getElementById('server-search').addEventListener('keydown', e => {
    if (e.key === 'Enter') applyServerSearch();
});

function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>
<?= $this->endSection() ?>
