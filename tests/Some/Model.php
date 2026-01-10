<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent\Some;

use Illuminate\Database\Eloquent\Model as Base;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PetrKnap\Eloquent\Casts\AsPrivate;

/**
 * @property-read int|null $id
 * @property string $value
 */
class Model extends Base
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'private_attribute' => AsPrivate::class,
        ];
    }
}
