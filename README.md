# A collection of enhancements and helper classes for Eloquent



## Casts

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use PetrKnap\Eloquent\Casts\AsUtc;

/**
 * @property Carbon $utc_attribute
 */
final class SomeModel extends Model {
    protected function casts(): array {
        return [
            'utc_attribute' => AsUtc::dateTime(),
        ];
    }
}

$model = new SomeModel();
$model->utc_attribute = Carbon::parse('2025-12-06 11:58:21 Europe/Prague');

print_r($model->getAttributes());
```
```
Array
(
    [utc_attribute] => 2025-12-06 10:58:21
)
```

---

Run `composer require petrknap/eloquent` to install it.
You can [support this project via donation](https://petrknap.github.io/donate.html).
The project is licensed under [the terms of the `LGPL-3.0-or-later`](./COPYING.LESSER).
