<?php
/**
 * Doesn't do anything, perfect for unit tests
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Writer
 */
class NoWriter extends AbstractWriter
{
    /**
     * @return void
     * @throws \RuntimeException
     */
    public function init()
    {
    }

    /**
     * @param LogEvent $logEvent
     * @return void
     */
    protected function _write(LogEvent $logEvent)
    {
    }
}
