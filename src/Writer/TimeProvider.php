<?php

namespace Zalora\Punyan;

/**
 * This is a simple wrapper to provide functionality for creation of time.
 */
class TimeProvider
{
    /**
     * Returns the micro time in float.
     *
     * @return mixed
     */
    public function getMicroTime()
    {
        return round(microtime(true) * 1000);
    }
}