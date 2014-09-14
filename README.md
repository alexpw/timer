Timer
=====

A quick to write and easy to use PHP timer for benchmarking.

### Usage

```php
require 'vendor/autoload.php';

use Alexpw\Timer\Timer;

$t = new Timer;

$max = 500000;
$xs  = range(1, $max);
$odd = function ($x) {
    return $x % 2 === 1;
};
function isOdd($x) {
	return $x % 2 === 1;
}
$rs  = array();

$run = $t->start("odd? control");

foreach ($xs as $x) {
    if ($x % 2 === 1) {
        $rs[] = $x;
    }
}
$t->end($run);

$rs  = array(); // use the gap between runs to teardown/setup

$run = $t->start("odd? closure");

foreach ($xs as $x) {
    if ($odd($x)) {
        $rs[] = $x;
    }
}
$t->end($run);

$rs  = array();
$run = $t->start("odd? function");

foreach ($xs as $x) {
    if (isOdd($x)) {
        $rs[] = $x;
    }
}
$t->end($run);

$rs  = array();
$run = $t->start("odd? array_filter closure");
$rs = array_filter($xs, $odd);
$t->end($run);

$rs  = array();
$run = $t->start("odd? array_filter function");
$rs = array_filter($xs, 'isOdd');
$t->end($run);

unset($rs);

echo $t->render();
```

### Example Output
```
   label                                   time (ms)   perc           mem
------------------------------------------------------------------------------
   odd? control                            71.5       11.90      22.98 MB
   odd? closure                            131.3      21.85      22.98 MB
   odd? function                           123.9      20.62      22.98 MB
   odd? array_filter closure               125.9      20.95      22.98 MB
   odd? array_filter function              151.6      25.22      22.98 MB
------------------------------------------------------------------------------
```
The tim
### License

Licensed under the MIT License - see the LICENSE file for details
