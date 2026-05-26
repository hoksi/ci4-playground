<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-shield-exclamation me-2"></i>예외 처리</h1>
    <p>PageNotFoundException, 커스텀 예외 클래스, try/catch 패턴, 글로벌 예외 핸들러 설정을 학습합니다.</p>
</div>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active">예외 처리</li>
    </ol>
</nav>

<?php $result = session()->getFlashdata('result'); ?>
<?php if ($result): ?>
<div class="alert <?= $result['caught'] ? 'alert-warning' : 'alert-success' ?> d-flex gap-2 align-items-start">
    <i class="bi <?= $result['caught'] ? 'bi-exclamation-triangle' : 'bi-check-circle' ?> mt-1"></i>
    <div>
        <strong><?= $result['caught'] ? '예외 캐치됨' : '정상 처리' ?></strong><br>
        <?php if (isset($result['class'])): ?>
        <code><?= esc($result['class']) ?></code> (code: <?= $result['code'] ?>)<br>
        <?php endif; ?>
        <?= esc($result['message']) ?>
    </div>
</div>
<?php endif; ?>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-404">404 처리</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-custom">커스텀 예외</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-global">글로벌 핸들러</a></li>
</ul>

<div class="tab-content">

    <!-- ── 라이브 데모 ──────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-demo">
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-play-circle text-primary"></i>
                <h5>예외 시나리오 실행</h5>
            </div>
            <div class="example-card-body">
                <form method="post" action="<?= base_url('examples/exception/demo') ?>">
                    <?= csrf_field() ?>
                    <div class="d-flex flex-wrap gap-3 mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" value="trycatch" id="r1" checked>
                            <label class="form-check-label" for="r1">RuntimeException → catch</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" value="custom" id="r2">
                            <label class="form-check-label" for="r2">InvalidArgumentException → catch</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" value="404" id="r3">
                            <label class="form-check-label" for="r3">PageNotFoundException (실제 404 페이지)</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-lightning me-1"></i> 예외 발생 실행
                    </button>
                </form>
                <div class="result-box info mt-3">
                    <strong>PageNotFoundException</strong> 시나리오는 CI4의 실제 404 에러 페이지로 이동합니다.
                    브라우저 뒤로가기로 돌아오세요.
                </div>
            </div>
        </div>
    </div>

    <!-- ── 404 처리 ────────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-404">
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-file-earmark-x text-danger"></i>
                <h5>PageNotFoundException — 404 처리</h5>
            </div>
            <div class="example-card-body">
                <p class="mb-3">CI4는 <code>PageNotFoundException</code>을 던지면 자동으로 404 응답을 반환합니다. 컨트롤러에서 리소스를 찾지 못할 때 사용합니다.</p>
                <pre><code class="language-php">use CodeIgniter\Exceptions\PageNotFoundException;

public function show(int $id): string
{
    $post = $this->model->find($id);

    if (! $post) {
        // 자동으로 404 응답 + app/Views/errors/html/error_404.php 렌더링
        throw PageNotFoundException::forPageNotFound();
    }

    return view('post/show', ['post' => $post]);
}
</code></pre>

                <div class="code-label mt-4">커스텀 404 뷰 — app/Views/errors/html/error_404.php</div>
                <pre><code class="language-php">&lt;!-- 이 파일을 만들면 CI4 기본 404 페이지 대신 사용됨 --&gt;
&lt;?php $message ??= '페이지를 찾을 수 없습니다.'; ?&gt;
&lt;h1&gt;404 Not Found&lt;/h1&gt;
&lt;p&gt;&lt;?= esc($message) ?&gt;&lt;/p&gt;
</code></pre>
            </div>
        </div>
    </div>

    <!-- ── 커스텀 예외 ─────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-custom">
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-exclamation-octagon text-warning"></i>
                <h5>커스텀 예외 클래스 + try/catch</h5>
            </div>
            <div class="example-card-body">
                <pre><code class="language-php">// app/Exceptions/InsufficientBalanceException.php
namespace App\Exceptions;

class InsufficientBalanceException extends \RuntimeException
{
    public function __construct(int $required, int $available)
    {
        parent::__construct(
            "잔액 부족: 필요={$required}, 보유={$available}",
            402
        );
    }
}
</code></pre>

                <div class="code-label mt-4">서비스에서 던지고 컨트롤러에서 잡기</div>
                <pre><code class="language-php">// Service
public function transfer(int $from, int $to, int $amount): void
{
    $account = $this->model->find($from);
    if ($account->balance < $amount) {
        throw new InsufficientBalanceException($amount, $account->balance);
    }
    // ... 이체 처리
}

// Controller
try {
    $this->transferService->transfer($fromId, $toId, $amount);
    return redirect()->back()->with('success', '이체 완료');

} catch (InsufficientBalanceException $e) {
    // 도메인 오류 → 사용자에게 안내
    return redirect()->back()->with('error', $e->getMessage());

} catch (\Exception $e) {
    // 예상치 못한 오류 → 로그 + 일반 메시지
    log_message('error', $e->getMessage());
    return redirect()->back()->with('error', '처리 중 오류가 발생했습니다.');
}
</code></pre>
            </div>
        </div>
    </div>

    <!-- ── 글로벌 핸들러 ───────────────────────────────── -->
    <div class="tab-pane fade" id="tab-global">
        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-globe text-success"></i>
                <h5>글로벌 예외 핸들러 — app/Config/Exceptions.php</h5>
            </div>
            <div class="example-card-body">
                <p class="mb-3">잡히지 않은 예외를 전역에서 처리하려면 <code>app/Config/Exceptions.php</code>에서 <code>handler()</code>를 오버라이드합니다.</p>
                <pre><code class="language-php">// app/Config/Exceptions.php
namespace Config;

use CodeIgniter\Config\Exceptions as BaseExceptions;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class Exceptions extends BaseExceptions
{
    public function handler(int $statusCode, Throwable $exception,
                            RequestInterface $request,
                            ResponseInterface $response): ResponseInterface
    {
        // 404: 커스텀 응답
        if ($exception instanceof PageNotFoundException) {
            return $response->setStatusCode(404)
                ->setBody(view('errors/custom_404'));
        }

        // 운영 환경: 상세 오류 숨기고 로그만
        if (ENVIRONMENT === 'production') {
            log_message('critical', $exception->getMessage());
            return $response->setStatusCode(500)
                ->setBody(view('errors/custom_500'));
        }

        // 개발 환경: 기본 처리 위임
        return parent::handler($statusCode, $exception, $request, $response);
    }
}
</code></pre>
                <div class="result-box info mt-3">
                    <strong>운영/개발 환경 분리 팁</strong><br>
                    <code>ENVIRONMENT === 'production'</code> 조건으로 운영에서는 스택 트레이스를 감추고,
                    개발에서는 상세 오류를 표시하도록 분기하세요.
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
