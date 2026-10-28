<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent\Mixins;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use PetrKnap\Eloquent\Optional;

/**
 * @template TModel of Model
 *
 * @note Template `TModel` must be defined the same way AS IS definded in `barryvdh/laravel-ide-helper`.
 * @note Internal returns must use fully qualified class names.
 */
final class BuilderMixin
{
    /**
     * @return callable(): Optional<TModel>
     */
    public function option(): callable
    {
        /**
         * @return \PetrKnap\Eloquent\Optional<TModel>
         */
        return function (): Optional {
            /** @var Builder<TModel> $this */
            return Optional::ofSole($this);
        };
    }

    /**
     * @return callable(): (TModel|null)
     */
    public function soleOrNull(): callable
    {
        /**
         * @return (TModel|null)
         */
        return function (): ?Model {
            /**
             * @var Builder<TModel> $this
             * @var Optional<TModel> $option
             */
            $option = $this->option(); // @phpstan-ignore method.notFound
            return $option->orElse(null);
        };
    }
}
