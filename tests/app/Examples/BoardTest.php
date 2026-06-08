<?php

namespace Tests\App\Examples;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * 게시판 CRUD 예제 테스트
 */
class BoardTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testBoardIndexLoads(): void
    {
        $result = $this->get('examples/board');
        $result->assertStatus(200);
    }

    public function testBoardCreatePageRedirectsInReadOnlyMode(): void
    {
        // Board::READ_ONLY = true 이므로 작성 페이지는 302로 리다이렉트
        $result = $this->get('examples/board/create');
        $result->assertStatus(302);
    }

    public function testBoardStoreRedirectsInReadOnlyMode(): void
    {
        // READ_ONLY 모드에서 저장 시도 → 302 리다이렉트
        $result = $this->post('examples/board/store', [
            'title'   => '테스트 게시글',
            'content' => '테스트 내용',
            'author'  => '테스터',
        ]);
        $result->assertStatus(302);
    }

    public function testBoardShowNonExistentPostThrowsException(): void
    {
        // 존재하지 않는 게시글 → PageNotFoundException
        $this->expectException(PageNotFoundException::class);
        $this->get('examples/board/99999');
    }

    public function testBoardDeleteRedirectsInReadOnlyMode(): void
    {
        // READ_ONLY 모드에서 삭제 시도 → 302 리다이렉트
        $result = $this->get('examples/board/1/delete');
        $result->assertStatus(302);
    }
}
