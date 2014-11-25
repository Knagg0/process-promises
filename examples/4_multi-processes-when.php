<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Knagg0\ProcessPromises\Manager;
use Symfony\Component\Process\PhpProcess;

$process1 = new PhpProcess('<?php sleep(2); echo "1";');
$process2 = new PhpProcess('<?php sleep(1); echo "2";');
$process3 = new PhpProcess('<?php sleep(3); echo "3";');

$manager = new Manager();

$promise1 = $manager->start($process1);
$promise2 = $manager->start($process2);
$promise3 = $manager->start($process3);

$promise2->done(
    function ($result) {
        echo "Process 2 done: " . $result . "\n";
    }
);

$when = $manager->when(array($promise1, $promise3));

$when->done(
    function ($results) {
        echo "Process 1 + 3 done: " . implode(', ', $results) . "\n";
    }
);

$manager->wait();
