<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/views') ?>">뷰</a></li>
        <li class="breadcrumb-item active text-white">레이아웃</li>
    </ol></nav>
    <h1><i class="bi bi-layout-split me-2"></i>레이아웃 시스템 데모</h1>
    <p>이 페이지 자체가 레이아웃 상속의 실제 예시입니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header">
        <h5><i class="bi bi-diagram-3 me-2"></i>현재 페이지의 렌더링 구조</h5>
    </div>
    <div class="example-card-body">
        <div class="result-box info mb-3">
            <i class="bi bi-info-circle me-2"></i>
            이 페이지는 <code>layouts/main.php</code>를 상속하고 <code>content</code> 섹션을 채웁니다.
            헤더, 사이드바, 푸터는 레이아웃에서 자동으로 렌더링됩니다.
        </div>

        <div class="code-label">이 파일 (app/Views/examples/views/layout.php)</div>
        <pre><code class="language-php">// 1단계: 레이아웃 지정
<?= $this->extend('layouts/main') ?>

// 2단계: 'content' 섹션 시작
<?= $this->section('content') ?>

&lt;h1&gt;페이지 내용&lt;/h1&gt;
&lt;p&gt;이 내용이 layouts/main.php의 renderSection('content') 위치에 삽입됩니다.&lt;/p&gt;

// 3단계: 섹션 종료
<?= $this->endSection() ?>

// 추가 섹션 (예: 페이지별 스크립트)
<?= $this->section('scripts') ?>
&lt;script&gt;console.log('페이지 전용 스크립트');&lt;/script&gt;
<?= $this->endSection() ?></code></pre>

        <div class="code-label mt-3">레이아웃 파일 핵심 부분 (app/Views/layouts/main.php)</div>
        <pre><code class="language-php">&lt;body&gt;
    &lt;!-- 고정 헤더 (모든 페이지 공통) --&gt;
    &lt;header&gt;...&lt;/header&gt;

    &lt;!-- 사이드바 (모든 페이지 공통) --&gt;
    &lt;nav&gt;...&lt;/nav&gt;

    &lt;main&gt;
        &lt;!-- 각 페이지의 content 섹션이 여기에 삽입됨 --&gt;
        <?= $this->renderSection('content') ?>
    &lt;/main&gt;

    &lt;footer&gt;...&lt;/footer&gt;

    &lt;!-- 각 페이지의 scripts 섹션이 여기에 삽입됨 --&gt;
    <?= $this->renderSection('scripts') ?>
&lt;/body&gt;</code></pre>

        <div class="row mt-3 g-3">
            <div class="col-md-4">
                <div class="result-box text-center">
                    <div class="fw-bold mb-1">레이아웃 파일</div>
                    <code class="small">layouts/main.php</code>
                    <div class="text-muted small mt-1">공통 구조 정의</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="result-box text-center">
                    <div class="fw-bold mb-1">이 페이지</div>
                    <code class="small">views/layout.php</code>
                    <div class="text-muted small mt-1">content 섹션만 정의</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="result-box text-center">
                    <div class="fw-bold mb-1">최종 결과</div>
                    <code class="small">렌더링된 HTML</code>
                    <div class="text-muted small mt-1">레이아웃 + 콘텐츠 합쳐짐</div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="<?= base_url('examples/views') ?>" class="demo-btn" style="background:#198754;">← 뷰로</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
