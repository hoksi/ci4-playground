<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/auth') ?>" class="text-white-50">회원 인증</a></li>
        <li class="breadcrumb-item active text-white">로그인</li>
    </ol></nav>
    <h1><i class="bi bi-box-arrow-in-right me-2"></i>로그인</h1>
    <p>등록된 계정으로 로그인하세요.</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="example-card">
            <div class="example-card-header"><h5><i class="bi bi-box-arrow-in-right me-2"></i>로그인 폼</h5></div>
            <div class="example-card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="result-box mb-3">
                        <i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="result-box danger mb-3">
                        <i class="bi bi-x-circle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('examples/auth/login') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">이메일</label>
                        <input type="email" name="email" class="form-control" value="<?= esc(old('email') ?? '') ?>" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">비밀번호</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                        <i class="bi bi-box-arrow-in-right"></i> 로그인
                    </button>
                    <a href="<?= base_url('examples/auth/register') ?>" class="demo-btn outline">
                        <i class="bi bi-person-plus"></i> 회원가입
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
