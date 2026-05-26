<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/board') ?>">게시판</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url("examples/board/{$post->id}") ?>"><?= esc($post->title) ?></a></li>
        <li class="breadcrumb-item active text-white">수정</li>
    </ol></nav>
    <h1><i class="bi bi-pencil me-2"></i>게시글 수정</h1>
</div>

<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<?php if ($errors): ?>
<div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="example-card">
    <div class="example-card-body">
        <form method="post" action="<?= base_url("examples/board/{$post->id}/update") ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-bold">제목 <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control"
                       value="<?= old('title', esc($post->title)) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">작성자 <span class="text-danger">*</span></label>
                <input type="text" name="author" class="form-control"
                       value="<?= old('author', esc($post->author)) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">내용 <span class="text-danger">*</span></label>
                <textarea name="content" rows="8" class="form-control"><?= old('content', esc($post->content)) ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="demo-btn" style="background:#fd7e14;border:none;cursor:pointer;">
                    <i class="bi bi-save"></i> 수정 저장
                </button>
                <a href="<?= base_url("examples/board/{$post->id}") ?>" class="demo-btn outline">취소</a>
            </div>
        </form>
    </div>
</div>

<div class="example-card mt-4">
    <div class="example-card-header"><span class="badge bg-dark">소스</span><h5>수정 & 소프트 삭제</h5></div>
    <div class="example-card-body">
        <pre><code class="language-php">// 수정
public function update(int $id)
{
    $this->model->update($id, [
        'title'   => $this->request->getPost('title'),
        'content' => $this->request->getPost('content'),
        'author'  => $this->request->getPost('author'),
    ]);
    // updated_at 자동 갱신
}

// 소프트 삭제 (deleted_at 기록, 실제 데이터는 유지)
public function delete(int $id)
{
    $this->model->delete($id);
    // $model->useSoftDeletes = true 이므로 deleted_at에 시간 기록
    // findAll() 등 기본 조회에서는 자동으로 제외됨

    // 실제로 완전 삭제하려면:
    // $this->model->delete($id, true); // force delete
    // 또는: $this->model->withDeleted()->delete($id);
}</code></pre>
    </div>
</div>

<?= $this->endSection() ?>
