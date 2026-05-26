<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/views') ?>">뷰</a></li>
        <li class="breadcrumb-item active text-white">View Cell</li>
    </ol></nav>
    <h1><i class="bi bi-box me-2"></i>View Cell</h1>
    <p>독립적인 로직과 뷰를 가진 재사용 가능한 컴포넌트입니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header"><span class="badge bg-success">구조</span><h5>View Cell 구성 요소</h5></div>
    <div class="example-card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="code-label">Cell 클래스 (app/Cells/RecentPostsCell.php)</div>
                <pre><code class="language-php">namespace App\Cells;

use CodeIgniter\View\Cells\Cell;
use App\Models\PostModel;

class RecentPostsCell extends Cell
{
    // 외부에서 값 주입 가능한 프로퍼티
    public int $limit = 5;

    public function mount(): void
    {
        // 이 Cell의 독립적인 데이터 조회
        $model = new PostModel();
        $this->posts = $model->orderBy('created_at', 'DESC')
                             ->limit($this->limit)
                             ->find();
    }
}</code></pre>
            </div>
            <div class="col-md-6">
                <div class="code-label">Cell 뷰 (app/Views/cells/recent_posts_cell.php)</div>
                <pre><code class="language-php">&lt;div class="recent-posts"&gt;
    &lt;h5&gt;최근 게시글 (최대 &lt;?= $limit ?&gt;개)&lt;/h5&gt;
    &lt;?php foreach ($posts as $post): ?&gt;
        &lt;div&gt;&lt;?= esc($post->title) ?&gt;&lt;/div&gt;
    &lt;?php endforeach; ?&gt;
&lt;/div&gt;</code></pre>

                <div class="code-label mt-3">뷰에서 사용</div>
                <pre><code class="language-php">// 기본 사용
<?= view_cell('RecentPostsCell') ?>

// 파라미터 전달
<?= view_cell('RecentPostsCell', ['limit' => 3]) ?>

// 캐싱 (60초)
<?= view_cell('RecentPostsCell', [], 60) ?></code></pre>
            </div>
        </div>

        <div class="result-box warning mt-3">
            <i class="bi bi-layers me-2"></i>
            <strong>View Cell 활용 예시</strong>
            <ul class="mb-0 mt-2">
                <li>최근 게시글/댓글 위젯</li>
                <li>카테고리 메뉴 (DB에서 동적으로 로드)</li>
                <li>쇼핑 카트 아이콘 (수량 표시)</li>
                <li>날씨 위젯, 광고 배너 등</li>
            </ul>
        </div>

        <div class="result-box info mt-3">
            <i class="bi bi-info-circle me-2"></i>
            <strong>파셜 vs View Cell</strong>
            <table class="table table-sm mt-2 mb-0 bg-white rounded">
                <thead><tr><th>구분</th><th>파셜 (view())</th><th>View Cell</th></tr></thead>
                <tbody>
                    <tr><td>독립 DB 조회</td><td>❌</td><td>✅</td></tr>
                    <tr><td>자체 로직</td><td>❌</td><td>✅</td></tr>
                    <tr><td>캐싱 지원</td><td>❌</td><td>✅</td></tr>
                    <tr><td>단순 HTML 재사용</td><td>✅</td><td>가능하나 과함</td></tr>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <a href="<?= base_url('examples/views') ?>" class="demo-btn" style="background:#198754;">← 뷰로</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
