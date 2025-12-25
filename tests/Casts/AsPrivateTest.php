<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent\Casts;

use LogicException;
use PetrKnap\Eloquent\Some\Model;
use PetrKnap\Eloquent\TestCase;

final class AsPrivateTest extends TestCase
{
    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new Model();
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
