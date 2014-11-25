<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Knagg0\ProcessPromises\Manager;
use Symfony\Component\Process\PhpProcess;

$process = new PhpProcess('<?php parse->error;');

$manager = new Manager();

$promise = $manager->start($process);

$promise->done(
    function ($result) {
        echo "Process successful: $result\n";
    }
)->fail(
    function ($reason) {
        echo "Process fail: $reason\n";
    }
);

$manager->wait();
