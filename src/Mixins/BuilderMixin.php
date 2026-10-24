<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent\Mixins;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use PetrKnap\Eloquent\Optional;

/**
 * @template T of Model
 */
final class BuilderMixin
{
    /**
     * @return callable(): Optional<T>
     */
    public function option(): callable
    {
        /** @var callable(): Optional<T> */
        return function (): Optional {
            /** @var Builder<T> $this */
            return Optional::ofSole($this);
        };
    }

    /**
     * @return callable(): ?T
     */
    public function soleOrNull(): callable
    {
        return function (): ?Model {
            /**
             * @var Builder<T> $this
             * @var Optional<T> $option
             */
            $option = $this->option(); // @phpstan-ignore method.notFound
            return $option->orElse(null);
        };
    }
}
