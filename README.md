# Process Promises

With this library you can use [Symfony Process Component][1] processes with a [jQuery][2] like promise pattern.

## Installation

The package is available on Packagist. You can install it using Composer.

```json
"require": {
    "knagg0/process-promises": "dev-master"
}
```

## Usage

The following shows a simple example how to use this library.
For more examples, you can take a look at the [example files][3].

```php
$manager = new ProcessManager();

$manager->start(
    new PhpProcess('<?php echo "Hello World";');
)->done(function ($result) {
    echo 'Process done: ' . $result;
})->fail(function ($reason) {
    echo 'Process fail: ' . $reason;
});

$manager->wait();
```

## License

MIT License. See the LICENSE file for full details.

[1]: http://symfony.com/doc/current/components/process.html
[2]: http://api.jquery.com/Types/#Promise
[3]: https://github.com/knagg0/process-promises/tree/master/examples
