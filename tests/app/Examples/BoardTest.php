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

    public function testBoardCreatePageLoads(): void
    {
        // READ_ONLY = false → 작성 페이지 정상 로드
        $result = $this->get('examples/board/create');
        $result->assertStatus(200);
    }

    public function testBoardStoreCreatesPost(): void
    {
        // 정상 게시글 저장 → 스팸 미감지 → 302 리다이렉트
        $result = $this->post('examples/board/store', [
            'title'   => '테스트 게시글 제목입니다',
            'content' => '테스트 내용입니다. 충분한 길이의 내용을 작성합니다.',
            'author'  => '테스터',
        ]);
        $result->assertStatus(302);
    }

    public function testBoardStoreSpamIsRejected(): void
    {
        // 스팸 키워드 포함 게시글 → 302 리다이렉트 (에러와 함께 뒤로)
        $result = $this->post('examples/board/store', [
            'title'   => '비아그라 카지노 무료수익 대출',
            'content' => '카지노 비아그라 viagra casino 지금바로 클릭 https://spam1.com https://spam2.com https://spam3.com',
            'author'  => '스패머',
        ]);
        $result->assertStatus(302);
    }

    public function testBoardShowNonExistentPostThrowsException(): void
    {
        // 존재하지 않는 게시글 → PageNotFoundException
        $this->expectException(PageNotFoundException::class);
        $this->get('examples/board/99999');
    }

    public function testBoardDeleteRedirects(): void
    {
        // 게시글 직접 생성 후 삭제 → 302 리다이렉트
        $model = new \App\Models\PostModel();
        $id    = $model->insert([
            'title'   => '삭제 테스트 게시글',
            'content' => '삭제 테스트 내용입니다. 충분한 길이.',
            'author'  => '테스터',
        ]);

        $result = $this->get("examples/board/{$id}/delete");
        $result->assertStatus(302);
    }
}
