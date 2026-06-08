<?php

namespace Tests\App\Examples;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * 유효성 검사 예제 테스트
 */
class ValidationTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testValidationPageLoads(): void
    {
        $result = $this->get('examples/validation');
        $result->assertStatus(200);
    }

    public function testBasicValidationWithValidData(): void
    {
        $result = $this->post('examples/validation/basic', [
            'name'  => '홍길동',
            'email' => 'test@example.com',
            'age'   => 25,
        ]);
        $result->assertStatus(200);
        $result->assertSee('유효성 검사 통과');
    }

    public function testBasicValidationWithEmptyData(): void
    {
        $result = $this->post('examples/validation/basic', []);
        $result->assertStatus(200);
        $result->assertSee('invalid-feedback');
    }

    public function testBasicValidationWithInvalidEmail(): void
    {
        $result = $this->post('examples/validation/basic', [
            'name'  => '홍길동',
            'email' => 'invalid-email',
            'age'   => 25,
        ]);
        $result->assertStatus(200);
        $result->assertSee('invalid-feedback');
    }

    public function testAdvancedValidationPageLoads(): void
    {
        $result = $this->get('examples/advancedvalidation');
        $result->assertStatus(200);
    }
}
