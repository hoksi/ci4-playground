<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'CI4 Playground') ?> — CodeIgniter 4</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 56px;
            --ci-red: #dd4814;
            --ci-dark: #1a1a2e;
        }
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }

        /* Header */
        .app-header {
            height: var(--header-height);
            background: var(--ci-dark);
            position: fixed; top: 0; left: 0; right: 0; z-index: 1030;
            display: flex; align-items: center; padding: 0 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.3);
        }
        .app-header .brand { color: #fff; font-weight: 700; font-size: 1.15rem; text-decoration: none; }
        .app-header .brand span { color: var(--ci-red); }
        .app-header .version-badge {
            background: var(--ci-red); color: #fff;
            font-size: .7rem; padding: 2px 8px; border-radius: 20px;
            margin-left: .5rem; font-weight: 600;
        }

        /* Sidebar */
        .app-sidebar {
            width: var(--sidebar-width);
            position: fixed; top: var(--header-height); left: 0; bottom: 0;
            background: #fff; overflow-y: auto; border-right: 1px solid #e9ecef;
            padding: 1rem 0;
        }
        .nav-section-title {
            font-size: .7rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .08em; color: #adb5bd;
            padding: .5rem 1.25rem .25rem;
        }
        .app-sidebar .nav-link {
            color: #495057; padding: .45rem 1.25rem;
            font-size: .9rem; border-radius: 0;
            display: flex; align-items: center; gap: .6rem;
        }
        .app-sidebar .nav-link:hover { background: #f8f9fa; color: var(--ci-red); }
        .app-sidebar .nav-link.active {
            background: #fff3ef; color: var(--ci-red); font-weight: 600;
            border-right: 3px solid var(--ci-red);
        }
        .app-sidebar .nav-link .bi { font-size: 1rem; opacity: .7; }

        /* Main content */
        .app-main {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 2rem;
            min-height: calc(100vh - var(--header-height));
        }

        /* Page header */
        .page-header {
            background: linear-gradient(135deg, var(--ci-dark) 0%, #16213e 100%);
            color: #fff; border-radius: 12px; padding: 2rem 2.5rem;
            margin-bottom: 2rem;
        }
        .page-header h1 { font-size: 1.8rem; font-weight: 700; margin: 0 0 .5rem; }
        .page-header p { margin: 0; opacity: .8; font-size: .95rem; }

        /* Example cards */
        .example-card {
            background: #fff; border-radius: 10px;
            border: 1px solid #e9ecef;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
            margin-bottom: 1.5rem; overflow: hidden;
        }
        .example-card-header {
            background: #f8f9fa; padding: .75rem 1.25rem;
            border-bottom: 1px solid #e9ecef;
            display: flex; align-items: center; gap: .75rem;
        }
        .example-card-header h5 { margin: 0; font-size: 1rem; font-weight: 600; }
        .example-card-body { padding: 1.25rem; }

        /* Code blocks */
        pre { margin: 0; border-radius: 8px; }
        pre code { font-size: .84rem; line-height: 1.6; }
        .code-label {
            font-size: .75rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: .06em; color: #6c757d; margin-bottom: .4rem;
        }

        /* Result box */
        .result-box {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-radius: 8px; padding: 1rem 1.25rem;
        }
        .result-box.info { background: #eff6ff; border-color: #bfdbfe; }
        .result-box.warning { background: #fffbeb; border-color: #fde68a; }
        .result-box.danger { background: #fef2f2; border-color: #fecaca; }

        /* Demo link buttons */
        .demo-btn {
            display: inline-flex; align-items: center; gap: .4rem;
            background: var(--ci-red); color: #fff;
            padding: .35rem .9rem; border-radius: 6px;
            font-size: .85rem; text-decoration: none; font-weight: 500;
        }
        .demo-btn:hover { background: #c03a0f; color: #fff; }
        .demo-btn.outline {
            background: transparent; border: 1px solid var(--ci-red); color: var(--ci-red);
        }
        .demo-btn.outline:hover { background: var(--ci-red); color: #fff; }

        /* Breadcrumb */
        .breadcrumb { font-size: .85rem; }
        .breadcrumb-item a { color: var(--ci-red); text-decoration: none; }

        /* Footer */
        .app-footer {
            margin-left: var(--sidebar-width); padding: 1rem 2rem;
            border-top: 1px solid #e9ecef; font-size: .8rem; color: #adb5bd;
            background: #fff;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .app-sidebar { display: none; }
            .app-main, .app-footer { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="app-header">
    <a href="<?= base_url() ?>" class="brand">
        <span>CI4</span> Playground
    </a>
    <span class="version-badge">v4.7.3</span>
    <div class="ms-auto d-flex align-items-center gap-3">
        <a href="https://www.cikorea.net" target="_blank" class="text-white-50 text-decoration-none" style="font-size:.85rem;">
            <i class="bi bi-book"></i> 한국어문서
        </a>
    </div>
</header>

<!-- Sidebar -->
<nav class="app-sidebar">
    <div class="nav-section-title">시작하기</div>
    <a href="<?= base_url() ?>" class="nav-link <?= uri_string() === '' ? 'active' : '' ?>">
        <i class="bi bi-house-door"></i> 홈 / 목차
    </a>

    <div class="nav-section-title mt-2">핵심 기능</div>
    <a href="<?= base_url('examples/routing') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/routing') ? 'active' : '' ?>">
        <i class="bi bi-sign-turn-right"></i> 라우팅
    </a>
    <a href="<?= base_url('examples/controllers') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/controllers') ? 'active' : '' ?>">
        <i class="bi bi-cpu"></i> 컨트롤러
    </a>
    <a href="<?= base_url('examples/views') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/views') ? 'active' : '' ?>">
        <i class="bi bi-window"></i> 뷰
    </a>
    <a href="<?= base_url('examples/models') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/models') ? 'active' : '' ?>">
        <i class="bi bi-database"></i> 모델 & 데이터베이스
    </a>

    <a href="<?= base_url('examples/entityadvanced') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/entityadvanced') ? 'active' : '' ?>">
        <i class="bi bi-box"></i> Entity 심화
    </a>

    <div class="nav-section-title mt-2">고급 기능</div>
    <a href="<?= base_url('examples/filters') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/filters') ? 'active' : '' ?>">
        <i class="bi bi-funnel"></i> 필터
    </a>
    <a href="<?= base_url('examples/api') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/api') ? 'active' : '' ?>">
        <i class="bi bi-braces"></i> RESTful API
    </a>
    <a href="<?= base_url('examples/apiv2') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/apiv2') ? 'active' : '' ?>">
        <i class="bi bi-braces-asterisk"></i> RESTful API v2 (JWT)
    </a>

    <div class="nav-section-title mt-2">입출력 처리</div>
    <a href="<?= base_url('examples/fileupload') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/fileupload') ? 'active' : '' ?>">
        <i class="bi bi-cloud-upload"></i> 파일 업로드
    </a>
    <a href="<?= base_url('examples/session') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/session') ? 'active' : '' ?>">
        <i class="bi bi-archive"></i> 세션 & 쿠키
    </a>
    <a href="<?= base_url('examples/validation') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/validation') ? 'active' : '' ?>">
        <i class="bi bi-shield-check"></i> 유효성 검사
    </a>
    <a href="<?= base_url('examples/httpclient') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/httpclient') ? 'active' : '' ?>">
        <i class="bi bi-globe"></i> HTTP 클라이언트
    </a>
    <a href="<?= base_url('examples/email') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/email') ? 'active' : '' ?>">
        <i class="bi bi-envelope"></i> 이메일 발송
    </a>

    <div class="nav-section-title mt-2">아키텍처 패턴</div>
    <a href="<?= base_url('examples/servicelayer') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/servicelayer') ? 'active' : '' ?>">
        <i class="bi bi-layers"></i> 서비스 레이어
    </a>
    <a href="<?= base_url('examples/repository') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/repository') ? 'active' : '' ?>">
        <i class="bi bi-diagram-3"></i> Repository 패턴
    </a>
    <a href="<?= base_url('examples/helper') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/helper') ? 'active' : '' ?>">
        <i class="bi bi-tools"></i> 커스텀 헬퍼
    </a>
    <a href="<?= base_url('examples/cache') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/cache') ? 'active' : '' ?>">
        <i class="bi bi-lightning-charge"></i> 캐싱
    </a>
    <a href="<?= base_url('examples/lang') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/lang') ? 'active' : '' ?>">
        <i class="bi bi-translate"></i> 다국어 (i18n)
    </a>
    <a href="<?= base_url('examples/events') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/events') ? 'active' : '' ?>">
        <i class="bi bi-bell"></i> 이벤트 시스템
    </a>
    <a href="<?= base_url('examples/cli') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/cli') ? 'active' : '' ?>">
        <i class="bi bi-terminal"></i> CLI 커맨드
    </a>
    <a href="<?= base_url('examples/testing') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/testing') ? 'active' : '' ?>">
        <i class="bi bi-check2-circle"></i> 테스팅
    </a>

    <div class="nav-section-title mt-2">실무 패턴</div>
    <a href="<?= base_url('examples/transaction') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/transaction') ? 'active' : '' ?>">
        <i class="bi bi-arrow-left-right"></i> DB 트랜잭션
    </a>
    <a href="<?= base_url('examples/logging') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/logging') ? 'active' : '' ?>">
        <i class="bi bi-journal-text"></i> 로깅
    </a>
    <a href="<?= base_url('examples/exception') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/exception') ? 'active' : '' ?>">
        <i class="bi bi-shield-exclamation"></i> 예외 처리
    </a>
    <a href="<?= base_url('examples/throttler') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/throttler') ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> Throttler
    </a>
    <a href="<?= base_url('examples/modelcallback') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/modelcallback') ? 'active' : '' ?>">
        <i class="bi bi-arrow-repeat"></i> Model 콜백
    </a>
    <a href="<?= base_url('examples/configenv') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/configenv') ? 'active' : '' ?>">
        <i class="bi bi-sliders"></i> Config 환경 분리
    </a>
    <a href="<?= base_url('examples/advancedvalidation') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/advancedvalidation') ? 'active' : '' ?>">
        <i class="bi bi-shield-check"></i> 유효성 검사 고급
    </a>
    <a href="<?= base_url('examples/apiauth') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/apiauth') ? 'active' : '' ?>">
        <i class="bi bi-key"></i> API 인증
    </a>
    <a href="<?= base_url('examples/securitydemo') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/securitydemo') ? 'active' : '' ?>">
        <i class="bi bi-shield-lock"></i> Security
    </a>
    <a href="<?= base_url('examples/querybuilderadvanced') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/querybuilderadvanced') ? 'active' : '' ?>">
        <i class="bi bi-database-gear"></i> Query Builder 고급
    </a>
    <a href="<?= base_url('examples/paginationadvanced') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/paginationadvanced') ? 'active' : '' ?>">
        <i class="bi bi-collection"></i> Pagination 심화
    </a>
    <a href="<?= base_url('examples/multidb') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/multidb') ? 'active' : '' ?>">
        <i class="bi bi-database-add"></i> 다중 DB 연결
    </a>
    <a href="<?= base_url('examples/imageprocess') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/imageprocess') ? 'active' : '' ?>">
        <i class="bi bi-image"></i> 이미지 처리
    </a>
    <a href="<?= base_url('examples/encryption') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/encryption') ? 'active' : '' ?>">
        <i class="bi bi-lock"></i> 암호화 & 해싱
    </a>

    <div class="nav-section-title mt-2">실전 예제</div>
    <a href="<?= base_url('examples/board') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/board') ? 'active' : '' ?>">
        <i class="bi bi-card-list"></i> 게시판 CRUD
    </a>
    <a href="<?= base_url('examples/auth') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/auth') ? 'active' : '' ?>">
        <i class="bi bi-person-lock"></i> 회원 인증
    </a>
    <a href="<?= base_url('examples/queue') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/queue') ? 'active' : '' ?>">
        <i class="bi bi-collection-play"></i> 큐(Queue) 시스템
    </a>
    <a href="<?= base_url('examples/csv-excel') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/csv-excel') ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-spreadsheet"></i> CSV/Excel
    </a>
    <a href="<?= base_url('examples/official-queue') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/official-queue') ? 'active' : '' ?>">
        <i class="bi bi-collection-play-fill"></i> CI4 공식 Queue
    </a>
    <a href="<?= base_url('examples/taskscheduler') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/taskscheduler') ? 'active' : '' ?>">
        <i class="bi bi-clock-history"></i> Task Scheduler
    </a>
    <a href="<?= base_url('examples/pdfgeneration') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/pdfgeneration') ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-pdf-fill"></i> PDF 생성
    </a>
    <a href="<?= base_url('examples/sse') ?>" class="nav-link <?= str_starts_with(uri_string(), 'examples/sse') ? 'active' : '' ?>">
        <i class="bi bi-broadcast"></i> SSE 실시간
    </a>
</nav>

<!-- Main -->
<main class="app-main">
    <?= $this->renderSection('content') ?>
</main>

<!-- Footer -->
<footer class="app-footer">
    <div class="d-flex justify-content-between align-items-center">
        <span>CI4 Playground &mdash; CodeIgniter <?= \CodeIgniter\CodeIgniter::CI_VERSION ?> 예제 모음</span>
        <span>PHP <?= phpversion() ?></span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('pre code').forEach(el => hljs.highlightElement(el));
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
