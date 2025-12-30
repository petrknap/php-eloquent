# A collection of enhancements and helper classes for Eloquent

- [Casts](#casts)
- [Optionals](#optionals)
- [Repositories](#repositories)



## [Casts](./src/Casts/)

Casts define **how values move between the database and your domain**, ensuring each field is automatically transformed into the model’s native type.

```php
namespace PetrKnap\Eloquent\Casts;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $local_datetime
 * @property Carbon $utc_datetime
 */
final class SomeModel extends Model
{
    protected function casts(): array
    {
        return [
            'utc_datetime' => AsUtc::dateTime(),
            'local_datetime_utc' => AsUtc::dateTime(readonly: true),
            'local_datetime_timezone' => AsPrivate::class,
        ];
    }

    protected function localDatetime(): Attribute
    {
        return AsUtc::withTimezone(
            'local_datetime_utc',
            $this->getDateFormat(),
            'local_datetime_timezone',
        );
    }
}

$datetime = Carbon::parse('2025-12-06 11:58:21 Europe/Prague');

$model = new SomeModel();
$model->utc_datetime = $datetime;
$model->local_datetime = $datetime;
$model->save();

$model->refresh();
printf(
    "  UTC DT: %s %s\nLocal DT: %s %s\n",
    $model->utc_datetime->toDateTimeString(), $model->utc_datetime->getTimezone(),
    $model->local_datetime->toDateTimeString(), $model->local_datetime->getTimezone(),
);
```
```
  UTC DT: 2025-12-06 10:58:21 UTC
Local DT: 2025-12-06 11:58:21 Europe/Prague
```



## Optionals

Optionals let you **delegate the handling of missing models** to another layer of your application.
Instead of forcing a decision at the point of retrieval, they give you a structured way to express uncertainty and decide later how absence should be interpreted.

```php
namespace PetrKnap\Eloquent;

// someone selects the model as option
$modelOption = Optional::ofSole(
    Some\Model::query()->where('value', '=', 'unique'),
);

// someone else decides that it must be found and prints it
printf(
    "There is exactly one %s result.\n",
    $modelOption->orElseThrow()->value,
);
```
```
There is exactly one unique result.
```



## Repositories

Repositories provide a **clean, expressive interface** for working with your data layer.
They encapsulate all persistence logic—queries, inserts, updates, and deletes—so the rest of your application can interact with models through a consistent, intention‑revealing API.
By centralizing data access, repositories help keep your domain logic focused and testable.

```php
namespace PetrKnap\Eloquent;

function some_update(Some\ModelRepository $modelRepository): void
{
    $modelRepository->getConnection()->transaction(function () use ($modelRepository): void
    {
        foreach ($modelRepository->findByValue('common', lockForUpdate: true) as $commonModel)
        {
            $commonModel->value .= ' #' . $commonModel->id;
            $modelRepository->save($commonModel);
        }
    });
}
some_update(new Some\ModelRepository());

use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase
{
    public function testUpdates(): void
    {
        $model = new Some\Model();
        $model->id = 1;
        $model->value = 'common';

        $connection = self::createMock(Connection::class);
        $connection->expects(self::once())->method('transaction')
            ->willReturnCallback(fn (callable $callback): mixed => $callback($connection));

        $repository = self::createMock(Some\ModelRepository::class);
        $repository->expects(self::once())->method('getConnection')
            ->willReturn($connection);
        $repository->expects(self::once())->method('findByValue')
            ->with($model->value, true)
            ->willReturn(new Collection([$model]));
        $repository->expects(self::once())->method('save')
            ->with($model)
            ->willReturnCallback(function (Some\Model $model): Some\Model {
                self::assertSame('common #1', $model->value);
                return $model;
            });

        some_update($repository);
    }
}
(new SomeTest('example'))->testUpdates();
```

---

Run `composer require petrknap/eloquent` to install it.
You can [support this project via donation](https://petrknap.github.io/donate.html).
The project is licensed under [the terms of the `LGPL-3.0-or-later`](./COPYING.LESSER).
