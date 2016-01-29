<?php
/**
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Processor;
use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Processor
 */
interface IProcessor
{
    /**
     * Key in event log for processors
     * @var string
     */
    const PROCESSOR_DATA_KEY = 'proc';

    /**
     * Every processor has to define this key to check for onDemand
     * @var string
     */
    const PROCESSOR_KEY = null;

    /**
     * @var string
     */
    const PROCESSOR_NAMESPACE = __NAMESPACE__;

    /**
     * Add data to the log event here
     * @param LogEvent $logEvent
     */
    public function process(LogEvent $logEvent);
}
