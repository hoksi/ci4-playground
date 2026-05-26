<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/auth') ?>" class="text-white-50">회원 인증</a></li>
        <li class="breadcrumb-item active text-white">회원가입</li>
    </ol></nav>
    <h1><i class="bi bi-person-plus me-2"></i>회원가입</h1>
    <p>새 계정을 만들어 보호된 페이지에 접근하세요.</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="example-card">
            <div class="example-card-header"><h5><i class="bi bi-person-plus me-2"></i>회원가입 폼</h5></div>
            <div class="example-card-body">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="result-box danger mb-3">
                        <i class="bi bi-exclamation-circle me-2"></i><strong>입력값을 확인하세요:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ((array) session()->getFlashdata('errors') as $f => $msg): ?>
                                <li><strong><?= esc($f) ?>:</strong> <?= esc($msg) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('examples/auth/register') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">사용자명 <small class="text-muted">(2~50자)</small></label>
                        <input type="text" name="username" class="form-control" value="<?= esc(old('username') ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">이메일</label>
                        <input type="email" name="email" class="form-control" value="<?= esc(old('email') ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">비밀번호 <small class="text-muted">(최소 6자)</small></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">비밀번호 확인</label>
                        <input type="password" name="password_confirm" class="form-control" required>
                    </div>
                    <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                        <i class="bi bi-check-circle"></i> 회원가입
                    </button>
                    <a href="<?= base_url('examples/auth/login') ?>" class="demo-btn outline">
                        <i class="bi bi-box-arrow-in-right"></i> 로그인으로
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
