<?php

namespace Tests\App\Examples;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * REST API 예제 테스트
 */
class ApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testApiIndexLoads(): void
    {
        $result = $this->get('examples/api');
        $result->assertStatus(200);
    }

    public function testApiUsersReturnsJson(): void
    {
        $result = $this->get('examples/api/users');
        $result->assertStatus(200);
        $result->assertIsJSON();
    }

    public function testApiUsersReturnsArray(): void
    {
        $result = $this->get('examples/api/users');
        $json   = json_decode($result->getJSON(), true);
        $this->assertIsArray($json);
    }

    public function testApiUserNotFoundReturns404(): void
    {
        $result = $this->get('examples/api/users/99999');
        $result->assertStatus(404);
    }

    public function testApiCreateUserReturnsJson(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('examples/api/users', [
                'name'  => '테스트유저',
                'email' => 'test@example.com',
            ]);
        $result->assertStatus(201);
        $result->assertIsJSON();
    }

    public function testApiV2IndexLoads(): void
    {
        $result = $this->get('examples/apiv2');
        $result->assertStatus(200);
    }

    public function testApiV2UsersRequiresAuth(): void
    {
        $result = $this->get('examples/apiv2/users');
        $result->assertStatus(401);
    }

    public function testApiV2CreateUserRequiresAuth(): void
    {
        // createUser도 JWT 인증 필요 → 401 반환
        $result = $this->withBodyFormat('json')->post('examples/apiv2/users', [
            'username' => 'tester',
            'email'    => 'tester@example.com',
            'password' => 'test1234',
        ]);
        $result->assertStatus(401);
    }

    public function testApiV2TokenWithInvalidCredentialsReturns401(): void
    {
        $result = $this->withBodyFormat('json')->post('examples/apiv2/auth/token', [
            'email'    => 'nobody@example.com',
            'password' => 'wrongpassword',
        ]);
        $result->assertStatus(401);
        $result->assertIsJSON();
    }
}
