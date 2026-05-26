<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">세션 & 쿠키</li>
    </ol></nav>
    <h1><i class="bi bi-archive me-2"></i>세션 & 쿠키</h1>
    <p>CI4의 세션 관리, Flash 데이터, 쿠키 처리를 알아봅니다.</p>
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

<?php $tab = session()->getFlashdata('tab') ?? 'session'; ?>
<ul class="nav nav-tabs mb-3" id="sessionTab">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'session' ? 'active' : '' ?>" href="#" onclick="showTab('session');return false;">세션</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'flash' ? 'active' : '' ?>" href="#" onclick="showTab('flash');return false;">Flash 데이터</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'cookie' ? 'active' : '' ?>" href="#" onclick="showTab('cookie');return false;">쿠키</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'code' ? 'active' : '' ?>" href="#" onclick="showTab('code');return false;">코드 설명</a>
    </li>
</ul>

<!-- 세션 탭 -->
<div id="tab-session" class="tab-content-pane" style="display:<?= $tab === 'session' ? 'block' : 'none' ?>">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="example-card h-100">
                <div class="example-card-header"><h5><i class="bi bi-plus-circle me-2"></i>세션 저장</h5></div>
                <div class="example-card-body">
                    <form method="post" action="<?= base_url('examples/session/set') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-2">
                            <label class="form-label fw-bold">키</label>
                            <input type="text" name="key" class="form-control" placeholder="예: username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">값</label>
                            <input type="text" name="value" class="form-control" placeholder="예: 홍길동">
                        </div>
                        <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                            <i class="bi bi-save"></i> 세션 저장
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="example-card h-100">
                <div class="example-card-header">
                    <h5><i class="bi bi-list-ul me-2"></i>현재 세션 데이터</h5>
                    <form method="post" action="<?= base_url('examples/session/destroy') ?>" class="ms-auto">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('세션을 초기화하시겠습니까?')">
                            <i class="bi bi-trash"></i> 전체 삭제
                        </button>
                    </form>
                </div>
                <div class="example-card-body">
                    <?php
                    $displayKeys = array_filter(array_keys($sessionData ?? []), fn($k) => !str_starts_with($k, '__'));
                    ?>
                    <?php if (empty($displayKeys)): ?>
                        <p class="text-muted mb-0">저장된 세션 데이터가 없습니다.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-dark"><tr><th>키</th><th>값</th><th></th></tr></thead>
                                <tbody>
                                <?php foreach ($displayKeys as $k): ?>
                                <tr>
                                    <td><code><?= esc($k) ?></code></td>
                                    <td><?= esc(is_array($sessionData[$k]) ? json_encode($sessionData[$k]) : $sessionData[$k]) ?></td>
                                    <td>
                                        <form method="post" action="<?= base_url('examples/session/remove') ?>" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="key" value="<?= esc($k) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0"><i class="bi bi-x"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Flash 데이터 탭 -->
<div id="tab-flash" class="tab-content-pane" style="display:<?= $tab === 'flash' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-lightning me-2"></i>Flash 데이터</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                Flash 데이터는 <strong>다음 요청 한 번만</strong> 유효합니다. 주로 리다이렉트 후 메시지 표시에 사용됩니다.
            </div>
            <?php if (session()->getFlashdata('flash_demo')): ?>
            <div class="alert alert-warning">
                <i class="bi bi-lightning-fill me-2"></i>
                <strong>Flash 메시지:</strong> <?= esc(session()->getFlashdata('flash_demo')) ?>
                <div class="mt-1"><small class="text-muted">이 메시지는 한 번만 표시됩니다. 새로고침 시 사라집니다.</small></div>
            </div>
            <?php endif; ?>
            <form method="post" action="<?= base_url('examples/session/flash') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Flash 메시지 내용</label>
                    <input type="text" name="msg" class="form-control" value="안녕하세요! 이것은 Flash 메시지입니다." required>
                </div>
                <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                    <i class="bi bi-lightning"></i> Flash 저장 후 리다이렉트
                </button>
            </form>
        </div>
    </div>
</div>

<!-- 쿠키 탭 -->
<div id="tab-cookie" class="tab-content-pane" style="display:<?= $tab === 'cookie' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-cookie me-2"></i>쿠키 관리</h5></div>
        <div class="example-card-body">
            <div class="result-box mb-3 <?= $cookieVal !== '' ? '' : 'info' ?>">
                <i class="bi bi-cookie me-2"></i>
                현재 <code>playground_cookie</code> 값:
                <strong><?= $cookieVal !== '' ? esc($cookieVal) : '(없음)' ?></strong>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <form method="post" action="<?= base_url('examples/session/cookie/set') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">쿠키 값</label>
                            <input type="text" name="value" class="form-control" placeholder="저장할 값 입력" value="<?= esc($cookieVal) ?>">
                        </div>
                        <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                            <i class="bi bi-save"></i> 쿠키 저장 (1시간)
                        </button>
                    </form>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <form method="post" action="<?= base_url('examples/session/cookie/delete') ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="demo-btn outline" style="border:1px solid #dd4814;cursor:pointer;">
                            <i class="bi bi-trash"></i> 쿠키 삭제
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 코드 설명 탭 -->
<div id="tab-code" class="tab-content-pane" style="display:<?= $tab === 'code' ? 'block' : 'none' ?>">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>세션 기본 사용법</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 세션 저장
session()->set('username', '홍길동');
session()->set(['role' => 'admin', 'logged_in' => true]);

// 세션 읽기
$name = session()->get('username');
$all  = session()->get();           // 전체

// 세션 삭제
session()->remove('username');
session()->destroy();               // 전체 초기화</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>Flash 데이터</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 저장 — 다음 요청 한 번만 유효
session()->setFlashdata('msg', '저장 완료!');

// 읽기
$msg = session()->getFlashdata('msg');

// 리다이렉트와 함께 사용 (redirect()->with()도 내부적으로 flash 사용)
return redirect()->to('/dashboard')->with('success', '로그인 성공');</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>쿠키</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">// 저장 (컨트롤러에서 response 객체 사용)
$this->response->setCookie('name', 'value', 3600); // 1시간

// 읽기
$val = $this->request->getCookie('name');

// 삭제 (만료시간을 과거로 설정)
$this->response->deleteCookie('name');

// app/Config/Cookie.php 에서 전역 기본값 설정 가능
// $prefix, $domain, $path, $secure, $httponly, $samesite</code></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function showTab(name) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('#sessionTab .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
<?= $this->endSection() ?>
