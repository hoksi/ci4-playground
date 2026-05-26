<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">모델 & 데이터베이스</li>
    </ol></nav>
    <h1><i class="bi bi-database me-2"></i>모델 & 데이터베이스</h1>
    <p>CI4 Model 클래스를 이용한 데이터베이스 연동, CRUD, Entity를 알아봅니다.</p>
</div>

<!-- Model 기본 구조 -->
<div class="example-card">
    <div class="example-card-header"><span class="badge bg-purple" style="background:#6f42c1;">1</span><h5>Model 기본 구조</h5></div>
    <div class="example-card-body">
        <p class="text-muted">Model 클래스에 테이블 정보, 허용 필드, 유효성 검사 규칙을 선언하면 CRUD 메서드를 자동으로 사용할 수 있습니다.</p>
        <div class="code-label">app/Models/PostModel.php</div>
        <pre><code class="language-php">class PostModel extends Model
{
    protected $table         = 'posts';        // DB 테이블명
    protected $returnType    = Post::class;    // Entity 클래스로 반환
    protected $useSoftDeletes = true;          // 소프트 삭제 활성화
    protected $allowedFields = ['title', 'content', 'author', 'views'];
    protected $useTimestamps = true;           // created_at, updated_at 자동 관리

    protected $validationRules = [
        'title'   => 'required|min_length[2]|max_length[200]',
        'content' => 'required|min_length[10]',
        'author'  => 'required',
    ];
}</code></pre>
    </div>
</div>

<!-- 기본 CRUD -->
<div class="example-card">
    <div class="example-card-header"><span class="badge" style="background:#6f42c1;">2</span><h5>기본 CRUD 메서드</h5></div>
    <div class="example-card-body">
        <pre><code class="language-php">$model = new PostModel();

// 전체 조회
$posts = $model->findAll();

// ID로 단건 조회
$post = $model->find(1);

// 조건 조회
$post = $model->where('author', '김철수')->first();

// 삽입
$id = $model->insert(['title' => '새 글', 'content' => '...', 'author' => '홍길동']);

// 수정
$model->update(1, ['title' => '수정된 제목']);

// 삭제 (소프트 삭제: deleted_at 설정)
$model->delete(1);

// 완전 삭제
$model->delete(1, true);</code></pre>

        <div class="code-label mt-3">실제 DB 조회 결과 (최신 5개)</div>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-dark">
                    <tr><th>ID</th><th>제목</th><th>작성자</th><th>조회수</th><th>작성일</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($posts)): ?>
                        <tr><td colspan="5" class="text-center text-muted">데이터가 없습니다. <a href="<?= base_url() ?>">시더를 실행</a>해주세요.</td></tr>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?= esc($post->id) ?></td>
                            <td><?= esc($post->title) ?></td>
                            <td><?= esc($post->author) ?></td>
                            <td><span class="badge bg-secondary"><?= esc($post->views) ?></span></td>
                            <td><small><?= esc($post->getFormattedDate()) ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 하위 메뉴 -->
<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold"><i class="bi bi-filter me-2" style="color:#6f42c1;"></i>Query Builder</h6>
                <p class="text-muted small">WHERE, JOIN, GROUP BY, 집계 함수를 PHP 코드로 작성합니다.</p>
                <a href="<?= base_url('examples/models/querybuilder') ?>" class="demo-btn" style="background:#6f42c1;">데모 보기</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold"><i class="bi bi-file-earmark-text me-2" style="color:#6f42c1;"></i>페이지네이션</h6>
                <p class="text-muted small">paginate()로 데이터를 나눠서 보여주는 페이지네이션을 구현합니다.</p>
                <a href="<?= base_url('examples/models/pagination') ?>" class="demo-btn" style="background:#6f42c1;">데모 보기</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold"><i class="bi bi-box me-2" style="color:#6f42c1;"></i>Entity 클래스</h6>
                <p class="text-muted small">데이터를 객체로 다루는 Entity 클래스와 커스텀 메서드를 알아봅니다.</p>
                <a href="<?= base_url('examples/models/entity') ?>" class="demo-btn" style="background:#6f42c1;">데모 보기</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
