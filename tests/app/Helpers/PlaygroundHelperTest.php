<?php

namespace Tests\App\Helpers;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * playground_helper 단위 테스트
 *
 * 실행: ./vendor/bin/phpunit tests/app/Helpers/PlaygroundHelperTest.php
 */
final class PlaygroundHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('playground');
    }

    // ── format_filesize ─────────────────────────────────────
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

    public function testFormatFilesizeGigabytes(): void
    {
        $result = format_filesize(1073741824);
        $this->assertStringContainsString('GB', $result);
        $this->assertStringContainsString('1', $result);
    }

    // ── truncate_text ───────────────────────────────────────
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

    public function testTruncateTextCustomSuffix(): void
    {
        $result = truncate_text('긴 텍스트 내용이 여기에 있습니다.', 5, '…');
        $this->assertStringEndsWith('…', $result);
    }

    // ── time_ago ────────────────────────────────────────────
    public function testTimeAgoSeconds(): void
    {
        $result = time_ago(time() - 30);
        $this->assertStringContainsString('초 전', $result);
    }

    public function testTimeAgoMinutes(): void
    {
        $result = time_ago(time() - 3600);
        $this->assertStringContainsString('시간 전', $result);
    }

    public function testTimeAgoDays(): void
    {
        $result = time_ago(time() - 86400 * 3);
        $this->assertStringContainsString('일 전', $result);
    }

    // ── korean_number ───────────────────────────────────────
    public function testKoreanNumberUnder10000(): void
    {
        $this->assertSame('999', korean_number(999));
    }

    public function testKoreanNumberMan(): void
    {
        $this->assertStringContainsString('만', korean_number(50000));
    }

    public function testKoreanNumberEok(): void
    {
        $this->assertStringContainsString('억', korean_number(100000000));
    }
}
