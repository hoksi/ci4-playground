<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <div class="rounded-circle d-flex align-items-center justify-content-center"
         style="width:52px;height:52px;background:#fce4ec;">
        <i class="bi bi-file-earmark-pdf-fill fs-3" style="color:#e53935;"></i>
    </div>
    <div>
        <h2 class="mb-0">PDF 생성</h2>
        <p class="text-muted mb-0">dompdf/dompdf — HTML → PDF 변환, 인라인 보기 / 파일 다운로드</p>
    </div>
</div>

<ul class="nav nav-tabs mb-4" id="mainTab">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-code">코드</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-font">한글 폰트</a></li>
</ul>

<div class="tab-content">

<!-- ══════════════════════════════════════════════════════
     탭 1 — 라이브 데모
══════════════════════════════════════════════════════ -->
<div class="tab-pane fade show active" id="tab-demo">

    <div class="alert alert-warning d-flex gap-2 mb-4">
        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
        <div>
            기본 내장 폰트(DejaVu Sans)는 <strong>한글을 지원하지 않습니다</strong>.
            한글이 포함된 데이터는 PDF에서 네모(□)로 표시됩니다.
            <a href="#tab-font" data-bs-toggle="tab" class="alert-link">한글 폰트 설정 방법 →</a>
        </div>
    </div>

    <div class="row g-4">

        <!-- 상품 목록 PDF -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3"
                         style="width:56px;height:56px;background:#fce4ec;">
                        <i class="bi bi-bag-fill fs-3 text-danger"></i>
                    </div>
                    <h5>상품 목록 보고서</h5>
                    <p class="text-muted small">playground_products 데이터를<br>스타일된 표 형식 PDF로 출력</p>
                    <div class="d-grid gap-2 mt-3">
                        <a href="<?= base_url('examples/pdfgeneration/products') ?>"
                           target="_blank" class="btn btn-outline-danger">
                            <i class="bi bi-eye me-1"></i>브라우저에서 보기
                        </a>
                        <a href="<?= base_url('examples/pdfgeneration/products?download=1') ?>"
                           class="btn btn-danger btn-sm">
                            <i class="bi bi-download me-1"></i>PDF 다운로드
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 인보이스 PDF -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3"
                         style="width:56px;height:56px;background:#e8f5e9;">
                        <i class="bi bi-receipt fs-3 text-success"></i>
                    </div>
                    <h5>인보이스 샘플</h5>
                    <p class="text-muted small">로고·항목·합계·부가세가 포함된<br>전문적인 인보이스 PDF</p>
                    <div class="d-grid gap-2 mt-3">
                        <a href="<?= base_url('examples/pdfgeneration/invoice') ?>"
                           target="_blank" class="btn btn-outline-success">
                            <i class="bi bi-eye me-1"></i>브라우저에서 보기
                        </a>
                        <a href="<?= base_url('examples/pdfgeneration/invoice?download=1') ?>"
                           class="btn btn-success btn-sm">
                            <i class="bi bi-download me-1"></i>PDF 다운로드
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 게시글 목록 PDF -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3"
                         style="width:56px;height:56px;background:#e3f2fd;">
                        <i class="bi bi-newspaper fs-3 text-primary"></i>
                    </div>
                    <h5>게시글 목록</h5>
                    <p class="text-muted small">posts 테이블 최근 20개를<br>목록 형식 PDF로 출력</p>
                    <div class="d-grid gap-2 mt-3">
                        <a href="<?= base_url('examples/pdfgeneration/posts') ?>"
                           target="_blank" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>브라우저에서 보기
                        </a>
                        <a href="<?= base_url('examples/pdfgeneration/posts?download=1') ?>"
                           class="btn btn-primary btn-sm">
                            <i class="bi bi-download me-1"></i>PDF 다운로드
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 인라인 미리보기 -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-display me-2"></i>인라인 미리보기 (iframe)</span>
            <small class="text-muted">위 "브라우저에서 보기" 클릭 후 여기에 표시됩니다</small>
        </div>
        <div class="card-body p-0">
            <div class="text-center text-muted py-5" id="previewPlaceholder">
                <i class="bi bi-file-earmark-pdf display-4 text-danger opacity-25"></i>
                <p class="mt-2">PDF를 생성하면 여기에 미리보기가 표시됩니다</p>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════════════════════
     탭 2 — 코드
══════════════════════════════════════════════════════ -->
<div class="tab-pane fade" id="tab-code">
    <div class="row g-4">

        <!-- 설치 -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-terminal me-2"></i>1. 설치 (Installation)
                </div>
                <div class="card-body p-0">
<pre><code class="language-bash"># DOMPDF 패키지 설치
composer require dompdf/dompdf

# composer.json 에 추가됨
# "dompdf/dompdf": "^2.0"</code></pre>
                </div>
            </div>
        </div>

        <!-- 기본 사용법 -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-code-slash me-2"></i>2. 기본 사용법
                </div>
                <div class="card-body p-0">
<pre><code class="language-php">&lt;?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGeneration extends BaseController
{
    public function products(): void
    {
        // ① 데이터 조회
        $products = db_connect()->table('playground_products')->get()->getResultArray();

        // ② HTML 생성 (CI4 뷰 활용)
        $html = view('examples/pdf_generation/tpl_products', [
            'products'  =&gt; $products,
            'generated' =&gt; date('Y-m-d H:i:s'),
        ], ['saveData' =&gt; false]);

        // ③ DOMPDF 옵션 설정
        $options = new Options();
        $options-&gt;set('defaultFont', 'DejaVu Sans');    // 기본 폰트
        $options-&gt;set('isHtml5ParserEnabled', true);    // HTML5 파서 활성화
        $options-&gt;set('isRemoteEnabled', false);        // 외부 리소스 비활성화 (보안)
        $options-&gt;setChroot([FCPATH]);                  // 접근 허용 경로

        // ④ PDF 생성
        $dompdf = new Dompdf($options);
        $dompdf-&gt;loadHtml($html, 'UTF-8');  // HTML 로드
        $dompdf-&gt;setPaper('A4', 'portrait'); // 용지 크기 및 방향
        $dompdf-&gt;render();                   // PDF 렌더링

        // ⑤ 출력 방식 선택
        $download = $this-&gt;request-&gt;getGet('download') === '1';
        $dompdf-&gt;stream('products.pdf', [
            'Attachment' =&gt; (int) $download, // 0: 인라인 보기, 1: 다운로드
        ]);
    }
}</code></pre>
                </div>
            </div>
        </div>

        <!-- PDF 템플릿 뷰 -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-file-code me-2"></i>3. PDF 템플릿 뷰 (app/Views/examples/pdf_generation/tpl_products.php)
                </div>
                <div class="card-body p-0">
<pre><code class="language-html">&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
&lt;meta charset="UTF-8"&gt;
&lt;style&gt;
/* DOMPDF은 인라인 CSS만 지원 — 외부 CSS 파일 사용 불가 */
body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; }
table { width: 100%; border-collapse: collapse; }
thead th { background: #1a1a2e; color: #fff; padding: 8px; }
tbody td { padding: 7px; border-bottom: 1px solid #eee; }
tbody tr:nth-child(even) { background: #f9fafb; }
&lt;/style&gt;
&lt;/head&gt;
&lt;body&gt;
&lt;h1&gt;Product Report — &lt;?= esc($generated) ?&gt;&lt;/h1&gt;
&lt;table&gt;
    &lt;thead&gt;
        &lt;tr&gt;&lt;th&gt;#&lt;/th&gt;&lt;th&gt;Name&lt;/th&gt;&lt;th&gt;Price&lt;/th&gt;&lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
    &lt;?php foreach ($products as $i =&gt; $p): ?&gt;
    &lt;tr&gt;
        &lt;td&gt;&lt;?= $i+1 ?&gt;&lt;/td&gt;
        &lt;td&gt;&lt;?= esc($p['name']) ?&gt;&lt;/td&gt;
        &lt;td&gt;₩ &lt;?= number_format($p['price']) ?&gt;&lt;/td&gt;
    &lt;/tr&gt;
    &lt;?php endforeach ?&gt;
    &lt;/tbody&gt;
&lt;/table&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
                </div>
            </div>
        </div>

        <!-- 주요 옵션 -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">Options 주요 설정</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>옵션</th><th>기본값</th><th>설명</th></tr></thead>
                        <tbody>
                        <?php foreach ([
                            ['defaultFont',          'serif',   '기본 폰트'],
                            ['isHtml5ParserEnabled', 'false',   'HTML5 파서 사용 여부'],
                            ['isRemoteEnabled',      'false',   '외부 URL 이미지·CSS 허용'],
                            ['defaultPaperSize',     'letter',  '용지 크기 (A4, letter 등)'],
                            ['isPhpEnabled',         'false',   'PHP 실행 허용 (보안상 비권장)'],
                            ['tempDir',              '/tmp',    '임시 파일 저장 경로'],
                        ] as [$opt, $def, $desc]): ?>
                        <tr>
                            <td><code class="small"><?= $opt ?></code></td>
                            <td><span class="badge bg-light text-dark border"><?= $def ?></span></td>
                            <td><small><?= $desc ?></small></td>
                        </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- stream 옵션 -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">출력 방식</div>
                <div class="card-body">
<pre><code class="language-php">// 브라우저 인라인 보기
$dompdf-&gt;stream('file.pdf', ['Attachment' =&gt; 0]);

// 파일 다운로드
$dompdf-&gt;stream('file.pdf', ['Attachment' =&gt; 1]);

// 문자열로 반환 (저장·이메일 첨부 등)
$pdfString = $dompdf-&gt;output();
file_put_contents('/path/to/output.pdf', $pdfString);

// 용지 방향
$dompdf-&gt;setPaper('A4', 'portrait');   // 세로
$dompdf-&gt;setPaper('A4', 'landscape'); // 가로</code></pre>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     탭 3 — 한글 폰트
══════════════════════════════════════════════════════ -->
<div class="tab-pane fade" id="tab-font">
    <div class="row g-4">

        <div class="col-12">
            <div class="alert alert-info d-flex gap-2">
                <i class="bi bi-info-circle-fill mt-1"></i>
                <div>
                    DOMPDF 기본 폰트(DejaVu, Helvetica 등)는 <strong>한글을 지원하지 않습니다.</strong>
                    한글 PDF를 생성하려면 한글 TTF/OTF 폰트를 로드해야 합니다.
                </div>
            </div>
        </div>

        <!-- 폰트 준비 -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">1. 한글 폰트 파일 준비</div>
                <div class="card-body">
<pre><code class="language-bash"># Noto Sans KR 다운로드 (Google Fonts)
# https://fonts.google.com/noto/specimen/Noto+Sans+KR

# 프로젝트 폰트 디렉터리에 복사
mkdir -p public/fonts
cp NotoSansKR-Regular.ttf public/fonts/
cp NotoSansKR-Bold.ttf    public/fonts/</code></pre>
                </div>
            </div>
        </div>

        <!-- 폰트 등록 -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">2. DOMPDF에 폰트 등록</div>
                <div class="card-body">
<pre><code class="language-php">// 최초 1회만 실행 (CLI 또는 별도 스크립트)
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options-&gt;setChroot([FCPATH]);

$dompdf = new Dompdf($options);

// 폰트 메트릭스 로드 후 TTF 설치
$fontMetrics = $dompdf-&gt;getFontMetrics();
$fontMetrics-&gt;registerFont(
    ['family' =&gt; 'NotoSansKR', 'style' =&gt; 'normal', 'weight' =&gt; 'normal'],
    FCPATH . 'fonts/NotoSansKR-Regular.ttf'
);
$fontMetrics-&gt;registerFont(
    ['family' =&gt; 'NotoSansKR', 'style' =&gt; 'normal', 'weight' =&gt; 'bold'],
    FCPATH . 'fonts/NotoSansKR-Bold.ttf'
);</code></pre>
                </div>
            </div>
        </div>

        <!-- CSS 적용 -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">3. PDF 템플릿에서 폰트 사용</div>
                <div class="card-body p-0">
<pre><code class="language-html">&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
&lt;meta charset="UTF-8"&gt;
&lt;style&gt;
/* 등록된 NotoSansKR 폰트 지정 */
body {
    font-family: 'NotoSansKR', 'DejaVu Sans', sans-serif;
}
&lt;/style&gt;
&lt;/head&gt;
&lt;body&gt;
&lt;h1&gt;한글이 정상 출력됩니다&lt;/h1&gt;
&lt;p&gt;상품명: 갤럭시 스마트폰, 가격: ₩1,200,000&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
                </div>
            </div>
        </div>

        <!-- 팁 -->
        <div class="col-12">
            <div class="card border-0 shadow-sm border-start border-4 border-warning">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="bi bi-lightbulb-fill text-warning me-2"></i>실무 팁</h6>
                    <ul class="mb-0 small text-muted">
                        <li>폰트 등록은 최초 1회 실행 후 캐시 파일(<code>*.ufm</code>)이 생성됩니다. 이후 재등록 불필요.</li>
                        <li><code>Options::setFontCache()</code>로 캐시 경로를 <code>writable/cache/dompdf/</code>로 지정하면 관리가 편합니다.</li>
                        <li>NotoSansKR 파일 크기는 약 4MB — PDF 생성 시간에 영향을 줄 수 있습니다.</li>
                        <li><code>isRemoteEnabled = true</code> + Google Fonts CDN URL은 서버 환경에 따라 느릴 수 있어 로컬 폰트를 권장합니다.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

</div><!-- /tab-content -->

<?= $this->endSection() ?>
