<?php
namespace Tests;
use PHPUnit\Framework\TestCase;
final class ExampleTest extends TestCase {
    public function testQuestionTitleMinimumLengthRule(): void {
        self::assertGreaterThanOrEqual(12, mb_strlen('How does PHP work?'));
    }
    public function testVoteValuesAreRestricted(): void {
        self::assertContains(1, [-1,1]);
        self::assertNotContains(0, [-1,1]);
    }
    public function testPasswordHashIsNotPlainText(): void {
        $hash = password_hash('example-password', PASSWORD_DEFAULT);
        self::assertNotSame('example-password', $hash);
        self::assertTrue(password_verify('example-password', $hash));
    }
    public function testHtmlOutputIsEscaped(): void {
        $escaped = htmlspecialchars('<script>alert(1)</script>', ENT_QUOTES, 'UTF-8');
        self::assertStringNotContainsString('<script>', $escaped);
    }
    public function testCsrfTokenHasSufficientEntropyLength(): void {
        self::assertSame(64, strlen(bin2hex(random_bytes(32))));
    }
}
