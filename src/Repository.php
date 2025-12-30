<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Enumerable;
use InvalidArgumentException;

/**
 * @template TModel of Model
 *
 * @note keep your repositories non-final = mockable
 *
 * @todo keep all method descriptions the same as for methods from `@see`
 */
abstract class Repository
{
    #region Builder related methods
    /**
     * Find a model(s) by its primary key(s).
     *
     * @template TKey of array-key
     *
     * @param TKey|array<TKey> $key
     *
     * @return ($key is array<TKey> ? Enumerable<TKey, TModel> : Optional<TModel>) model or models keyed by key
     *
     * @see Builder::find()
     */
    public function find(array|int|string $key, bool $lockForUpdate = false): Enumerable|Optional
    {
        $query = $this->newQuery();
        if ($lockForUpdate) {
            $query->lockForUpdate();
        }
        return is_array($key)
            ? $query->findMany($key)->keyBy($query->getModel()->getKeyName())
            : Optional::ofSole($query->whereKey($key));
    }

    /**
     * Get the database connection instance.
     *
     * @see Builder::getConnection()
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->newQuery()->getConnection();
    }
    #endregion

    #region Model related methods
    /**
     * Get all of the models from the database.
     *
     * @return Enumerable<array-key, TModel> models keyed by key
     *
     * @see Model::all()
     */
    public function all(): Enumerable
    {
        $query = $this->newQuery();
        return $query->lazy()->keyBy($query->getModel()->getKeyName());
    }

    /**
     * Delete the model from the database.
     *
     * @param TModel $model
     *
     * @see Model::delete()
     */
    public function delete(Model $model): void
    {
        self::callModel(__METHOD__, [$model, 'delete']);
    }

    /**
     * Save the model to the database.
     *
     * @param TModel $model
     *
     * @return TModel
     *
     * @see Model::save()
     */
    public function save(Model $model): Model
    {
        return self::callModel(__METHOD__, [$model, 'save']);
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return Builder<TModel>
     * - use {@see Builder::lazy()} when {@see Enumerable} may be large
     * - use {@see Optional::ofSole()} when {@see Model} may be absent
     * - use {@see Builder::sole()} when {@see Model} must be present
     *
     * @see Model::newQuery()
     *
     * @note {@see Builder::first()} is risky because it silently ignores additional matching records
     */
    protected function newQuery(): Builder
    {
        return (new (static::getModelClass())())->newQuery();
    }
    #endregion

    /**
     * @return class-string<TModel>
     */
    abstract protected static function getModelClass(): string;

    /**
     * @param callable&array{0: TModel, 1: non-empty-string} $callable
     *
     * @return TModel
     */
    private function callModel(string $method, array $callable): Model
    {
        [$model] = $callable;
        if (!is_a($model, static::getModelClass()) || $model->getConnection() !== $this->getConnection()) {
            throw new InvalidArgumentException(sprintf(
                '%s does not accept %s',
                $method,
                $model::class,
            ));
        }

        $callable();

        return $model;
    }
}
