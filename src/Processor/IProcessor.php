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
     * @var string
     */
    const PROCESSOR_KEY = 'proc';

    /**
     * @var string
     */
    const PROCESSOR_NAMESPACE = __NAMESPACE__;

    /**
     * Add data to the log event here
     * @param LogEvent $event
     */
    public function process(LogEvent $event);
}
