<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\MultipleItemsFoundException;
use PetrKnap\Optional\Exception;

final class OptionalTest extends TestCase
{
    public function testCreatesItselfFromBuilder(): void
    {
        self::assertFalse(Optional::ofSole(
            Some\Model::query()->where('value', '=', 'unknown'),
        )->isPresent());

        self::assertInstanceOf(Some\Model::class, Optional::ofSole(
            Some\Model::query()->where('value', '=', 'unique'),
        )->orElseThrow());

        self::expectException(MultipleRecordsFoundException::class);
        Optional::ofSole(
            Some\Model::query()->where('value', '=', 'common'),
        );
    }

    public function testCreatesItselfFromEnumerable(): void
    {
        self::assertFalse(Optional::ofSole(
            new Collection([]),
        )->isPresent());

        self::assertInstanceOf(Some\Model::class, Optional::ofSole(
            new Collection([new Some\Model()]),
        )->orElseThrow());

        self::expectException(MultipleItemsFoundException::class);
        Optional::ofSole(
            new Collection([new Some\Model(), new Some\Model()]),
        );
    }

    public function testCorrectlyThrowsOnMissingModel(): void
    {
        $exception = null;
        try {
            Optional::ofSole(
                Some\Model::query()->where('value', '=', 'unknown'),
            )->orElseThrow();
        } catch (Exception\CouldNotGetValueOfEmptyOptional $exception) {
            $exception = $exception->getPrevious();
        }
        self::assertInstanceOf(ModelNotFoundException::class, $exception);
    }
}
