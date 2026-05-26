<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/board') ?>">게시판</a></li>
        <li class="breadcrumb-item active text-white"><?= esc($post->title) ?></li>
    </ol></nav>
    <h1><?= esc($post->title) ?></h1>
    <div class="d-flex gap-3 mt-2" style="opacity:.8;font-size:.9rem;">
        <span><i class="bi bi-person me-1"></i><?= esc($post->author) ?></span>
        <span><i class="bi bi-clock me-1"></i><?= esc($post->getFormattedDate()) ?></span>
        <span><i class="bi bi-eye me-1"></i><?= esc($post->views) ?></span>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<div class="example-card">
    <div class="example-card-body">
        <div style="white-space:pre-line; line-height:1.8;"><?= esc($post->content) ?></div>
    </div>
</div>

<div class="d-flex justify-content-between mt-3">
    <a href="<?= base_url('examples/board') ?>" class="demo-btn outline">← 목록</a>
    <div class="d-flex gap-2">
        <a href="<?= base_url("examples/board/{$post->id}/edit") ?>" class="demo-btn" style="background:#fd7e14;">
            <i class="bi bi-pencil"></i> 수정
        </a>
        <a href="<?= base_url("examples/board/{$post->id}/delete") ?>"
           class="demo-btn" style="background:#dc3545;"
           onclick="return confirm('정말 삭제하시겠습니까?')">
            <i class="bi bi-trash"></i> 삭제
        </a>
    </div>
</div>

<div class="example-card mt-4">
    <div class="example-card-header"><span class="badge bg-dark">소스</span><h5>상세 조회 + 조회수 증가</h5></div>
    <div class="example-card-body">
        <pre><code class="language-php">public function show(int $id): string
{
    $post = $this->model->find($id);

    if (! $post) {
        throw PageNotFoundException::forPageNotFound();
    }

    // 조회수 +1 (raw expression 사용)
    $this->model->incrementViews($id);

    return view('examples/board/show', ['post' => $post]);
}

// PostModel의 메서드
public function incrementViews(int $id): void
{
    $this->set('views', 'views + 1', false)->where('id', $id)->update();
}</code></pre>
    </div>
</div>

<?= $this->endSection() ?>
