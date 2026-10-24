<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent\Mixins;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\MultipleRecordsFoundException;
use PetrKnap\Eloquent\Optional;
use PetrKnap\Eloquent\Some;
use PetrKnap\Eloquent\TestCase;

final class BuilderMixinTest extends TestCase
{
    /**
     * @note The `phpstan-ignore method.notFound` ensures that the method does not collide with any method of the {@see Builder}.
     */
    public function testDoesNotOverrideNativeMethods(): void
    {
        $this->expectNotToPerformAssertions();

        $builder = Some\Model::query()->where('value', '=', 'unique');
        $builder->option(); // @phpstan-ignore method.notFound
        $builder->soleOrNull(); // @phpstan-ignore method.notFound
    }

    public function testOptionMethodReturnsPresentOptionForUniqueResult(): void
    {
        $modelOption = Some\Model::query() // @phpstan-ignore method.notFound
            ->where('value', '=', 'unique')
            ->option();

        $this->assertInstanceOf(Some\Model::class, $modelOption->orElse(null));
    }

    public function testOptionMethodReturnsEmptyOptionForUnknownResult(): void
    {
        $modelOption = Some\Model::query() // @phpstan-ignore method.notFound
            ->where('value', '=', 'unknown')
            ->option();

        $this->assertNull($modelOption->orElse(null));
    }

    public function testOptionMethodThrowsOnCommonResult(): void
    {
        $this->expectException(MultipleRecordsFoundException::class);

        Some\Model::query() // @phpstan-ignore method.notFound
            ->where('value', '=', 'common')
            ->option();
    }

    public function testSoleOrNullMethodReturnsModelForUniqueResult(): void
    {
        $modelOrNull = Some\Model::query() // @phpstan-ignore method.notFound
            ->where('value', '=', 'unique')
            ->soleOrNull();

        $this->assertInstanceOf(Some\Model::class, $modelOrNull);
    }

    public function testSoleOrNullMethodReturnsNullForUnknownResult(): void
    {
        $modelOrNull = Some\Model::query() // @phpstan-ignore method.notFound
            ->where('value', '=', 'unknown')
            ->soleOrNull();

        $this->assertNull($modelOrNull);
    }

    public function testSoleOrNullMethodThrowsOnCommonResult(): void
    {
        $this->expectException(MultipleRecordsFoundException::class);

        Some\Model::query() // @phpstan-ignore method.notFound
            ->where('value', '=', 'common')
            ->soleOrNull();
    }
}
