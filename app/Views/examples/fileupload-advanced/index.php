<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">파일 업로드 심화</li>
    </ol></nav>
    <h1><i class="bi bi-cloud-upload-fill me-2"></i>파일 업로드 심화</h1>
    <p>Drag &amp; Drop, AJAX 업로드, 진행률 표시, 이미지 미리보기 패턴을 구현합니다.</p>
</div>

<?php $tab = $tab ?? 'demo'; ?>
<ul class="nav nav-tabs mb-3" id="uploadTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'demo' ? 'active' : '' ?>" href="#" onclick="showTab('demo');return false;">데모</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- 데모 탭 -->
<div id="tab-demo" class="tab-content-pane" style="display:<?= $tab === 'demo' ? 'block' : 'none' ?>">

    <!-- Drag & Drop 영역 -->
    <div class="example-card mb-3">
        <div class="example-card-header"><h5><i class="bi bi-cloud-arrow-up me-2"></i>파일 선택 / 드래그 앤 드롭</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                허용 형식: jpg, jpeg, png, gif, webp, pdf, txt, zip &nbsp;|&nbsp; 최대 5MB / 파일
            </div>

            <!-- 드롭존 -->
            <div id="drop-zone"
                 style="border:2px dashed #dee2e6; border-radius:12px; padding:3rem 2rem;
                        text-align:center; cursor:pointer; transition:all .2s;
                        background:#fafafa;"
                 onclick="document.getElementById('file-input').click()">
                <i class="bi bi-cloud-arrow-up-fill d-block mb-2"
                   style="font-size:3rem; color:#adb5bd;"></i>
                <p class="mb-1 fw-semibold" style="color:#495057;">
                    파일을 이곳에 드래그하거나 클릭하여 선택
                </p>
                <small class="text-muted">여러 파일 동시 선택 가능</small>
                <input type="file" id="file-input" multiple style="display:none;"
                       accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.txt,.zip">
            </div>

            <!-- 선택된 파일 목록 -->
            <div id="selected-list" class="mt-3" style="display:none;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>선택된 파일</strong>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                            <i class="bi bi-x-circle me-1"></i>선택 취소
                        </button>
                        <button class="btn btn-sm" id="btn-upload-all"
                                style="background:var(--ci-red);color:#fff;"
                                onclick="uploadAll()">
                            <i class="bi bi-upload me-1"></i>모두 업로드
                        </button>
                    </div>
                </div>
                <div id="file-items"></div>
            </div>
        </div>
    </div>

    <!-- 업로드 완료 파일 목록 -->
    <div class="example-card" id="uploaded-card">
        <div class="example-card-header">
            <h5><i class="bi bi-folder2-open me-2"></i>업로드된 파일
                <small class="text-muted fw-normal ms-2" id="file-count">(<?= count($files) ?>개)</small>
            </h5>
        </div>
        <div class="example-card-body p-0" id="uploaded-list">
            <?php if (empty($files)): ?>
            <div id="empty-msg" class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                업로드된 파일이 없습니다.
            </div>
            <?php else: ?>
            <?php foreach ($files as $f): ?>
            <div class="d-flex align-items-center gap-3 px-4 py-2 border-bottom file-row"
                 data-name="<?= esc($f['name']) ?>">
                <?php if ($f['isImage']): ?>
                <img src="<?= base_url('examples/fileupload-advanced/thumb/' . $f['name']) ?>"
                     style="width:48px;height:48px;object-fit:cover;border-radius:6px;flex-shrink:0;">
                <?php else: ?>
                <div class="d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:48px;height:48px;background:#f1f3f5;border-radius:6px;">
                    <i class="bi bi-file-earmark fs-4 text-muted"></i>
                </div>
                <?php endif; ?>
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-semibold text-truncate small"><?= esc($f['name']) ?></div>
                    <small class="text-muted"><?= number_format($f['size'] / 1024, 1) ?> KB &middot; .<?= esc($f['ext']) ?></small>
                </div>
                <button class="btn btn-sm btn-outline-danger flex-shrink-0"
                        onclick="deleteFile('<?= esc($f['name']) ?>', this)">
                    <i class="bi bi-trash3"></i>
                </button>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 코드 설명 탭 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>Drag &amp; Drop 이벤트 처리</h5></div>
        <div class="example-card-body">
            <pre><code class="language-javascript">const zone = document.getElementById('drop-zone');

// 드래그 진입/이탈 시 스타일 변경
zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('drag-over'); });
zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));

// 파일 드롭 처리
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('drag-over');
    handleFiles(e.dataTransfer.files); // FileList → 처리
});</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>FileReader API — 이미지 미리보기</h5></div>
        <div class="example-card-body">
            <pre><code class="language-javascript">function previewImage(file, imgEl) {
    const reader = new FileReader();
    reader.onload = e => { imgEl.src = e.target.result; };
    reader.readAsDataURL(file); // Base64 URL로 읽기
}

// 이미지 파일 여부 확인
const isImage = file.type.startsWith('image/');</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>XHR AJAX 업로드 + 진행률</h5></div>
        <div class="example-card-body">
            <pre><code class="language-javascript">function uploadFile(file, progressEl) {
    const xhr  = new XMLHttpRequest();
    const form = new FormData();
    form.append('file', file);
    form.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>'); // CSRF

    // 진행률 이벤트
    xhr.upload.addEventListener('progress', e => {
        if (e.lengthComputable) {
            const pct = Math.round(e.loaded / e.total * 100);
            progressEl.style.width = pct + '%';
            progressEl.textContent = pct + '%';
        }
    });

    xhr.onload = () => {
        const res = JSON.parse(xhr.responseText);
        if (res.success) {
            // 업로드 완료 처리
        }
    };

    xhr.open('POST', '/examples/fileupload-advanced/upload');
    xhr.send(form);
}</code></pre>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>서버 — AJAX 업로드 처리 (JSON 응답)</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">public function upload(): Response
{
    $file = $this->request->getFile('file');

    if (! $file || ! $file->isValid()) {
        return $this->response->setStatusCode(400)
            ->setJSON(['error' => $file->getErrorString()]);
    }

    // 크기·확장자 검증
    if ($file->getSize() > 5 * 1024 * 1024) {
        return $this->response->setStatusCode(400)
            ->setJSON(['error' => '5MB 초과']);
    }

    $newName = $file->getRandomName();
    $file->move(WRITEPATH . 'uploads/advanced/', $newName);

    return $this->response->setJSON([
        'success'  => true,
        'name'     => $newName,
        'original' => $file->getClientName(),
        'isImage'  => str_starts_with($file->getMimeType(), 'image/'),
    ]);
}</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<style>
#drop-zone.drag-over {
    border-color: var(--ci-red) !important;
    background: #fff3ef !important;
}
#drop-zone.drag-over i { color: var(--ci-red) !important; }
.file-item { border:1px solid #e9ecef; border-radius:8px; padding:.75rem 1rem; margin-bottom:.5rem; background:#fff; }
.progress-bar-track { height:6px; background:#e9ecef; border-radius:4px; overflow:hidden; margin-top:.4rem; }
.progress-bar-fill  { height:100%; background:var(--ci-red); border-radius:4px; width:0; transition:width .1s; font-size:0; }
</style>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#uploadTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}

// ── 선택된 파일 목록 ──────────────────────────────────────
let selectedFiles = [];

const dropZone  = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');

dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    addFiles(e.dataTransfer.files);
});
fileInput.addEventListener('change', () => addFiles(fileInput.files));

function addFiles(fileList) {
    Array.from(fileList).forEach(f => {
        if (!selectedFiles.find(s => s.name === f.name && s.size === f.size)) {
            selectedFiles.push(f);
        }
    });
    renderSelected();
    fileInput.value = '';
}

function renderSelected() {
    const list = document.getElementById('file-items');
    const wrap = document.getElementById('selected-list');
    if (!selectedFiles.length) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'block';
    list.innerHTML = '';
    selectedFiles.forEach((f, i) => {
        const isImg = f.type.startsWith('image/');
        const div = document.createElement('div');
        div.className = 'file-item d-flex align-items-center gap-3';
        div.id = 'sel-' + i;
        div.innerHTML = `
            <div class="flex-shrink-0" style="width:48px;height:48px;">
                ${isImg
                    ? `<img id="prev-${i}" style="width:48px;height:48px;object-fit:cover;border-radius:6px;">`
                    : `<div class="d-flex align-items-center justify-content-center w-100 h-100" style="background:#f1f3f5;border-radius:6px;"><i class="bi bi-file-earmark fs-4 text-muted"></i></div>`}
            </div>
            <div class="flex-grow-1 min-w-0">
                <div class="fw-semibold text-truncate small">${esc(f.name)}</div>
                <small class="text-muted">${(f.size/1024).toFixed(1)} KB</small>
                <div class="progress-bar-track" id="track-${i}" style="display:none;">
                    <div class="progress-bar-fill" id="bar-${i}"></div>
                </div>
                <small id="status-${i}" class="text-muted"></small>
            </div>
            <button class="btn btn-sm btn-outline-secondary flex-shrink-0" onclick="removeSelected(${i})">
                <i class="bi bi-x"></i>
            </button>`;
        list.appendChild(div);
        if (isImg) {
            const reader = new FileReader();
            reader.onload = e => { document.getElementById('prev-' + i).src = e.target.result; };
            reader.readAsDataURL(f);
        }
    });
}

function esc(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function removeSelected(i) {
    selectedFiles.splice(i, 1);
    renderSelected();
}

function clearSelection() {
    selectedFiles = [];
    renderSelected();
}

// ── AJAX 업로드 ──────────────────────────────────────────
let csrfToken = '<?= csrf_token() ?>';
let csrfHash  = '<?= csrf_hash() ?>';

function uploadAll() {
    if (!selectedFiles.length) return;
    document.getElementById('btn-upload-all').disabled = true;
    const promises = selectedFiles.map((f, i) => uploadOne(f, i));
    Promise.all(promises).then(() => {
        document.getElementById('btn-upload-all').disabled = false;
        selectedFiles = [];
        renderSelected();
        refreshList();
    });
}

function uploadOne(file, idx) {
    return new Promise(resolve => {
        const track  = document.getElementById('track-' + idx);
        const bar    = document.getElementById('bar-' + idx);
        const status = document.getElementById('status-' + idx);

        track.style.display = 'block';
        status.textContent  = '업로드 중...';

        const xhr  = new XMLHttpRequest();
        const form = new FormData();
        form.append('file', file);
        form.append(csrfToken, csrfHash);

        xhr.upload.addEventListener('progress', e => {
            if (e.lengthComputable) {
                bar.style.width = Math.round(e.loaded / e.total * 100) + '%';
            }
        });

        xhr.onload = () => {
            try {
                const res = JSON.parse(xhr.responseText);
                if (xhr.status === 200 && res.success) {
                    bar.style.background = '#198754';
                    bar.style.width = '100%';
                    status.innerHTML = '<span style="color:#198754;">완료</span>';
                    csrfHash = res.csrf_hash ?? csrfHash;
                } else {
                    bar.style.background = '#dc3545';
                    status.innerHTML = `<span style="color:#dc3545;">${esc(res.error ?? '오류')}</span>`;
                }
            } catch(e) {
                status.innerHTML = '<span style="color:#dc3545;">응답 오류</span>';
            }
            resolve();
        };

        xhr.onerror = () => {
            status.innerHTML = '<span style="color:#dc3545;">네트워크 오류</span>';
            resolve();
        };

        xhr.open('POST', '<?= base_url('examples/fileupload-advanced/upload') ?>');
        xhr.send(form);
    });
}

// ── 파일 목록 갱신 ──────────────────────────────────────
function refreshList() {
    fetch('<?= base_url('examples/fileupload-advanced/list') ?>')
        .then(r => r.json())
        .then(files => {
            const ul   = document.getElementById('uploaded-list');
            const cnt  = document.getElementById('file-count');
            cnt.textContent = '(' + files.length + '개)';

            if (!files.length) {
                ul.innerHTML = `<div id="empty-msg" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>업로드된 파일이 없습니다.</div>`;
                return;
            }

            ul.innerHTML = files.map(f => `
            <div class="d-flex align-items-center gap-3 px-4 py-2 border-bottom file-row" data-name="${esc(f.name)}">
                ${f.isImage
                    ? `<img src="<?= base_url('examples/fileupload-advanced/thumb/') ?>${f.name}"
                           style="width:48px;height:48px;object-fit:cover;border-radius:6px;flex-shrink:0;">`
                    : `<div class="d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:48px;height:48px;background:#f1f3f5;border-radius:6px;">
                           <i class="bi bi-file-earmark fs-4 text-muted"></i></div>`}
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-semibold text-truncate small">${esc(f.name)}</div>
                    <small class="text-muted">${(f.size/1024).toFixed(1)} KB &middot; .${esc(f.ext)}</small>
                </div>
                <button class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="deleteFile('${esc(f.name)}', this)">
                    <i class="bi bi-trash3"></i>
                </button>
            </div>`).join('');
        });
}

// ── 파일 삭제 ────────────────────────────────────────────
function deleteFile(name, btn) {
    if (!confirm(`'${name}'을(를) 삭제하시겠습니까?`)) return;
    btn.disabled = true;

    const form = new FormData();
    form.append(csrfToken, csrfHash);

    fetch('<?= base_url('examples/fileupload-advanced/delete/') ?>' + encodeURIComponent(name), {
        method: 'POST', body: form
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            btn.closest('.file-row, .d-flex').remove();
            const cnt = document.getElementById('file-count');
            const cur = parseInt(cnt.textContent.replace(/\D/g,'')) - 1;
            cnt.textContent = '(' + cur + '개)';
            csrfHash = res.csrf_hash ?? csrfHash;
            if (cur === 0) refreshList();
        }
    })
    .catch(() => { btn.disabled = false; });
}
</script>
<?= $this->endSection() ?>
