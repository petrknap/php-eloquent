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
    private Throwable|null $notFoundException = null;

    // @todo update optional and override `ofSingle` method, call `ofSole` if the input is `Builder` or `Enumerable`

    /**
     * An "illuminated" alternative to {@see Optional::ofSingle()}
     *
     * @param Builder<T>|Enumerable<array-key, T> $value
     *
     * @see Builder::sole()
     * @see Enumerable::sole()
     */
    public static function ofSole(Builder|Enumerable $value): static
    {
        try {
            $model = $value->sole();
            $notFoundException = null;
        } catch (ItemNotFoundException | ModelNotFoundException $notFoundException) {
            $model = null;
        }
        $self = self::ofNullable($model);
        $self->notFoundException = $notFoundException;
        return $self;
    }

    public function orElseThrow(
        callable|string|null $exceptionSupplier = null,
        string|null $message = null,
    ): Model {
        return parent::orElseThrow(
            $exceptionSupplier
                ?? fn (string|null $message): Exception\CouldNotGetValueOfEmptyOptional => $message === null
                ? new Exception\CouldNotGetValueOfEmptyOptional(previous: $this->notFoundException)
                : new Exception\CouldNotGetValueOfEmptyOptional($message, previous: $this->notFoundException),
            $message,
        );
    }

    protected static function getInstanceOf(): string
    {
        return Model::class;
    }
}
