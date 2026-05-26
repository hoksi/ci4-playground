<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-check2-circle me-2"></i>테스팅</h1>
    <p>PHPUnit + CI4 테스트 도구(CIUnitTestCase, DatabaseTestTrait)로 단위/통합 테스트를 작성하는 방법을 학습합니다.</p>
</div>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item active">테스팅</li>
    </ol>
</nav>

<!-- 탭 -->
<ul class="nav nav-tabs mb-4" id="testingTabs">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab-overview">개요</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-helper">헬퍼 테스트</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-service">서비스 테스트</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-run">테스트 실행</a>
    </li>
</ul>

<div class="tab-content">

    <!-- ── 개요 ─────────────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-overview">

        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-info-circle text-primary"></i>
                <h5>CI4 테스팅 기초</h5>
            </div>
            <div class="example-card-body">
                <p class="mb-3">CodeIgniter 4는 PHPUnit 기반의 테스트 도구를 내장하고 있습니다. <code>CIUnitTestCase</code>를 상속하면 CI4 서비스, 헬퍼, DB에 손쉽게 접근할 수 있습니다.</p>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="result-box info">
                            <strong><i class="bi bi-1-circle me-1"></i>단위 테스트 (Unit Test)</strong>
                            <p class="mb-0 mt-2 small">개별 함수나 메서드를 독립적으로 검증합니다. 외부 의존성(DB, 네트워크) 없이 빠르게 실행됩니다. <code>CIUnitTestCase</code>를 상속해 헬퍼·서비스·유틸을 테스트합니다.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="result-box">
                            <strong><i class="bi bi-2-circle me-1"></i>통합 테스트 (Integration Test)</strong>
                            <p class="mb-0 mt-2 small">실제 DB와 연동하여 비즈니스 로직 전체를 검증합니다. <code>DatabaseTestTrait</code>를 사용하면 테스트마다 DB를 자동으로 초기화합니다.</p>
                        </div>
                    </div>
                </div>

                <div class="code-label">기본 테스트 클래스 구조</div>
                <pre><code class="language-php">&lt;?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;

final class MyTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // 각 테스트 전에 실행
    }

    public function testSomething(): void
    {
        $this->assertSame('expected', someFunction());
    }
}
</code></pre>
            </div>
        </div>

        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-database text-success"></i>
                <h5>DatabaseTestTrait</h5>
            </div>
            <div class="example-card-body">
                <p class="mb-3">DB가 필요한 테스트에는 <code>DatabaseTestTrait</code>를 사용합니다. 테스트용 SQLite DB에 마이그레이션을 자동 실행하고, 각 테스트 후 롤백합니다.</p>
                <div class="code-label">DatabaseTestTrait 설정</div>
                <pre><code class="language-php">use CodeIgniter\Test\DatabaseTestTrait;

final class PostServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    // 테스트 DB에 마이그레이션 자동 실행
    protected $migrate  = true;
    // 마이그레이션 파일 경로
    protected $basePath = 'tests/_support/Database';

    protected function setUp(): void
    {
        parent::setUp();
        // skipValidation(true) 으로 시드 데이터 직접 삽입
        (new PostModel())->skipValidation(true)->insertBatch([
            ['title' => '게시물 A', 'content' => '내용 A ...', 'views' => 100],
        ]);
    }
}
</code></pre>
                <div class="result-box info mt-3">
                    <strong>테스트용 마이그레이션 파일 위치</strong><br>
                    <code>tests/_support/Database/Migrations/</code> 에 마이그레이션을 두면 운영 DB와 완전히 분리됩니다.
                </div>
            </div>
        </div>

        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-terminal text-warning"></i>
                <h5>테스트 실행 명령어</h5>
            </div>
            <div class="example-card-body">
                <div class="code-label">전체 테스트 실행</div>
                <pre><code class="language-bash">./vendor/bin/phpunit --testdox</code></pre>

                <div class="code-label mt-3">특정 파일만 실행</div>
                <pre><code class="language-bash">./vendor/bin/phpunit tests/app/Helpers/PlaygroundHelperTest.php --testdox
./vendor/bin/phpunit tests/app/Services/PostServiceTest.php --testdox</code></pre>

                <div class="code-label mt-3">phpunit.xml.dist (CI4 기본 설정)</div>
                <pre><code class="language-xml">&lt;testsuites&gt;
    &lt;testsuite name="App"&gt;
        &lt;directory suffix="Test.php"&gt;./tests&lt;/directory&gt;
    &lt;/testsuite&gt;
&lt;/testsuites&gt;</code></pre>
            </div>
        </div>

    </div><!-- /tab-overview -->

    <!-- ── 헬퍼 테스트 ───────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-helper">

        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-tools text-primary"></i>
                <h5>PlaygroundHelperTest — 단위 테스트</h5>
            </div>
            <div class="example-card-body">
                <p class="mb-3">커스텀 헬퍼 함수 5개(<code>format_filesize</code>, <code>time_ago</code>, <code>truncate_text</code>, <code>highlight_keyword</code>, <code>korean_number</code>)에 대한 13개의 단위 테스트입니다.</p>

                <div class="code-label">tests/app/Helpers/PlaygroundHelperTest.php</div>
                <pre><code class="language-php">final class PlaygroundHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('playground');   // 헬퍼 로드
    }

    // format_filesize: 바이트 → 사람이 읽기 쉬운 크기 문자열
    public function testFormatFilesizeBytes(): void
    {
        $this->assertSame('512 B', format_filesize(512));
    }

    public function testFormatFilesizeKilobytes(): void
    {
        $this->assertSame('1.5 KB', format_filesize(1536));
    }

    public function testFormatFilesizeMegabytes(): void
    {
        $result = format_filesize(2097152);
        $this->assertStringContainsString('MB', $result);
        $this->assertStringContainsString('2', $result);
    }

    // truncate_text: 지정 길이 초과 시 말줄임표
    public function testTruncateTextShortString(): void
    {
        $this->assertSame('짧은글', truncate_text('짧은글', 20));
    }

    public function testTruncateTextLongString(): void
    {
        $result = truncate_text('가나다라마바사아자차카타파하가나다라마바사', 10);
        $this->assertStringEndsWith('...', $result);
        $this->assertLessThanOrEqual(13, mb_strlen($result));
    }

    // time_ago: 타임스탬프 → "N분 전" 형식
    public function testTimeAgoSeconds(): void
    {
        $this->assertStringContainsString('초 전', time_ago(time() - 30));
    }

    // korean_number: 숫자 → 만/억 단위 포맷
    public function testKoreanNumberMan(): void
    {
        $this->assertStringContainsString('만', korean_number(50000));
    }

    public function testKoreanNumberEok(): void
    {
        $this->assertStringContainsString('억', korean_number(100000000));
    }
}
</code></pre>
            </div>
        </div>

    </div><!-- /tab-helper -->

    <!-- ── 서비스 테스트 ─────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-service">

        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-layers text-success"></i>
                <h5>PostServiceTest — 통합 테스트</h5>
            </div>
            <div class="example-card-body">
                <p class="mb-3"><code>DatabaseTestTrait</code>를 사용해 실제 SQLite 테스트 DB 위에서 서비스 레이어의 동작을 검증합니다. 7개의 테스트로 구성됩니다.</p>

                <div class="code-label">tests/app/Services/PostServiceTest.php</div>
                <pre><code class="language-php">final class PostServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate  = true;
    protected $basePath = 'tests/_support/Database';

    private PostService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PostService(new PostModel());

        // 테스트용 시드 데이터 직접 삽입
        (new PostModel())->skipValidation(true)->insertBatch([
            ['title' => '게시물 A', 'content' => '테스트 내용입니다 A', 'views' => 100],
            ['title' => '게시물 B', 'content' => '테스트 내용입니다 B', 'views' => 50],
            ['title' => '게시물 C', 'content' => '테스트 내용입니다 C', 'views' => 200],
        ]);
    }

    // getTopPosts: 조회수 내림차순 정렬 확인
    public function testGetTopPostsOrderedByViews(): void
    {
        $posts = $this->service->getTopPosts(5);
        if (count($posts) >= 2) {
            $this->assertGreaterThanOrEqual($posts[1]->views, $posts[0]->views);
        }
        $this->assertTrue(true);
    }

    // getSummary: 필수 키 존재 여부 확인
    public function testGetSummaryReturnsRequiredKeys(): void
    {
        $summary = $this->service->getSummary();
        $this->assertArrayHasKey('total', $summary);
        $this->assertArrayHasKey('total_views', $summary);
        $this->assertArrayHasKey('avg_views', $summary);
    }

    // search: 빈 키워드 → 빈 배열 반환
    public function testSearchReturnsEmptyForBlankKeyword(): void
    {
        $this->assertEmpty($this->service->search(''));
    }

    // create: 제목 없이 생성 시도 → 실패
    public function testCreateFailsWithMissingTitle(): void
    {
        $result = $this->service->create(['content' => '본문만 있음']);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
    }

    // create: 정상 데이터 → 성공 + id 반환
    public function testCreateSucceedsWithValidData(): void
    {
        $result = $this->service->create([
            'title'   => '테스트 게시물',
            'content' => '서비스 레이어를 통해 저장한 테스트 본문입니다.',
            'author'  => '테스트작성자',
        ]);
        $this->assertTrue($result['success']);
        $this->assertGreaterThan(0, $result['id']);
    }
}
</code></pre>
            </div>
        </div>

        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-exclamation-triangle text-warning"></i>
                <h5>주요 주의사항</h5>
            </div>
            <div class="example-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="result-box warning">
                            <strong>공유 Validator 인스턴스 문제</strong>
                            <p class="small mt-2 mb-0"><code>Services::validation()</code>은 싱글톤을 반환해 이전 테스트의 에러가 남아 있습니다. <code>Services::validation(null, false)</code>로 항상 새 인스턴스를 생성해야 합니다.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="result-box warning">
                            <strong>테스트 DB 테이블 미존재</strong>
                            <p class="small mt-2 mb-0"><code>$basePath = 'tests/_support/Database'</code>를 지정하고 해당 경로에 테스트 전용 마이그레이션 파일을 두어야 합니다. 운영 마이그레이션과 분리하세요.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /tab-service -->

    <!-- ── 테스트 실행 ────────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-run">

        <div class="example-card">
            <div class="example-card-header">
                <i class="bi bi-play-circle text-success"></i>
                <h5>테스트 실행하기</h5>
            </div>
            <div class="example-card-body">
                <p class="text-muted small mb-3">실행할 테스트 스위트를 선택하고 PHPUnit을 실행합니다.</p>

                <form method="post" action="<?= current_url() ?>#tab-run">
                    <?= csrf_field() ?>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button type="submit" name="suite" value="all" class="btn btn-success">
                            <i class="bi bi-play-fill me-1"></i>전체 테스트
                        </button>
                        <button type="submit" name="suite" value="helper" class="btn btn-outline-primary">
                            <i class="bi bi-tools me-1"></i>헬퍼 테스트만
                        </button>
                        <button type="submit" name="suite" value="service" class="btn btn-outline-secondary">
                            <i class="bi bi-layers me-1"></i>서비스 테스트만
                        </button>
                    </div>
                </form>

                <?php if ($output !== null): ?>
                <div class="mt-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <?php if ($exitCode === 0): ?>
                            <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>PASS</span>
                        <?php else: ?>
                            <span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i>FAIL</span>
                        <?php endif; ?>
                        <span class="text-muted small">종료 코드: <?= $exitCode ?></span>
                    </div>
                    <pre style="background:#1e1e1e;color:#d4d4d4;padding:1.25rem;border-radius:8px;font-size:.83rem;line-height:1.6;white-space:pre-wrap;word-break:break-all;"><?= esc($output) ?></pre>
                </div>
                <?php else: ?>
                <div class="result-box info mt-3">
                    <i class="bi bi-info-circle me-2"></i>위 버튼을 클릭하면 실제 PHPUnit 실행 결과가 표시됩니다.
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /tab-run -->

</div><!-- /tab-content -->

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// URL 해시에 따라 탭 활성화
document.addEventListener('DOMContentLoaded', () => {
    const hash = location.hash;
    if (hash) {
        const tab = document.querySelector(`[href="${hash}"]`);
        if (tab) bootstrap.Tab.getOrCreateInstance(tab).show();
    }
});
</script>
<?= $this->endSection() ?>
