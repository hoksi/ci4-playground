<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">TinyMCE 에디터</li>
    </ol></nav>
    <h1><i class="bi bi-pencil-square me-2"></i>TinyMCE 에디터</h1>
    <p>CI4와 TinyMCE 리치 텍스트 에디터를 연동하여 HTML 콘텐츠를 편집·저장·출력합니다.</p>
</div>

<?php $tab = $tab ?? 'demo'; ?>
<ul class="nav nav-tabs mb-3" id="tinyTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'demo' ? 'active' : '' ?>" href="#" onclick="showTab('demo');return false;">에디터 데모</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- 에디터 데모 탭 -->
<div id="tab-demo" class="tab-content-pane" style="display:<?= $tab === 'demo' ? 'block' : 'none' ?>">

    <?php if ($saved): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>콘텐츠가 저장되었습니다.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="result-box info mb-3">
        <i class="bi bi-info-circle me-2"></i>
        TinyMCE CDN을 사용합니다. API 키 없이도 동작하지만 알림이 표시될 수 있습니다.
        <a href="https://www.tiny.cloud/auth/signup/" target="_blank" class="ms-1">무료 API 키 발급</a> 후
        <code>.env</code>에 <code>TINYMCE_API_KEY=your_key</code>를 설정하세요.
    </div>

    <!-- 에디터 카드 -->
    <div class="example-card mb-3">
        <div class="example-card-header"><h5><i class="bi bi-type me-2"></i>리치 텍스트 편집</h5></div>
        <div class="example-card-body">
            <form method="post" action="<?= base_url('examples/tinymce/save') ?>" id="editor-form">
                <?= csrf_field() ?>
                <textarea id="content" name="content"><?= esc($content) ?></textarea>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                        <i class="bi bi-floppy me-1"></i>저장
                    </button>
                    <button type="button" class="demo-btn outline" onclick="resetContent()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>초기화
                    </button>
                    <button type="button" class="demo-btn outline ms-auto" onclick="togglePreview()">
                        <i class="bi bi-eye me-1"></i>HTML 미리보기
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 저장된 콘텐츠 출력 -->
    <div class="example-card mb-3">
        <div class="example-card-header"><h5><i class="bi bi-file-earmark-richtext me-2"></i>저장된 콘텐츠 출력</h5></div>
        <div class="example-card-body">
            <div class="p-3 border rounded" style="min-height:100px; background:#fff;">
                <?= $content ?>
            </div>
        </div>
    </div>

    <!-- HTML 소스 보기 (토글) -->
    <div class="example-card" id="html-preview-card" style="display:none;">
        <div class="example-card-header"><h5><i class="bi bi-code-slash me-2"></i>HTML 소스</h5></div>
        <div class="example-card-body">
            <pre id="html-source" style="background:#0d1117; color:#e6e6e6; border-radius:8px; padding:1rem; font-size:.8rem; overflow:auto; max-height:300px; white-space:pre-wrap;"></pre>
        </div>
    </div>
</div>

<!-- 코드 설명 탭 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>TinyMCE 초기화 (기본 설정)</h5></div>
        <div class="example-card-body">
            <pre><code class="language-javascript">// CDN 로드 (API 키 있으면 no-api-key → 발급받은 키로 교체)
// &lt;script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js"&gt;&lt;/script&gt;

tinymce.init({
    selector: '#content',        // textarea id
    language: 'ko_KR',           // 한국어 UI (langpack 필요)
    height: 450,
    menubar: false,
    plugins: [
        'anchor', 'autolink', 'image', 'link',
        'lists', 'searchreplace', 'table', 'wordcount', 'codesample',
    ],
    toolbar: 'undo redo | blocks | bold italic underline | '
           + 'forecolor backcolor | alignleft aligncenter alignright | '
           + 'bullist numlist | table link image | codesample | removeformat',
    images_upload_url: '/examples/tinymce/upload',  // 이미지 업로드 엔드포인트
    automatic_uploads: true,
    file_picker_types: 'image',
});</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>폼 저장 — CI4 Controller</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 뷰: textarea에 TinyMCE 연결
// &lt;textarea id="content" name="content"&gt;&lt;/textarea&gt;

public function save(): RedirectResponse
{
    $content = $this->request->getPost('content') ?? '';

    // 실제 서비스: HTMLPurifier 등으로 XSS 필터링 필요
    // $content = $purifier->purify($content);

    // DB 저장 예시
    // $this->articleModel->save(['content' => $content]);

    // 여기서는 세션에 임시 저장
    session()->setFlashdata('tinymce_content', $content);
    return redirect()->to(base_url('examples/tinymce'));
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>이미지 업로드 엔드포인트</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">public function upload(): Response
{
    $file = $this->request->getFile('file');

    if (! $file->isValid()) {
        return $this->response->setStatusCode(400)
            ->setJSON(['error' => ['message' => '업로드 실패']]);
    }

    $newName = $file->getRandomName();
    $file->move(WRITEPATH . 'uploads/tinymce/', $newName);

    // TinyMCE가 기대하는 응답 형식: { "location": "이미지URL" }
    return $this->response->setJSON([
        'location' => base_url('examples/tinymce/image/' . $newName),
    ]);
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>저장된 HTML 출력 시 XSS 주의</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// TinyMCE가 생성한 HTML을 그대로 출력할 때는
// esc() 를 사용하지 않습니다 — esc()는 HTML 태그를 이스케이프합니다.
// 단, 저장 전 반드시 서버 측 HTML 필터링 필요.

// 권장: HTMLPurifier 또는 league/html-to-markdown
// composer require ezyang/htmlpurifier

use HTMLPurifier;
use HTMLPurifier_Config;

$config   = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$clean    = $purifier->purify($rawHtml);

// 뷰에서 필터링된 HTML 출력
echo $clean;  // esc() 없이 출력 (이미 정제됨)</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">5</span><h5>API 키 설정 (.env)</h5></div>
        <div class="example-card-body">
            <pre><code class="language-ini"># .env
TINYMCE_API_KEY = your_api_key_here</code></pre>
            <pre><code class="language-php">// Controller 또는 View에서 읽기
$apiKey = env('TINYMCE_API_KEY', 'no-api-key');</code></pre>
            <pre><code class="language-html">&lt;!-- 뷰에서 사용 --&gt;
&lt;script src="https://cdn.tiny.cloud/1/&lt;?= env('TINYMCE_API_KEY','no-api-key') ?&gt;/tinymce/7/tinymce.min.js"&gt;&lt;/script&gt;</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="https://cdn.tiny.cloud/1/<?= env('TINYMCE_API_KEY', 'no-api-key') ?>/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#tinyTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}

const UPLOAD_URL  = '<?= base_url('examples/tinymce/upload') ?>';
const CSRF_TOKEN  = '<?= csrf_token() ?>';
const CSRF_HASH   = '<?= csrf_hash() ?>';
const DEFAULT_HTML = <?= json_encode($content) ?>;

tinymce.init({
    selector: '#content',
    height: 450,
    menubar: false,
    plugins: ['anchor', 'autolink', 'image', 'link', 'lists',
              'searchreplace', 'table', 'wordcount', 'codesample'],
    toolbar: 'undo redo | blocks | bold italic underline strikethrough | '
           + 'forecolor backcolor | alignleft aligncenter alignright alignjustify | '
           + 'bullist numlist outdent indent | table link image codesample | removeformat',
    images_upload_url: UPLOAD_URL,
    automatic_uploads: true,
    file_picker_types: 'image',
    images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
        const xhr  = new XMLHttpRequest();
        const form = new FormData();
        form.append('file', blobInfo.blob(), blobInfo.filename());
        form.append(CSRF_TOKEN, CSRF_HASH);

        xhr.upload.onprogress = e => {
            if (e.lengthComputable) progress(Math.round(e.loaded / e.total * 100));
        };
        xhr.onload = () => {
            if (xhr.status !== 200) {
                try { reject({ message: JSON.parse(xhr.responseText).error?.message ?? '업로드 실패' }); }
                catch(_) { reject({ message: '업로드 실패' }); }
                return;
            }
            const res = JSON.parse(xhr.responseText);
            resolve(res.location);
        };
        xhr.onerror = () => reject({ message: '네트워크 오류' });
        xhr.open('POST', UPLOAD_URL);
        xhr.send(form);
    }),
    setup: editor => {
        editor.on('submit', () => {
            document.querySelector('[name="content"]').value = editor.getContent();
        });
    },
    content_style: 'body { font-family: "Segoe UI", sans-serif; font-size: 15px; line-height: 1.6; }',
});

// 폼 제출 시 에디터 내용을 textarea에 동기화
document.getElementById('editor-form').addEventListener('submit', () => {
    const content = tinymce.get('content');
    if (content) {
        document.getElementById('content').value = content.getContent();
    }
});

function resetContent() {
    if (confirm('내용을 초기화하시겠습니까?')) {
        tinymce.get('content')?.setContent(DEFAULT_HTML);
    }
}

function togglePreview() {
    const card = document.getElementById('html-preview-card');
    const pre  = document.getElementById('html-source');
    if (card.style.display === 'none') {
        const content = tinymce.get('content')?.getContent() ?? '';
        pre.textContent = content;
        card.style.display = 'block';
        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        card.style.display = 'none';
    }
}
</script>
<?= $this->endSection() ?>
