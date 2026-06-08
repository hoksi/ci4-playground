<?php

namespace Tests\App\Examples;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * 핵심 예제 페이지 로드 테스트
 * 각 예제 페이지가 정상적으로 200 응답을 반환하는지 확인합니다.
 */
class CorePagesTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testHomePageLoads(): void
    {
        $result = $this->get('/');
        $result->assertStatus(200);
        $result->assertSee('CI4 Playground');
    }

    public function testRoutingPageLoads(): void
    {
        $result = $this->get('examples/routing');
        $result->assertStatus(200);
    }

    public function testControllersPageLoads(): void
    {
        $result = $this->get('examples/controllers');
        $result->assertStatus(200);
    }

    public function testViewsPageLoads(): void
    {
        $result = $this->get('examples/views');
        $result->assertStatus(200);
    }

    public function testModelsPageLoads(): void
    {
        $result = $this->get('examples/models');
        $result->assertStatus(200);
    }

    public function testFiltersPageLoads(): void
    {
        $result = $this->get('examples/filters');
        $result->assertStatus(200);
    }

    public function testSessionPageLoads(): void
    {
        $result = $this->get('examples/session');
        $result->assertStatus(200);
    }

    public function testValidationPageLoads(): void
    {
        $result = $this->get('examples/validation');
        $result->assertStatus(200);
    }

    public function testHelperPageLoads(): void
    {
        $result = $this->get('examples/helper');
        $result->assertStatus(200);
    }

    public function testCachePageLoads(): void
    {
        $result = $this->get('examples/cache');
        $result->assertStatus(200);
    }

    public function testLoggingPageLoads(): void
    {
        $result = $this->get('examples/logging');
        $result->assertStatus(200);
    }

    public function testEmailPageLoads(): void
    {
        $result = $this->get('examples/email');
        $result->assertStatus(200);
    }

    public function testPaginationAdvancedPageLoads(): void
    {
        $result = $this->get('examples/paginationadvanced');
        $result->assertStatus(200);
    }

    public function testBoardPageLoads(): void
    {
        $result = $this->get('examples/board');
        $result->assertStatus(200);
    }

    public function testApiPageLoads(): void
    {
        $result = $this->get('examples/api');
        $result->assertStatus(200);
    }
}
