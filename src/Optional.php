<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Support\Enumerable;
use Illuminate\Support\ItemNotFoundException;
use PetrKnap\Optional\OptionalObject;

/**
 * @template T of Model
 *
 * @extends OptionalObject<T>
 */
final class Optional extends OptionalObject
{
    /**
     * @var class-string<T>|null
     */
    private string|null $modelClass;

    /**
     * @param class-string<T>|null $modelClass
     */
    public static function empty(string|null $modelClass = null): static
    {
        $modelOption = parent::empty();
        $modelOption->modelClass = $modelClass;
        return $modelOption;
    }

    /**
     * @param T|Builder<T>|Enumerable<array-key, T> $value
     */
    public static function of(mixed $value): static
    {
        $model = self::unwrapValue($value, nullable: false);
        $modelOption = parent::of($model);
        $modelOption->modelClass = $model::class;
        return $modelOption;
    }

    /**
     * @param T|null|Builder<T>|Enumerable<array-key, T> $value
     * @param class-string<T>|null $modelClass
     */
    public static function ofNullable(
        mixed $value,
        string|null $modelClass = null,
    ): static {
        $model = self::unwrapValue($value, nullable: true);
        $modelOption = parent::ofNullable($model);
        /** @var class-string<T>|null $modelClass */
        $modelClass = match (true) {
            $model !== null => $model::class,
            $value instanceof Builder => $value->getModel()::class,
            default => $modelClass,
        };
        $modelOption->modelClass = $modelClass;
        return $modelOption;
    }

    public function orElseThrow(
        callable|string|null $exceptionSupplier = null,
        string|null $message = null,
    ): Model {
        return parent::orElseThrow($exceptionSupplier ?? function (string|null $message): ModelNotFoundException {
            if ($message === null) {
                $modelNotFoundException = new ModelNotFoundException();
                if ($this->modelClass !== null) {
                    $modelNotFoundException->setModel($this->modelClass);
                }
            } else {
                $modelNotFoundException = new ModelNotFoundException($message);
            }
            return $modelNotFoundException;
        }, $message);
    }

    protected static function getInstanceOf(): string
    {
        return Model::class;
    }

    /**
     * @param T|null|Builder<T>|Enumerable<array-key, T> $value
     *
     * @return ($nullable is true ? T|null : T)
     */
    private static function unwrapValue(
        Builder|Enumerable|Model|null $value,
        bool $nullable,
    ): Model|null {
        try {
            /** @var T|null */
            return match (true) {
                $value instanceof Builder, $value instanceof Enumerable => $value->sole(),
                default => $value,
            };
        } catch (RecordsNotFoundException | ItemNotFoundException $exception) {
            if ($nullable) {
                return null;
            }
            throw $exception;
        }
    }
}
