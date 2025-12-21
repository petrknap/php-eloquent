<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent;

use PDO;
use PetrKnap\Shorts\PhpUnit\MarkdownFileTestInterface;
use PetrKnap\Shorts\PhpUnit\MarkdownFileTestTrait;
use PetrKnap\Shorts\Testing\IlluminateDatabase;
use PHPUnit\Framework\TestCase;

final class ReadmeTest extends TestCase implements MarkdownFileTestInterface
{
    use MarkdownFileTestTrait;

    protected function setUp(): void
    {
        parent::setUp();

        IlluminateDatabase::createCapsuleManager(new PDO('sqlite::memory:'))->bootEloquent();
    }

    public static function getPathToMarkdownFile(): string
    {
        return __DIR__ . '/../README.md';
    }

    public static function getExpectedOutputsOfPhpExamples(): iterable
    {
        return [
            'casts' => self::OUTPUT_IN_MARKDOWN,
        ];
    }
}
