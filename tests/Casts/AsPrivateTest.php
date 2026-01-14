<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent\Casts;

use LogicException;
use PetrKnap\Eloquent\Some;
use PetrKnap\Eloquent\TestCase;

final class AsPrivateTest extends TestCase
{
    private Some\Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new class () extends Some\Model {
            public function __getAttributes(): array
            {
                return $this->attributes;
            }
            public function __setAttributes(array $attributes): void
            {
                $this->attributes = $attributes;
            }
        };
    }

    public function testDoesNotRead(): void
    {
        self::assertEquals(
            $this->model->non_existent_atributte, // @phpstan-ignore property.notFound
            $this->model->private_attribute, // @phpstan-ignore property.notFound
        );
    }

    public function testDoesNotWrite(): void
    {
        self::expectException(LogicException::class);

        $this->model->private_attribute = 1; // @phpstan-ignore property.notFound
    }

    /**
     * Eloquent sometime calls seter with cast value
     */
    public function testDoesNotChangeValue(): void
    {
        $attributes = [
            'private_attribute' => 1,
        ];
        $this->model->__setAttributes($attributes); // @phpstan-ignore method.notFound

        $this->model->private_attribute = $this->model->non_existent_atributte; // @phpstan-ignore property.notFound, property.notFound

        self::assertEquals($attributes, $this->model->__getAttributes()); // @phpstan-ignore method.notFound
    }
}
