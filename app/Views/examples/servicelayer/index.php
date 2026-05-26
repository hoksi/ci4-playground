<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">서비스 레이어</li>
    </ol></nav>
    <h1><i class="bi bi-layers me-2"></i>서비스 레이어</h1>
    <p>비즈니스 로직을 컨트롤러와 분리하는 서비스 레이어 패턴을 알아봅니다.</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php $tab = $tab ?? 'stats'; ?>
<ul class="nav nav-tabs mb-3" id="serviceTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'stats' ? 'active' : '' ?>" href="#" onclick="showTab('stats');return false;">통계 & 인기 글</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'search' ? 'active' : '' ?>" href="#" onclick="showTab('search');return false;">게시물 검색</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'create' ? 'active' : '' ?>" href="#" onclick="showTab('create');return false;">게시물 작성</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- 통계 & 인기 글 -->
<div id="tab-stats" class="tab-content-pane" style="display:<?= $tab === 'stats' ? 'block' : 'none' ?>">
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="example-card text-center py-3">
                <div class="fw-bold fs-3 text-primary"><?= $summary['total'] ?></div>
                <div class="text-muted small">전체 게시물</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="example-card text-center py-3">
                <div class="fw-bold fs-3 text-success"><?= number_format($summary['total_views']) ?></div>
                <div class="text-muted small">총 조회수</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="example-card text-center py-3">
                <div class="fw-bold fs-3 text-warning"><?= $summary['avg_views'] ?></div>
                <div class="text-muted small">평균 조회수</div>
            </div>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-trophy me-2"></i>인기 게시물 Top 5 <small class="text-muted fw-normal">(PostService::getTopPosts)</small></h5></div>
        <div class="example-card-body">
            <?php if (empty($top)): ?>
                <p class="text-muted mb-0">게시물이 없습니다.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-dark"><tr><th>#</th><th>제목</th><th>작성자</th><th>조회수</th></tr></thead>
                    <tbody>
                    <?php foreach ($top as $i => $post): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= $i + 1 ?></span></td>
                        <td><?= esc($post->title) ?></td>
                        <td><?= esc($post->author) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= number_format($post->views) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 검색 -->
<div id="tab-search" class="tab-content-pane" style="display:<?= $tab === 'search' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-search me-2"></i>키워드 검색 <small class="text-muted fw-normal">(PostService::search)</small></h5></div>
        <div class="example-card-body">
            <form method="get" action="<?= base_url('examples/servicelayer/search') ?>" class="mb-3">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="검색어 입력..." value="<?= esc($keyword ?? '') ?>">
                    <button type="submit" class="demo-btn" style="border:none;cursor:pointer;border-radius:0 6px 6px 0;">
                        <i class="bi bi-search"></i> 검색
                    </button>
                </div>
            </form>
            <?php if (isset($results)): ?>
                <?php if (empty($results)): ?>
                    <p class="text-muted mb-0">"<?= esc($keyword) ?>" 검색 결과 없음</p>
                <?php else: ?>
                <p class="text-muted mb-2"><?= count($results) ?>개 검색 결과</p>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-dark"><tr><th>제목</th><th>작성자</th><th>조회수</th></tr></thead>
                        <tbody>
                        <?php foreach ($results as $post): ?>
                        <tr>
                            <td><?= esc($post->title) ?></td>
                            <td><?= esc($post->author) ?></td>
                            <td><?= number_format($post->views) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 게시물 작성 -->
<div id="tab-create" class="tab-content-pane" style="display:<?= $tab === 'create' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-pencil me-2"></i>게시물 작성 <small class="text-muted fw-normal">(PostService::create)</small></h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                컨트롤러가 직접 Model을 사용하지 않고 <code>PostService::create()</code>를 호출합니다.
            </div>
            <form method="post" action="<?= base_url('examples/servicelayer/create') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">제목 <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                           value="<?= esc($old['title'] ?? '') ?>" placeholder="게시물 제목">
                    <?php if (isset($errors['title'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['title']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">본문 <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control <?= isset($errors['content']) ? 'is-invalid' : '' ?>" rows="4"
                              placeholder="게시물 내용"><?= esc($old['content'] ?? '') ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['content']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">작성자</label>
                    <input type="text" name="author" class="form-control" value="<?= esc($old['author'] ?? '') ?>" placeholder="익명">
                </div>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-save"></i> 저장 (서비스 레이어 경유)
                </button>
            </form>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>서비스 클래스 작성</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// app/Services/PostService.php
namespace App\Services;

class PostService
{
    public function __construct(private PostModel $model) {}

    public function getTopPosts(int $limit = 5): array
    {
        return $this->model->orderBy('views', 'DESC')->limit($limit)->findAll();
    }

    public function create(array $data): array
    {
        // 비즈니스 규칙: 유효성 검사, 전처리 등
        $validator = \Config\Services::validation();
        // ...
        $id = $this->model->insert($data, true);
        return ['success' => true, 'id' => $id];
    }
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>Config/Services.php 등록</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// app/Config/Services.php
class Services extends BaseService
{
    public static function postService(bool $getShared = true): \App\Services\PostService
    {
        if ($getShared) {
            return static::getSharedInstance('postService');
        }
        return new \App\Services\PostService(new \App\Models\PostModel());
    }
}

// 사용 (전역 어디서나)
$service = \Config\Services::postService();
// 또는
$service = service('postService');</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>컨트롤러에서 사용</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">class ServiceLayer extends BaseController
{
    private PostService $postService;

    public function __construct()
    {
        // 생성자 주입 (의존성 주입 패턴)
        $this->postService = new PostService(new PostModel());
        // 또는 서비스 컨테이너 이용
        // $this->postService = service('postService');
    }

    public function index(): string
    {
        return view('...', [
            'top'     => $this->postService->getTopPosts(5),
            'summary' => $this->postService->getSummary(),
        ]);
    }
}</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#serviceTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
<?= $this->endSection() ?>
