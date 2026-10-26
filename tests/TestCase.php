<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent;

use Illuminate\Contracts\Foundation\Application;
use PDO;
use PetrKnap\Eloquent\Providers\EloquentServiceProvider;
use PetrKnap\Shorts\Testing\IlluminateDatabase;
use PHPUnit\Framework\TestCase as Base;

abstract class TestCase extends Base
{
    protected PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec(
            'CREATE TABLE models (
                value TEXT NOT NULL,
                parent_id INTEGER NULL,
                -- Eloquent attributes
                id INTEGER PRIMARY KEY,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT NULL
            )',
        );

        $insert = $this->pdo->prepare('INSERT INTO models (value, parent_id) VALUES (?, ?)');
        $insert->execute(['unique', null]);
        $insert->execute(['common', 1]);
        $insert->execute(['common', 2]);
        $insert->execute(['common', 2]);

        (new EloquentServiceProvider(self::createStub(Application::class)))->boot();
        IlluminateDatabase::createCapsuleManager($this->pdo)->bootEloquent();
    }
}
