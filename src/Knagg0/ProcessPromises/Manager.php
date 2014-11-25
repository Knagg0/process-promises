<?php

namespace Knagg0\ProcessPromises;

use Symfony\Component\Process\Process;

class Manager
{
    /**
     * @var Process[]
     */
    protected $prosesses = array();

    /**
     * @var Promise[]
     */
    protected $promises = array();

    /**
     * Starts a process and returns a promise
     *
     * @param Process $process
     *
     * @return Promise
     */
    public function start(Process $process)
    {
        $promise = new Promise();

        $process->start(
            function ($type, $update) use ($promise) {
                $promise->notify($update, $type);
            }
        );

        $this->promises[] = $promise;
        $this->prosesses[] = $process;

        return $promise;
    }

    /**
     * Waits for all processes to terminate
     *
     * @param int $waitTimer    Halt time in micro seconds
     */
    public function wait($waitTimer = 1000)
    {
        while (count($this->prosesses) > 0) {
            foreach ($this->prosesses as $index => $process) {
                $process->checkTimeout();
                if (!$process->isRunning()) {
                    if ($process->isSuccessful()) {
                        $this->promises[$index]->resolve($process->getOutput());
                    } else {
                        $this->promises[$index]->reject($process->getErrorOutput());
                    }
                    unset($this->promises[$index], $this->prosesses[$index]);
                }
            }
            usleep($waitTimer);
        }
    }

    /**
     * Provides a way to execute callback functions based on one or more promises
     *
     * @param Promise[] $promises
     *
     * @return When
     */
    public function when(array $promises)
    {
        return new When($promises);
    }
}
