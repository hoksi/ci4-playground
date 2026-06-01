<?php
// 전체 예제 순서 (이전/다음 네비게이션)
$allExamples = [
    ['url' => 'examples/routing',             'label' => '라우팅',           'icon' => 'sign-turn-right'],
    ['url' => 'examples/controllers',         'label' => '컨트롤러',         'icon' => 'cpu'],
    ['url' => 'examples/views',               'label' => '뷰',               'icon' => 'window'],
    ['url' => 'examples/models',              'label' => '모델 & 데이터베이스','icon' => 'database'],
    ['url' => 'examples/entityadvanced',      'label' => 'Entity 심화',      'icon' => 'box'],
    ['url' => 'examples/filters',             'label' => '필터',             'icon' => 'funnel'],
    ['url' => 'examples/api',                 'label' => 'RESTful API',      'icon' => 'braces'],
    ['url' => 'examples/apiv2',               'label' => 'RESTful API v2 (JWT)','icon' => 'braces-asterisk'],
    ['url' => 'examples/fileupload',          'label' => '파일 업로드',      'icon' => 'cloud-upload'],
    ['url' => 'examples/session',             'label' => '세션 & 쿠키',      'icon' => 'archive'],
    ['url' => 'examples/validation',          'label' => '유효성 검사',      'icon' => 'shield-check'],
    ['url' => 'examples/httpclient',          'label' => 'HTTP 클라이언트',  'icon' => 'globe'],
    ['url' => 'examples/email',               'label' => '이메일 발송',      'icon' => 'envelope'],
    ['url' => 'examples/servicelayer',        'label' => '서비스 레이어',    'icon' => 'layers'],
    ['url' => 'examples/repository',          'label' => 'Repository 패턴', 'icon' => 'diagram-3'],
    ['url' => 'examples/helper',              'label' => '커스텀 헬퍼',      'icon' => 'tools'],
    ['url' => 'examples/cache',               'label' => '캐싱',             'icon' => 'lightning-charge'],
    ['url' => 'examples/lang',                'label' => '다국어 (i18n)',    'icon' => 'translate'],
    ['url' => 'examples/events',              'label' => '이벤트 시스템',    'icon' => 'bell'],
    ['url' => 'examples/cli',                 'label' => 'CLI 커맨드',       'icon' => 'terminal'],
    ['url' => 'examples/testing',             'label' => '테스팅',           'icon' => 'check2-circle'],
    ['url' => 'examples/transaction',         'label' => 'DB 트랜잭션',      'icon' => 'arrow-left-right'],
    ['url' => 'examples/logging',             'label' => '로깅',             'icon' => 'journal-text'],
    ['url' => 'examples/exception',           'label' => '예외 처리',        'icon' => 'shield-exclamation'],
    ['url' => 'examples/throttler',           'label' => 'Throttler',        'icon' => 'speedometer2'],
    ['url' => 'examples/modelcallback',       'label' => 'Model 콜백',       'icon' => 'arrow-repeat'],
    ['url' => 'examples/configenv',           'label' => 'Config 환경 분리', 'icon' => 'sliders'],
    ['url' => 'examples/advancedvalidation',  'label' => '유효성 검사 고급', 'icon' => 'shield-check'],
    ['url' => 'examples/apiauth',             'label' => 'API 인증',         'icon' => 'key'],
    ['url' => 'examples/securitydemo',        'label' => 'Security',         'icon' => 'shield-lock'],
    ['url' => 'examples/querybuilderadvanced','label' => 'Query Builder 고급','icon' => 'database-gear'],
    ['url' => 'examples/paginationadvanced',  'label' => 'Pagination 심화',  'icon' => 'collection'],
    ['url' => 'examples/multidb',             'label' => '다중 DB 연결',     'icon' => 'database-add'],
    ['url' => 'examples/imageprocess',        'label' => '이미지 처리',      'icon' => 'image'],
    ['url' => 'examples/encryption',          'label' => '암호화 & 해싱',    'icon' => 'lock'],
    ['url' => 'examples/board',               'label' => '게시판 CRUD',      'icon' => 'card-list'],
    ['url' => 'examples/auth',                'label' => '회원 인증',        'icon' => 'person-lock'],
    ['url' => 'examples/queue',               'label' => '큐 시스템',        'icon' => 'collection-play'],
    ['url' => 'examples/csv-excel',           'label' => 'CSV/Excel',        'icon' => 'file-earmark-spreadsheet'],
    ['url' => 'examples/official-queue',      'label' => 'CI4 공식 Queue',   'icon' => 'collection-play-fill'],
    ['url' => 'examples/taskscheduler',       'label' => 'Task Scheduler',   'icon' => 'clock-history'],
    ['url' => 'examples/pdfgeneration',       'label' => 'PDF 생성',         'icon' => 'file-earmark-pdf-fill'],
    ['url' => 'examples/sse',                 'label' => 'SSE 실시간',       'icon' => 'broadcast'],
    ['url' => 'examples/notification',        'label' => '알림 시스템',      'icon' => 'bell'],
    ['url' => 'examples/fileupload-advanced', 'label' => '파일 업로드 심화', 'icon' => 'cloud-upload-fill'],
    ['url' => 'examples/tinymce',             'label' => 'TinyMCE 에디터',  'icon' => 'pencil-square'],
];

$navGroups = [
    '핵심 기능' => [
        'examples/routing', 'examples/controllers', 'examples/views',
        'examples/models', 'examples/entityadvanced',
    ],
    '고급 기능' => [
        'examples/filters', 'examples/api', 'examples/apiv2',
    ],
    '입출력 처리' => [
        'examples/fileupload', 'examples/fileupload-advanced', 'examples/tinymce',
        'examples/session', 'examples/validation', 'examples/httpclient', 'examples/email',
    ],
    '아키텍처 패턴' => [
        'examples/servicelayer', 'examples/repository', 'examples/helper',
        'examples/cache', 'examples/lang', 'examples/events',
        'examples/cli', 'examples/testing',
    ],
    '실무 패턴' => [
        'examples/transaction', 'examples/logging', 'examples/exception',
        'examples/throttler', 'examples/modelcallback', 'examples/configenv',
        'examples/advancedvalidation', 'examples/apiauth', 'examples/securitydemo',
        'examples/querybuilderadvanced', 'examples/paginationadvanced',
        'examples/multidb', 'examples/imageprocess', 'examples/encryption',
    ],
    '실전 예제' => [
        'examples/board', 'examples/auth', 'examples/queue',
        'examples/csv-excel', 'examples/official-queue', 'examples/taskscheduler',
        'examples/pdfgeneration', 'examples/sse', 'examples/notification',
    ],
];

// 현재 예제 인덱스 파악 (이전/다음)
$exampleByUrl = [];
foreach ($allExamples as $i => $ex) { $exampleByUrl[$ex['url']] = $i; }

$currentUri = uri_string();
$currentIdx = -1;
foreach ($allExamples as $i => $ex) {
    if (str_starts_with($currentUri, $ex['url'])) { $currentIdx = $i; break; }
}
$prevEx = $currentIdx > 0 ? $allExamples[$currentIdx - 1] : null;
$nextEx = $currentIdx >= 0 && $currentIdx < count($allExamples) - 1 ? $allExamples[$currentIdx + 1] : null;

// 각 URL에 해당하는 example 정보 맵
$exMap = array_combine(array_column($allExamples, 'url'), $allExamples);
?>
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
            --header-height: 56px;
            --ci-red: #dd4814;
            --ci-dark: #1a1a2e;
        }
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; padding-top: var(--header-height); }

        /* ── Top Navbar ── */
        .app-navbar {
            min-height: var(--header-height);
            background: var(--ci-dark);
            position: fixed; top: 0; left: 0; right: 0; z-index: 1030;
            box-shadow: 0 2px 8px rgba(0,0,0,.3);
        }
        .app-navbar .navbar-brand {
            color: #fff; font-weight: 700; font-size: 1.15rem;
        }
        .app-navbar .navbar-brand span:not(.version-badge) { color: var(--ci-red); }
        .version-badge {
            background: var(--ci-red); color: #fff;
            font-size: .7rem; padding: 2px 8px; border-radius: 20px;
            margin-left: .4rem; font-weight: 600; vertical-align: middle;
        }
        .app-navbar .nav-link {
            color: rgba(255,255,255,.85) !important;
            font-size: .88rem; padding: .4rem .75rem !important;
        }
        .app-navbar .nav-link:hover,
        .app-navbar .nav-link.active { color: #fff !important; }
        .app-navbar .dropdown-menu {
            background: #fff; border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,.15);
            border-radius: 8px; min-width: 220px; padding: .5rem 0;
        }
        .app-navbar .dropdown-item {
            font-size: .875rem; padding: .45rem 1.1rem;
            color: #495057; display: flex; align-items: center; gap: .55rem;
        }
        .app-navbar .dropdown-item:hover { background: #fff3ef; color: var(--ci-red); }
        .app-navbar .dropdown-item.active { background: #fff3ef; color: var(--ci-red); font-weight: 600; }
        .app-navbar .dropdown-item .bi { font-size: .9rem; opacity: .7; }
        .app-navbar .navbar-toggler { border-color: rgba(255,255,255,.3); }
        .app-navbar .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,255,255,0.85%29' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ── Main content ── */
        .app-main { padding: 2rem; min-height: calc(100vh - var(--header-height) - 48px); }

        /* ── Page header ── */
        .page-header {
            background: linear-gradient(135deg, var(--ci-dark) 0%, #16213e 100%);
            color: #fff; border-radius: 12px; padding: 2rem 2.5rem; margin-bottom: 2rem;
        }
        .page-header h1 { font-size: 1.8rem; font-weight: 700; margin: 0 0 .5rem; }
        .page-header p { margin: 0; opacity: .8; font-size: .95rem; }

        /* ── Example cards ── */
        .example-card {
            background: #fff; border-radius: 10px; border: 1px solid #e9ecef;
            box-shadow: 0 1px 4px rgba(0,0,0,.05); margin-bottom: 1.5rem; overflow: hidden;
        }
        .example-card-header {
            background: #f8f9fa; padding: .75rem 1.25rem; border-bottom: 1px solid #e9ecef;
            display: flex; align-items: center; gap: .75rem;
        }
        .example-card-header h5 { margin: 0; font-size: 1rem; font-weight: 600; }
        .example-card-body { padding: 1.25rem; }

        /* ── Code blocks ── */
        pre { margin: 0; border-radius: 8px; }
        pre code { font-size: .84rem; line-height: 1.6; }
        .code-label {
            font-size: .75rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: .06em; color: #6c757d; margin-bottom: .4rem;
        }

        /* ── Result / Demo boxes ── */
        .result-box {
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 1rem 1.25rem;
        }
        .result-box.info    { background: #eff6ff; border-color: #bfdbfe; }
        .result-box.warning { background: #fffbeb; border-color: #fde68a; }
        .result-box.danger  { background: #fef2f2; border-color: #fecaca; }

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

        /* ── Breadcrumb ── */
        .breadcrumb { font-size: .85rem; }
        .breadcrumb-item a { color: var(--ci-red); text-decoration: none; }

        /* ── Prev / Next navigation ── */
        .page-nav {
            display: flex; justify-content: space-between; align-items: stretch;
            gap: 1rem; margin-top: 2.5rem; padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }
        .page-nav-btn {
            flex: 1; display: flex; align-items: center; gap: .75rem;
            padding: .9rem 1.25rem; border-radius: 10px;
            border: 1px solid #e9ecef; background: #fff;
            text-decoration: none; color: #495057;
            transition: all .15s; min-width: 0;
        }
        .page-nav-btn:hover { border-color: var(--ci-red); color: var(--ci-red); background: #fff3ef; }
        .page-nav-btn.next { justify-content: flex-end; text-align: right; }
        .page-nav-btn .nav-dir { font-size: .72rem; color: #adb5bd; text-transform: uppercase; letter-spacing: .06em; display: block; margin-bottom: .1rem; }
        .page-nav-btn .nav-label { font-size: .9rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .page-nav-btn .nav-icon { font-size: 1.4rem; flex-shrink: 0; opacity: .5; }
        .page-nav-btn > div { min-width: 0; overflow: hidden; }
        .page-nav-spacer { flex: 1; }

        /* ── Footer ── */
        .app-footer {
            padding: .75rem 2rem; border-top: 1px solid #e9ecef;
            font-size: .8rem; color: #adb5bd; background: #fff;
        }

        /* ── Mobile ── */
        @media (max-width: 576px) {
            .page-nav { gap: .5rem; }
            .page-nav-btn { padding: .65rem .75rem; }
            .page-nav-btn .nav-label { font-size: .8rem; }
            .page-nav-btn .nav-icon { font-size: 1.1rem; }
        }
        @media (max-width: 991px) {
            .app-navbar .navbar-collapse {
                background: var(--ci-dark);
                padding: .5rem 0 1rem;
                border-top: 1px solid rgba(255,255,255,.1);
                max-height: calc(100vh - var(--header-height));
                overflow-y: auto;
            }
            .app-navbar .dropdown-menu {
                box-shadow: none; border-radius: 0;
                background: rgba(255,255,255,.06);
                border: none; padding-left: 1rem;
            }
            .app-navbar .dropdown-item { color: rgba(255,255,255,.8); }
            .app-navbar .dropdown-item:hover,
            .app-navbar .dropdown-item.active {
                background: rgba(255,255,255,.1); color: #fff;
            }
        }
    </style>
</head>
<body>

<!-- ── Top Navbar ── -->
<nav class="app-navbar navbar navbar-expand-lg">
  <div class="container-fluid px-3">

    <a class="navbar-brand" href="<?= base_url() ?>">
      <span>CI4</span> Playground
      <span class="version-badge">v4.7.3</span>
    </a>

    <button class="navbar-toggler ms-auto" type="button"
            data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto ms-3">

        <!-- 홈 -->
        <li class="nav-item">
          <a class="nav-link <?= $currentUri === '' ? 'active fw-semibold' : '' ?>"
             href="<?= base_url() ?>">
            <i class="bi bi-house-door me-1"></i>홈
          </a>
        </li>

        <?php foreach ($navGroups as $groupName => $groupUrls): ?>
        <?php
            $groupActive = false;
            foreach ($groupUrls as $gUrl) {
                if (str_starts_with($currentUri, $gUrl)) { $groupActive = true; break; }
            }
        ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= $groupActive ? 'active fw-semibold' : '' ?>"
             href="#" role="button" data-bs-toggle="dropdown">
            <?= esc($groupName) ?>
          </a>
          <ul class="dropdown-menu">
            <?php foreach ($groupUrls as $gUrl): ?>
            <?php $ex = $exMap[$gUrl] ?? null; if (!$ex) continue; ?>
            <li>
              <a class="dropdown-item <?= str_starts_with($currentUri, $gUrl) ? 'active' : '' ?>"
                 href="<?= base_url($gUrl) ?>">
                <i class="bi bi-<?= $ex['icon'] ?>"></i>
                <?= esc($ex['label']) ?>
              </a>
            </li>
            <?php endforeach ?>
          </ul>
        </li>
        <?php endforeach ?>

      </ul>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a href="https://www.cikorea.net" target="_blank"
             class="nav-link" style="font-size:.82rem;">
            <i class="bi bi-book me-1"></i>한국어 문서
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- ── Main content ── -->
<main class="app-main">
    <?= $this->renderSection('content') ?>

    <?php if ($currentIdx >= 0): ?>
    <!-- 이전 / 다음 네비게이션 -->
    <div class="page-nav">
      <?php if ($prevEx): ?>
      <a href="<?= base_url($prevEx['url']) ?>" class="page-nav-btn prev">
        <i class="bi bi-chevron-left nav-icon"></i>
        <div>
          <span class="nav-dir">이전</span>
          <span class="nav-label"><?= esc($prevEx['label']) ?></span>
        </div>
      </a>
      <?php else: ?>
      <div class="page-nav-spacer"></div>
      <?php endif ?>

      <?php if ($nextEx): ?>
      <a href="<?= base_url($nextEx['url']) ?>" class="page-nav-btn next">
        <div>
          <span class="nav-dir">다음</span>
          <span class="nav-label"><?= esc($nextEx['label']) ?></span>
        </div>
        <i class="bi bi-chevron-right nav-icon"></i>
      </a>
      <?php else: ?>
      <div class="page-nav-spacer"></div>
      <?php endif ?>
    </div>
    <?php endif ?>
</main>

<!-- ── Footer ── -->
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

        // 모바일: 네비게이션 열릴 때 현재 페이지 카테고리 드롭다운 자동 펼침
        const activeItem = document.querySelector('.app-navbar .dropdown-item.active');
        if (activeItem) {
            const dropdownMenu   = activeItem.closest('.dropdown-menu');
            const dropdownToggle = dropdownMenu?.previousElementSibling;
            const mainNav        = document.getElementById('mainNav');

            if (dropdownMenu && dropdownToggle && mainNav) {
                mainNav.addEventListener('shown.bs.collapse', () => {
                    if (window.innerWidth < 992) {
                        dropdownToggle.classList.add('show');
                        dropdownToggle.setAttribute('aria-expanded', 'true');
                        dropdownMenu.classList.add('show');
                    }
                });
                mainNav.addEventListener('hidden.bs.collapse', () => {
                    dropdownToggle.classList.remove('show');
                    dropdownToggle.setAttribute('aria-expanded', 'false');
                    dropdownMenu.classList.remove('show');
                });
            }
        }
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
