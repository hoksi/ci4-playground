<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/filters') ?>">필터</a></li>
        <li class="breadcrumb-item active text-white">로그인</li>
    </ol></nav>
    <h1><i class="bi bi-person-lock me-2"></i>로그인 (필터 데모)</h1>
    <p>인증 필터에 의해 리다이렉트된 경우 이 페이지로 옵니다.</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <div class="example-card">
            <div class="example-card-header"><h5><i class="bi bi-key me-2"></i>데모 로그인</h5></div>
            <div class="example-card-body">
                <div class="result-box warning mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    데모 계정: ID <code>demo</code> / PW <code>1234</code>
                </div>

                <form method="post" action="<?= base_url('examples/filters/login') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">아이디</label>
                        <input type="text" name="user_id" class="form-control" placeholder="demo" value="demo">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">비밀번호</label>
                        <input type="password" name="password" class="form-control" placeholder="1234" value="1234">
                    </div>
                    <button type="submit" class="demo-btn w-100 justify-content-center" style="background:#fd7e14; border:none; cursor:pointer;">
                        <i class="bi bi-box-arrow-in-right"></i> 로그인
                    </button>
                </form>

                <div class="code-label mt-4">컨트롤러 처리 코드</div>
                <pre><code class="language-php">public function login()
{
    if ($this->request->getMethod() === 'post') {
        if ($id === 'demo' && $password === '1234') {
            session()->set(['filter_logged_in' => true, 'filter_user' => $id]);
            return redirect()->to(base_url('examples/filters/protected'));
        }
        return redirect()->back()->with('error', '인증 실패');
    }
    return view('examples/filters/login', ...);
}</code></pre>
            </div>
        </div>

        <div class="mt-3">
            <a href="<?= base_url('examples/filters') ?>" class="demo-btn" style="background:#fd7e14;">← 필터로</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
