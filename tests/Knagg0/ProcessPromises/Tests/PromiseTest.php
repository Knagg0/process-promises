<?php

namespace Knagg0\ProcessPromises\Tests;

use Knagg0\ProcessPromises\Promise;

class PromiseTest extends \PHPUnit_Framework_TestCase
{
    public function testDone()
    {
        $results = array();
        $doneCallback = function ($result) use (&$results) {
            $results[] = $result;
        };

        $promise = new Promise();
        $promise->done($doneCallback);
        $promise->done($doneCallback);
        $promise->done($doneCallback);
        $promise->resolve('foo');

        $this->assertCount(3, $results);
        $this->assertSame(
            array('foo', 'foo', 'foo'),
            $results
        );
    }

    public function testFail()
    {
        $reasons = array();
        $failCallback = function ($reason) use (&$reasons) {
            $reasons[] = $reason;
        };

        $promise = new Promise();
        $promise->fail($failCallback);
        $promise->fail($failCallback);
        $promise->reject('foo');

        $this->assertCount(2, $reasons);
        $this->assertSame(
            array('foo', 'foo'),
            $reasons
        );
    }

    public function testProgress()
    {
        $updates = array();
        $progressCallback = function ($update) use (&$updates) {
            $updates[] = $update;
        };

        $promise = new Promise();
        $promise->progress($progressCallback);
        $promise->progress($progressCallback);
        $promise->notify('foo');
        $promise->notify('foo');

        $this->assertCount(4, $updates);
        $this->assertSame(
            array('foo', 'foo', 'foo', 'foo'),
            $updates
        );
    }

    public function testAlways()
    {
        $results = array();
        $alwaysCallback = function ($result) use (&$results) {
            $results[] = $result;
        };

        $promise = new Promise();
        $promise->always($alwaysCallback);
        $promise->resolve('foo');
        $promise->reject('foo');

        $this->assertCount(2, $results);
        $this->assertSame(
            array('foo', 'foo'),
            $results
        );
    }

    public function testThen()
    {
        $results = array();
        $doneCallback = function ($result) use (&$results) {
            $results[] = $result;
        };

        $reasons = array();
        $failCallback = function ($reason) use (&$reasons) {
            $reasons[] = $reason;
        };

        $updates = array();
        $progressCallback = function ($update) use (&$updates) {
            $updates[] = $update;
        };

        $promise = new Promise();
        $promise->then($doneCallback);
        $promise->then($doneCallback, $failCallback);
        $promise->then($doneCallback, $failCallback, $progressCallback);

        $promise->resolve('foo');
        $promise->reject('bar');
        $promise->notify('foobar');

        $this->assertCount(3, $results);
        $this->assertSame(
            array('foo', 'foo', 'foo'),
            $results
        );

        $this->assertCount(2, $reasons);
        $this->assertSame(
            array('bar', 'bar'),
            $reasons
        );

        $this->assertCount(1, $updates);
        $this->assertSame(
            array('foobar'),
            $updates
        );
    }
}
