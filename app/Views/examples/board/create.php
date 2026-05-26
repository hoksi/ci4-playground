<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/board') ?>">게시판</a></li>
        <li class="breadcrumb-item active text-white">새 글 작성</li>
    </ol></nav>
    <h1><i class="bi bi-pencil-square me-2"></i>새 글 작성</h1>
</div>

<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<?php if ($errors): ?>
<div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="example-card">
    <div class="example-card-body">
        <form method="post" action="<?= base_url('examples/board/store') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-bold">제목 <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                       value="<?= old('title') ?>" placeholder="제목을 입력하세요 (2~200자)">
                <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?= esc($errors['title']) ?></div><?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">작성자 <span class="text-danger">*</span></label>
                <input type="text" name="author" class="form-control <?= isset($errors['author']) ? 'is-invalid' : '' ?>"
                       value="<?= old('author') ?>" placeholder="이름을 입력하세요">
                <?php if (isset($errors['author'])): ?><div class="invalid-feedback"><?= esc($errors['author']) ?></div><?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">내용 <span class="text-danger">*</span></label>
                <textarea name="content" rows="8" class="form-control <?= isset($errors['content']) ? 'is-invalid' : '' ?>"
                          placeholder="내용을 입력하세요 (10자 이상)"><?= old('content') ?></textarea>
                <?php if (isset($errors['content'])): ?><div class="invalid-feedback"><?= esc($errors['content']) ?></div><?php endif; ?>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-save"></i> 저장
                </button>
                <a href="<?= base_url('examples/board') ?>" class="demo-btn outline">취소</a>
            </div>
        </form>
    </div>
</div>

<div class="example-card mt-4">
    <div class="example-card-header"><span class="badge bg-dark">소스</span><h5>저장 처리 코드</h5></div>
    <div class="example-card-body">
        <pre><code class="language-php">public function store()
{
    if (! $this->validate($this->model->validationRules)) {
        return redirect()->back()
            ->withInput()                              // 입력값 유지
            ->with('errors', $this->validator->getErrors());
    }

    $this->model->insert([
        'title'   => $this->request->getPost('title'),
        'content' => $this->request->getPost('content'),
        'author'  => $this->request->getPost('author'),
    ]);

    return redirect()->to(base_url('examples/board'))
                     ->with('success', '게시글이 작성되었습니다.');
}</code></pre>
    </div>
</div>

<?= $this->endSection() ?>
