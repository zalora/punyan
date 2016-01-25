<?php
/**
 * Lazy processor doesn't do anything
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Processor;
use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Processor
 */
class NoOp implements IProcessor
{
    /**
     * @param LogEvent $event
     * @return void
     */
    public function process(LogEvent $event)
    {
    }
}
