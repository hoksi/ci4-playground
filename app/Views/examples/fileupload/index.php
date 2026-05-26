<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">파일 업로드</li>
    </ol></nav>
    <h1><i class="bi bi-cloud-upload me-2"></i>파일 업로드</h1>
    <p>CI4의 파일 업로드 처리, 유효성 검사, 다중 업로드를 알아봅니다.</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-circle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- 탭 -->
<?php $tab = session()->getFlashdata('tab') ?? 'single'; ?>
<ul class="nav nav-tabs mb-3" id="uploadTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'single' ? 'active' : '' ?>" href="#" onclick="showTab('single');return false;">단일 파일 업로드</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'multi' ? 'active' : '' ?>" href="#" onclick="showTab('multi');return false;">다중 파일 업로드</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- 단일 업로드 -->
<div id="tab-single" class="tab-content-pane" style="display:<?= $tab === 'single' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-file-earmark-arrow-up me-2"></i>단일 파일 업로드</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                허용 확장자: <code>jpg, jpeg, png, gif, pdf, txt, zip</code> &nbsp;|&nbsp; 최대 크기: <code>2MB</code>
            </div>
            <form method="post" action="<?= base_url('examples/fileupload/upload') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">파일 선택</label>
                    <input type="file" name="userfile" class="form-control" accept=".jpg,.jpeg,.png,.gif,.pdf,.txt,.zip">
                </div>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-upload"></i> 업로드
                </button>
            </form>
        </div>
    </div>
</div>

<!-- 다중 업로드 -->
<div id="tab-multi" class="tab-content-pane" style="display:<?= $tab === 'multi' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-files me-2"></i>다중 파일 업로드</h5></div>
        <div class="example-card-body">
            <form method="post" action="<?= base_url('examples/fileupload/multi') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">파일 여러 개 선택</label>
                    <input type="file" name="multifiles[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.txt,.zip">
                    <div class="form-text">Ctrl 또는 Shift 키로 여러 파일 선택</div>
                </div>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-upload"></i> 다중 업로드
                </button>
            </form>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>단일 파일 업로드</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 폼: enctype="multipart/form-data" 필수
// &lt;input type="file" name="userfile"&gt;

public function upload()
{
    $file  = $this->request->getFile('userfile');
    $rules = [
        'userfile' => [
            'rules' => [
                'uploaded[userfile]',          // 파일이 업로드되었는지
                'max_size[userfile,2048]',     // 최대 2MB
                'ext_in[userfile,jpg,png,pdf]', // 허용 확장자
            ],
        ],
    ];

    if (! $this->validate($rules)) {
        return redirect()->back()->with('error', $this->validator->getError('userfile'));
    }

    $newName = $file->getRandomName();     // 랜덤 파일명 생성
    $file->move(WRITEPATH . 'uploads/', $newName);

    // 파일 정보 접근
    $file->getClientName();       // 원본 파일명
    $file->getClientExtension();  // 확장자
    $file->getSize();             // 바이트 크기
    $file->getMimeType();         // MIME 타입
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>다중 파일 업로드</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 폼: &lt;input type="file" name="multifiles[]" multiple&gt;

public function multi()
{
    // getFileMultiple()로 배열로 받기
    $files = $this->request->getFileMultiple('multifiles');

    foreach ($files as $file) {
        if (! $file->isValid()) continue;

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/', $newName);
    }
}</code></pre>
        </div>
    </div>
</div>

<!-- 업로드된 파일 목록 -->
<div class="example-card mt-3">
    <div class="example-card-header">
        <h5><i class="bi bi-folder2-open me-2"></i>업로드된 파일 목록 <span class="badge bg-secondary"><?= count($files) ?></span></h5>
    </div>
    <div class="example-card-body">
        <?php if (empty($files)): ?>
            <p class="text-muted mb-0">업로드된 파일이 없습니다.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-dark">
                        <tr><th>파일명</th><th>크기</th><th>확장자</th><th>업로드 시각</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($files as $f): ?>
                        <tr>
                            <td><i class="bi bi-file-earmark me-1"></i><?= esc($f['name']) ?></td>
                            <td><?= number_format($f['size'] / 1024, 1) ?> KB</td>
                            <td><span class="badge bg-secondary"><?= esc($f['ext']) ?></span></td>
                            <td><small><?= date('Y-m-d H:i', $f['time']) ?></small></td>
                            <td>
                                <a href="<?= base_url('examples/fileupload/delete/' . esc($f['name'])) ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('삭제하시겠습니까?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#uploadTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
<?= $this->endSection() ?>
