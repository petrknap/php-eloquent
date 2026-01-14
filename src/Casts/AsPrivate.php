<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use LogicException;

/**
 * @implements CastsAttributes<null, mixed>
 */
final class AsPrivate implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): null
    {
        return null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) { // Eloquent sometimes calls setter with the cast value
            return $attributes[$key];
        }

        throw new LogicException(sprintf('%s::$%s is private', get_class($model), $key));
    }
}
