<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent\Some;

use Illuminate\Support\Enumerable;
use PetrKnap\Eloquent\Repository;

/**
 * @extends Repository<Model>
 */
class ModelRepository extends Repository
{
    /**
     * @return Enumerable<array-key, Model>
     */
    public function findByValue(string $value, bool $lockForUpdate = false): Enumerable
    {
        $query = $this->newQuery()->where('value', '=', $value);
        if ($lockForUpdate) {
            $query->lockForUpdate();
        }
        return $query->get();
    }

    protected static function getModelClass(): string
    {
        return Model::class;
    }
}
