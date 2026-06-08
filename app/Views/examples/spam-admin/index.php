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
$builtin  = count(array_filter($keywords, fn($k) => $k['is_builtin']));
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
            <div style="font-size:2rem;font-weight:700;color:#6c757d;"><?= $builtin ?></div>
            <div class="text-muted">기본 키워드</div>
        </div>
    </div>
</div>

<!-- 검토 대기 게시글 -->
<?php $reviewCount = count($reviewPosts); ?>
<div class="example-card mb-4">
    <div class="example-card-header">
        <span class="badge bg-warning text-dark">검토 대기</span>
        <h5>
            검토 대기 게시글
            <?php if ($reviewCount > 0): ?>
                <span class="badge bg-danger ms-1"><?= $reviewCount ?></span>
            <?php endif; ?>
        </h5>
    </div>
    <div class="example-card-body p-0">
        <?php if (empty($reviewPosts)): ?>
        <div class="text-center py-4 text-muted">
            <i class="bi bi-check-circle" style="font-size:2rem;opacity:.3;"></i>
            <p class="mt-2 mb-0">검토 대기 중인 게시글이 없습니다.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="60">번호</th>
                        <th>제목 / 내용 요약</th>
                        <th width="90">작성자</th>
                        <th width="130">작성일</th>
                        <th width="160" class="text-center">처리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviewPosts as $post): ?>
                    <tr>
                        <td class="text-muted"><?= esc($post->id) ?></td>
                        <td>
                            <a href="<?= base_url("examples/board/{$post->id}") ?>" target="_blank"
                               class="fw-semibold text-decoration-none">
                                <?= esc($post->title) ?>
                                <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                            </a>
                            <div class="text-muted small"><?= esc($post->getExcerpt(80)) ?></div>
                        </td>
                        <td><?= esc($post->author) ?></td>
                        <td><small class="text-muted"><?= esc($post->getFormattedDate()) ?></small></td>
                        <td class="text-center">
                            <a href="<?= base_url("examples/spam-admin/post/{$post->id}/approve") ?>"
                               class="btn btn-sm btn-success me-1"
                               onclick="return confirm('이 게시글을 승인하시겠습니까?')">
                                <i class="bi bi-check-lg"></i> 승인
                            </a>
                            <a href="<?= base_url("examples/spam-admin/post/{$post->id}/spam") ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('이 게시글을 스팸으로 처리하시겠습니까?')">
                                <i class="bi bi-shield-x"></i> 스팸
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

<!-- 키워드 목록 -->
<div class="example-card mb-4">
    <div class="example-card-header">
        <span class="badge bg-danger">키워드</span>
        <h5>스팸 키워드 목록 (<?= $total ?>개)</h5>
    </div>
    <div class="example-card-body p-0">
        <?php if (empty($keywords)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-robot" style="font-size:3rem;opacity:.3;"></i>
            <p class="mt-2">키워드가 없습니다.<br>마이그레이션 및 시더를 실행해주세요.</p>
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
                            <span class="badge <?= $kw['active'] ? 'bg-danger' : 'bg-secondary' ?> me-1">
                                <?= esc($kw['keyword']) ?>
                            </span>
                            <?php if ($kw['is_builtin']): ?>
                                <span class="badge bg-dark" title="기본 제공 키워드 (삭제 불가)">내장</span>
                            <?php endif; ?>
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
                            <?php if (! $kw['is_builtin']): ?>
                            <a href="<?= base_url("examples/spam-admin/{$kw['id']}/delete") ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('<?= esc($kw['keyword']) ?> 키워드를 삭제할까요?')"
                               title="삭제">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php else: ?>
                            <button class="btn btn-sm btn-outline-secondary" disabled title="내장 키워드는 삭제할 수 없습니다">
                                <i class="bi bi-lock"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
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
// 1단계: 규칙 기반 (DB 키워드: 내장 + AI 학습 통합)
//   점수 70+ → spam (즉시 차단)
//   점수 30↓  → approved (즉시 허용)
//   점수 31~69 → StopForumSpam IP 체크 후 Groq AI 호출
// 2단계: StopForumSpam IP 평판 체크
//   confidence 90+ → +40점, 60~89 → +20점, Tor Exit → +15점
// 3단계: Groq AI
//   is_spam: true  → spam + 키워드 자동 DB 저장
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

        let sfsHtml = '';
        if (json.sfs) {
            const sfs = json.sfs;
            const sfsColor = sfs.appears ? (sfs.confidence >= 80 ? 'danger' : 'warning') : 'success';
            const sfsIcon  = sfs.appears ? 'exclamation-triangle' : 'check-circle';
            sfsHtml = `<div class="mt-2 p-2 rounded" style="background:rgba(0,0,0,.05);font-size:.8rem;">
                <i class="bi bi-shield-${sfs.appears ? 'x' : 'check'} text-${sfsColor} me-1"></i>
                <strong>StopForumSpam:</strong>
                ${sfs.appears
                    ? `차단 IP (신뢰도 <strong>${sfs.confidence.toFixed(1)}%</strong>, ${sfs.frequency}회 신고${sfs.torexit ? ' · Tor Exit' : ''})`
                    : '정상 IP'}
            </div>`;
        }

        result.innerHTML = `
            <div class="text-center w-100">
                <div class="text-${s.cls}" style="font-size:2rem;"><i class="bi bi-${s.icon}"></i></div>
                <div class="fw-bold text-${s.cls}">${s.text}</div>
                <div class="text-muted small mt-1">점수: ${json.score ?? '-'} / 100</div>
                <div class="text-muted small">${json.reason ?? ''}</div>
                ${sfsHtml}
                ${kwHtml}
            </div>`;
    } catch (e) {
        result.innerHTML = '<span class="text-danger">요청 실패: ' + e.message + '</span>';
    }
});
</script>

<?= $this->endSection() ?>
