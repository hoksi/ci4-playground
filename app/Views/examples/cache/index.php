<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">캐싱</li>
    </ol></nav>
    <h1><i class="bi bi-lightning-charge me-2"></i>캐싱</h1>
    <p>CI4의 Cache 서비스로 데이터를 저장하고 성능을 향상시키는 방법을 알아봅니다.</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php $tab = $tab ?? 'demo'; ?>
<ul class="nav nav-tabs mb-3" id="cacheTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'demo' ? 'active' : '' ?>" href="#" onclick="showTab('demo');return false;">캐시 데모</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- 캐시 데모 -->
<div id="tab-demo" class="tab-content-pane" style="display:<?= $tab === 'demo' ? 'block' : 'none' ?>">

    <!-- 상태 표시 -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="example-card text-center py-3">
                <?php if ($isCached): ?>
                    <div class="fw-bold fs-4 text-success"><i class="bi bi-check-circle"></i></div>
                    <div class="text-success fw-bold">캐시 히트</div>
                    <div class="text-muted small">저장된 캐시 사용</div>
                <?php else: ?>
                    <div class="fw-bold fs-4 text-warning"><i class="bi bi-x-circle"></i></div>
                    <div class="text-warning fw-bold">캐시 미스</div>
                    <div class="text-muted small">새로 조회 후 저장</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="example-card text-center py-3">
                <div class="fw-bold fs-3 <?= $isCached ? 'text-success' : 'text-danger' ?>">
                    <?= $isCached ? '0' : number_format($elapsed, 1) ?> ms
                </div>
                <div class="text-muted small">조회 소요 시간</div>
                <?php if (! $isCached): ?><div class="text-muted small">(0.2초 지연 포함)</div><?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="example-card text-center py-3">
                <div class="fw-bold fs-3 text-info">60s</div>
                <div class="text-muted small">캐시 TTL</div>
                <?php if (! empty($cacheInfo['expire'])): ?>
                <div class="text-muted small">만료: <?= date('H:i:s', $cacheInfo['expire']) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="example-card">
        <div class="example-card-header">
            <h5><i class="bi bi-database me-2"></i>캐시된 데이터 <code class="fs-6"><?= esc($cacheKey) ?></code></h5>
            <div class="ms-auto d-flex gap-2">
                <a href="<?= base_url('examples/cache') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> 새로고침
                </a>
                <a href="<?= base_url('examples/cache/clear') ?>" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-trash"></i> 캐시 삭제
                </a>
                <a href="<?= base_url('examples/cache/clear-all') ?>" class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('전체 캐시를 삭제하시겠습니까?')">
                    <i class="bi bi-trash3"></i> 전체 삭제
                </a>
            </div>
        </div>
        <div class="example-card-body">
            <?php if (empty($data)): ?>
                <p class="text-muted mb-0">캐시 데이터 없음</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-dark"><tr><th>#</th><th>제목</th><th>작성자</th><th>조회수</th></tr></thead>
                    <tbody>
                    <?php foreach ($data as $i => $row): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($row['title']) ?></td>
                        <td><?= esc($row['author']) ?></td>
                        <td><?= number_format($row['views']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 result-box info">
                <i class="bi bi-info-circle me-2"></i>
                <?php if ($isCached): ?>
                    캐시에서 즉시 반환 — 0.2초 지연 없이 결과 표시.
                    <strong>"캐시 삭제"</strong> 후 새로고침하면 실제 조회 시간을 확인할 수 있습니다.
                <?php else: ?>
                    DB 조회 + 0.2초 지연 후 캐시에 저장됨. 새로고침하면 캐시에서 즉시 반환됩니다.
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>기본 캐시 조작</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">$cache = \Config\Services::cache();

// 저장 (TTL: 초 단위, 0 = 영구)
$cache->save('key', $data, 300); // 5분

// 읽기
$data = $cache->get('key');      // 없으면 null

// 삭제
$cache->delete('key');
$cache->clean(); // 전체 삭제

// 메타데이터
$meta = $cache->getMetaData('key');
// ['expire' => timestamp, 'mtime' => ..., 'data' => ...]

// remember() — 없으면 클로저 실행 후 저장
$data = cache()->remember('key', 300, function() {
    return expensiveQuery();
});</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>캐시 드라이버 설정 (.env)</html></h5></div>
        <div class="example-card-body">
            <pre><code class="language-ini"># .env
cache.handler     = file    # file, redis, memcached, predis, wincache
cache.prefix      = ci4_    # 키 충돌 방지 접두사
cache.ttl         = 60      # 기본 TTL (초)
cache.file.storePath = writable/cache/

# Redis 사용 시
cache.handler     = redis
cache.redis.host  = 127.0.0.1
cache.redis.port  = 6379</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>뷰 캐싱</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 컨트롤러에서 뷰 전체를 캐싱 (페이지 캐시)
// 응답 전체를 60초 캐시
$this->cachePage(60);

return view('expensive_page', $data);</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#cacheTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
<?= $this->endSection() ?>
