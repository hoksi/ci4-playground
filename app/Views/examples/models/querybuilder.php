<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/models') ?>">모델</a></li>
        <li class="breadcrumb-item active text-white">Query Builder</li>
    </ol></nav>
    <h1><i class="bi bi-filter me-2"></i>Query Builder</h1>
    <p>메서드 체이닝으로 SQL 쿼리를 안전하게 작성합니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header"><span class="badge" style="background:#6f42c1;">조건 조회</span><h5>where, orderBy, limit</h5></div>
    <div class="example-card-body">
        <div class="code-label">코드</div>
        <pre><code class="language-php">$model = new PostModel();

// 조회수 50 초과, 내림차순 정렬, 최대 3개
$topPosts = $model->where('views >', 50)
                  ->orderBy('views', 'DESC')
                  ->findAll(3);</code></pre>
        <div class="code-label mt-3">결과 (조회수 50 초과 게시글)</div>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="table-dark"><tr><th>제목</th><th>작성자</th><th>조회수</th></tr></thead>
                <tbody>
                    <?php foreach ($topPosts as $p): ?>
                    <tr><td><?= esc($p->title) ?></td><td><?= esc($p->author) ?></td><td><?= esc($p->views) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="example-card">
    <div class="example-card-header"><span class="badge" style="background:#6f42c1;">집계 함수</span><h5>selectSum, countAllResults</h5></div>
    <div class="example-card-body">
        <pre><code class="language-php">// 전체 조회수 합산
$totalViews = $model->selectSum('views')->first()->views;

// 전체 게시글 수 (소프트 삭제 제외)
$count = $model->countAllResults();</code></pre>
        <div class="row mt-3 g-3">
            <div class="col-md-6">
                <div class="result-box text-center">
                    <div class="text-muted small">전체 조회수 합계</div>
                    <div class="fs-2 fw-bold" style="color:#6f42c1;"><?= number_format($totalViews) ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="result-box text-center">
                    <div class="text-muted small">전체 게시글 수</div>
                    <div class="fs-2 fw-bold" style="color:#6f42c1;"><?= number_format($countAll) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="example-card">
    <div class="example-card-header"><span class="badge" style="background:#6f42c1;">GROUP BY</span><h5>작성자별 통계</h5></div>
    <div class="example-card-body">
        <pre><code class="language-php">$db = \Config\Database::connect();
$result = $db->table('posts')
    ->select('author, SUM(views) as total_views, COUNT(*) as post_count')
    ->groupBy('author')
    ->orderBy('total_views', 'DESC')
    ->get()->getResultArray();</code></pre>
        <div class="table-responsive mt-3">
            <table class="table table-sm">
                <thead class="table-dark"><tr><th>작성자</th><th>게시글 수</th><th>총 조회수</th></tr></thead>
                <tbody>
                    <?php foreach ($rawResult as $row): ?>
                    <tr>
                        <td><?= esc($row['author']) ?></td>
                        <td><?= esc($row['post_count']) ?></td>
                        <td><?= number_format($row['total_views']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3"><a href="<?= base_url('examples/models') ?>" class="demo-btn" style="background:#6f42c1;">← 모델로</a></div>

<?= $this->endSection() ?>
