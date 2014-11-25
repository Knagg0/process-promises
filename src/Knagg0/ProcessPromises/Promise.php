<?php

namespace Knagg0\ProcessPromises;

class Promise
{
    /**
     * @var callable[]
     */
    protected $doneCallbacks = [];

    /**
     * @var callable[]
     */
    protected $failCallbacks = [];

    /**
     * @var callable[]
     */
    protected $progressCallbacks = [];

    /**
     * Add handlers to be called when the promise object is resolved
     *
     * @param callable $doneCallback
     *
     * @return $this
     */
    public function done(callable $doneCallback)
    {
        $this->doneCallbacks[] = $doneCallback;

        return $this;
    }

    /**
     * Add handlers to be called when the promise object is rejected
     *
     * @param callable $failCallback
     *
     * @return $this
     */
    public function fail(callable $failCallback)
    {
        $this->failCallbacks[] = $failCallback;

        return $this;
    }

    /**
     * Add handlers to be called when the promise object generates progress notifications
     *
     * @param callable $progressCallback
     *
     * @return $this
     */
    public function progress(callable $progressCallback)
    {
        $this->progressCallbacks[] = $progressCallback;

        return $this;
    }

    /**
     * Add handlers to be called when the promise object is either resolved or rejected
     *
     * @param callable $alwayFallback
     *
     * @return $this
     */
    public function always(callable $alwayFallback)
    {
        $this->done($alwayFallback);
        $this->fail($alwayFallback);

        return $this;
    }

    /**
     * Add handlers to be called when the promise object is resolved, rejected or still in progress
     *
     * @param callable $doneCallback
     * @param callable $failCallback
     * @param callable $progressCallback
     *
     * @return $this
     */
    public function then(callable $doneCallback, callable $failCallback = null, callable $progressCallback = null)
    {
        $this->done($doneCallback);

        if ($failCallback !== null) {
            $this->fail($failCallback);
        }

        if ($progressCallback !== null) {
            $this->progress($progressCallback);
        }

        return $this;
    }

    /**
     * Reject a promise object and call any failCallbacks with the given reason
     *
     * @param mixed $reason
     *
     * @return $this
     */
    public function reject($reason = null)
    {
        foreach ($this->failCallbacks as $failCallback) {
            $failCallback($reason);
        }

        return $this;
    }

    /**
     * Resolve a promise object and call any doneCallbacks with the given result
     *
     * @param mixed $result
     *
     * @return $this
     */
    public function resolve($result = null)
    {
        foreach ($this->doneCallbacks as $doneCallback) {
            $doneCallback($result);
        }

        return $this;
    }

    /**
     * Call the progressCallbacks on a promise object with the given args
     *
     * @param mixed  $update
     * @param string $type
     *
     * @return $this
     */
    public function notify($update = null, $type = null)
    {
        foreach ($this->progressCallbacks as $progressCallback) {
            $progressCallback($update, $type);
        }

        return $this;
    }
}
