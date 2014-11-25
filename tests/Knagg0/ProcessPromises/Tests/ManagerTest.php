<?php

namespace Knagg0\ProcessPromises\Tests;

use Knagg0\ProcessPromises\Manager;
use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\Process;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Manager
     */
    private $manager;

    public function setUp()
    {
        $this->manager = new Manager();
    }

    public function testSimpleDoneOutput()
    {
        $expectedOutput = 'Process Output';
        $actualOutput = '';

        $promise = $this->manager->start(
            new PhpProcess('<?php echo "' . $expectedOutput . '";')
        );

        $promise->done(
            function ($output) use (&$actualOutput) {
                $actualOutput = $output;
            }
        );

        $this->manager->wait();

        $this->assertSame($expectedOutput, $actualOutput);
    }

    public function testSimpleFailOutput()
    {
        $actualOutput = '';

        $promise = $this->manager->start(
            new PhpProcess('<?php parse->error;')
        );

        $promise->fail(
            function ($output) use (&$actualOutput) {
                $actualOutput = $output;
            }
        );

        $this->manager->wait();

        $this->assertContains('PHP Parse error', $actualOutput);
    }

    public function testProgress()
    {
        $updates = $types = array();
        $progressCallback = function ($update, $type) use (&$updates, &$types) {
            $updates[] = $update;
            $types[] = $type;
        };

        $promise = $this->manager->start(
            new PhpProcess('<?php echo "1"; usleep(2000); file_put_contents("php://stderr", "2"); usleep(2000); echo "3";')
        );

        $promise->progress($progressCallback);

        $this->manager->wait();

        $this->assertCount(3, $updates);
        $this->assertCount(3, $types);

        $this->assertSame(
            array("1", "2", "3"),
            $updates
        );

        $this->assertSame(
            array(Process::OUT, Process::ERR, Process::OUT),
            $types
        );
    }

    public function testSequence()
    {
        $outputs = array();
        $doneCallback = function ($output) use (&$outputs) {
            $outputs[] = $output;
        };

        $waitTimer = 1000;

        $promise1 = $this->manager->start(
            new PhpProcess('<?php usleep(' . $waitTimer * 100 . '); echo "1";')
        );

        $promise2 = $this->manager->start(
            new PhpProcess('<?php usleep(' . ($waitTimer) . '); echo "2";')
        );

        $promise1->done($doneCallback);
        $promise2->done($doneCallback);

        $this->manager->wait($waitTimer);

        $this->assertCount(2, $outputs);
        $this->assertSame('2', $outputs[0]);
        $this->assertSame('1', $outputs[1]);
    }

    public function testWhen()
    {
        $this->assertInstanceOf('Knagg0\ProcessPromises\When', $this->manager->when(array()));
    }
}
