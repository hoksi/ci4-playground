<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">다국어</li>
    </ol></nav>
    <h1><i class="bi bi-translate me-2"></i>다국어 (i18n)</h1>
    <p>CI4의 Language 클래스로 다국어 지원 애플리케이션을 만드는 방법을 알아봅니다.</p>
</div>

<?php $tab = $tab ?? 'demo'; ?>
<ul class="nav nav-tabs mb-3" id="langTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'demo' ? 'active' : '' ?>" href="#" onclick="showTab('demo');return false;">번역 데모</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- 번역 데모 -->
<div id="tab-demo" class="tab-content-pane" style="display:<?= $tab === 'demo' ? 'block' : 'none' ?>">

    <!-- 언어 선택 -->
    <div class="example-card mb-3">
        <div class="example-card-header"><h5><i class="bi bi-globe me-2"></i>언어 선택</h5></div>
        <div class="example-card-body">
            <div class="d-flex gap-2 flex-wrap">
                <?php foreach ($supported as $locale => $name): ?>
                <a href="<?= base_url("examples/lang/switch/{$locale}") ?>"
                   class="demo-btn <?= $currentLang === $locale ? '' : 'outline' ?>"
                   style="<?= $currentLang === $locale ? '' : 'border:1px solid #dd4814;' ?>">
                    <?= $currentLang === $locale ? '<i class="bi bi-check-lg"></i> ' : '' ?><?= esc($name) ?>
                </a>
                <?php endforeach; ?>
            </div>
            <div class="mt-2 result-box info">
                <i class="bi bi-info-circle me-2"></i>
                현재 언어: <strong><?= esc($supported[$currentLang]) ?></strong>
                &nbsp;|&nbsp; 로케일 코드: <code><?= esc($currentLang) ?></code>
            </div>
        </div>
    </div>

    <!-- 번역 결과 -->
    <div class="example-card mb-3">
        <div class="example-card-header"><h5><i class="bi bi-card-list me-2"></i>번역 결과 <small class="fw-normal text-muted">(lang('Playground.key'))</small></h5></div>
        <div class="example-card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-dark"><tr><th>키</th><th>번역 결과</th></tr></thead>
                    <tbody>
                    <?php foreach ($translations as $key => $value): ?>
                    <tr>
                        <td><code>Playground.<?= esc($key) ?></code></td>
                        <td><?= esc($value) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 파라미터 치환 -->
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-braces me-2"></i>파라미터 치환 <small class="fw-normal text-muted">({0}, {1} 플레이스홀더)</small></h5></div>
        <div class="example-card-body">
            <?php foreach ($withParams as $val): ?>
            <div class="result-box mb-2"><code><?= esc($val) ?></code></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>언어 파일 구조</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// app/Language/ko/Playground.php
return [
    'greeting'    => '안녕하세요!',
    'items_found' => '{0}개의 항목을 찾았습니다.',
    'page_of'     => '{0} / {1} 페이지',
];

// app/Language/en/Playground.php
return [
    'greeting'    => 'Hello!',
    'items_found' => '{0} items found.',
    'page_of'     => 'Page {0} of {1}',
];</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>lang() 함수 사용</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 기본 번역
echo lang('Playground.greeting');          // 안녕하세요!

// 파라미터 치환 ({0}, {1} 순서 기반)
echo lang('Playground.items_found', [42]); // 42개의 항목을 찾았습니다.
echo lang('Playground.page_of', [3, 10]); // 3 / 10 페이지

// 로케일 지정
echo lang('Playground.greeting', [], 'en'); // Hello!</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>로케일 설정</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 런타임에 변경
service('language')->setLocale('en');

// app/Config/App.php
public string $defaultLocale = 'ko';
public string $negotiateLocale = true;  // Accept-Language 헤더 자동 협상
public array $supportedLocales = ['ko', 'en', 'ja'];

// 현재 로케일 확인
$locale = service('request')->getLocale(); // 'ko'

// 뷰에서 사용
// &lt;?= lang('Playground.greeting') ?&gt;</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#langTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
<?= $this->endSection() ?>
