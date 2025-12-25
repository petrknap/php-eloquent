<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent\Some;

use Illuminate\Database\Eloquent\Model as Base;
use PetrKnap\Eloquent\Casts\AsPrivate;

/**
 * @property string $value
 */
final class Model extends Base
{
    protected function casts(): array
    {
        return [
            'private_attribute' => AsPrivate::class,
        ];
    }
}
