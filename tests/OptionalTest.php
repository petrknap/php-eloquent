<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\MultipleItemsFoundException;

final class OptionalTest extends TestCase
{
    public function testCreatesItselfFromBuilder(): void
    {
        self::assertFalse(Optional::ofNullable(
            Some\Model::query()->where('value', '=', 'unknown'),
        )->isPresent());

        self::assertInstanceOf(Some\Model::class, Optional::ofNullable(
            Some\Model::query()->where('value', '=', 'unique'),
        )->orElseThrow());

        self::expectException(MultipleRecordsFoundException::class);
        Optional::ofNullable(
            Some\Model::query()->where('value', '=', 'common'),
        );
    }

    public function testCreatesItselfFromEnumerable(): void
    {
        self::assertFalse(Optional::ofNullable(
            new Collection([]),
        )->isPresent());

        self::assertInstanceOf(Some\Model::class, Optional::ofNullable(
            new Collection([new Some\Model()]),
        )->orElseThrow());

        self::expectException(MultipleItemsFoundException::class);
        Optional::ofNullable(
            new Collection([new Some\Model(), new Some\Model()]),
        );
    }

    public function testThrowsOnMissingModel(): void
    {
        self::expectException(ModelNotFoundException::class);
        self::expectExceptionMessage(sprintf('No query results for model [%s]', Some\Model::class));

        Optional::empty(Some\Model::class)->orElseThrow();
    }
}
