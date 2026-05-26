<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/auth') ?>" class="text-white-50">회원 인증</a></li>
        <li class="breadcrumb-item active text-white">대시보드</li>
    </ol></nav>
    <h1><i class="bi bi-speedometer2 me-2"></i>대시보드</h1>
    <p>로그인한 사용자만 볼 수 있는 보호된 페이지입니다.</p>
</div>

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

<div class="row g-3">
    <div class="col-md-6">
        <div class="example-card">
            <div class="example-card-header"><h5><i class="bi bi-person-circle me-2"></i>내 정보</h5></div>
            <div class="example-card-body">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr><th style="width:120px;">ID</th><td><?= esc($user->id) ?></td></tr>
                        <tr><th>사용자명</th><td><?= esc($user->username) ?></td></tr>
                        <tr><th>이메일</th><td><?= esc($user->email) ?></td></tr>
                        <tr><th>가입일</th><td><small><?= esc($user->created_at ?? '') ?></small></td></tr>
                        <tr><th>최근 수정</th><td><small><?= esc($user->updated_at ?? '') ?></small></td></tr>
                    </tbody>
                </table>
                <a href="<?= base_url('examples/auth/logout') ?>" class="demo-btn outline mt-3">
                    <i class="bi bi-box-arrow-right"></i> 로그아웃
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="example-card">
            <div class="example-card-header"><h5><i class="bi bi-shield-lock me-2"></i>세션 정보</h5></div>
            <div class="example-card-body">
                <p class="text-muted small mb-2">세션에 저장된 <code>auth_user</code>:</p>
                <pre style="background:#0d1117;color:#f8f8f2;border-radius:8px;padding:.8rem;font-size:.8rem;margin:0;"><code><?= esc(json_encode($auth, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></code></pre>
                <p class="text-muted small mt-3 mb-0">
                    Session ID: <code><?= esc(session_id()) ?></code>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="example-card mt-3">
    <div class="example-card-header"><h5><i class="bi bi-key me-2"></i>비밀번호 변경</h5></div>
    <div class="example-card-body">
        <form method="post" action="<?= base_url('examples/auth/profile') ?>" class="row g-2">
            <?= csrf_field() ?>
            <div class="col-md-4">
                <label class="form-label fw-bold">현재 비밀번호</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">새 비밀번호 <small class="text-muted">(6자 이상)</small></label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">새 비밀번호 확인</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <div class="col-12 mt-3">
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-arrow-repeat"></i> 비밀번호 변경
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
