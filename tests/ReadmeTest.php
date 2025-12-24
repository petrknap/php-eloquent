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

        $pdo = new PDO('sqlite::memory:');
        $pdo->exec(
            'CREATE TABLE some_models (
                id INTEGER PRIMARY KEY,
                utc_datetime DATETIME DEFAULT NULL,
                local_datetime_utc DATETIME DEFAULT NULL,
                local_datetime_timezone TEXT DEFAULT NULL,
                -- Eloquent attributes
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT NULL
            )',
        );

        IlluminateDatabase::createCapsuleManager($pdo)->bootEloquent();
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
