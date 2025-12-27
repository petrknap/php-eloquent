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
    private Throwable|null $previousException = null;

    /**
     * @param Builder<T>|Enumerable<array-key, T> $value
     *
     * @return self<T>
     */
    public static function soleOf(Builder|Enumerable $value): self
    {
        try {
            $model = $value->sole();
            $notFoundException = null;
        } catch (ItemNotFoundException | ModelNotFoundException $notFoundException) {
            $model = null;
        }
        $self = self::ofNullable($model);
        $self->previousException = $notFoundException;
        return $self;
    }

    public function orElseThrow(
        callable|string|null $exceptionSupplier = null,
        string|null $message = null,
    ): Model {
        return parent::orElseThrow(
            $exceptionSupplier
                ?? fn (string|null $message): Exception\CouldNotGetValueOfEmptyOptional => $message === null
                ? new Exception\CouldNotGetValueOfEmptyOptional(previous: $this->previousException)
                : new Exception\CouldNotGetValueOfEmptyOptional($message, previous: $this->previousException),
            $message,
        );
    }

    protected static function getInstanceOf(): string
    {
        return Model::class;
    }
}
