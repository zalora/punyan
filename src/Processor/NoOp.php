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
class NoOp extends AbstractProcessor
{
    /**
     * @var string
     */
    const PROCESSOR_KEY = 'NoOp';

    /**
     * @param LogEvent $logEvent
     * @return void
     */
    public function process(LogEvent $logEvent)
    {
    }
}
