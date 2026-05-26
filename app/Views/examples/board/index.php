<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">게시판</li>
    </ol></nav>
    <h1><i class="bi bi-card-list me-2"></i>실전 예제: 게시판 CRUD</h1>
    <p>Model, View, Controller, Migration, Seeder가 연동된 완성형 게시판입니다.</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted">총 <strong><?= $pager->getTotal() ?></strong>개의 게시글</span>
    <a href="<?= base_url('examples/board/create') ?>" class="demo-btn">
        <i class="bi bi-pencil-square"></i> 새 글 작성
    </a>
</div>

<div class="example-card">
    <div class="example-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="60">번호</th>
                        <th>제목</th>
                        <th width="100">작성자</th>
                        <th width="70">조회</th>
                        <th width="120">작성일</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                게시글이 없습니다.
                                <a href="<?= base_url('examples/board/create') ?>">첫 글을 작성해보세요!</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                        <tr style="cursor:pointer;" onclick="location.href='<?= base_url("examples/board/{$post->id}") ?>'">
                            <td class="text-muted"><?= esc($post->id) ?></td>
                            <td>
                                <span class="fw-semibold"><?= esc($post->title) ?></span>
                                <div class="text-muted small"><?= esc($post->getExcerpt(60)) ?></div>
                            </td>
                            <td><?= esc($post->author) ?></td>
                            <td><span class="badge bg-secondary"><?= esc($post->views) ?></span></td>
                            <td><small class="text-muted"><?= esc($post->getFormattedDate()) ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 페이지네이션 -->
<div class="d-flex justify-content-center mt-3">
    <?= $pager->links() ?>
</div>

<!-- 코드 설명 -->
<div class="example-card mt-4">
    <div class="example-card-header"><span class="badge bg-dark">소스</span><h5>게시판 구성 파일</h5></div>
    <div class="example-card-body">
        <div class="row g-2">
            <div class="col-md-4">
                <div class="result-box">
                    <div class="code-label">컨트롤러</div>
                    <code>app/Controllers/Examples/Board.php</code>
                </div>
            </div>
            <div class="col-md-4">
                <div class="result-box info">
                    <div class="code-label">모델</div>
                    <code>app/Models/PostModel.php</code><br>
                    <code>app/Entities/Post.php</code>
                </div>
            </div>
            <div class="col-md-4">
                <div class="result-box warning">
                    <div class="code-label">DB 스키마</div>
                    <code>app/Database/Migrations/..._CreatePostsTable.php</code><br>
                    <code>app/Database/Seeds/PostSeeder.php</code>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
