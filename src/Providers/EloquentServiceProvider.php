<?php

declare(strict_types=1);

namespace PetrKnap\Eloquent\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use PetrKnap\Eloquent\Mixins\BuilderMixin;

final class EloquentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Builder::mixin(new BuilderMixin());
    }
}
