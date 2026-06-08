<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">스팸 키워드 관리</li>
    </ol></nav>
    <h1><i class="bi bi-shield-exclamation me-2"></i>스팸 키워드 관리</h1>
    <p>AI가 학습한 스팸 키워드를 관리하고, 새로운 게시글을 스팸 테스트합니다.</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- 통계 -->
<?php
$total    = count($keywords);
$active   = count(array_filter($keywords, fn($k) => $k['active']));
$inactive = $total - $active;
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="example-card text-center p-3">
            <div style="font-size:2rem;font-weight:700;color:#dc3545;"><?= $total ?></div>
            <div class="text-muted">학습된 키워드</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="example-card text-center p-3">
            <div style="font-size:2rem;font-weight:700;color:#198754;"><?= $active ?></div>
            <div class="text-muted">활성 키워드</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="example-card text-center p-3">
            <div style="font-size:2rem;font-weight:700;color:#6c757d;"><?= count($builtinKeywords) ?></div>
            <div class="text-muted">기본 키워드</div>
        </div>
    </div>
</div>

<!-- 스팸 테스트 패널 -->
<div class="example-card mb-4">
    <div class="example-card-header">
        <span class="badge bg-primary">테스트</span>
        <h5>스팸 감지 테스트</h5>
    </div>
    <div class="example-card-body">
        <div class="row g-3">
            <div class="col-md-5">
                <input type="text" id="testTitle" class="form-control mb-2" placeholder="제목 입력">
                <textarea id="testContent" class="form-control" rows="4" placeholder="내용 입력"></textarea>
            </div>
            <div class="col-md-2 d-flex align-items-center justify-content-center">
                <button id="testBtn" class="demo-btn" style="border:none;cursor:pointer;width:100%;">
                    <i class="bi bi-shield-check"></i><br>검사
                </button>
            </div>
            <div class="col-md-5">
                <div id="testResult" class="result-box h-100 d-flex align-items-center justify-content-center text-muted">
                    결과가 여기에 표시됩니다.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 키워드 추가 폼 -->
<div class="example-card mb-4">
    <div class="example-card-header">
        <span class="badge bg-warning text-dark">수동 추가</span>
        <h5>키워드 직접 추가</h5>
    </div>
    <div class="example-card-body">
        <form method="post" action="<?= base_url('examples/spam-admin/store') ?>" class="d-flex gap-2">
            <?= csrf_field() ?>
            <input type="text" name="keyword" class="form-control" placeholder="추가할 스팸 키워드 입력" style="max-width:400px;">
            <button type="submit" class="demo-btn" style="border:none;cursor:pointer;white-space:nowrap;">
                <i class="bi bi-plus-circle"></i> 추가
            </button>
        </form>
    </div>
</div>

<!-- 학습된 키워드 목록 -->
<div class="example-card mb-4">
    <div class="example-card-header">
        <span class="badge bg-danger">AI 학습</span>
        <h5>학습된 스팸 키워드 (<?= $total ?>개)</h5>
    </div>
    <div class="example-card-body p-0">
        <?php if (empty($keywords)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-robot" style="font-size:3rem;opacity:.3;"></i>
            <p class="mt-2">아직 AI가 학습한 키워드가 없습니다.<br>스팸 게시글이 AI에 의해 감지되면 자동으로 추가됩니다.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>키워드</th>
                        <th width="100" class="text-center">감지 횟수</th>
                        <th width="100" class="text-center">상태</th>
                        <th width="120" class="text-center">관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($keywords as $kw): ?>
                    <tr class="<?= $kw['active'] ? '' : 'table-secondary' ?>">
                        <td>
                            <span class="badge <?= $kw['active'] ? 'bg-danger' : 'bg-secondary' ?> me-2">
                                <?= esc($kw['keyword']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-dark"><?= esc($kw['frequency']) ?>회</span>
                        </td>
                        <td class="text-center">
                            <?php if ($kw['active']): ?>
                                <span class="badge bg-success">활성</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">비활성</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="<?= base_url("examples/spam-admin/{$kw['id']}/toggle") ?>"
                               class="btn btn-sm <?= $kw['active'] ? 'btn-outline-warning' : 'btn-outline-success' ?> me-1"
                               title="<?= $kw['active'] ? '비활성화' : '활성화' ?>">
                                <i class="bi bi-<?= $kw['active'] ? 'pause' : 'play' ?>-fill"></i>
                            </a>
                            <a href="<?= base_url("examples/spam-admin/{$kw['id']}/delete") ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('<?= esc($kw['keyword']) ?> 키워드를 삭제할까요?')"
                               title="삭제">
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

<!-- 기본 내장 키워드 (읽기 전용) -->
<div class="example-card mb-4">
    <div class="example-card-header">
        <span class="badge bg-secondary">내장</span>
        <h5>기본 내장 키워드 (<?= count($builtinKeywords) ?>개, 읽기 전용)</h5>
    </div>
    <div class="example-card-body">
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($builtinKeywords as $kw): ?>
                <span class="badge bg-secondary"><?= esc($kw) ?></span>
            <?php endforeach; ?>
        </div>
        <div class="text-muted small mt-2">
            <i class="bi bi-info-circle me-1"></i>
            기본 키워드는 <code>app/Services/SpamChecker.php</code>의 <code>BUILTIN_KEYWORDS</code> 상수에서 관리합니다.
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div class="example-card mt-4">
    <div class="example-card-header"><span class="badge bg-dark">소스</span><h5>스팸 감지 흐름</h5></div>
    <div class="example-card-body">
        <pre><code class="language-php">// Board::store() — 게시글 등록 시 동기 스팸 검사
$spam = (new SpamChecker())->check($title, $content, $ip);

if ($spam['status'] === 'spam') {
    return redirect()->back()->with('error', '스팸 감지');
}
$this->model->insert([..., 'spam_status' => $spam['status']]);

// SpamChecker 흐름
// 1단계: 규칙 기반 (내장 키워드 + DB 학습 키워드)
//   점수 70+ → spam (즉시 차단)
//   점수 30↓  → approved (즉시 허용)
//   점수 31~69 → Groq AI 호출
// 2단계: Groq AI
//   is_spam: true  → spam + 키워드 DB 저장
//   is_spam: false → approved
//   오류           → review (관리자 검토)</code></pre>
    </div>
</div>

<script>
document.getElementById('testBtn').addEventListener('click', async () => {
    const title   = document.getElementById('testTitle').value.trim();
    const content = document.getElementById('testContent').value.trim();
    const result  = document.getElementById('testResult');

    if (!title || !content) {
        result.innerHTML = '<span class="text-warning">제목과 내용을 모두 입력해주세요.</span>';
        return;
    }

    result.innerHTML = '<span class="text-muted"><i class="bi bi-hourglass-split me-1"></i>검사 중...</span>';

    try {
        const fd = new FormData();
        fd.append('title',   title);
        fd.append('content', content);
        fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        const res  = await fetch('<?= base_url('examples/spam-admin/test') ?>', { method: 'POST', body: fd });
        const json = await res.json();

        const statusMap = {
            spam:     { cls: 'danger',  icon: 'shield-x',        text: '스팸 감지' },
            review:   { cls: 'warning', icon: 'shield-exclamation', text: '검토 필요' },
            approved: { cls: 'success', icon: 'shield-check',    text: '정상' },
        };
        const s = statusMap[json.status] ?? statusMap.review;

        let kwHtml = '';
        if (json.keywords && json.keywords.length > 0) {
            kwHtml = '<div class="mt-2">' +
                json.keywords.map(k => `<span class="badge bg-danger me-1">${k}</span>`).join('') +
                '</div>';
        }

        result.innerHTML = `
            <div class="text-center w-100">
                <div class="text-${s.cls}" style="font-size:2rem;"><i class="bi bi-${s.icon}"></i></div>
                <div class="fw-bold text-${s.cls}">${s.text}</div>
                <div class="text-muted small mt-1">점수: ${json.score ?? '-'} / 100</div>
                <div class="text-muted small">${json.reason ?? ''}</div>
                ${kwHtml}
            </div>`;
    } catch (e) {
        result.innerHTML = '<span class="text-danger">요청 실패: ' + e.message + '</span>';
    }
});
</script>

<?= $this->endSection() ?>
