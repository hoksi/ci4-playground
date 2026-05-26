<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/views') ?>">뷰</a></li>
        <li class="breadcrumb-item active text-white">파셜</li>
    </ol></nav>
    <h1><i class="bi bi-puzzle me-2"></i>파셜 & 컴포넌트 include</h1>
    <p>재사용 가능한 뷰 조각을 분리하고 포함하는 방법입니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header"><h5>아래 아이템 목록은 파셜로 렌더링된 결과입니다</h5></div>
    <div class="example-card-body">
        <div class="result-box mb-3">
            <div class="code-label">컨트롤러에서 데이터 전달</div>
            <pre style="background:transparent;padding:0;margin:0;"><code class="language-php">$items = ['사과', '바나나', '오렌지', '포도', '딸기'];
return view('examples/views/partial', ['items' => $items]);</code></pre>
        </div>

        <div class="code-label">이 뷰 파일에서 파셜 포함</div>
        <pre><code class="language-php">// 데이터를 전달하며 파셜 포함
<?= view('components/item_list', ['items' => $items]) ?>

// 경고 컴포넌트 포함
<?= view('components/alert', ['type' => 'info', 'message' => '목록입니다']) ?></code></pre>

        <div class="code-label mt-3">실제 렌더링 결과</div>
        <!-- 파셜 직접 인라인 렌더링 (components/item_list 역할) -->
        <div class="border rounded p-3 bg-light">
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($items as $item): ?>
                    <span class="badge bg-success fs-6"><?= esc($item) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="code-label mt-3">파셜 파일 예시 (app/Views/components/item_list.php)</div>
        <pre><code class="language-php">&lt;!-- app/Views/components/item_list.php --&gt;
&lt;ul class="list-group"&gt;
    &lt;?php foreach ($items as $item): ?&gt;
        &lt;li class="list-group-item"&gt;&lt;?= esc($item) ?&gt;&lt;/li&gt;
    &lt;?php endforeach; ?&gt;
&lt;/ul&gt;</code></pre>

        <div class="result-box info mt-3">
            <i class="bi bi-lightbulb me-2"></i>
            <strong>팁:</strong> 파셜은 독립적인 로직 없이 단순히 데이터를 표시할 때 사용합니다.
            로직이 필요하면 <a href="<?= base_url('examples/views/cell') ?>">View Cell</a>을 사용하세요.
        </div>

        <div class="mt-3">
            <a href="<?= base_url('examples/views') ?>" class="demo-btn" style="background:#198754;">← 뷰로</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
