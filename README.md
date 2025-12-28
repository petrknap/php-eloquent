# A collection of enhancements and helper classes for Eloquent

- [Casts](#casts)
- [Optional](#optional)



## Casts

Casts are useful for **automatically converting values** between the database representation and the models native types.

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



## Optional

Model options are helpful when you need to **delegate the decision** of how to handle a missing model to another part of the system.

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

---

Run `composer require petrknap/eloquent` to install it.
You can [support this project via donation](https://petrknap.github.io/donate.html).
The project is licensed under [the terms of the `LGPL-3.0-or-later`](./COPYING.LESSER).
