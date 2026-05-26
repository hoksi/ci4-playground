<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-arrow-repeat me-2"></i>Model 콜백 (Callbacks)</h1>
    <p>beforeInsert, beforeUpdate, afterFind 콜백으로 데이터 저장/조회 시 자동 처리를 구현합니다.</p>
</div>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active">Model 콜백</li>
    </ol>
</nav>

<?php if ($success = session()->getFlashdata('success')): ?>
<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= esc($success) ?></div>
<?php endif; ?>
<?php if ($errors = session()->getFlashdata('errors')): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
        <li><?= esc($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<ul class="nav nav-tabs mb-4" id="mainTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-code">콜백 코드 설명</a></li>
</ul>

<div class="tab-content">

    <!-- ── 라이브 데모 ──────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-demo">

        <div class="row g-4">
            <!-- 유저 생성 폼 -->
            <div class="col-md-5">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-person-plus text-success"></i>
                        <h5>유저 생성 (beforeInsert 콜백)</h5>
                    </div>
                    <div class="example-card-body">
                        <p class="text-muted small mb-3">비밀번호를 입력하면 <code>beforeInsert</code> 콜백이 자동으로 <code>password_hash()</code>를 적용합니다.</p>
                        <form action="<?= base_url('examples/modelcallback/store') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">사용자명</label>
                                <input type="text" name="username" class="form-control" value="<?= old('username') ?>" placeholder="예: honggildong" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">이메일</label>
                                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" placeholder="예: user@example.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">비밀번호 <span class="text-muted small">(자동 해싱됨)</span></label>
                                <input type="text" name="password" class="form-control" placeholder="평문 입력 → DB에 bcrypt 저장" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">역할</label>
                                <select name="role" class="form-select">
                                    <option value="user">user</option>
                                    <option value="editor">editor</option>
                                    <option value="admin">admin</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-person-plus me-1"></i> 유저 저장
                            </button>
                        </form>
                        <div class="mt-3">
                            <a href="<?= base_url('examples/modelcallback/reset') ?>" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> 샘플 데이터 초기화 (3명 재삽입)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 유저 목록 -->
            <div class="col-md-7">
                <div class="example-card">
                    <div class="example-card-header">
                        <i class="bi bi-people text-primary"></i>
                        <h5>유저 목록 (afterFind 콜백 — 비밀번호 마스킹)</h5>
                    </div>
                    <div class="example-card-body">
                        <p class="text-muted small mb-3"><code>afterFind</code> 콜백이 조회된 모든 레코드의 password 필드를 <code>••••••••</code>로 치환합니다.</p>
                        <?php if (empty($users)): ?>
                        <div class="result-box warning text-center">
                            <i class="bi bi-info-circle me-1"></i> 데이터가 없습니다.
                            <a href="<?= base_url('examples/modelcallback/reset') ?>" class="btn btn-sm btn-warning ms-2">샘플 삽입</a>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>사용자명</th>
                                        <th>이메일</th>
                                        <th>비밀번호</th>
                                        <th>역할</th>
                                        <th>상태</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= esc($user['id']) ?></td>
                                        <td><?= esc($user['username']) ?></td>
                                        <td class="text-muted small"><?= esc($user['email']) ?></td>
                                        <td><code class="text-danger"><?= esc($user['password']) ?></code></td>
                                        <td>
                                            <?php $roleColors = ['admin' => 'danger', 'editor' => 'warning', 'user' => 'secondary']; ?>
                                            <span class="badge bg-<?= $roleColors[$user['role']] ?? 'secondary' ?>"><?= esc($user['role']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($user['status']): ?>
                                            <span class="badge bg-success">활성</span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">비활성</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="result-box info mt-3">
                            <i class="bi bi-info-circle me-1"></i>
                            DB에는 bcrypt 해시가 저장되어 있으며, <code>afterFind</code> 콜백이 화면 출력 전 <strong>••••••••</strong>로 치환합니다.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ── 콜백 코드 설명 ─────────────────────────────── -->
    <div class="tab-pane fade" id="tab-code">

        <div class="example-card mb-4">
            <div class="example-card-header">
                <i class="bi bi-code-slash text-primary"></i>
                <h5>UserCallbackModel — 콜백 등록</h5>
            </div>
            <div class="example-card-body">
                <div class="code-label">app/Models/UserCallbackModel.php</div>
                <pre><code class="language-php">class UserCallbackModel extends Model
{
    protected $table          = 'users_demo';
    protected $allowedFields  = ['username', 'email', 'password', 'role', 'status'];
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowCallbacks = true;   // 콜백 활성화 (필수!)

    // 삽입·수정 전 실행되는 콜백 목록
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    // 조회 후 실행되는 콜백 목록
    protected $afterFind    = ['maskPassword'];
}</code></pre>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-lock text-warning"></i>
                        <h5>beforeInsert / beforeUpdate — 비밀번호 해싱</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">protected function hashPassword(array $data): array
{
    // $data['data'] 에 실제 저장될 배열이 담김
    if (! empty($data['data']['password'])) {
        $data['data']['password'] = password_hash(
            $data['data']['password'],
            PASSWORD_DEFAULT   // bcrypt
        );
    }
    return $data;
}</code></pre>
                        <div class="result-box info mt-3 small">
                            <strong>포인트:</strong> 콜백은 반드시 <code>$data</code> 배열을 반환해야 합니다.
                            수정하지 않은 값도 그대로 반환해야 데이터 손실이 없습니다.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="example-card h-100">
                    <div class="example-card-header">
                        <i class="bi bi-eye-slash text-danger"></i>
                        <h5>afterFind — 비밀번호 마스킹</h5>
                    </div>
                    <div class="example-card-body">
                        <pre><code class="language-php">protected function maskPassword(array $data): array
{
    if (isset($data['data'])) {
        if (is_array($data['data'])
            && isset($data['data']['id'])) {
            // 단일 레코드 (find / first)
            $data['data']['password'] = '••••••••';
        } elseif (is_array($data['data'])) {
            // 목록 (findAll)
            foreach ($data['data'] as &$row) {
                if (is_array($row)) {
                    $row['password'] = '••••••••';
                }
            }
        }
    }
    return $data;
}</code></pre>
                        <div class="result-box info mt-3 small">
                            <strong>포인트:</strong> <code>afterFind</code>는 단건/목록 모두 호출됩니다.
                            <code>$data['singleton']</code>로 단건 여부를 구분할 수도 있습니다.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="example-card mt-4">
            <div class="example-card-header">
                <i class="bi bi-list-check text-success"></i>
                <h5>사용 가능한 콜백 종류</h5>
            </div>
            <div class="example-card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr><th>콜백</th><th>실행 시점</th><th>$data 키</th></tr>
                        </thead>
                        <tbody class="small">
                            <tr><td><code>beforeInsert</code></td><td>insert() 실행 전</td><td>data, result</td></tr>
                            <tr><td><code>afterInsert</code></td><td>insert() 실행 후</td><td>id, data, result</td></tr>
                            <tr><td><code>beforeUpdate</code></td><td>update() 실행 전</td><td>id, data, result</td></tr>
                            <tr><td><code>afterUpdate</code></td><td>update() 실행 후</td><td>id, data, result</td></tr>
                            <tr><td><code>afterFind</code></td><td>find*/first 실행 후</td><td>data, singleton, id</td></tr>
                            <tr><td><code>beforeDelete</code></td><td>delete() 실행 전</td><td>id, purge</td></tr>
                            <tr><td><code>afterDelete</code></td><td>delete() 실행 후</td><td>id, data, purge, result</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<?= $this->endSection() ?>
