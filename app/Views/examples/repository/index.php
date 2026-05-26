<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">Repository 패턴</li>
    </ol></nav>
    <h1><i class="bi bi-diagram-3 me-2"></i>Repository 패턴</h1>
    <p>Interface → Repository → Controller 레이어로 DB 접근을 추상화하는 아키텍처 패턴.</p>
</div>

<ul class="nav nav-tabs mb-3" id="repoTab">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="code">코드 설명</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="compare">서비스 레이어 vs Repository</a></li>
</ul>

<!-- 라이브 데모 -->
<div id="tab-demo" class="tab-content-pane">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="example-card">
                <div class="example-card-header"><h5><i class="bi bi-plus-square me-2"></i>포스트 생성 (Repository::create)</h5></div>
                <div class="example-card-body">
                    <form id="create-form">
                        <div class="mb-2">
                            <label class="form-label fw-bold">제목</label>
                            <input type="text" name="title" class="form-control" value="Repository 패턴 적용" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold">작성자</label>
                            <input type="text" name="author" class="form-control" value="아키텍트">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">내용</label>
                            <textarea name="content" class="form-control" rows="3" required>인터페이스로 추상화한 Repository 패턴 예제입니다.</textarea>
                        </div>
                        <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                            <i class="bi bi-save"></i> 생성
                        </button>
                    </form>
                    <div id="create-result" class="mt-3" style="display:none;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="example-card">
                <div class="example-card-header">
                    <h5><i class="bi bi-list-ul me-2"></i>포스트 목록 (Repository::findRecent)</h5>
                    <button id="reload-btn" class="btn btn-sm btn-outline-secondary ms-auto">
                        <i class="bi bi-arrow-clockwise"></i> 새로고침
                    </button>
                </div>
                <div class="example-card-body">
                    <div id="list-area">
                        <p class="text-muted mb-0">목록을 불러오는 중...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>Interface 정의 (계약)</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// app/Interfaces/PostRepositoryInterface.php
namespace App\Interfaces;

interface PostRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): ?object;
    public function create(array $data): int|false;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findRecent(int $limit = 10): array;
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>Repository 구현체</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// app/Repositories/PostRepository.php
namespace App\Repositories;

use App\Interfaces\PostRepositoryInterface;
use App\Models\PostModel;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(private PostModel $model) {}

    public function findAll(): array
    {
        return $this-&gt;model-&gt;orderBy('id', 'DESC')-&gt;findAll();
    }

    public function findById(int $id): ?object
    {
        return $this-&gt;model-&gt;find($id);
    }

    public function create(array $data): int|false
    {
        if (! $this-&gt;model-&gt;insert($data)) return false;
        return (int) $this-&gt;model-&gt;getInsertID();
    }

    public function update(int $id, array $data): bool
    {
        return $this-&gt;model-&gt;update($id, $data);
    }

    public function delete(int $id): bool
    {
        return (bool) $this-&gt;model-&gt;delete($id);
    }

    public function findRecent(int $limit = 10): array
    {
        return $this-&gt;model-&gt;orderBy('id', 'DESC')-&gt;findAll($limit);
    }
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>Controller에서 사용</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// app/Controllers/Examples/Repository.php
class Repository extends BaseController
{
    private PostRepositoryInterface $repo;

    public function __construct()
    {
        // Interface 타입 힌트, 구현체 주입 (DI 컨테이너로 교체 가능)
        $this-&gt;repo = new PostRepository(new PostModel());
    }

    public function list()
    {
        $posts = $this-&gt;repo-&gt;findRecent(10);
        return $this-&gt;response-&gt;setJSON([...]);
    }

    public function store()
    {
        $id = $this-&gt;repo-&gt;create($this-&gt;request-&gt;getPost());
        return $this-&gt;response-&gt;setJSON(['id' =&gt; $id]);
    }
}</code></pre>
        </div>
    </div>
    <div class="result-box info mt-3">
        <strong><i class="bi bi-lightbulb me-2"></i>Repository 패턴의 장점</strong>
        <ul class="mb-0 mt-2">
            <li><strong>테스트 용이성</strong> — Interface 구현 Mock을 주입해 DB 없이 단위 테스트.</li>
            <li><strong>저장소 교체</strong> — DB → 캐시, DB → API 호출로 구현체만 교체 가능.</li>
            <li><strong>비즈니스 로직 분리</strong> — Controller는 데이터 접근의 "방법"을 모름.</li>
            <li><strong>중복 쿼리 제거</strong> — <code>findPublished()</code>, <code>findByAuthor()</code> 같은 의미있는 메서드로 응집.</li>
        </ul>
    </div>
</div>

<!-- 비교표 -->
<div id="tab-compare" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-arrow-left-right me-2"></i>서비스 레이어 vs Repository</h5></div>
        <div class="example-card-body">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th></th><th>Service Layer</th><th>Repository</th></tr>
                </thead>
                <tbody>
                    <tr><th>책임</th><td>비즈니스 로직, 트랜잭션, 오케스트레이션</td><td>데이터 영속화, CRUD 추상화</td></tr>
                    <tr><th>의존 대상</th><td>여러 Repository, 외부 API, 메일 등</td><td>Model / Query Builder / 외부 DB</td></tr>
                    <tr><th>예시 메서드</th><td><code>registerUser()</code>, <code>placeOrder()</code></td><td><code>findById()</code>, <code>create()</code></td></tr>
                    <tr><th>리턴 타입</th><td>도메인 객체 / Result 객체</td><td>Entity / 배열 / id</td></tr>
                    <tr><th>트랜잭션 처리</th><td>여기서 시작/커밋</td><td>관여하지 않음</td></tr>
                    <tr><th>레이어 위치</th><td>Controller 바로 아래</td><td>Service 아래, Model 위</td></tr>
                </tbody>
            </table>

            <h6 class="mt-4"><i class="bi bi-stack me-2"></i>전형적인 계층 구조</h6>
            <pre style="background:#f8f9fa;border:1px solid #e9ecef;border-radius:8px;padding:1rem;"><code>┌─────────────────────────────────────┐
│  Controller (HTTP 입출력 처리)       │
└─────────────┬───────────────────────┘
              ↓
┌─────────────────────────────────────┐
│  Service Layer (비즈니스 로직)        │
└─────────────┬───────────────────────┘
              ↓
┌─────────────────────────────────────┐
│  Repository (Interface)             │
└─────────────┬───────────────────────┘
              ↓
┌─────────────────────────────────────┐
│  Model / Query Builder / DB         │
└─────────────────────────────────────┘</code></pre>
            <div class="result-box warning mt-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>주의:</strong> 소규모 프로젝트에서 Repository 패턴은 과한 추상화일 수 있습니다.
                <strong>Model이 충분히 단순한 CRUD라면</strong> Model 자체가 Repository 역할을 합니다.
                "여러 데이터 소스를 결합해야 한다", "테스트 용이성이 중요하다" 같은 명확한 이유가 있을 때 도입하세요.
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('#repoTab .nav-link').forEach(el => {
    el.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('#repoTab .nav-link').forEach(n => n.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.tab-content-pane').forEach(p => p.style.display = 'none');
        document.getElementById('tab-' + el.dataset.tab).style.display = 'block';
    });
});

async function loadList() {
    const res  = await fetch('<?= base_url('examples/repository/list') ?>');
    const data = await res.json();
    const area = document.getElementById('list-area');
    if (! data.success || data.count === 0) {
        area.innerHTML = '<p class="text-muted mb-0">포스트가 없습니다. 왼쪽에서 새로 만들어보세요.</p>';
        return;
    }
    let html = '<div class="result-box info mb-2"><i class="bi bi-info-circle me-1"></i>총 ' + data.count + '개</div>';
    html += '<table class="table table-sm"><thead class="table-dark"><tr><th>#</th><th>제목</th><th>작성자</th><th>작성일</th></tr></thead><tbody>';
    data.posts.forEach(p => {
        html += `<tr><td>${p.id}</td><td>${p.title}</td><td>${p.author}</td><td><small>${p.created}</small></td></tr>`;
    });
    html += '</tbody></table>';
    area.innerHTML = html;
}

document.getElementById('reload-btn').addEventListener('click', loadList);
loadList();

document.getElementById('create-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const res  = await fetch('<?= base_url('examples/repository/store') ?>', { method: 'POST', body: formData });
    const data = await res.json();
    const area = document.getElementById('create-result');
    area.style.display = 'block';
    if (! data.success) {
        area.innerHTML = '<div class="result-box danger"><i class="bi bi-x-circle me-2"></i>' + data.message + '</div>';
        return;
    }
    area.innerHTML = '<div class="result-box"><i class="bi bi-check-circle me-2"></i>' + data.message + ' (ID: ' + data.post.id + ')</div>';
    loadList();
});
</script>
<?= $this->endSection() ?>
