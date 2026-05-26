<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">이미지 처리</li>
    </ol></nav>
    <h1><i class="bi bi-image me-2"></i>이미지 처리</h1>
    <p>CodeIgniter Image Service로 리사이즈, 크롭(fit), 회전, 워터마크를 처리합니다.</p>
</div>

<ul class="nav nav-tabs mb-3" id="ipTab">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="code">코드 설명</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="methods">지원 메서드</a></li>
</ul>

<!-- 라이브 데모 -->
<div id="tab-demo" class="tab-content-pane">
    <div class="example-card">
        <div class="example-card-header">
            <h5><i class="bi bi-upload me-2"></i>이미지 업로드 & 처리</h5>
        </div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                허용 확장자: <code>jpg, jpeg, png, gif, webp</code> &nbsp;|&nbsp; 최대 크기: <code>5MB</code><br>
                업로드 시 자동으로 <strong>썸네일(150x150 crop)</strong>과 <strong>리사이즈(가로 800px)</strong> 버전을 생성합니다.
            </div>
            <form id="upload-form">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                <div class="mb-3">
                    <label class="form-label fw-bold">이미지 선택</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-magic"></i> 업로드 & 처리
                </button>
            </form>

            <div id="result-area" class="mt-4" style="display:none;">
                <div class="result-box mb-3" id="result-message"></div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>구분</th>
                                <th>파일명</th>
                                <th>크기 (bytes)</th>
                                <th>해상도</th>
                                <th>미리보기</th>
                            </tr>
                        </thead>
                        <tbody id="result-rows"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if (! empty($files)): ?>
    <div class="example-card mt-3">
        <div class="example-card-header">
            <h5><i class="bi bi-images me-2"></i>최근 처리 이미지 <span class="badge bg-secondary"><?= count($files) ?></span></h5>
        </div>
        <div class="example-card-body">
            <div class="row g-3">
                <?php foreach ($files as $f): ?>
                <div class="col-md-2 col-sm-3 col-4">
                    <div class="border rounded p-2 text-center">
                        <div style="height:80px;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#f8f9fa;">
                            <img src="<?= base_url('examples/imageprocess/file/' . esc($f['name'])) ?>" style="max-width:100%;max-height:80px;" onerror="this.style.display='none'">
                        </div>
                        <small class="d-block text-truncate mt-1" title="<?= esc($f['name']) ?>"><?= esc(substr($f['name'], 0, 14)) ?></small>
                        <small class="text-muted"><?= number_format($f['size'] / 1024, 1) ?> KB</small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <p class="text-muted mt-3 mb-0"><small>저장 경로: <code>writable/uploads/images/</code></small></p>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>이미지 리사이즈</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// CodeIgniter Image Service 호출
$image = \Config\Services::image();

// 리사이즈: 비율 유지, 가로 800px 기준
$image->withFile($sourcePath)
      ->resize(800, 600, true, 'width')  // (가로, 세로, 비율유지, 기준축)
      ->save($destPath);

// 가로/세로 중 더 큰쪽 기준으로 리사이즈
$image->withFile($source)
      ->resize(800, 600, true, 'auto')
      ->save($dest);</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>썸네일 크롭 (fit)</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 정사각형 썸네일: 중앙 기준 크롭
$image->withFile($sourcePath)
      ->fit(150, 150, 'center')   // (가로, 세로, 위치)
      ->save($thumbPath);

// 위치 옵션: top-left, top, top-right, left,
//          center, right, bottom-left, bottom, bottom-right</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>회전 / 좌우 반전 / 포맷 변환</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 회전 (90, 180, 270만 지원)
$image->withFile($src)->rotate(90)->save($dest);

// 좌우 반전
$image->withFile($src)->flip('horizontal')->save($dest);

// JPEG → PNG 변환 (확장자만 바꾸면 자동 변환)
$image->withFile($src)->convert(IMAGETYPE_PNG)->save($destPng);

// 품질 지정 후 저장
$image->withFile($src)
      ->resize(800, 600, true, 'width')
      ->save($dest, 80); // JPEG 품질 80</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">4</span><h5>워터마크 (텍스트)</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">$image->withFile($src)
      ->text('© CI4 Playground', [
          'color'      => 'ffffff',
          'opacity'    => 50,
          'withShadow' => true,
          'hAlign'     => 'right',
          'vAlign'     => 'bottom',
          'fontSize'   => 20,
          'padding'    => 20,
      ])
      ->save($watermarkedPath);</code></pre>
        </div>
    </div>
</div>

<!-- 지원 메서드 -->
<div id="tab-methods" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-list-check me-2"></i>주요 메서드</h5></div>
        <div class="example-card-body">
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr><th>메서드</th><th>설명</th><th>예시</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>withFile()</code></td><td>처리할 이미지 파일을 지정</td><td><code>withFile('/path/img.jpg')</code></td></tr>
                    <tr><td><code>resize()</code></td><td>리사이즈 (비율 유지/무시 옵션)</td><td><code>resize(800, 600, true, 'width')</code></td></tr>
                    <tr><td><code>fit()</code></td><td>지정 크기에 맞춰 크롭 (썸네일용)</td><td><code>fit(150, 150, 'center')</code></td></tr>
                    <tr><td><code>crop()</code></td><td>좌표 기반 영역 크롭</td><td><code>crop(100, 100, 50, 50)</code></td></tr>
                    <tr><td><code>rotate()</code></td><td>이미지 회전 (90/180/270만 가능)</td><td><code>rotate(90)</code></td></tr>
                    <tr><td><code>flip()</code></td><td>이미지 반전</td><td><code>flip('horizontal')</code></td></tr>
                    <tr><td><code>convert()</code></td><td>다른 포맷으로 변환</td><td><code>convert(IMAGETYPE_PNG)</code></td></tr>
                    <tr><td><code>text()</code></td><td>텍스트 워터마크</td><td><code>text('© 2026', [...])</code></td></tr>
                    <tr><td><code>save()</code></td><td>처리 결과 저장 (품질 옵션)</td><td><code>save($path, 90)</code></td></tr>
                    <tr><td><code>getFile()</code></td><td>처리된 Image 객체 반환</td><td><code>getFile()</code></td></tr>
                </tbody>
            </table>
            <div class="result-box warning mt-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>드라이버 안내:</strong> 기본 드라이버는 <code>GD</code>입니다. ImageMagick 사용 시
                <code>app/Config/Images.php</code>의 <code>$defaultHandler</code>를 <code>'imagick'</code>로 변경하세요.
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('#ipTab .nav-link').forEach(el => {
    el.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('#ipTab .nav-link').forEach(n => n.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.tab-content-pane').forEach(p => p.style.display = 'none');
        document.getElementById('tab-' + el.dataset.tab).style.display = 'block';
    });
});

document.getElementById('upload-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const res = await fetch('<?= base_url('examples/imageprocess/upload') ?>', {
        method: 'POST',
        body: formData,
    });
    const data = await res.json();

    const area    = document.getElementById('result-area');
    const message = document.getElementById('result-message');
    const rows    = document.getElementById('result-rows');
    area.style.display = 'block';

    if (! data.success) {
        message.className = 'result-box danger mb-3';
        message.innerHTML = '<i class="bi bi-x-circle me-2"></i>' + data.message;
        rows.innerHTML = '';
        return;
    }
    message.className = 'result-box mb-3';
    message.innerHTML = '<i class="bi bi-check-circle me-2"></i>' + data.message + ' (처리 시간: ' + data.elapsed_ms + ' ms)';

    const fmt = (b) => (b / 1024).toFixed(1) + ' KB';
    const baseUrl = '<?= base_url('writable-files/uploads/images/') ?>';
    rows.innerHTML = `
        <tr><td><span class="badge bg-secondary">원본</span></td><td><code>${data.original.name}</code></td><td>${fmt(data.original.size)}</td><td>${data.original.width} × ${data.original.height}</td><td><span class="badge bg-light text-dark">저장됨</span></td></tr>
        <tr><td><span class="badge bg-primary">썸네일</span></td><td><code>${data.thumb.name}</code></td><td>${fmt(data.thumb.size)}</td><td>${data.thumb.width} × ${data.thumb.height}</td><td><span class="badge bg-light text-dark">crop(center)</span></td></tr>
        <tr><td><span class="badge bg-success">리사이즈</span></td><td><code>${data.resized.name}</code></td><td>${fmt(data.resized.size)}</td><td>${data.resized.width} × ${data.resized.height}</td><td><span class="badge bg-light text-dark">width 800</span></td></tr>
    `;
});
</script>
<?= $this->endSection() ?>
