<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Enumerable;
use Illuminate\Support\ItemNotFoundException;
use PetrKnap\Optional\Exception;
use PetrKnap\Optional\OptionalObject;
use Throwable;

/**
 * @template T of Model
 *
 * @extends OptionalObject<T>
 */
final class Optional extends OptionalObject
{
    private Throwable|null $constructionException = null;

    /**
     * @param T|Builder<T>|Enumerable<array-key, T> $value
     */
    public static function of(mixed $value): static
    {
        return parent::of(self::unwrapValue($value));
    }

    /**
     * @param T|null|Builder<T>|Enumerable<array-key, T> $value
     */
    public static function ofNullable(mixed $value): static
    {
        $exception = null;
        try {
            $model = $value !== null ? self::unwrapValue($value) : null;
        } catch (ItemNotFoundException | ModelNotFoundException $exception) {
            $model = null;
        }
        $modelOption = parent::ofNullable($model);
        $modelOption->constructionException = $exception;
        return $modelOption;
    }

    public function orElseThrow(
        callable|string|null $exceptionSupplier = null,
        string|null $message = null,
    ): Model {
        return parent::orElseThrow(
            $exceptionSupplier
                ?? fn (string|null $message): Exception\CouldNotGetValueOfEmptyOptional => $message === null
                ? new Exception\CouldNotGetValueOfEmptyOptional(previous: $this->constructionException)
                : new Exception\CouldNotGetValueOfEmptyOptional($message, previous: $this->constructionException),
            $message,
        );
    }

    protected static function getInstanceOf(): string
    {
        return Model::class;
    }

    /**
     * @param T|Builder<T>|Enumerable<array-key, T> $value
     *
     * @return T
     *
     * @throws Throwable
     */
    private static function unwrapValue(mixed $value): mixed
    {
        /** @var T */
        return match (true) {
            $value instanceof Builder, $value instanceof Enumerable => $value->sole(),
            default => $value,
        };
    }
}
