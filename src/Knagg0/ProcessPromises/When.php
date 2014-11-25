<?php

namespace Knagg0\ProcessPromises;

class When extends Promise
{
    /**
     * Provides a way to execute callback functions based on one or more promise objects
     *
     * @param Promise[] $promises
     */
    public function __construct($promises)
    {
        $results = array();
        $isRejected = false;

        foreach ($promises as $promise) {
            $promise->fail(
                function ($reason) use (&$isRejected) {
                    if ($isRejected === false) {
                        $this->reject($reason);
                        $isRejected = true;
                    }
                }
            );
            $promise->done(
                function ($result) use (&$promises, &$results) {
                    $results[] = $result;
                    if (count($results) >= count($promises)) {
                        $this->resolve($results);
                    }
                }
            );
        }
    }
}
