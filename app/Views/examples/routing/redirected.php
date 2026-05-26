<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/routing') ?>">라우팅</a></li>
        <li class="breadcrumb-item active text-white">리다이렉트</li>
    </ol></nav>
    <h1><i class="bi bi-arrow-repeat me-2"></i>리다이렉트 완료!</h1>
    <p><code>/examples/routing/redirect</code>에서 이 페이지로 리다이렉트되었습니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header">
        <h5><i class="bi bi-check-circle text-success me-2"></i>리다이렉트 성공</h5>
    </div>
    <div class="example-card-body">
        <div class="result-box">
            <p class="mb-1"><i class="bi bi-arrow-right text-success me-2"></i><code>/examples/routing/redirect</code></p>
            <p class="mb-0"><i class="bi bi-arrow-right text-success me-2"></i><strong>현재 페이지:</strong> <code><?= current_url() ?></code></p>
        </div>
        <div class="code-label mt-3">컨트롤러 코드</div>
        <pre><code class="language-php">public function redirect()
{
    // URL로 직접 리다이렉트
    return redirect()->to(base_url('examples/routing/redirected'));
}

// 다른 리다이렉트 방법들:
// return redirect()->route('routing.named');        // Named Route
// return redirect()->back();                        // 이전 페이지
// return redirect()->back()->with('msg', '완료!');  // 플래시 데이터와 함께
// return redirect()->to('/')->with('error', '..'); // 에러 메시지와 함께</code></pre>
        <div class="mt-3 d-flex gap-2">
            <a href="<?= base_url('examples/routing/redirect') ?>" class="demo-btn outline">
                <i class="bi bi-arrow-repeat"></i> 다시 리다이렉트
            </a>
            <a href="<?= base_url('examples/routing') ?>" class="demo-btn">← 라우팅으로</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
