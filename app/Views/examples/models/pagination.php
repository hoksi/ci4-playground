<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/models') ?>">모델</a></li>
        <li class="breadcrumb-item active text-white">페이지네이션</li>
    </ol></nav>
    <h1><i class="bi bi-file-earmark-text me-2"></i>페이지네이션</h1>
    <p>paginate()로 자동 페이지네이션을 구현합니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header"><h5>코드 & 실행 결과</h5></div>
    <div class="example-card-body">
        <div class="code-label">컨트롤러</div>
        <pre><code class="language-php">$model = new PostModel();

// 페이지당 3개, 'default' 페이저 그룹
$posts = $model->orderBy('created_at', 'DESC')->paginate(3, 'default');
$pager = $model->pager;</code></pre>
        <div class="code-label mt-3">뷰</div>
        <pre><code class="language-php"><?php foreach ($posts as $post): ?>
    <div><?= esc($post->title) ?></div>
<?php endforeach; ?>

// 페이지 링크 출력
<?= $pager->links() ?>

// 심플 스타일 (이전/다음만 표시)
<?= $pager->simpleLinks() ?></code></pre>

        <hr>
        <div class="code-label">페이지당 3개 — 실제 결과</div>
        <?php foreach ($posts as $post): ?>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
            <div>
                <strong><?= esc($post->title) ?></strong>
                <span class="text-muted ms-2 small">by <?= esc($post->author) ?></span>
            </div>
            <small class="text-muted"><?= esc($post->getFormattedDate()) ?></small>
        </div>
        <?php endforeach; ?>

        <div class="mt-3 d-flex justify-content-center">
            <?= $pager->links() ?>
        </div>
    </div>
</div>

<div class="mt-3"><a href="<?= base_url('examples/models') ?>" class="demo-btn" style="background:#6f42c1;">← 모델로</a></div>

<?= $this->endSection() ?>
