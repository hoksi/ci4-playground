<?php

namespace Tests\App\Services;

use App\Models\PostModel;
use App\Services\PostService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * PostService 통합 테스트 (실제 SQLite DB 사용)
 *
 * 실행: ./vendor/bin/phpunit tests/app/Services/PostServiceTest.php
 */
final class PostServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate  = true;
    protected $basePath = 'tests/_support/Database';

    private PostService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PostService(new PostModel());

        // 테스트용 샘플 데이터 직접 삽입
        $model = new PostModel();
        $model->skipValidation(true)->insertBatch([
            ['title' => '게시물 A', 'content' => '테스트 내용입니다 A', 'author' => '홍길동', 'views' => 100],
            ['title' => '게시물 B', 'content' => '테스트 내용입니다 B', 'author' => '김철수', 'views' => 50],
            ['title' => '게시물 C', 'content' => '테스트 내용입니다 C', 'author' => '이영희', 'views' => 200],
        ]);
    }

    public function testGetTopPostsReturnsArray(): void
    {
        $result = $this->service->getTopPosts(3);
        $this->assertIsArray($result);
        $this->assertLessThanOrEqual(3, count($result));
    }

    public function testGetTopPostsOrderedByViews(): void
    {
        $posts = $this->service->getTopPosts(5);
        if (count($posts) >= 2) {
            $this->assertGreaterThanOrEqual($posts[1]->views, $posts[0]->views);
        }
        $this->assertTrue(true);
    }

    public function testGetSummaryReturnsRequiredKeys(): void
    {
        $summary = $this->service->getSummary();
        $this->assertArrayHasKey('total', $summary);
        $this->assertArrayHasKey('total_views', $summary);
        $this->assertArrayHasKey('avg_views', $summary);
    }

    public function testGetSummaryTotalIsNonNegative(): void
    {
        $summary = $this->service->getSummary();
        $this->assertGreaterThanOrEqual(0, $summary['total']);
    }

    public function testSearchReturnsEmptyForBlankKeyword(): void
    {
        $result = $this->service->search('');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testCreateFailsWithMissingTitle(): void
    {
        $result = $this->service->create(['content' => '본문만 있음']);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
    }

    public function testCreateSucceedsWithValidData(): void
    {
        $result = $this->service->create([
            'title'   => '테스트 게시물',
            'content' => '서비스 레이어를 통해 저장한 테스트 본문입니다.',
            'author'  => '테스트작성자',
        ]);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('id', $result);
        $this->assertGreaterThan(0, $result['id']);
    }
}
