<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent\Casts;

use Illuminate\Database\Eloquent\Model;
use LogicException;
use PHPUnit\Framework\TestCase;

final class AsPrivateTest extends TestCase
{
    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new class () extends Model {
            protected function casts(): array
            {
                return [
                    'private_attribute' => AsPrivate::class,
                ];
            }
        };
    }

    public function testDoesNotRead(): void
    {
        self::assertEquals(
            $this->model->non_existend_atributte, // @phpstan-ignore property.notFound
            $this->model->private_attribute, // @phpstan-ignore property.notFound
        );
    }

    public function testDoesNotWrite(): void
    {
        self::expectException(LogicException::class);

        $this->model->private_attribute = 1; // @phpstan-ignore property.notFound
    }
}
