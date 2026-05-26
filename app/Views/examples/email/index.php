<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">이메일 발송</li>
    </ol></nav>
    <h1><i class="bi bi-envelope me-2"></i>이메일 발송</h1>
    <p>CI4의 Email 서비스로 텍스트/HTML 이메일을 작성하고 발송합니다.</p>
</div>

<?php $tab = $tab ?? 'send'; ?>

<ul class="nav nav-tabs mb-3" id="emailTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'send' ? 'active' : '' ?>" href="#" onclick="showTab('send');return false;">이메일 작성</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- 이메일 작성 탭 -->
<div id="tab-send" class="tab-content-pane" style="display:<?= $tab === 'send' ? 'block' : 'none' ?>">

    <?php if (isset($sent)): ?>
        <?php if ($sent): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>이메일 발송 완료!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php else: ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>SMTP 미설정 — 발송 실패</strong>
            <div class="mt-1 small">.env 파일에서 SMTP 설정 후 실제 발송이 가능합니다. 아래 디버그 정보에서 이메일 내용을 확인하세요.</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-pencil-square me-2"></i>이메일 작성</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                SMTP가 설정되지 않으면 발송은 실패하지만, <strong>디버그 정보로 이메일 내용을 미리 확인</strong>할 수 있습니다.
            </div>
            <form method="post" action="<?= base_url('examples/email/send') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">받는 사람 <span class="text-danger">*</span></label>
                    <input type="text" name="to" class="form-control <?= isset($errors['to']) ? 'is-invalid' : '' ?>"
                           value="<?= esc($old['to'] ?? 'test@example.com') ?>" placeholder="example@email.com">
                    <?php if (isset($errors['to'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['to']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">제목 <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control <?= isset($errors['subject']) ? 'is-invalid' : '' ?>"
                           value="<?= esc($old['subject'] ?? '[CI4 Playground] 테스트 이메일') ?>">
                    <?php if (isset($errors['subject'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['subject']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">본문 <span class="text-danger">*</span></label>
                    <textarea name="message" class="form-control <?= isset($errors['message']) ? 'is-invalid' : '' ?>" rows="5"><?= esc($old['message'] ?? "<h2>안녕하세요!</h2>\n<p>CI4 Email 서비스 테스트입니다.</p>\n<p>이 이메일은 <strong>CodeIgniter 4</strong>에서 발송되었습니다.</p>") ?></textarea>
                    <?php if (isset($errors['message'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['message']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_html" value="1" class="form-check-input" id="isHtml"
                        <?= ($old['is_html'] ?? 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="isHtml">HTML 형식으로 발송</label>
                </div>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-send"></i> 이메일 발송 시도
                </button>
            </form>
        </div>
    </div>

    <?php if (isset($preview)): ?>
    <div class="example-card mt-3">
        <div class="example-card-header"><h5><i class="bi bi-eye me-2"></i>디버그 정보 (이메일 내용 미리보기)</h5></div>
        <div class="example-card-body">
            <pre style="background:#0d1117; color:#e6e6e6; border-radius:8px; padding:1rem; font-size:.8rem; overflow:auto; max-height:400px;"><?= esc($preview) ?></pre>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- 코드 설명 탭 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>기본 이메일 발송</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">$email = \Config\Services::email();

$email->setFrom('from@example.com', '보내는 사람');
$email->setTo('to@example.com');
$email->setCC('cc@example.com');      // 참조
$email->setBCC('bcc@example.com');    // 숨은 참조
$email->setSubject('이메일 제목');
$email->setMessage('&lt;h1&gt;HTML 본문&lt;/h1&gt;');
$email->setMailType('html');          // 'text' 또는 'html'

if ($email->send()) {
    // 발송 성공
} else {
    echo $email->printDebugger(); // 오류 확인
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>SMTP 설정 (.env)</h5></div>
        <div class="example-card-body">
            <pre><code class="language-ini"># .env 파일에 SMTP 설정 추가
email.protocol   = smtp
email.SMTPHost   = smtp.gmail.com
email.SMTPUser   = your@gmail.com
email.SMTPPass   = your-app-password
email.SMTPPort   = 587
email.SMTPCrypto = tls
email.mailType   = html
email.charset    = UTF-8
email.wordWrap   = true</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>첨부 파일 & 복수 수신자</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 복수 수신자
$email->setTo(['a@example.com', 'b@example.com']);

// 파일 첨부
$email->attach('/path/to/file.pdf');
$email->attach('/path/to/image.jpg', 'inline'); // 인라인

// 대체 텍스트 (HTML 이메일의 텍스트 버전)
$email->setAltMessage('HTML을 지원하지 않는 클라이언트에 표시될 텍스트');

// 우선순위 (1=최고, 3=보통, 5=낮음)
$email->setPriority(3);</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#emailTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
<?= $this->endSection() ?>
