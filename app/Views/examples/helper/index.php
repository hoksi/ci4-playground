<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">커스텀 헬퍼</li>
    </ol></nav>
    <h1><i class="bi bi-tools me-2"></i>커스텀 헬퍼</h1>
    <p>재사용 가능한 헬퍼 함수를 만들고 로드하는 방법을 알아봅니다.</p>
</div>

<?php $tab = $tab ?? 'demo'; ?>
<ul class="nav nav-tabs mb-3" id="helperTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'demo' ? 'active' : '' ?>" href="#" onclick="showTab('demo');return false;">함수 데모</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- 함수 데모 -->
<div id="tab-demo" class="tab-content-pane" style="display:<?= $tab === 'demo' ? 'block' : 'none' ?>">

    <!-- format_filesize -->
    <div class="example-card">
        <div class="example-card-header">
            <h5><code>format_filesize()</code> — 파일 크기 포맷</h5>
        </div>
        <div class="example-card-body">
            <div class="row g-2">
                <?php foreach ($demos['filesize'] as $val): ?>
                <div class="col-6 col-md-3">
                    <div class="result-box text-center">
                        <span class="fw-bold"><?= esc($val) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- time_ago -->
    <div class="example-card">
        <div class="example-card-header">
            <h5><code>time_ago()</code> — 상대 시간 표시</h5>
        </div>
        <div class="example-card-body">
            <div class="row g-2">
                <?php foreach ($demos['time_ago'] as $val): ?>
                <div class="col-6 col-md-3">
                    <div class="result-box text-center">
                        <span class="fw-bold"><?= esc($val) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- truncate_text -->
    <div class="example-card">
        <div class="example-card-header">
            <h5><code>truncate_text()</code> — 텍스트 자르기</h5>
        </div>
        <div class="example-card-body">
            <?php foreach ($demos['truncate'] as $val): ?>
            <div class="result-box mb-2">
                <code><?= esc($val) ?></code>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- highlight_keyword -->
    <div class="example-card">
        <div class="example-card-header">
            <h5><code>highlight_keyword()</code> — 키워드 강조</h5>
        </div>
        <div class="example-card-body">
            <?php foreach ($demos['highlight'] as $item): ?>
            <div class="result-box mb-2">
                <?= highlight_keyword($item['text'], $item['keyword']) ?>
                <small class="text-muted ms-2">(키워드: "<?= esc($item['keyword']) ?>")</small>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- korean_number -->
    <div class="example-card">
        <div class="example-card-header">
            <h5><code>korean_number()</code> — 한국어 숫자 단위</h5>
        </div>
        <div class="example-card-body">
            <div class="row g-2">
                <?php foreach ($demos['korean_number'] as $val): ?>
                <div class="col-6 col-md-3">
                    <div class="result-box text-center">
                        <span class="fw-bold"><?= esc($val) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>헬퍼 파일 생성</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// app/Helpers/playground_helper.php
// 파일명: {name}_helper.php 규칙

if (! function_exists('format_filesize')) {
    function format_filesize(int $bytes, int $decimals = 1): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 &amp;&amp; $i &lt; count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, $decimals) . ' ' . $units[$i];
    }
}

// if (! function_exists()) 로 감싸면 중복 정의 방지</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>헬퍼 로드 방법</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 컨트롤러/함수에서 직접 로드
helper('playground');         // 단일
helper(['playground', 'url']); // 복수

// 자동 로드 (항상 필요한 헬퍼)
// app/Config/Autoload.php
public $helpers = ['playground', 'url'];

// BaseController에서 미리 로드
class BaseController extends Controller
{
    protected $helpers = ['playground'];
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>CI4 내장 헬퍼 목록</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">helper('url');       // base_url(), site_url(), redirect() 등
helper('form');      // form_open(), form_input(), set_value() 등
helper('html');      // ul(), ol(), img(), link_tag() 등
helper('text');      // word_limiter(), character_limiter(), highlight_phrase() 등
helper('date');      // now(), timezone_menu() 등
helper('array');     // dot_array_search() 등
helper('cookie');    // set_cookie(), get_cookie(), delete_cookie() 등
helper('filesystem');// write_file(), read_file(), delete_files() 등
helper('inflector'); // singular(), plural(), camelize(), underscore() 등
helper('number');    // number_to_size(), number_to_amount() 등</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#helperTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
<?= $this->endSection() ?>
