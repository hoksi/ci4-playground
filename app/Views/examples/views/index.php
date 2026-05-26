<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">뷰</li>
    </ol></nav>
    <h1><i class="bi bi-window me-2"></i>뷰</h1>
    <p>CI4의 뷰 렌더링, 레이아웃 시스템, 파셜, View Cell을 알아봅니다.</p>
</div>

<!-- 1. 기본 뷰 렌더링 -->
<div class="example-card">
    <div class="example-card-header"><span class="badge bg-success">1</span><h5>기본 뷰 렌더링</h5></div>
    <div class="example-card-body">
        <p class="text-muted">컨트롤러에서 <code>view()</code> 헬퍼로 뷰를 렌더링하고 데이터를 전달합니다.</p>
        <div class="code-label">컨트롤러</div>
        <pre><code class="language-php">public function index(): string
{
    // 뷰 파일: app/Views/examples/views/index.php
    return view('examples/views/index', [
        'title' => '뷰',         // $title 로 사용 가능
        'items' => [1, 2, 3],   // $items 로 사용 가능
    ]);
}</code></pre>
        <div class="code-label mt-3">뷰 파일</div>
        <pre><code class="language-php">&lt;?php // app/Views/examples/views/index.php ?&gt;

&lt;!-- XSS 방지: esc() 필수 --&gt;
&lt;h1&gt;&lt;?= esc($title) ?&gt;&lt;/h1&gt;

&lt;?php foreach ($items as $item): ?&gt;
    &lt;li&gt;&lt;?= esc($item) ?&gt;&lt;/li&gt;
&lt;?php endforeach; ?&gt;

&lt;!-- 뷰 내에서 다른 뷰 포함 --&gt;
&lt;?= view('components/alert', ['message' =&gt; '성공!']) ?&gt;</code></pre>
        <div class="result-box info mt-3">
            <i class="bi bi-shield-check me-2"></i>
            <strong>보안:</strong> 사용자 입력은 항상 <code>esc($value)</code>로 출력하여 XSS를 방지합니다.
        </div>
    </div>
</div>

<!-- 2. 레이아웃 시스템 -->
<div class="example-card">
    <div class="example-card-header"><span class="badge bg-success">2</span><h5>레이아웃 시스템</h5></div>
    <div class="example-card-body">
        <p class="text-muted">공통 레이아웃을 정의하고 각 페이지에서 <code>extend()</code>로 상속합니다. 이 페이지 자체도 레이아웃을 사용하고 있습니다.</p>
        <div class="row">
            <div class="col-md-6">
                <div class="code-label">레이아웃 파일 (app/Views/layouts/main.php)</div>
                <pre><code class="language-php">&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;title&gt;&lt;?= esc($title ?? 'CI4') ?&gt;&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;!-- 각 페이지의 콘텐츠가 여기에 삽입됩니다 --&gt;
    &lt;?= $this-&gt;renderSection('content') ?&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
            </div>
            <div class="col-md-6">
                <div class="code-label">페이지 파일 (app/Views/examples/views/layout.php)</div>
                <pre><code class="language-php">// 레이아웃 상속
&lt;?= $this-&gt;extend('layouts/main') ?&gt;

// 'content' 섹션 정의
&lt;?= $this-&gt;section('content') ?&gt;

&lt;h1&gt;페이지 내용&lt;/h1&gt;
&lt;p&gt;여기에 콘텐츠를 작성합니다.&lt;/p&gt;

// 섹션 종료
&lt;?= $this-&gt;endSection() ?&gt;</code></pre>
            </div>
        </div>
        <div class="mt-3">
            <a href="<?= base_url('examples/views/layout') ?>" class="demo-btn" style="background:#198754;">
                <i class="bi bi-play-fill"></i> 레이아웃 데모
            </a>
        </div>
    </div>
</div>

<!-- 3. 파셜 -->
<div class="example-card">
    <div class="example-card-header"><span class="badge bg-success">3</span><h5>파셜 & 컴포넌트 include</h5></div>
    <div class="example-card-body">
        <p class="text-muted">반복 사용되는 UI 조각을 별도 파일로 분리하고 <code>view()</code>로 포함합니다.</p>
        <div class="code-label">뷰에서 파셜 포함</div>
        <pre><code class="language-php">// 단순 포함
&lt;?= view('components/footer') ?&gt;

// 데이터 전달
&lt;?= view('components/alert', ['type' =&gt; 'success', 'message' =&gt; '저장 완료!']) ?&gt;

// 배열 데이터 전달
&lt;?= view('components/item_list', ['items' =&gt; $items]) ?&gt;</code></pre>
        <div class="mt-3">
            <a href="<?= base_url('examples/views/partial') ?>" class="demo-btn" style="background:#198754;">
                <i class="bi bi-play-fill"></i> 파셜 데모
            </a>
        </div>
    </div>
</div>

<!-- 4. View Cell -->
<div class="example-card">
    <div class="example-card-header"><span class="badge bg-success">4</span><h5>View Cell — 재사용 컴포넌트</h5></div>
    <div class="example-card-body">
        <p class="text-muted">View Cell은 독립적인 로직을 가진 재사용 가능한 UI 컴포넌트입니다. 자체 DB 조회, 데이터 처리가 가능합니다.</p>
        <div class="code-label">Cell 클래스 (app/Cells/RecentPostsCell.php)</div>
        <pre><code class="language-php">namespace App\Cells;

use CodeIgniter\View\Cells\Cell;

class RecentPostsCell extends Cell
{
    public int $limit = 5; // 기본값, 외부에서 주입 가능

    public function mount(): void
    {
        // DB 조회 등 독립 로직 처리
    }
}</code></pre>
        <div class="code-label mt-3">뷰에서 사용</div>
        <pre><code class="language-php">&lt;?= view_cell('RecentPostsCell') ?&gt;
&lt;?= view_cell('RecentPostsCell', ['limit' =&gt; 3]) ?&gt;
&lt;?= view_cell('App\Cells\RecentPostsCell::render', ['limit' =&gt; 10]) ?&gt;</code></pre>
        <div class="mt-3">
            <a href="<?= base_url('examples/views/cell') ?>" class="demo-btn" style="background:#198754;">
                <i class="bi bi-play-fill"></i> View Cell 데모
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
