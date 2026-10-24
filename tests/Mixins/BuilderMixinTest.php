<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent\Mixins;

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
        $option = Some\Model::query() // @phpstan-ignore method.notFound
            ->where('value', '=', 'unique')
            ->option();

        $this->assertInstanceOf(Optional::class, $option);
        $this->assertTrue($option->isPresent());
    }

    public function testOptionMethodReturnsEmptyOptionForUnknownResult(): void
    {
        $option = Some\Model::query() // @phpstan-ignore method.notFound
            ->where('value', '=', 'unknown')
            ->option();

        $this->assertInstanceOf(Optional::class, $option);
        $this->assertFalse($option->isPresent());
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
        $model = Some\Model::query() // @phpstan-ignore method.notFound
            ->where('value', '=', 'unique')
            ->soleOrNull();

        $this->assertInstanceOf(Some\Model::class, $model);
    }

    public function testSoleOrNullMethodReturnsNullForUnknownResult(): void
    {
        $model = Some\Model::query() // @phpstan-ignore method.notFound
            ->where('value', '=', 'unknown')
            ->soleOrNull();

        $this->assertNull($model);
    }

    public function testSoleOrNullMethodThrowsOnCommonResult(): void
    {
        $this->expectException(MultipleRecordsFoundException::class);

        Some\Model::query() // @phpstan-ignore method.notFound
            ->where('value', '=', 'common')
            ->soleOrNull();
    }
}
