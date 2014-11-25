<?php

namespace Knagg0\ProcessPromises\Tests;

use Knagg0\ProcessPromises\Promise;
use Knagg0\ProcessPromises\When;

class WhenTest extends \PHPUnit_Framework_TestCase
{
    public function testDone()
    {
        $results = array();
        $doneCallback = function ($result) use (&$results) {
            $results[] = $result;
        };

        $reasons = array();
        $failCallback = function ($reason) use (&$reasons) {
            $reasons[] = $reason;
        };

        $promise1 = new Promise();
        $promise2 = new Promise();

        $when = new When(array($promise1, $promise2));

        $when->done($doneCallback);
        $when->fail($failCallback);

        $promise1->resolve('foo');
        $promise2->resolve('bar');

        $this->assertCount(1, $results);
        $this->assertSame(
            array(
                array('foo', 'bar')
            ),
            $results
        );

        $this->assertCount(0, $reasons);
    }

    public function testFail()
    {
        $results = array();
        $doneCallback = function ($result) use (&$results) {
            $results[] = $result;
        };

        $reasons = array();
        $failCallback = function ($reason) use (&$reasons) {
            $reasons[] = $reason;
        };

        $promise1 = new Promise();
        $promise2 = new Promise();
        $promise3 = new Promise();
        $promise4 = new Promise();

        $when = new When(array($promise1, $promise2, $promise3, $promise4));

        $when->done($doneCallback);
        $when->fail($failCallback);

        $promise1->resolve('foo');
        $promise2->reject('bar');
        $promise3->resolve('foobar');
        $promise4->resolve('barfoo');

        $this->assertCount(0, $results);

        $this->assertCount(1, $reasons);
        $this->assertSame(
            array('bar'),
            $reasons
        );
    }
}
