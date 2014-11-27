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

        foreach ($promises as $promise) {
            $promise->fail(
                function ($reason) use (&$isRejected) {
                    if ($this->isRejected() === false) {
                        $this->reject($reason);
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
