<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Group;

/**
 * @todo keep test names in sync with method descriptions
 */
final class RepositoryTest extends TestCase
{
    private Some\ModelRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new Some\ModelRepository();
    }

    #region Builder related tests
    #[Group(Builder::class)]
    public function testFindsAModelByItsPrimaryKey(): void
    {
        self::assertSame(2, $this->repository->find(2)->orElseThrow()->id);

        self::assertFalse($this->repository->find('unknown')->isPresent());
    }

    #[Group(Builder::class)]
    public function testFindsAModelsByItsPrimaryKeys(): void
    {
        self::assertSame(
            [2 => 2, 3 => 3],
            $this->repository->find([2, 3])->map(static fn (Some\Model $model): int|null => $model->id)->all(),
        );

        self::assertCount(1, $this->repository->find([2, 'unknown']));
        self::assertCount(0, $this->repository->find(['unknown']));
    }

    #[Group(Builder::class)]
    public function testGetsTheDatabaseConnectionInstance(): void
    {
        self::assertInstanceOf(Connection::class, $this->repository->getConnection());
    }
    #endregion

    #region Model related tests
    #[Group(Model::class)]
    public function testGetsAllOfTheModelsFromTheDatabase(): void
    {
        self::assertCount(3, $this->repository->all());
    }

    #[Group(Model::class)]
    public function testDeletesTheModelFromTheDatabase(): void
    {
        $model = self::createMock(Some\Model::class);
        $model->method('getConnection')->willReturn($this->repository->getConnection());
        $model->expects(self::once())->method('delete');

        $this->repository->delete($model);

        self::expectException(InvalidArgumentException::class);
        // @phpstan-ignore-next-line argument.type
        $this->repository->delete(new class () extends Model {
        });
    }

    #[Group(Model::class)]
    public function testSavesTheModelToTheDatabase(): void
    {
        $model = self::createMock(Some\Model::class);
        $model->method('getConnection')->willReturn($this->repository->getConnection());
        $model->expects(self::once())->method('save');

        $this->repository->save($model);

        self::expectException(InvalidArgumentException::class);
        // @phpstan-ignore-next-line argument.type
        $this->repository->save(new class () extends Model {
        });
    }
    #endregion
}
