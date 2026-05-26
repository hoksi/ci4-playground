<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active text-white">Entity 심화</li>
    </ol></nav>
    <h1><i class="bi bi-box me-2"></i>Entity 심화</h1>
    <p>Entity의 <code>$casts</code>, <code>$datamap</code>, Virtual Property, Setter를 라이브로 확인합니다.</p>
</div>

<ul class="nav nav-tabs mb-3" id="entTab">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="demo">라이브 데모</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="code">코드 설명</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-tab="casts">캐스팅 타입</a></li>
</ul>

<!-- 라이브 데모 -->
<div id="tab-demo" class="tab-content-pane">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-pencil-square me-2"></i>Entity 입력 폼</h5></div>
        <div class="example-card-body">
            <div class="result-box info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                입력값은 <strong>setter</strong>에서 정규화되고, 접근 시 <strong>$casts</strong>에 따라 타입 변환됩니다.
            </div>
            <form id="entity-form" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">First Name <small class="text-muted">(setter: ucfirst)</small></label>
                    <input type="text" name="first_name" class="form-control" value="john" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Last Name <small class="text-muted">(setter: ucfirst)</small></label>
                    <input type="text" name="last_name" class="form-control" value="doe" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Email <small class="text-muted">(setter: strtolower)</small></label>
                    <input type="text" name="email" class="form-control" value="John.DOE@Example.COM" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Score <small class="text-muted">(float)</small></label>
                    <input type="text" name="score" class="form-control" value="95.5">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isAct" checked>
                        <label class="form-check-label fw-bold" for="isAct">Is Active <small class="text-muted">(bool)</small></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tags <small class="text-muted">(CSV → array)</small></label>
                    <input type="text" name="tags" class="form-control" value="php,ci4,backend">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Metadata <small class="text-muted">(JSON → array)</small></label>
                    <input type="text" name="metadata" class="form-control" value='{"role":"admin","level":5}'>
                </div>
                <div class="col-12">
                    <button type="submit" class="demo-btn" style="border:none;cursor:pointer;">
                        <i class="bi bi-magic"></i> Entity 처리
                    </button>
                </div>
            </form>

            <div id="result-area" class="mt-4" style="display:none;">
                <div class="result-box mb-3" id="result-message"></div>

                <h6 class="mt-4"><i class="bi bi-arrow-right-circle me-1"></i>$casts 적용 결과</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-dark">
                            <tr><th>속성</th><th>입력값 (raw)</th><th>캐스팅 후 값</th><th>PHP 타입</th></tr>
                        </thead>
                        <tbody id="cast-rows"></tbody>
                    </table>
                </div>

                <h6 class="mt-4"><i class="bi bi-eye me-1"></i>Virtual Properties (getter)</h6>
                <table class="table table-bordered table-sm">
                    <tbody id="virtual-rows"></tbody>
                </table>

                <h6 class="mt-4"><i class="bi bi-link-45deg me-1"></i>$datamap 별칭 접근</h6>
                <table class="table table-bordered table-sm">
                    <tbody id="datamap-rows"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 코드 설명 -->
<div id="tab-code" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">1</span><h5>Entity 클래스 정의</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">namespace App\Entities;

use CodeIgniter\Entity\Entity;

class UserEntity extends Entity
{
    // 외부 키 ↔ 내부 속성 매핑 (alias)
    protected $datamap = [
        'email_address' =&gt; 'email',
    ];

    // Time 객체로 자동 변환
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    // 자동 캐스팅 규칙
    protected $casts = [
        'id'         =&gt; 'integer',
        'is_active'  =&gt; 'boolean',
        'metadata'   =&gt; 'json-array',  // JSON 자동 직렬화/역직렬화
        'tags'       =&gt; 'csv',         // CSV → array 자동 변환
        'score'      =&gt; 'float',
    ];

    // Virtual property (DB 컬럼 없이 getter로만 존재)
    public function getFullName(): string
    {
        return trim($this-&gt;attributes['first_name'] . ' ' . $this-&gt;attributes['last_name']);
    }

    // Setter (입력 정규화)
    public function setEmail(string $email): static
    {
        $this-&gt;attributes['email'] = strtolower(trim($email));
        return $this;
    }
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">2</span><h5>Model에 연결</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">namespace App\Models;

use App\Entities\UserEntity;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $returnType = UserEntity::class;  // ← 핵심
    // find/findAll 결과가 UserEntity로 반환됨
}</code></pre>
        </div>
    </div>
    <div class="example-card">
        <div class="example-card-header"><span class="badge bg-dark">3</span><h5>사용 패턴</h5></div>
        <div class="example-card-body">
            <pre><code class="language-php">$user = new UserEntity([
    'first_name' =&gt; 'john',
    'last_name'  =&gt; 'doe',
    'email'      =&gt; 'JOHN.DOE@Example.COM',
]);

// Setter 자동 호출
echo $user-&gt;email;       // → 'john.doe@example.com'
echo $user-&gt;first_name;  // → 'John'

// Virtual property (getter)
echo $user-&gt;full_name;   // → 'John Doe' (getFullName 호출)

// Datamap alias
echo $user-&gt;email_address; // → 'john.doe@example.com'

// toArray / toRawArray
$user-&gt;toArray();    // 캐스팅 적용된 배열
$user-&gt;toRawArray(); // 원본 속성 (DB 저장용)</code></pre>
        </div>
    </div>
</div>

<!-- 캐스팅 타입 -->
<div id="tab-casts" class="tab-content-pane" style="display:none;">
    <div class="example-card">
        <div class="example-card-header"><h5><i class="bi bi-table me-2"></i>$casts 지원 타입</h5></div>
        <div class="example-card-body">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>타입</th><th>PHP 변환</th><th>예시 입력</th><th>변환 결과</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>integer</code></td><td>(int)</td><td><code>'42'</code></td><td><code>42</code></td></tr>
                    <tr><td><code>float</code></td><td>(float)</td><td><code>'3.14'</code></td><td><code>3.14</code></td></tr>
                    <tr><td><code>double</code></td><td>(float) 별칭</td><td><code>'2.71'</code></td><td><code>2.71</code></td></tr>
                    <tr><td><code>string</code></td><td>(string)</td><td><code>42</code></td><td><code>'42'</code></td></tr>
                    <tr><td><code>boolean</code></td><td>(bool)</td><td><code>0 / 1 / 'true'</code></td><td><code>false / true</code></td></tr>
                    <tr><td><code>array</code></td><td>serialize/unserialize</td><td><code>['a','b']</code></td><td><code>array</code></td></tr>
                    <tr><td><code>csv</code></td><td>CSV ↔ array</td><td><code>'php,ci4,sql'</code></td><td><code>['php','ci4','sql']</code></td></tr>
                    <tr><td><code>json</code></td><td>JSON ↔ stdClass</td><td><code>'{"a":1}'</code></td><td><code>stdClass</code></td></tr>
                    <tr><td><code>json-array</code></td><td>JSON ↔ array</td><td><code>'{"a":1}'</code></td><td><code>['a'=&gt;1]</code></td></tr>
                    <tr><td><code>datetime</code></td><td>→ Time 객체</td><td><code>'2026-05-26'</code></td><td><code>CodeIgniter\I18n\Time</code></td></tr>
                    <tr><td><code>timestamp</code></td><td>→ int(unix ts)</td><td><code>'2026-05-26'</code></td><td><code>1779562800</code></td></tr>
                    <tr><td><code>uri</code></td><td>→ URI 객체</td><td><code>'https://...'</code></td><td><code>CodeIgniter\HTTP\URI</code></td></tr>
                </tbody>
            </table>
            <div class="result-box info mt-3">
                <i class="bi bi-lightbulb me-2"></i>
                <strong>Nullable 타입:</strong> 각 타입 뒤에 <code>?</code>를 붙이면 NULL 허용 — 예: <code>'integer?'</code>, <code>'boolean?'</code>.
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('#entTab .nav-link').forEach(el => {
    el.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('#entTab .nav-link').forEach(n => n.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.tab-content-pane').forEach(p => p.style.display = 'none');
        document.getElementById('tab-' + el.dataset.tab).style.display = 'block';
    });
});

document.getElementById('entity-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const res  = await fetch('<?= base_url('examples/entityadvanced/demo') ?>', { method: 'POST', body: formData });
    const data = await res.json();

    const area = document.getElementById('result-area');
    const msg  = document.getElementById('result-message');
    area.style.display = 'block';

    if (! data.success) {
        msg.className = 'result-box danger mb-3';
        msg.innerHTML = '<i class="bi bi-x-circle me-2"></i>오류';
        return;
    }
    msg.className = 'result-box mb-3';
    msg.innerHTML = '<i class="bi bi-check-circle me-2"></i>Entity 처리 완료. 캐스팅 / setter / virtual property 결과를 아래에서 확인하세요.';

    const fmt = (v) => {
        if (v === null || v === undefined) return '<em class="text-muted">null</em>';
        if (typeof v === 'object') return '<code>' + JSON.stringify(v) + '</code>';
        if (typeof v === 'boolean') return v ? '<span class="badge bg-success">true</span>' : '<span class="badge bg-danger">false</span>';
        return '<code>' + v + '</code>';
    };

    const castRows = document.getElementById('cast-rows');
    castRows.innerHTML = '';
    Object.entries(data.casted).forEach(([key, info]) => {
        const raw = data.raw[key];
        castRows.innerHTML += `<tr>
            <td><code>${key}</code></td>
            <td>${fmt(raw)}</td>
            <td>${fmt(info.value)}</td>
            <td><span class="badge bg-secondary">${info.type}</span></td>
        </tr>`;
    });

    const vrows = document.getElementById('virtual-rows');
    vrows.innerHTML = '';
    Object.entries(data.virtual).forEach(([key, val]) => {
        vrows.innerHTML += `<tr><th style="width:200px;"><code>$user->${key}</code></th><td>${fmt(val)}</td></tr>`;
    });

    const drows = document.getElementById('datamap-rows');
    drows.innerHTML = '';
    Object.entries(data.datamap).forEach(([key, val]) => {
        drows.innerHTML += `<tr><th style="width:280px;"><code>$user->email_address</code></th><td>${fmt(val)}</td></tr>`;
    });
});
</script>
<?= $this->endSection() ?>
